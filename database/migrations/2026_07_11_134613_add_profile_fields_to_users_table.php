<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
             // Attributs 1-vers-1 : pas besoin de table séparée, un utilisateur
            // a exactement une localisation, une bio, etc.
            $table->string('location')->nullable()->after('username')->index();
            // ->index() : on prévoit de filtrer "utilisateurs proches" plus tard.
            // Coût d'écriture minime ici (colonne peu mise à jour après le profil initial).


            $table->text('bio')->nullable()->after('location');
            $table->text('goals')->nullable()->after('bio');
            $table->text('ideal_partner')->nullable()->after('goals');
            // Pas d'index sur ces 3 colonnes : jamais utilisées en clause WHERE,
            // seulement affichées. Un index ici coûterait en écriture pour
            // aucun bénéfice de lecture (voir DDIA ch.3 sur le coût des index).
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['location', 'bio', 'goals', 'ideal_partner']);
        });
    }
};