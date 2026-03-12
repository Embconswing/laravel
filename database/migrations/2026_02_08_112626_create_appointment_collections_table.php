<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointment_collections', function (Blueprint $table) {
    $table->id();

    $table->string('appointment_no', 50)
        ->collation('utf8mb4_0900_ai_ci');

    $table->date('collection_date');
    $table->time('collection_time');
    $table->string('passport_no', 50);

    $table->timestamps();

    $table->unique('appointment_no');

    $table->foreign('appointment_no')
        ->references('appointment_no')
        ->on('appointments')
        ->cascadeOnDelete();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_collections');
    }
};
