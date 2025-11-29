<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function index()
    {
        $query = Shift::query();
        
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        $shifts = $query->orderBy('name')->paginate(20);

        return view('masters.shifts.index', compact('shifts'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');
        
        return view('masters.shifts.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:shifts,code',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'tolerance_late_minutes' => 'nullable|integer|min:0',
            'tolerance_early_leave_minutes' => 'nullable|integer|min:0',
            'is_night_shift' => 'boolean',
        ]);

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        Shift::create(array_merge([
            'tolerance_late_minutes' => 0,
            'tolerance_early_leave_minutes' => 0,
            'is_night_shift' => false,
        ], $data));

        return redirect()->route('shifts.index')->with('success', 'Shift ditambahkan.');
    }

    public function edit(int $id)
    {
        $shift = Shift::findOrFail($id);
        
        if (Auth::user()->company_id && $shift->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this shift.');
        }

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('masters.shifts.edit', compact('shift', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $shift = Shift::findOrFail($id);
        
        if (Auth::user()->company_id && $shift->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this shift.');
        }

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:shifts,code,' . $id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'tolerance_late_minutes' => 'nullable|integer|min:0',
            'tolerance_early_leave_minutes' => 'nullable|integer|min:0',
            'is_night_shift' => 'boolean',
        ]);
        
        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        $data['tolerance_late_minutes'] = $data['tolerance_late_minutes'] ?? 0;
        $data['tolerance_early_leave_minutes'] = $data['tolerance_early_leave_minutes'] ?? 0;
        $data['is_night_shift'] = $request->boolean('is_night_shift');

        $shift->update($data);

        return redirect()->route('shifts.index')->with('success', 'Shift diperbarui.');
    }

    public function destroy(int $id)
    {
        $shift = Shift::findOrFail($id);
        
        if (Auth::user()->company_id && $shift->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this shift.');
        }

        $shift->delete();
        return redirect()->route('shifts.index')->with('success', 'Shift dihapus.');
    }
}
