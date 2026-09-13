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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->constrained('rounds')->onDelete('cascade');
            $table->foreignId('home_registration_id')->constrained('tournament_registrations')->onDelete('cascade');
            $table->foreignId('away_registration_id')->constrained('tournament_registrations')->onDelete('cascade');
            $table->dateTime('scheduled_at');
            $table->string('venue')->nullable(); // Campo / Cancha
            $table->unsignedSmallInteger('home_score')->default(0);
            $table->unsignedSmallInteger('away_score')->default(0);
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'suspended', 'postponed'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
