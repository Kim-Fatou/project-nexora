<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la possibilité de joindre un fichier (image ou document) à un
     * message de chat. Le message texte devient optionnel : un message
     * peut désormais être uniquement une pièce jointe, sans texte.
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Le texte n'est plus obligatoire (un message peut être "juste une photo")
            $table->text('message')->nullable()->change();

            // Chemin de stockage RÉEL du fichier (jamais exposé tel quel au
            // navigateur — on passe toujours par une route protégée qui
            // vérifie que l'utilisateur fait bien partie de la discussion).
            $table->string('attachment_path')->nullable()->after('message');

            // Nom d'origine du fichier tel qu'uploadé (pour l'affichage,
            // ex: "vacances-paris.jpg"), différent du nom de stockage
            // (qui est généré aléatoirement pour éviter les collisions
            // et les problèmes de sécurité liés aux noms de fichiers).
            $table->string('attachment_name')->nullable()->after('attachment_path');

            // Type MIME (ex: "image/jpeg", "application/pdf") — sert à
            // décider si on affiche une miniature d'image ou une "carte
            // fichier" générique avec une icône.
            $table->string('attachment_mime')->nullable()->after('attachment_name');

            // Taille en octets, pour l'affichage ("2,4 Mo") côté interface.
            $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_mime');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['attachment_path', 'attachment_name', 'attachment_mime', 'attachment_size']);
            $table->text('message')->nullable(false)->change();
        });
    }
};