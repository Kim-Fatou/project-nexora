<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * DDIA Justification: Ajout de colonnes pour les fonctionnalités d'édition, de notifications de messages non-lus (badge)
     * et de réponses imbriquées.
     * L'index composite (connection_id, read_at) est crucial pour compter instantanément le nombre de messages non-lus 
     * d'une discussion sans devoir scanner toute la table (index-only query).
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->index();
            }
            if (!Schema::hasColumn('chat_messages', 'is_edited')) {
                $table->boolean('is_edited')->default(false);
            }
            if (!Schema::hasColumn('chat_messages', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->constrained('chat_messages')
                    ->nullOnDelete();
            }
        });

        Schema::table('room_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('room_messages', 'is_edited')) {
                $table->boolean('is_edited')->default(false);
            }
            if (!Schema::hasColumn('room_messages', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->constrained('room_messages')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['read_at', 'is_edited', 'parent_id']);
        });

        Schema::table('room_messages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['is_edited', 'parent_id']);
        });
    }
};
