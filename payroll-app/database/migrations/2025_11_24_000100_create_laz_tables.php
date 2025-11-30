<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->enum('allowed_recipient_type', ['individual', 'organization', 'both'])->default('both');
            $table->string('coverage_scope')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('program_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->dateTime('open_at');
            $table->dateTime('close_at');
            $table->unsignedInteger('application_quota')->nullable();
            $table->decimal('budget_quota', 15, 2)->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'archived'])->default('draft');
            $table->timestamps();
        });

        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('national_id');
            $table->string('full_name');
            $table->date('birth_date');
            $table->string('address');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('registration_number')->nullable();
            $table->string('address');
            $table->string('contact_phone');
            $table->string('contact_email')->nullable();
            $table->string('responsible_person');
            $table->timestamps();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_period_id')->constrained()->cascadeOnDelete();
            $table->enum('applicant_type', ['individual', 'organization']);
            $table->foreignId('applicant_id')->nullable()->constrained('applicants')->nullOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('requested_amount', 15, 2);
            $table->string('requested_aid_type');
            $table->text('need_description');
            $table->string('location_province');
            $table->string('location_regency');
            $table->string('location_district')->nullable();
            $table->string('location_village')->nullable();
            $table->enum('status', [
                'draft',
                'submitted',
                'screening',
                'survey_assigned',
                'surveying',
                'waiting_approval',
                'approved',
                'rejected',
                'disbursement_in_progress',
                'completed',
            ])->default('submitted');
            $table->timestamps();
        });

        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->string('file_path');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('surveyor_id')->constrained('users')->cascadeOnDelete();
            $table->date('survey_date')->nullable();
            $table->string('method')->nullable();
            $table->text('summary')->nullable();
            $table->smallInteger('economic_condition_score')->nullable();
            $table->string('recommendation')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('survey_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('decided_at')->nullable();
            $table->string('decision');
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->string('approved_aid_type')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('disbursed_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('disbursed_at');
            $table->string('method');
            $table->decimal('total_amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('disbursement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disbursement_id')->constrained()->cascadeOnDelete();
            $table->string('item_description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_value', 15, 2)->nullable();
            $table->decimal('total_value', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('disbursement_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disbursement_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->text('description');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('disbursement_proofs');
        Schema::dropIfExists('disbursement_items');
        Schema::dropIfExists('disbursements');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('survey_photos');
        Schema::dropIfExists('surveys');
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('applicants');
        Schema::dropIfExists('program_periods');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
