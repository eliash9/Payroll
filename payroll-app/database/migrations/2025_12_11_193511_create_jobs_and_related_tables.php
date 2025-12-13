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
        Schema::create('job_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('title', 191);
            $table->string('code', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('job_responsibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_profiles')->cascadeOnDelete();
            $table->text('responsibility');
            $table->boolean('is_primary')->default(true);
            $table->timestamps();
        });

        Schema::create('job_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_profiles')->cascadeOnDelete();
            $table->text('requirement');
            $table->enum('type', ['education', 'skill', 'experience', 'certification', 'other'])->default('other');
            $table->timestamps();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->foreignId('job_id')->nullable()->after('company_id')->constrained('job_profiles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
            $table->dropColumn(['job_id']);
        });

        Schema::dropIfExists('job_requirements');
        Schema::dropIfExists('job_responsibilities');
        Schema::dropIfExists('job_profiles');
    }
};
