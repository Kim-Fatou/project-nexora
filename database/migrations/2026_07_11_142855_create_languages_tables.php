<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Table de référence des langues (source unique de vérité,
        //    identique au pattern de `interests`).
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // Ex: 'fr', 'bm', 'en' (ISO-ish, clé stable)
            $table->string('name', 100);          // Nom d'affichage : "Français"
            $table->timestamps();
        });

        // 2. Table pivot Many-to-Many entre User et Language,
        //    avec un flag pour distinguer la langue "native" des autres.
        Schema::create('language_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();

            // is_native : évite d'avoir une colonne `native_language` séparée
            // sur `users` en plus de ce pivot — une seule source de vérité
            // pour "quelles langues parle cet utilisateur", avec un simple
            // booléen pour indiquer laquelle est la langue native.
            $table->boolean('is_native')->default(false);

            // CLÉ PRIMAIRE COMPOSITE : empêche les doublons au niveau SQL
            // (un utilisateur ne peut pas avoir 2x la même langue),
            // et sert d'index pour les jointures dans les deux sens.
            $table->primary(['user_id', 'language_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('language_user');
        Schema::dropIfExists('languages');
    }
};