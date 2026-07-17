<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Justification DDIA (Kleppmann) :
            // L'utilisation d'une recherche générique LIKE '%...%' force un scan complet de la table (full table scan)
            // en complexité O(N), ce qui ne passe pas à l'échelle. Pour optimiser les performances de la recherche de
            // contenu des publications, nous créons un index FULLTEXT (index inversé).
            // Cela permet de chercher par mots-clés dans un dictionnaire ordonné avec une complexité logarithmique O(log N).
            
            // SQLite ne supporte pas nativement l'index FULLTEXT via cette syntaxe (utilisé pour les tests)
            if (DB::getDriverName() !== 'sqlite') {
                $table->fullText('content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropFullText(['content']);
            }
        });
    }
};
