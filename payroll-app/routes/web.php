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


// Fix for shared hosting storage link
Route::get('/storage-link', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Storage link created successfully.';
    } catch (\Exception $e) {
        return 'Error creating storage link: ' . $e->getMessage();
    }
});

// Fallback route to serve storage files directly if symlink fails
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);

    if (!file_exists($filePath)) {
        abort(404);
    }

    $file = \Illuminate\Support\Facades\File::get($filePath);
    $type = \Illuminate\Support\Facades\File::mimeType($filePath);

    $response = \Illuminate\Support\Facades\Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->where('path', '.*');

Route::middleware(['auth', 'verified', 'company.scope'])->group(function () {
    Route::get('/dashboard/volunteer/me', [DashboardController::class, 'myVolunteerDashboard'])->name('dashboard.volunteer.me');
    Route::get('/dashboard/volunteer', [DashboardController::class, 'volunteer'])->name('dashboard.volunteer');
});

Route::middleware(['auth', 'verified', 'company.scope', 'role:admin,manager'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    Route::get('/payslips', [PayslipController::class, 'index'])->name('payslips.index');
    Route::get('/payslip/{periodId}/{employeeId}', [PayslipController::class, 'show'])->name('payslips.show');
    Route::get('/payslip/{periodId}/{employeeId}/edit', [PayslipController::class, 'edit'])->name('payslips.edit');
    Route::put('/payslip/{periodId}/{employeeId}', [PayslipController::class, 'update'])->name('payslips.update');

    Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('employees/import-template', [EmployeeController::class, 'importTemplate'])->name('employees.import-template');
    Route::get('employees/custom-locations', [EmployeeController::class, 'customLocations'])->name('employees.custom-locations'); // New route
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::post('employees/{employee}/components', [EmployeeController::class, 'storeComponent'])->name('employees.components.store');
    Route::delete('employees/{employee}/components/{component}', [EmployeeController::class, 'destroyComponent'])->name('employees.components.destroy');
    
    Route::post('employees/{employee}/educations', [EmployeeController::class, 'storeEducation'])->name('employees.educations.store');
    Route::delete('employees/{employee}/educations/{education}', [EmployeeController::class, 'destroyEducation'])->name('employees.educations.destroy');
    
    Route::post('employees/{employee}/certifications', [EmployeeController::class, 'storeCertification'])->name('employees.certifications.store');
    Route::delete('employees/{employee}/certifications/{certification}', [EmployeeController::class, 'destroyCertification'])->name('employees.certifications.destroy');

    Route::get('employees/{employee}/mutations/create', [\App\Http\Controllers\MutationController::class, 'create'])->name('employees.mutations.create');
    Route::post('employees/{employee}/mutations', [\App\Http\Controllers\MutationController::class, 'store'])->name('employees.mutations.store');
    // Branches, Departments, etc. are usually company-specific, so managers can manage them.
    Route::get('branches/export', [BranchController::class, 'export'])->name('branches.export');
    Route::post('branches/import', [BranchController::class, 'import'])->name('branches.import');
    Route::get('branches/import-template', [BranchController::class, 'importTemplate'])->name('branches.import-template');
    Route::resource('branches', BranchController::class)->except(['show']);
    Route::get('departments/export', [DepartmentController::class, 'export'])->name('departments.export');
    Route::post('departments/import', [DepartmentController::class, 'import'])->name('departments.import');
    Route::get('departments/import-template', [DepartmentController::class, 'importTemplate'])->name('departments.import-template');
    Route::resource('departments', DepartmentController::class)->except(['show']);

    Route::get('positions/export', [PositionController::class, 'export'])->name('positions.export');
    Route::post('positions/import', [PositionController::class, 'import'])->name('positions.import');
    Route::get('positions/import-template', [PositionController::class, 'importTemplate'])->name('positions.import-template');
    Route::resource('positions', PositionController::class)->except(['show']);
    Route::resource('jobs', \App\Http\Controllers\JobController::class);
    Route::resource('shifts', ShiftController::class)->except(['show']);
    Route::resource('leave-types', LeaveTypeController::class)->except(['show']);
    Route::get('payroll-components/bulk-assign', [PayrollComponentController::class, 'bulkAssign'])->name('payroll-components.bulk-assign');
    Route::post('payroll-components/bulk-assign', [PayrollComponentController::class, 'storeBulkAssign'])->name('payroll-components.bulk-assign.store');
    Route::resource('payroll-components', PayrollComponentController::class)->except(['show']);
    Route::resource('bpjs-rates', BpjsRateController::class)->except(['show']);
    Route::resource('tax-rates', TaxRateController::class)->except(['show']);
    Route::resource('kpi', KpiMasterController::class)->except(['show']);
    Route::resource('employee-kpi', EmployeeKpiAssignmentController::class)->except(['show']);
    Route::resource('employee-bpjs', EmployeeBpjsController::class)->except(['show']);
    Route::resource('employee-loans', \App\Http\Controllers\EmployeeLoanController::class)->except(['show']);
    Route::resource('work-locations', \App\Http\Controllers\WorkLocationController::class)->except(['show']);
    Route::get('fundraising-transactions', [FundraisingTransactionUiController::class, 'index'])->name('fundraising.transactions.index');
    Route::get('fundraising-transactions/create', [FundraisingTransactionUiController::class, 'create'])->name('fundraising.transactions.create');
    Route::post('fundraising-transactions', [FundraisingTransactionUiController::class, 'store'])->name('fundraising.transactions.store');

    Route::resource('expense-claims', ExpenseClaimUiController::class)->only(['index', 'create', 'store']);
    Route::post('expense-claims/{id}/status', [ExpenseClaimUiController::class, 'updateStatus'])->name('expense-claims.update-status');

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
    Route::get('/payroll-periods/{id}', [PayrollPeriodController::class, 'show'])->name('payroll.periods.show');
    Route::delete('/payroll-periods/{id}', [PayrollPeriodController::class, 'destroy'])->name('payroll.periods.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reports
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/payroll', [App\Http\Controllers\ReportController::class, 'payroll'])->name('reports.payroll');
    Route::get('/reports/attendance', [App\Http\Controllers\ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('/reports/fundraising', [App\Http\Controllers\ReportController::class, 'fundraising'])->name('reports.fundraising');
});

// Admin only routes (Super Admin or System Admin)
Route::middleware(['auth', 'verified', 'company.scope', 'role:admin'])->group(function () {
    Route::resource('companies', CompanyController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show']);
});


// LAZ Module Routes
Route::middleware(['auth'])->prefix('laz')->name('laz.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Laz\LazDashboardController::class, 'index'])->name('dashboard');

    Route::resource('programs', App\Http\Controllers\Laz\ProgramController::class)->middleware('laz.role:admin,admin_pusat');
    Route::resource('periods', App\Http\Controllers\Laz\ProgramPeriodController::class)->middleware('laz.role:admin,admin_pusat');
    Route::resource('survey-templates', App\Http\Controllers\Laz\SurveyTemplateController::class)->middleware('laz.role:admin,admin_pusat');

    Route::get('applications', [App\Http\Controllers\Laz\ApplicationController::class, 'index'])->name('applications.index')->middleware('laz.role:admin,admin_pusat,admin_cabang,approver,keuangan,surveyor,auditor');
    Route::get('applications/{application}', [App\Http\Controllers\Laz\ApplicationController::class, 'show'])->name('applications.show')->middleware('laz.role:admin,admin_pusat,admin_cabang,approver,keuangan,surveyor,auditor');
    Route::post('applications/{application}/status', [App\Http\Controllers\Laz\ApplicationController::class, 'updateStatus'])->name('applications.status')->middleware('laz.role:admin,admin_pusat,admin_cabang');
    Route::post('applications/{application}/assign-surveyor', [App\Http\Controllers\Laz\ApplicationController::class, 'assignSurveyor'])->name('applications.assign-surveyor')->middleware('laz.role:admin,admin_pusat,admin_cabang');

    Route::get('surveys', [App\Http\Controllers\Laz\SurveyController::class, 'index'])->name('surveys.index')->middleware('laz.role:admin,admin_pusat,admin_cabang,surveyor');
    Route::get('applications/{application}/survey/create', [App\Http\Controllers\Laz\SurveyController::class, 'create'])->name('surveys.create')->middleware('laz.role:admin,admin_pusat,admin_cabang,surveyor');
    Route::post('applications/{application}/survey', [App\Http\Controllers\Laz\SurveyController::class, 'store'])->name('surveys.store')->middleware('laz.role:admin,admin_pusat,admin_cabang,surveyor');
    Route::get('surveys/{survey}', [App\Http\Controllers\Laz\SurveyController::class, 'show'])->name('surveys.show')->middleware('laz.role:admin,admin_pusat,admin_cabang,surveyor,approver,keuangan,auditor');

    Route::get('approvals', [App\Http\Controllers\Laz\ApprovalController::class, 'index'])->name('approvals.index')->middleware('laz.role:admin,approver');
    Route::post('applications/{application}/approve', [App\Http\Controllers\Laz\ApprovalController::class, 'store'])->name('approvals.store')->middleware('laz.role:admin,approver');

    Route::get('disbursements', [App\Http\Controllers\Laz\DisbursementController::class, 'index'])->name('disbursements.index')->middleware('laz.role:admin,keuangan');
    Route::post('applications/{application}/disburse', [App\Http\Controllers\Laz\DisbursementController::class, 'store'])->name('disbursements.store')->middleware('laz.role:admin,keuangan');

    Route::get('reports/export/excel/detail', [App\Http\Controllers\Laz\LazReportController::class, 'exportDetailExcel'])->name('reports.export.excel.detail')->middleware('laz.role:admin,admin_pusat,auditor');
    Route::get('reports/export/excel/rekap', [App\Http\Controllers\Laz\LazReportController::class, 'exportRekapExcel'])->name('reports.export.excel.rekap')->middleware('laz.role:admin,admin_pusat,auditor');
    Route::get('reports/export/pdf/detail', [App\Http\Controllers\Laz\LazReportController::class, 'exportDetailPdf'])->name('reports.export.pdf.detail')->middleware('laz.role:admin,admin_pusat,auditor');
    Route::get('reports/export/pdf/rekap', [App\Http\Controllers\Laz\LazReportController::class, 'exportRekapPdf'])->name('reports.export.pdf.rekap')->middleware('laz.role:admin,admin_pusat,auditor');
    Route::get('reports', [App\Http\Controllers\Laz\LazReportController::class, 'index'])->name('reports.index')->middleware('laz.role:admin,admin_pusat,auditor');

    Route::view('guide', 'laz.guide')->name('guide')->middleware('laz.role:admin,admin_pusat,admin_cabang,surveyor,approver,keuangan,auditor');

    Route::get('settings', [App\Http\Controllers\Laz\LazSettingController::class, 'index'])->name('settings.index')->middleware('laz.role:admin,admin_pusat');
    Route::post('settings', [App\Http\Controllers\Laz\LazSettingController::class, 'update'])->name('settings.update')->middleware('laz.role:admin,admin_pusat');
    Route::post('settings/test', [App\Http\Controllers\Laz\LazSettingController::class, 'sendTest'])->name('settings.test')->middleware('laz.role:admin,admin_pusat');
});

require __DIR__.'/auth.php';

