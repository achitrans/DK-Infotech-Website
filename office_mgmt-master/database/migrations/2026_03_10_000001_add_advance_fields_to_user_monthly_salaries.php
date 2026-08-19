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
        Schema::table('user_monthly_salaries', function (Blueprint $table) {
            $table->json('advance_deductions')->nullable()->after('payment_details');
            $table->decimal('advance_total_deduction', 10, 2)->default(0)->after('advance_deductions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_monthly_salaries', function (Blueprint $table) {
            $table->dropColumn(['advance_deductions', 'advance_total_deduction']);
        });
    }
};
