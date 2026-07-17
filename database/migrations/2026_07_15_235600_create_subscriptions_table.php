<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * DDIA Justification: Création de la table des abonnements. L'index composite (user_id, status)
     * permet de vérifier très rapidement si un utilisateur dispose d'un abonnement actif (O(1) à O(log N))
     * sans balayer toute la table (Table Scan).
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('active')->index(); // e.g., active, expired, canceled
            $table->string('plan_type', 30)->default('premium'); // e.g., monthly, yearly
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            // Index composite pour accélérer la recherche des abonnements actifs par utilisateur
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
