<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Une publication = texte ET/OU image, visible publiquement par tous
        // les utilisateurs connectés (comme les Salons, contrairement aux
        // Stories qui sont réservées aux amis).
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->text('content')->nullable(); // nullable : un post peut être une image seule

            // Image hors public/, servie uniquement via PostAttachmentController
            // (même logique de sécurité que story-attachments / room-attachments).
            $table->string('image_path')->nullable();
            $table->string('image_mime')->nullable();

            $table->timestamps();

            // Index pour le tri du flux (les plus récents en premier)
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
