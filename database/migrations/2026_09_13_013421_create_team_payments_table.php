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
        Schema::create('team_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_registration_id')->constrained('tournament_registrations')->onDelete('cascade');
            $table->foreignId('match_id')->nullable()->constrained('matches')->onDelete('set null'); // Opcional si es pago de arbitraje
            $table->enum('concept', ['entry_fee', 'referee_fee', 'team_fine', 'other']);
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method'); // transfer, cash, card, etc.
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_payments');
    }
};
