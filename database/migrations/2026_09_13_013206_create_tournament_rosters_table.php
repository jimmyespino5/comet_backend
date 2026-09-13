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
        Schema::create('tournament_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_registration_id')->constrained('tournament_registrations')->onDelete('cascade');
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->unsignedTinyInteger('jersey_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tournament_registration_id', 'player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_rosters');
    }
};
