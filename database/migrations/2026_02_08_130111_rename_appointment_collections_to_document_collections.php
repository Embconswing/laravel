<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('appointment_collections', 'document_collections');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('document_collections', 'appointment_collections');
    }
};
