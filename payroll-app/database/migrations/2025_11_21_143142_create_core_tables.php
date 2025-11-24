<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Master organisasi
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 50)->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('npwp', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name', 191);
            $table->string('code', 50)->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->timestamps();
            $table->index('company_id', 'idx_branches_company_id');
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name', 191);
            $table->string('code', 50)->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name', 191);
            $table->string('grade', 50)->nullable();
            $table->timestamps();
        });

        // Master karyawan/relawan
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();

            $table->string('employee_code', 50)->unique();
            $table->string('full_name', 191);
            $table->string('nickname', 100)->nullable();
            $table->string('national_id_number', 32)->nullable();
            $table->string('family_card_number', 32)->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->tinyInteger('dependents_count')->nullable();

            $table->string('phone', 50)->nullable();
            $table->string('email', 191)->nullable();
            $table->text('address')->nullable();

            $table->enum('employment_type', ['permanent', 'contract', 'intern', 'outsourcing'])->default('permanent');
            $table->enum('status', ['active', 'inactive', 'suspended', 'terminated'])->default('active');
            $table->date('join_date')->nullable();
            $table->date('end_date')->nullable();

            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->enum('payroll_type', ['monthly', 'daily', 'hourly', 'commission'])->default('monthly');
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 100)->nullable();
            $table->string('bank_account_holder', 191)->nullable();

            // Volunteer / fundraiser extension
            $table->boolean('is_volunteer')->default(false);
            $table->string('fundraiser_type', 50)->default('volunteer');
            $table->decimal('hourly_rate', 15, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('max_commission_cap', 15, 2)->nullable();

            $table->softDeletes();
            $table->timestamps();
            $table->index(['company_id', 'employee_code'], 'idx_employees_company');
        });

        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('contract_number', 100)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('contract_type', ['permanent', 'fixed_term', 'probation']);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_tax_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('tax_id_number', 50)->nullable();
            $table->string('ptkp_status', 20);
            $table->boolean('is_tax_bruto')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_bpjs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('bpjs_kesehatan_number', 50)->nullable();
            $table->string('bpjs_kesehatan_class', 10)->nullable();
            $table->string('bpjs_ketenagakerjaan_number', 50)->nullable();
            $table->date('start_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Absensi & shift
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->nullable()->unique();
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('tolerance_late_minutes')->default(0);
            $table->integer('tolerance_early_leave_minutes')->default(0);
            $table->boolean('is_night_shift')->default(false);
            $table->timestamps();
        });

        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->date('work_date');
            $table->boolean('is_day_off')->default(false);
            $table->timestamps();
            $table->index(['employee_id', 'work_date'], 'idx_employee_schedules_emp_date');
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('device_id', 100)->nullable();
            $table->dateTime('scan_time');
            $table->enum('scan_type', ['in', 'out']);
            $table->enum('source', ['device', 'mobile', 'web', 'import']);
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'scan_time'], 'idx_attendance_logs_emp_scan');
        });

        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('work_date');
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->enum('status', ['present', 'late', 'early_leave', 'absent', 'leave', 'sick', 'off', 'wfh']);
            $table->integer('late_minutes')->default(0);
            $table->integer('early_leave_minutes')->default(0);
            $table->integer('worked_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);
            $table->text('remarks')->nullable();
            $table->boolean('locked')->default(false);
            $table->timestamps();
            $table->index(['employee_id', 'work_date'], 'idx_attendance_summaries_emp_date');
        });

        // Cuti & lembur
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->nullable()->unique();
            $table->boolean('is_paid')->default(true);
            $table->boolean('is_annual_quota')->default(false);
            $table->decimal('default_quota_days', 5, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_days', 5, 2);
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('approver_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('work_date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('total_minutes');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('approver_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('overtime_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
            $table->decimal('weekday_rate', 5, 2)->nullable();
            $table->decimal('weekend_rate', 5, 2)->nullable();
            $table->decimal('holiday_rate', 5, 2)->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();
        });

        // Payroll
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name', 100)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'calculated', 'approved', 'closed'])->default('draft');
            $table->dateTime('locked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payroll_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->enum('type', ['earning', 'deduction']);
            $table->enum('category', ['fixed', 'variable', 'kpi', 'bpjs', 'tax', 'loan', 'other']);
            $table->enum('calculation_method', ['manual', 'formula', 'attendance_based', 'kpi_based'])->default('manual');
            $table->boolean('is_taxable')->default(true);
            $table->boolean('show_in_payslip')->default(true);
            $table->integer('sequence')->default(0);
            $table->text('formula')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_payroll_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('payroll_component_id')->constrained('payroll_components')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('loan_number', 50)->unique();
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('remaining_amount', 15, 2);
            $table->decimal('installment_amount', 15, 2);
            $table->foreignId('start_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignId('end_period_id')->nullable()->constrained('payroll_periods')->nullOnDelete();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_loan_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_loan_id')->constrained('employee_loans')->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->boolean('is_paid')->default(false);
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payroll_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('gross_income', 15, 2)->default(0);
            $table->decimal('total_deduction', 15, 2)->default(0);
            $table->decimal('net_income', 15, 2)->default(0);
            $table->enum('status', ['draft', 'calculated', 'approved'])->default('draft');
            $table->dateTime('generated_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approver_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
            $table->index(['payroll_period_id', 'employee_id'], 'idx_payroll_headers_period_emp');
        });

        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_header_id')->constrained('payroll_headers')->cascadeOnDelete();
            $table->foreignId('payroll_component_id')->constrained('payroll_components')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('remark', 255)->nullable();
            $table->timestamps();
        });

        // Pajak
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->integer('year');
            $table->decimal('range_min', 15, 2);
            $table->decimal('range_max', 15, 2)->nullable();
            $table->decimal('rate_percent', 5, 2);
            $table->timestamps();
        });

        Schema::create('tax_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_header_id')->constrained('payroll_headers')->cascadeOnDelete();
            $table->decimal('annual_gross', 15, 2);
            $table->decimal('deductible', 15, 2);
            $table->decimal('pkp', 15, 2);
            $table->decimal('annual_tax', 15, 2);
            $table->decimal('monthly_tax', 15, 2);
            $table->timestamps();
        });

        // KPI
        Schema::create('kpi_master', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name', 191);
            $table->string('code', 50)->unique();
            $table->enum('type', ['numeric', 'percent', 'boolean']);
            $table->decimal('target_default', 15, 2)->nullable();
            $table->decimal('weight_default', 5, 2)->nullable();
            $table->enum('period_type', ['monthly', 'weekly', 'quarterly', 'yearly']);
            $table->enum('category', ['individual', 'team', 'division']);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_kpi_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('kpi_id')->constrained('kpi_master')->cascadeOnDelete();
            $table->decimal('target', 15, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'kpi_id'], 'idx_kpi_assign_emp_kpi');
        });

        Schema::create('employee_kpi_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('kpi_id')->constrained('kpi_master')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('actual_value', 15, 2);
            $table->decimal('achievement_percentage', 5, 2);
            $table->decimal('score', 5, 2)->nullable();
            $table->foreignId('evaluator_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'period_start', 'period_end'], 'idx_kpi_results_emp_period');
        });

        Schema::create('kpi_payroll_mapping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')->constrained('kpi_master')->cascadeOnDelete();
            $table->foreignId('payroll_component_id')->constrained('payroll_components')->cascadeOnDelete();
            $table->text('formula')->nullable();
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->timestamps();
        });

        // Fundraising
        Schema::create('fundraising_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fundraiser_id')->constrained('employees')->cascadeOnDelete();
            $table->string('donation_code', 50)->nullable()->unique();
            $table->string('donor_name', 191)->nullable();
            $table->string('donor_phone', 50)->nullable();
            $table->string('donor_email', 191)->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('IDR');
            $table->enum('source', ['offline', 'online', 'event', 'qr', 'transfer', 'other']);
            $table->string('campaign_name', 191)->nullable();
            $table->enum('category', ['zakat', 'infaq', 'shodaqoh', 'wakaf', 'donation', 'other'])->nullable();
            $table->dateTime('date_received');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['fundraiser_id', 'date_received'], 'idx_fundraising_tx_fundraiser_date');
            $table->index('campaign_name', 'idx_fundraising_tx_campaign');
        });

        Schema::create('fundraising_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fundraiser_id')->constrained('employees')->cascadeOnDelete();
            $table->date('summary_date');
            $table->decimal('total_amount', 15, 2);
            $table->integer('total_transactions');
            $table->integer('new_donors')->default(0);
            $table->integer('repeat_donors')->default(0);
            $table->timestamps();
            $table->index(['fundraiser_id', 'summary_date'], 'idx_fundraising_daily_fundraiser_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fundraising_daily_summaries');
        Schema::dropIfExists('fundraising_transactions');
        Schema::dropIfExists('kpi_payroll_mapping');
        Schema::dropIfExists('employee_kpi_results');
        Schema::dropIfExists('employee_kpi_assignments');
        Schema::dropIfExists('kpi_master');
        Schema::dropIfExists('tax_calculations');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('payroll_details');
        Schema::dropIfExists('payroll_headers');
        Schema::dropIfExists('employee_loan_schedules');
        Schema::dropIfExists('employee_loans');
        Schema::dropIfExists('employee_payroll_components');
        Schema::dropIfExists('payroll_components');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('overtime_policies');
        Schema::dropIfExists('overtime_requests');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('attendance_summaries');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('employee_schedules');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('employee_bpjs');
        Schema::dropIfExists('employee_tax_profiles');
        Schema::dropIfExists('employee_contracts');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('companies');
    }
};
