<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseClaimUiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\ExpenseClaim::query()
            ->with(['employee', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($user->role !== 'admin') {
            // If regular user/volunteer, only see own claims
            // Assuming user is linked to employee via email or some other mechanism. 
            // For now, let's assume we filter by company_id at least.
             $query->where('company_id', $user->company_id);
             // Ideally we filter by employee_id if the user is an employee.
             // But for now let's just show all company claims for admin, and maybe filter for others.
        } else {
             $query->where('company_id', $user->company_id);
        }

        $claims = $query->orderByDesc('date')->paginate(20);
        $employees = \App\Models\Employee::where('company_id', $user->company_id)->orderBy('full_name')->get();

        return view('expense_claims.index', compact('claims', 'employees'));
    }

    public function create()
    {
        $user = request()->user();
        $employees = \App\Models\Employee::where('company_id', $user->company_id)->orderBy('full_name')->get();
        return view('expense_claims.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'receipt_url' => 'nullable|string|url',
        ]);

        $data['company_id'] = $request->user()->company_id;
        $data['status'] = 'pending';
        
        \App\Models\ExpenseClaim::create($data);

        return redirect()->route('expense-claims.index')->with('success', 'Klaim berhasil diajukan');
    }

    public function updateStatus(Request $request, $id)
    {
        $claim = \App\Models\ExpenseClaim::findOrFail($id);
        
        // Check authorization (only admin/approver)
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string',
        ]);

        $claim->update([
            'status' => $data['status'],
            'rejection_reason' => $data['rejection_reason'] ?? null,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Status klaim diperbarui');
    }
}
