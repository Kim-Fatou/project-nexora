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
        // Choix DDIA : Stocker le like directement sur la table story_views sous forme de flag booléen.
        // Cela évite une table de jointure supplémentaire et optimise la lecture/écriture en O(1) pour l'utilisateur.
        Schema::table('story_views', function (Blueprint $table) {
            $table->boolean('liked')->default(false)->after('viewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('story_views', function (Blueprint $table) {
            $table->dropColumn('liked');
        });
    }
};
