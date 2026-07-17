<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table pivot "like" : un utilisateur ne peut liker qu'une seule fois
        // une publication donnée (contrainte unique ci-dessous).
        Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // 🚀 SÉCURITÉ & INTÉGRITÉ (DDIA) : empêche le double-like côté DB,
            // pas seulement côté application (toggle Livewire).
            $table->unique(['post_id', 'user_id']);

            // Accélère le calcul du nombre de likes / la vérification "j'ai déjà liké"
            $table->index('post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_likes');
    }
};
