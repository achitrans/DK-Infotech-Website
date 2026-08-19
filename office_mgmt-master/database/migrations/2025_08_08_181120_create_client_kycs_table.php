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
        Schema::create('client_kycs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('business_type', ['individual','proprietorship', 'partnership','llc', 'limited', 'opc']);
            $table->string('business_name');
            $table->string('business_address');
            $table->string('business_phone');
            $table->string('business_email');
            $table->string('business_website')->nullable();
            $table->string('business_pan');
            $table->string('business_gstin')->nullable();
            $table->string('bank_account_number');
            $table->string('bank_ifsc_code');
            $table->string('bank_name');
            $table->string('bank_branch');
            $table->enum('kyc_status',['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_kyc_docs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_kyc_id');
            $table->foreign('client_kyc_id')->references('id')->on('client_kycs')->onDelete('cascade');
            $table->string('document_type');
            $table->string('document_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_kyc_docs');
        Schema::dropIfExists('client_kycs');
    }
};
