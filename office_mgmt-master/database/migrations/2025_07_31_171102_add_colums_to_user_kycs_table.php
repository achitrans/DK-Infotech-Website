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
            $table->string('account_no')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->text('bank_doc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_kycs', function (Blueprint $table) {
            $table->dropColumn('account_no');
            $table->dropColumn('ifsc_code');
            $table->dropColumn('bank_name');
            $table->dropColumn('bank_branch');
            $table->dropColumn('bank_doc');
        });
    }
};
