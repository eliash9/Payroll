<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $query = LeaveType::query();
        
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        $leaveTypes = $query->orderBy('name')->paginate(20);

        return view('masters.leave_types.index', compact('leaveTypes'));
    }

    public function create()
    {
        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');
        
        return view('masters.leave_types.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:leave_types,code',
            'is_paid' => 'boolean',
            'is_annual_quota' => 'boolean',
            'default_quota_days' => 'nullable|numeric|min:0',
        ]);

        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        LeaveType::create(array_merge([
            'is_paid' => false,
            'is_annual_quota' => false,
        ], $data));

        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti/izin ditambahkan.');
    }

    public function edit(int $id)
    {
        $leaveType = LeaveType::findOrFail($id);
        
        if (Auth::user()->company_id && $leaveType->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this leave type.');
        }

        $companiesQuery = Company::query();
        if (Auth::user()->company_id) {
            $companiesQuery->where('id', Auth::user()->company_id);
        }
        $companies = $companiesQuery->pluck('name', 'id');

        return view('masters.leave_types.edit', compact('leaveType', 'companies'));
    }

    public function update(Request $request, int $id)
    {
        $leaveType = LeaveType::findOrFail($id);
        
        if (Auth::user()->company_id && $leaveType->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this leave type.');
        }

        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:leave_types,code,' . $id,
            'is_paid' => 'boolean',
            'is_annual_quota' => 'boolean',
            'default_quota_days' => 'nullable|numeric|min:0',
        ]);
        
        if (Auth::user()->company_id && $data['company_id'] != Auth::user()->company_id) {
            abort(403, 'Unauthorized company selection.');
        }

        $data['is_paid'] = $request->boolean('is_paid');
        $data['is_annual_quota'] = $request->boolean('is_annual_quota');

        $leaveType->update($data);

        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti/izin diperbarui.');
    }

    public function destroy(int $id)
    {
        $leaveType = LeaveType::findOrFail($id);
        
        if (Auth::user()->company_id && $leaveType->company_id != Auth::user()->company_id) {
            abort(403, 'Unauthorized access to this leave type.');
        }

        $leaveType->delete();
        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti/izin dihapus.');
    }
}
