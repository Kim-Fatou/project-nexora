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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('idNotif');
            $table->string('texte');
            $table->dateTime('datenotif')->useCurrent();
            $table->boolean('vue')->default(false);
            $table->unsignedBigInteger('id_utilisateur');
            
            $table->foreign('id_utilisateur')->references('idUtilisateur')->on('utilisateurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
