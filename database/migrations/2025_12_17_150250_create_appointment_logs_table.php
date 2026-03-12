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
        Schema::create('appointment_logs', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('appointment_id');
    $table->string('action'); // created, called, completed
    $table->timestamp('action_at');

    $table->json('meta')->nullable();
    $table->timestamps();

    $table->foreign('appointment_id')
          ->references('id')
          ->on('appointments')
          ->cascadeOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_logs');
    }
};
