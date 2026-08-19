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
        Schema::create('offer_letters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('career_id')->constrained('careers')->cascadeOnDelete();
            $table->string('position', 255);

            $table->foreignId('interview_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('interview_by_name', 255)->nullable();

            $table->decimal('ctc', 12,0);
            $table->decimal('salary', 12, 0)->nullable();
            $table->text('stipend')->nullable();
            $table->date('date_of_joining');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('career_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_letters');
    }
};
