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
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('interview_id')->nullable();
            $table->date('interview_date')->nullable();
            $table->time('interview_time')->nullable();
            $table->string('interview_type')->nullable();
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('mobile', 255)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('department_skills_id')->nullable();
            $table->string('pincode', 20)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('resume', 255)->nullable();
            $table->enum('office_location', ['Patna', 'Noida', 'Ranchi', 'Durgapur'])->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->text('skills')->nullable();
            $table->json('others')->nullable();
            $table->timestamps();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('department_skills_id')->references('id')->on('department_skills')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
