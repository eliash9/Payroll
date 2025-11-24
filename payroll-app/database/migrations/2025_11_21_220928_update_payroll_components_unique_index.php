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
        Schema::table('payroll_components', function (Blueprint $table) {
            // Drop existing unique on code only, then add composite unique company_id + code
            $table->dropUnique('payroll_components_code_unique');
            $table->unique(['company_id', 'code'], 'idx_payroll_components_company_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_components', function (Blueprint $table) {
            $table->dropUnique('idx_payroll_components_company_code');
            $table->unique('code');
        });
    }
};
