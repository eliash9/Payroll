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
        Schema::table('branches', function (Blueprint $table) {
            $table->string('province_code', 20)->nullable()->after('grade');
            $table->string('province_name', 100)->nullable()->after('province_code');
            $table->string('city_code', 20)->nullable()->after('province_name');
            $table->string('city_name', 100)->nullable()->after('city_code');
            $table->string('district_code', 20)->nullable()->after('city_name');
            $table->string('district_name', 100)->nullable()->after('district_code');
            $table->string('village_code', 20)->nullable()->after('district_name');
            $table->string('village_name', 100)->nullable()->after('village_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'province_code', 'province_name',
                'city_code', 'city_name',
                'district_code', 'district_name',
                'village_code', 'village_name'
            ]);
        });
    }
};
