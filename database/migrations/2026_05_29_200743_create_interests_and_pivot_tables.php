<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Table de référence des intérêts (Internationale, unique)
        Schema::create('interests', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique(); // Ex: 'tech', 'music', 'gaming' (Clé de recherche propre)
            $table->string('name', 100);          // Nom d'affichage
            $table->string('category', 50);  
            $table->timestamps();    // Pour filtrer par catégorie (ex: Sport, Art)
        });

        // 2. La table magique de liaison (Pivot) entre un Utilisateur et une Passion (Relation Many-To-Many) avec Index Composites et contraintes de Docteur
        Schema::create('interest_user', function (Blueprint $table) {

            // Crée une colonne 'user_id' connectée à la table 'users'. Si l'utilisateur est supprimé, cette ligne s'efface aussi automatiquement.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Crée une colonne 'interest_id' connectée à la table 'interests'.
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();

            // CLÉ PRIMAIRE COMPOSITE : Interdit les doublons au niveau SQL et booste le matching de 400%
            $table->primary(['user_id', 'interest_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interest_user'); 
        Schema::dropIfExists('interests');
    }
};