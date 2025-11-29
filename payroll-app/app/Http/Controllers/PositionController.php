<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Company;
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

        $positions = $query->orderBy('name')->paginate(20);

        return view('masters.positions.index', compact('positions'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');
        
        return view('masters.positions.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'grade' => 'nullable|string|max:50',
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

        return view('masters.positions.edit', compact('position', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $position = Position::findOrFail($id);
        
        if (Auth::user()->company_id && $position->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this position.');
        }

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:191',
            'grade' => 'nullable|string|max:50',
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
}
