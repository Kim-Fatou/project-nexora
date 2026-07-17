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
          Schema::create('connections', function (Blueprint $table) {
            $table->id();
            
            // L'utilisateur qui fait la demande (le demandeur)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // L'utilisateur qui reçoit la demande (le receveur)
            $table->foreignId('friend_id')->constrained('users')->cascadeOnDelete();
            
            // L'état de la discussion : 'pending' (en attente), 'accepted' (accepté), 'declined' (refusé)
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            
            $table->timestamps();

            // 🚀 SÉCURITÉ & INDEX DE RECHERCHE (DDIA)
            // Empêche d'envoyer deux fois la même demande entre les deux mêmes personnes
            $table->unique(['user_id', 'friend_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};
