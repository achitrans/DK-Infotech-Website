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
        Schema::create('loan_inquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('category',['retail','business','agriculture','others'])->default('retail');
            $table->string('type')->default('personal loan');
            $table->decimal('amount',12,0);
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->text('remarks')->nullable();
            $table->text('statement_file')->nullable();
            $table->enum('source', ['website', 'phone', 'email', 'in person', 'referral', 'other', 'advertisement'])->default('website');
            $table->enum('status', ['inquiry', 'login', 'credit', 'technical', 'legal', 'sanction', 'disburse'])->default('login');
            $table->timestamp('follow_up_due')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_inquiries');
    }
};
