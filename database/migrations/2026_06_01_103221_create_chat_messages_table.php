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
        //
        Schema::create('chat_messages', function (Blueprint $table) {
        $table->id();
        
        // Le message appartient à une connexion spécifique (la discussion à deux)
        $table->foreignId('connection_id')->constrained()->cascadeOnDelete();
        
        // Qui a écrit le message (l'expéditeur)
        $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
        
        $table->text('message');
        $table->timestamps();

        // Index pour charger l'historique de la discussion privée instantanément par date
        $table->index(['connection_id', 'created_at']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
