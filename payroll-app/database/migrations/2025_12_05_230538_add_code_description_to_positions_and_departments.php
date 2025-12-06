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
        Schema::table('positions', function (Blueprint $table) {
            $table->string('code', 50)->nullable()->unique()->after('company_id');
            $table->text('description')->nullable()->after('name');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn(['code', 'description']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
    }
};
