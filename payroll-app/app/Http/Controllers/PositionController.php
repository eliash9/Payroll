<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Company;
use App\Models\Department;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    public function index()
    {
        $query = Position::query();
        
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        // Fetch all positions needed for hierarchical sorting
        $allPositions = $query->with(['department', 'parent', 'job.responsibilities', 'job.requirements'])->orderBy('parent_id')->orderBy('name')->get();
        
        $positions = $this->sortPositions($allPositions);

        return view('masters.positions.index', compact('positions'));
    }

    private function sortPositions($positions, $parentId = null, $depth = 0)
    {
        $result = collect();
        
        // Find children of current parent
        $children = $positions->filter(function ($item) use ($parentId) {
            return $item->parent_id == $parentId;
        })->sortBy('name');

        foreach ($children as $child) {
            $child->depth = $depth;
            $result->push($child);
            // Recursively add children
            $result = $result->merge($this->sortPositions($positions, $child->id, $depth + 1));
        }

        return $result;
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        $departmentsQuery = Department::query();
        if (Auth::user()->company_id) {
            $departmentsQuery->where('company_id', Auth::user()->company_id);
        }
        $departments = $departmentsQuery->pluck('name', 'id');

        $parentsQuery = Position::query();
        if (Auth::user()->company_id) {
            $parentsQuery->where('company_id', Auth::user()->company_id);
        }
        $parents = $parentsQuery->pluck('name', 'id');
        
        $jobsQuery = Job::query();
        if (Auth::user()->company_id) {
            $jobsQuery->where('company_id', Auth::user()->company_id);
        }
        $jobs = $jobsQuery->pluck('title', 'id');

        return view('masters.positions.create', compact('companies', 'departments', 'parents', 'jobs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'job_id' => 'nullable|integer|exists:job_profiles,id',
            'parent_id' => 'nullable|integer|exists:positions,id',
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:positions,code',
            'grade' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        Position::create($data);

        return redirect()->route('positions.index')->with('success', 'Jabatan ditambahkan.');
    }

    public function edit(int $id)
    {
        $position = Position::findOrFail($id);
        
        if (Auth::user()->company_id && $position->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this position.');
        }

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        $departmentsQuery = Department::query();
        if (Auth::user()->company_id) {
            $departmentsQuery->where('company_id', Auth::user()->company_id);
        }
        $departments = $departmentsQuery->pluck('name', 'id');

        $parentsQuery = Position::query()->where('id', '!=', $id);
        if (Auth::user()->company_id) {
            $parentsQuery->where('company_id', Auth::user()->company_id);
        }
        $parents = $parentsQuery->pluck('name', 'id');

        $jobsQuery = Job::query();
        if (Auth::user()->company_id) {
            $jobsQuery->where('company_id', Auth::user()->company_id);
        }
        $jobs = $jobsQuery->pluck('title', 'id');

        return view('masters.positions.edit', compact('position', 'companies', 'departments', 'parents', 'jobs'));
    }

    public function update(Request $request, int $id)
    {
        $position = Position::findOrFail($id);
        
        if (Auth::user()->company_id && $position->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this position.');
        }

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'job_id' => 'nullable|integer|exists:job_profiles,id',
            'parent_id' => 'nullable|integer|exists:positions,id',
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:positions,code,' . $id,
            'grade' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);
        
        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        $position->update($data);

        return redirect()->route('positions.index')->with('success', 'Jabatan diperbarui.');
    }

    public function destroy(int $id)
    {
        $position = Position::findOrFail($id);
        
        if (Auth::user()->company_id && $position->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this position.');
        }

        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Jabatan dihapus.');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PositionExport, 'positions.xlsx');
    }

    public function importTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PositionTemplateExport, 'position_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PositionImport, $request->file('file'));
            return redirect()->route('positions.index')->with('success', 'Positions imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->route('positions.index')->with('error', 'Import Validation Errors: ' . implode(' | ', $messages));
        } catch (\Exception $e) {
            return redirect()->route('positions.index')->with('error', 'Error importing positions: ' . $e->getMessage());
        }
    }
}
