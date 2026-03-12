<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointment_trackings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('assigned_to')
                ->constrained('users');

            $table->foreignId('assigned_by')
                ->constrained('users');

            $table->string('action'); // assigned, reassigned, processing_started, completed

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['appointment_id', 'assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_trackings');
    }
};

