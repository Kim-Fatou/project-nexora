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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id('idUtilisateur'); // Identifiant unique
            $table->string('nom')->nullable(); // Nom de l'utilisateur
            $table->string('prenom')->nullable(); // Prénom de l'utilisateur
            $table->string('email')->unique(); // Email obligatoire et unique
            $table->string('mot_de_passe')->nullable(); // Optionnel pour les réseaux sociaux
            $table->string('provider_name')->nullable(); // Ex: google, github, twitter, apple
            $table->string('provider_id')->nullable(); // ID fourni par le réseau social
            $table->dateTime('date_inscription')->useCurrent(); // Date de création du compte
            $table->boolean('est_en_ligne')->default(false); // Utilisateur connecté ou non
            $table->string('role')->default('user'); // Rôle par défaut
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
