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
        Schema::create('user_monthly_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->year('salary_year');
            $table->string('salary_month');
            $table->date('salary_date')->nullable();
            $table->decimal('basic', 10, 2)->default(0);
            $table->decimal('hra', 10, 2)->default(0);
            $table->decimal('conveyance', 10, 2)->default(0);
            $table->decimal('special_allowance', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('other_allowance', 10, 2)->default(0);

            $table->decimal('gross_salary', 10, 2)->default(0);

            $table->integer('total_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('paid_leaves')->default(0);
            $table->integer('absent_days')->default(0);

            // Deductions (attendance-based)
            $table->decimal('pf', 10, 2)->default(0);
            $table->decimal('esi', 10, 2)->default(0);
            $table->decimal('professional_tax', 10, 2)->default(0);
            $table->decimal('tds', 10, 2)->default(0);
            $table->decimal('lop_days', 5, 2)->default(0);
            $table->decimal('lop_amount', 10, 2)->default(0);

            // Net Payable
            $table->decimal('net_salary', 10, 2);

            $table->enum('is_approved',['yes', 'no'])->default('no');
            $table->datetime('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->enum('payment_status',['unpaid', 'paid', 'pending'])->default('unpaid');
            $table->date('payment_date')->nullable();
            $table->text('remarks')->nullable();
            $table->json('payment_details')->nullable(); // JSON field for payment details (e.g., transaction ID, payment method, account)

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'salary_year', 'salary_month']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_monthly_salaries');
    }
};
