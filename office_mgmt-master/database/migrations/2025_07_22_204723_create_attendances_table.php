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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'leave', 'late', 'half day'])->default('present');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->decimal('working_hours', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'attendance_date']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
