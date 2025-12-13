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

        Schema::table('programs', function (Blueprint $table) {
            $table->text('specific_requirements')->nullable()->after('description'); // Syarat & Ketentuan Khusus (Text/HTML)
            $table->json('required_documents')->nullable()->after('specific_requirements'); // List dokumen yg wajib (JSON Array)
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['specific_requirements', 'required_documents']);
        });

    }
};
