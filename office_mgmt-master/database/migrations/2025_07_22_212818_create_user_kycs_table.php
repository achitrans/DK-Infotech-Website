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
        Schema::create('user_kycs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('mobile_number', 15);
            $table->string('mobile_number_alt', 15);
            $table->string('email')->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('country' )->default('india');
            $table->string('postal_code', 10);

            $table->enum('address_proof_type', ['aadhaar', 'passport', 'voter id', 'driving license', 'utility bill', 'other']);
            $table->string('address_proof_number', 50);
            $table->string('address_proof_doc_path')->nullable();

            $table->enum('id_proof_type', ['aadhaar', 'pan', 'passport', 'voter id', 'driving license', 'nrega', 'other']);
            $table->string('id_proof_number', 50);
            $table->string('id_proof_doc_path')->nullable();

            $table->string('pan_number', 10)->nullable();
            $table->string('aadhaar_last4', 4)->nullable();
            $table->string('photograph_path')->nullable();

            $table->enum('kyc_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_kycs');
    }
};
