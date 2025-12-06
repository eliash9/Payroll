<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function profile(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee record not found'], 404);
        }

        // Load relationships if needed (e.g., position, department)
        $employee->load(['position', 'department', 'branch']);

        return response()->json([
            'user' => $user,
            'employee' => $employee
        ]);
    }

    public function salarySlip(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee record not found'], 404);
        }

        // Logic to fetch latest salary slip
        // This is a placeholder as the exact salary slip logic depends on the Payroll module implementation
        // For now, we return basic info
        
        return response()->json([
            'basic_salary' => $employee->basic_salary,
            'allowances' => [], // Placeholder
            'deductions' => [], // Placeholder
            'net_salary' => $employee->basic_salary // Placeholder
        ]);
    }
}
