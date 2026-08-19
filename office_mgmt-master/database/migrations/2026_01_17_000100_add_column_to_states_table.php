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

        Schema::table('states', function (Blueprint $table) {
            if (!Schema::hasColumn('states', 'gst_code')) {
                $table->string('gst_code', 2)->nullable()->unique();
            }
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            if (Schema::hasColumn('states', 'gst_code')) {
                $table->dropUnique(['gst_code']);
                $table->dropColumn('gst_code');
            }
        });
    }
};
