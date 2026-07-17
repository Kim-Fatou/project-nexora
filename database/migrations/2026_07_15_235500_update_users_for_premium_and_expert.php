<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * DDIA Justification: Dénormalisation du score de discussion. Stocker le score directement
     * dans la table users évite des requêtes d'agrégation de type COUNT(*) très coûteuses en temps
     * de calcul et en lectures disque sur la table des messages à chaque accès à l'espace Premium.
     * Un index sur discussion_score permet des sélections rapides en temps constant O(1).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'discussion_score')) {
                $table->integer('discussion_score')->default(0)->index();
            }
            if (!Schema::hasColumn('users', 'is_expert')) {
                $table->boolean('is_expert')->default(false)->index();
            }
            if (!Schema::hasColumn('users', 'expertise_interest_id')) {
                $table->foreignId('expertise_interest_id')
                    ->nullable()
                    ->constrained('interests')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['expertise_interest_id']);
            $table->dropColumn(['discussion_score', 'is_expert', 'expertise_interest_id']);
        });
    }
};
