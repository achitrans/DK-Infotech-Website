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
        Schema::table('user_kycs', function (Blueprint $table) {
            $table->string('past_experience_letter')->nullable();
            $table->string('past_offer_letter')->nullable();
            $table->string('past_salary_slip')->nullable();
            $table->json('qualifications')->nullable();
            $table->json('others')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_kycs', function (Blueprint $table) {
            $table->dropColumn('past_experience_letter', 'past_offer_letter', 'past_salary_slip','qualifications','others');
        });
    }
};
