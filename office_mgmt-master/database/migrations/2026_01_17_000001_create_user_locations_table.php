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
        Schema::create('user_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // When the location was recorded (timeline key)
            $table->timestamp('recorded_at')->index();

            // Coordinates
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // Optional telemetry
            $table->decimal('accuracy_m', 8, 2)->nullable();
            $table->decimal('altitude_m', 8, 2)->nullable();
            $table->decimal('speed_mps', 8, 2)->nullable();
            $table->unsignedSmallInteger('heading_deg')->nullable();

            // Metadata
            $table->string('source', 16)->nullable();
            $table->string('device_id', 64)->nullable();
            $table->string('ip', 45)->nullable();

            $table->timestamps();

            $table->index(['user_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_locations');
    }
};
