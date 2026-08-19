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
        Schema::create('project_task_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_task_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable(); // for threaded replies
            $table->text('comment');
            $table->boolean('is_internal')->default(false); // internal vs client-visible
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_task_id')->references('id')->on('project_tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('project_task_comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task_comments');
    }
};
