<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Un salon = un espace de discussion de groupe, généralement lié à
        // une passion (interest_id), mais pas obligatoirement (un salon peut
        // être créé librement par un utilisateur sans passion associée).
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique()->index();
            $table->text('description')->nullable();
            $table->string('icon', 10)->default('💬');
            $table->foreignId('interest_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_public')->default(true)->index();
            $table->timestamps();
        });

        // Appartenance à un salon (many-to-many user <-> room).
        Schema::create('room_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();
            $table->unique(['room_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_user');
        Schema::dropIfExists('rooms');
    }
};