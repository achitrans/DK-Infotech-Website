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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->enum('type', ['admin', 'employee','client','associate'])->default('employee')->change();
            $table->enum('department', ['admin', 'manager', 'hr', 'development', 'intern', 'digital marketing', 'sales','client','associate'])->default('development')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['parent_id','created_by']);
            $table->enum('type', ['admin', 'employee','client'])->default('employee')->change();
            $table->enum('department', ['admin', 'manager', 'hr', 'development', 'intern', 'digital marketing', 'sales','client'])->default('development')->change();
        });
    }
};
