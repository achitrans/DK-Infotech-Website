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
        Schema::table('internship_interests', function (Blueprint $table) {
            $table->enum('parent_relation', ['S/O', 'D/O'])->nullable();
            $table->foreignId('graduation_course_id')
                ->nullable()
                ->constrained('graduation_courses')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internship_interests', function (Blueprint $table) {
            $table->dropForeign(['graduation_course_id']);
            $table->dropColumn(['parent_relation', 'graduation_course_id']);
        });
    }
};
