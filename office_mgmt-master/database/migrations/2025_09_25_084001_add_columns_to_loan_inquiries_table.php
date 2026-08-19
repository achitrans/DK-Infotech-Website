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
        Schema::table('loan_inquiries', function (Blueprint $table) {
            $table->string('pin_code')->after('state')->nullable();
            $table->string('gender')->after('phone')->nullable();
            $table->date('dob')->after('phone')->nullable();
            $table->string('pan')->after('gender')->nullable();
            $table->string('aadhar')->after('pan')->nullable();
            $table->string('tenure')->after('amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_monthly_salaries', function (Blueprint $table) {
            $table->dropColumn('pin_code');
            $table->dropColumn('gender');
            $table->dropColumn('pan');
            $table->dropColumn('aadhar');
            $table->dropColumn('tenure');
        });
    }
};
