<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class ExpenseClaimUiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $query = DB::table('expense_claims')
            ->where('employee_id', $employee->id)
            ->orderByDesc('date');

        return response()->json(['data' => $query->paginate(20)]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $data = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'receipt_url' => 'nullable|string', // Assuming image handling separately or URL
        ]);

        $claimId = DB::table('expense_claims')->insertGetId([
            'company_id' => $user->company_id,
            'employee_id' => $employee->id,
            'date' => $data['date'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'receipt_url' => $data['receipt_url'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Klaim pengeluaran berhasil diajukan', 'id' => $claimId], 201);
    }
}
