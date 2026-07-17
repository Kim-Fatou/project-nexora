<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('connections', function (Blueprint $table) {
            // NULL = pas de blocage. Sinon, contient l'ID de la personne
            // qui a bloqué l'autre. On garde la discussion (historique
            // conservé), on empêche juste l'envoi de nouveaux messages
            // et l'écoute du canal temps réel tant que le blocage existe.
            $table->foreignId('blocked_by')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('connections', function (Blueprint $table) {
            $table->dropForeign(['blocked_by']);
            $table->dropColumn('blocked_by');
        });
    }
};