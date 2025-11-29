<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveOvertimeController;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\PayrollComponentController;
use App\Http\Controllers\BpjsRateController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\KpiMasterController;
use App\Http\Controllers\EmployeeKpiAssignmentController;
use App\Http\Controllers\EmployeeBpjsController;
use App\Http\Controllers\FundraisingTransactionUiController;
use App\Http\Controllers\ExpenseClaimUiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'company.scope'])->group(function () {
    Route::get('/dashboard/volunteer', [DashboardController::class, 'volunteer'])->name('dashboard.volunteer');
});

Route::middleware(['auth', 'verified', 'company.scope', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    Route::get('/payslips', [PayslipController::class, 'index'])->name('payslips.index');
    Route::get('/payslip/{periodId}/{employeeId}', [PayslipController::class, 'show'])->name('payslips.show');

    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::resource('companies', CompanyController::class)->except(['show']);
    Route::resource('branches', BranchController::class)->except(['show']);
    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::resource('positions', PositionController::class)->except(['show']);
    Route::resource('shifts', ShiftController::class)->except(['show']);
    Route::resource('leave-types', LeaveTypeController::class)->except(['show']);
    Route::resource('payroll-components', PayrollComponentController::class)->except(['show']);
    Route::resource('bpjs-rates', BpjsRateController::class)->except(['show']);
    Route::resource('tax-rates', TaxRateController::class)->except(['show']);
    Route::resource('kpi', KpiMasterController::class)->except(['show']);
    Route::resource('employee-kpi', EmployeeKpiAssignmentController::class)->except(['show']);
    Route::resource('employee-bpjs', EmployeeBpjsController::class)->except(['show']);
    Route::resource('employee-loans', \App\Http\Controllers\EmployeeLoanController::class)->except(['show']);
    Route::get('fundraising-transactions', [FundraisingTransactionUiController::class, 'index'])->name('fundraising.transactions.index');
    Route::get('fundraising-transactions/create', [FundraisingTransactionUiController::class, 'create'])->name('fundraising.transactions.create');
    Route::post('fundraising-transactions', [FundraisingTransactionUiController::class, 'store'])->name('fundraising.transactions.store');

    Route::resource('expense-claims', ExpenseClaimUiController::class)->only(['index', 'create', 'store']);
    Route::post('expense-claims/{id}/status', [ExpenseClaimUiController::class, 'updateStatus'])->name('expense-claims.update-status');

    Route::resource('users', UserController::class)->except(['show']);
    Route::post('/payroll-periods/{id}/approve', [PayrollPeriodController::class, 'approve'])->name('payroll.periods.approve');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('/leave-overtime', [LeaveOvertimeController::class, 'index'])->name('leaveovertime.index');
    Route::post('/leave-overtime/leave', [LeaveOvertimeController::class, 'storeLeave'])->name('leaveovertime.leave.store');
    Route::post('/leave-overtime/overtime', [LeaveOvertimeController::class, 'storeOvertime'])->name('leaveovertime.overtime.store');
    Route::post('/leave-overtime/leave/{id}/status', [LeaveOvertimeController::class, 'updateLeaveStatus'])->name('leaveovertime.leave.status');
    Route::post('/leave-overtime/overtime/{id}/status', [LeaveOvertimeController::class, 'updateOvertimeStatus'])->name('leaveovertime.overtime.status');

    Route::get('/payroll-periods', [PayrollPeriodController::class, 'index'])->name('payroll.periods.index');
    Route::get('/payroll-periods/create', [PayrollPeriodController::class, 'create'])->name('payroll.periods.create');
    Route::post('/payroll-periods', [PayrollPeriodController::class, 'store'])->name('payroll.periods.store');
    Route::get('/payroll-periods/{id}/preview-regular', [PayrollPeriodController::class, 'previewRegular'])->name('payroll.periods.preview.regular');
    Route::get('/payroll-periods/{id}/preview-volunteer', [PayrollPeriodController::class, 'previewVolunteer'])->name('payroll.periods.preview.volunteer');
    Route::post('/payroll-periods/{id}/generate-volunteer', [PayrollPeriodController::class, 'generateVolunteer'])->name('payroll.periods.generate.volunteer');
    Route::post('/payroll-periods/{id}/generate-regular', [PayrollPeriodController::class, 'generateRegular'])->name('payroll.periods.generate.regular');
    Route::delete('/payroll-periods/{id}', [PayrollPeriodController::class, 'destroy'])->name('payroll.periods.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
