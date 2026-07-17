<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Commentaires avec réponses imbriquées (style Facebook) : un commentaire
        // "de premier niveau" a parent_id = null, une réponse pointe vers le
        // commentaire auquel elle répond via parent_id. On reste sur UN seul
        // niveau de profondeur côté UI (une réponse ne peut pas avoir de
        // sous-réponse), mais la colonne parent_id le permettrait techniquement.
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Auto-référence : null = commentaire racine, sinon = réponse à un autre commentaire
            $table->foreignId('parent_id')->nullable()->constrained('post_comments')->cascadeOnDelete();

            $table->text('content');
            $table->timestamps();

            // Accélère : "tous les commentaires racines d'un post", "toutes les réponses à un commentaire"
            $table->index(['post_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};
