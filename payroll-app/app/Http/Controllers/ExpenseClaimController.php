<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseClaimController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\ExpenseClaim::query();

        $this->assertCompanyAccess($user?->company_id, $request->input('company_id'));

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        } elseif ($user?->company_id) {
            $query->where('company_id', $user->company_id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->orderByDesc('date')->paginate(20));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'employee_id' => 'required|integer|exists:employees,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'receipt_url' => 'nullable|string|url',
        ]);

        $this->assertCompanyAccess($user?->company_id, $data['company_id']);

        $claim = \App\Models\ExpenseClaim::create(array_merge($data, ['status' => 'pending']));

        return response()->json(['status' => 'ok', 'data' => $claim], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        $claim = \App\Models\ExpenseClaim::findOrFail($id);

        $this->assertCompanyAccess($user?->company_id, $claim->company_id);

        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string',
        ]);

        $claim->update([
            'status' => $data['status'],
            'rejection_reason' => $data['rejection_reason'] ?? null,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json(['status' => 'ok', 'data' => $claim]);
    }

    private function assertCompanyAccess(?int $userCompanyId, ?int $targetCompanyId): void
    {
        if ($userCompanyId && $targetCompanyId && $userCompanyId !== (int) $targetCompanyId) {
            abort(403, 'Company scope mismatch.');
        }
    }
}
