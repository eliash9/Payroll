<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function index()
    {
        $query = Branch::query();
        
        // HasCompanyScope trait should handle filtering if applied.
        // But for safety and explicit control in controller:
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $branches = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('masters.branches.index', compact('branches'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');
        
        return view('masters.branches.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:branches,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'grade' => 'nullable|string|max:50',
            'province_code' => 'nullable|string',
            'province_name' => 'nullable|string',
            'city_code' => 'nullable|string',
            'city_name' => 'nullable|string',
            'district_code' => 'nullable|string',
            'district_name' => 'nullable|string',
            'village_code' => 'nullable|string',
            'village_name' => 'nullable|string',
            'is_headquarters' => 'nullable|boolean',
        ]);

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        Branch::create($data);

        return redirect()->route('branches.index')->with('success', 'Cabang ditambahkan.');
    }

    public function edit(int $id)
    {
        $branch = Branch::findOrFail($id);
        
        if (Auth::user()->company_id && $branch->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this branch.');
        }

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('masters.branches.edit', compact('branch', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $branch = Branch::findOrFail($id);
        
        if (Auth::user()->company_id && $branch->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this branch.');
        }

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:branches,code,' . $id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'grade' => 'nullable|string|max:50',
            'province_code' => 'nullable|string',
            'province_name' => 'nullable|string',
            'city_code' => 'nullable|string',
            'city_name' => 'nullable|string',
            'district_code' => 'nullable|string',
            'district_name' => 'nullable|string',
            'village_code' => 'nullable|string',
            'village_name' => 'nullable|string',
            'is_headquarters' => 'nullable|boolean',
        ]);
        
        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        $branch->update($data);

        return redirect()->route('branches.index')->with('success', 'Cabang diperbarui.');
    }

    public function destroy(int $id)
    {
        $branch = Branch::findOrFail($id);
        
        if (Auth::user()->company_id && $branch->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this branch.');
        }

        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Cabang dihapus.');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BranchExport, 'branches.xlsx');
    }

    public function importTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BranchTemplateExport, 'branch_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\BranchImport, $request->file('file'));
            return redirect()->route('branches.index')->with('success', 'Branches imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->route('branches.index')->with('error', 'Import Validation Errors: ' . implode(' | ', $messages));
        } catch (\Exception $e) {
            return redirect()->route('branches.index')->with('error', 'Error importing branches: ' . $e->getMessage());
        }
    }
}
