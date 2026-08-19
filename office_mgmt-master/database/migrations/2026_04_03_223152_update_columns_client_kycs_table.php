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
        Schema::table('client_kycs', function (Blueprint $table) {
            $table->string('bank_account_number')->nullable()->change();
            $table->string('bank_ifsc_code')->nullable()->change();
            $table->string('bank_name')->nullable()->change();
            $table->string('bank_branch')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_kycs', function (Blueprint $table) {
            $table->dropColumn('owner_name');
        });
    }
};
