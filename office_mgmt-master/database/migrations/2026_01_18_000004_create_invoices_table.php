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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('client_id')->nullable()->constrained('users');
            $table->text('buyer_name');
            $table->text('buyer_mobile')->nullable();
            $table->text('buyer_gstin')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();

            $table->date('invoice_date')->index();
            $table->date('due_date')->nullable();

            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('total_cgst', 15, 2)->default(0);
            $table->decimal('total_sgst', 15, 2)->default(0);
            $table->decimal('total_igst', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->string('status')->default('unpaid');
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
