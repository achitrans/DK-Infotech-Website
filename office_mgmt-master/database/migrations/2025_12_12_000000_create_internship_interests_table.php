<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('internship_interests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('type', ['internship', 'training'])->default('internship');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('degree')->nullable();
            $table->string('university')->nullable();
            $table->integer('graduation_year')->nullable();
            $table->string('position')->nullable();
            $table->date('start_date_preference')->nullable();
            $table->integer('availability_weeks')->nullable();
            $table->text('skills')->nullable();
            $table->string('portfolio_link')->nullable();
            $table->string('github_link')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('resume_file')->nullable();
            $table->enum('source', ['website', 'referral', 'campus', 'email', 'other'])->default('website');
            $table->enum('status', ['new', 'reviewed', 'shortlisted', 'interviewed', 'offered', 'rejected'])->default('new');
            $table->text('notes')->nullable();
            $table->boolean('consent')->default(false);
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_interests');
    }
};
