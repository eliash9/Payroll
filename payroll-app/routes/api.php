<?php

use App\Http\Controllers\FundraisingSummaryController;
use App\Http\Controllers\FundraisingTransactionController;
use App\Http\Controllers\RegularPayrollController;
use App\Http\Controllers\VolunteerPayrollController;
use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\Laz\PublicProgramController;
use App\Http\Controllers\Api\Laz\PublicApplicationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthTokenController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Illuminate\Http\Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'company.scope'])->group(function () {
    Route::post('/fundraising/transactions', [FundraisingTransactionController::class, 'store']);
    Route::get('/fundraising/transactions', [FundraisingTransactionController::class, 'index']);
    Route::post('/fundraising/summary/generate', [FundraisingSummaryController::class, 'generate']);
    Route::get('/fundraising/summary', [FundraisingSummaryController::class, 'index']);

    Route::get('/expense-claims', [\App\Http\Controllers\ExpenseClaimController::class, 'index']);
    Route::post('/expense-claims', [\App\Http\Controllers\ExpenseClaimController::class, 'store']);
    Route::post('/expense-claims/{id}/status', [\App\Http\Controllers\ExpenseClaimController::class, 'updateStatus']);

    Route::post('/payroll-periods/{id}/generate-volunteer-payroll', [VolunteerPayrollController::class, 'generate']);
    Route::post('/payroll-periods/{id}/generate-regular-payroll', [RegularPayrollController::class, 'generate']);

    Route::get('/companies/{company}/branches', function (int $company) {
        $userCompany = request()->user()?->company_id;
        if ($userCompany && $userCompany !== $company) {
            abort(403, 'Company scope mismatch.');
        }
        return DB::table('branches')->where('company_id', $company)->select('id', 'name')->orderBy('name')->get();
    });

    Route::get('/companies/{company}/departments', function (int $company) {
        $userCompany = request()->user()?->company_id;
        if ($userCompany && $userCompany !== $company) {
            abort(403, 'Company scope mismatch.');
        }
        return DB::table('departments')->where('company_id', $company)->select('id', 'name')->orderBy('name')->get();
    });

    Route::get('/companies/{company}/positions', function (int $company) {
        $userCompany = request()->user()?->company_id;
        if ($userCompany && $userCompany !== $company) {
            abort(403, 'Company scope mismatch.');
        }
        return DB::table('positions')->where('company_id', $company)->select('id', 'name')->orderBy('name')->get();
    });

    // Employee App Routes
    Route::post('/attendance/clock-in', [\App\Http\Controllers\Api\AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [\App\Http\Controllers\Api\AttendanceController::class, 'clockOut']);
    Route::get('/attendance/history', [\App\Http\Controllers\Api\AttendanceController::class, 'history']);
    
    Route::get('/employee/profile', [\App\Http\Controllers\Api\EmployeeController::class, 'profile']);
    Route::get('/employee/salary-slip', [\App\Http\Controllers\Api\EmployeeController::class, 'salarySlip']);
});

// LAZ Public API
// LAZ Public API
Route::prefix('v1/laz')->middleware('throttle:60,1')->group(function () {
    Route::get('/programs', [PublicProgramController::class, 'index']);
    Route::get('/programs/{id}', [PublicProgramController::class, 'show']);
    
    // Stricter rate limit for submissions (e.g., 5 requests per minute per IP)
    Route::post('/applications', [PublicApplicationController::class, 'store'])->middleware('throttle:5,1');
    Route::post('/check-status', [PublicApplicationController::class, 'checkStatus'])->middleware('throttle:10,1');
});
