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
        Schema::table('internship_interests', function (Blueprint $table) {
            $table->string('payment_status')->nullable();
            $table->string('txn_id')->nullable();
            $table->string('gateway_txn_id')->nullable();
            $table->decimal('payment_amount')->nullable();
            $table->json('metadata')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internship_interests', function (Blueprint $table) {
            $table->dropColumn(['payment_status','payment_amount','metadata','txn_id','gateway_txn_id']);
        });
    }
};
