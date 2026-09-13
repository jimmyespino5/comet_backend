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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('edition')->nullable(); // Ej: "Apertura 2026"
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('entry_fee', 10, 2)->default(0.00); // Monto Inscripción
            $table->decimal('referee_fee_per_match', 10, 2)->default(0.00); // Arbitraje por juego
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
