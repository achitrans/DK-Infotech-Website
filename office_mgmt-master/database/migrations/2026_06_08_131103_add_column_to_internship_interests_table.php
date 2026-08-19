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
            $table->string('roll_no')->nullable()->after('university');
            $table->string('college')->nullable();
            $table->string('parent')->nullable();
            $table->date('date_of_joining')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internship_interests', function (Blueprint $table) {
            $table->dropColumn('roll_no');
            $table->dropColumn('college');
            $table->dropColumn('parent');
            $table->dropColumn('date_of_joining');
        });
    }
};
