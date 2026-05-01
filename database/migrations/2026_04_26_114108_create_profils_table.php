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
        Schema::create('profils', function (Blueprint $table) {
            $table->id('idProfil');
            $table->string('pseudo')->unique();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->integer('score_xp')->default(0);
            $table->unsignedBigInteger('idutilisateur');
            $table->unsignedBigInteger('idniveau');
            
            $table->foreign('idutilisateur')->references('idUtilisateur')->on('utilisateurs')->onDelete('cascade');
            $table->foreign('idniveau')->references('idniveau')->on('niveaus')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profils');
    }
};
