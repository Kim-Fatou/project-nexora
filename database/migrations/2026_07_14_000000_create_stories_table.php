<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Une story = une photo OU un texte avec fond coloré, visible 24h.
        // On stocke 'expires_at' directement (calculé à la création,
        // now() + 24h) plutôt que de le recalculer à chaque lecture :
        // plus simple à filtrer dans les requêtes (where('expires_at', '>', now())).
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 'image' = photo/vidéo uploadée, 'text' = texte sur fond coloré
            $table->enum('type', ['image', 'text'])->default('image');

            // Utilisé seulement si type = 'image'
            $table->string('media_path')->nullable();
            $table->string('media_mime')->nullable();

            // Utilisé seulement si type = 'text'
            $table->text('text_content')->nullable();
            $table->string('background_color', 20)->nullable(); // ex: "#6b1f2a"

            $table->timestamp('expires_at'); // now() + 24h au moment de la création
            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
