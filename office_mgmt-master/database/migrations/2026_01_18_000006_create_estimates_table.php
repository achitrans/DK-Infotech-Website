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
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->string('estimate_number')->unique();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('buyer_name');
            $table->text('buyer_mobile')->nullable();
            $table->text('buyer_gstin')->nullable();
            $table->date('estimate_date')->index();
            $table->date('expiry_date')->nullable();

            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->string('status')->default('draft');
            $table->foreignId('converted_invoice_id')->nullable()->constrained('invoices')->nullOnDelete()->unique();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};
