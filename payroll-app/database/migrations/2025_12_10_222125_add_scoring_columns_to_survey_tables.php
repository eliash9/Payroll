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
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->integer('weight')->default(0)->after('order'); // Bobot pertanyaan (persentase atau poin)
        });

        Schema::table('survey_responses', function (Blueprint $table) {
            $table->integer('score')->default(0)->after('answer'); // Skor yang didapat dari jawaban ini
        });

        Schema::table('surveys', function (Blueprint $table) {
            $table->integer('total_score')->default(0)->after('economic_condition_score'); // Total skor hasil kalkulasi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn('total_score');
        });

        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropColumn('score');
        });

        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};
