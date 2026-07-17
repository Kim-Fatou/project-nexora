<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Qui a vu quelle story, et quand. Sert à afficher "vu par X, Y..."
        // au propriétaire de la story (comme WhatsApp/Instagram).
        Schema::create('story_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->cascadeOnDelete();
            $table->foreignId('viewer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('viewed_at')->useCurrent();

            // Un même utilisateur ne peut "voir" une story qu'une seule fois
            // (la 2e visite ne crée pas une 2e ligne, elle ne fait rien).
            $table->unique(['story_id', 'viewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_views');
    }
};
