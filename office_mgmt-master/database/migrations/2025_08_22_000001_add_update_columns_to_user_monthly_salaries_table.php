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
            $table->decimal('gross_deduction', 10)
                ->after('lop_amount')
                ->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_monthly_salaries', function (Blueprint $table) {
            $table->dropColumn('gross_deduction');
        });
    }
};
