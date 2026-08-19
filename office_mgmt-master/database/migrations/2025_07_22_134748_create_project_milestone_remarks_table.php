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
        Schema::create('project_milestone_remarks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('milestone_id');
            $table->unsignedBigInteger('user_id');
            $table->text('remark_text');
            $table->enum('remark_type', ['internal', 'issue', 'feedback','client feedback']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('milestone_id')->references('id')->on('project_milestones')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_milestone_remarks');
    }
};
