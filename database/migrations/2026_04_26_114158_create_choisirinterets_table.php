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
        Schema::create('choisirinterets', function (Blueprint $table) {
            $table->unsignedBigInteger('idprofil');
            $table->unsignedBigInteger('idinteret');
            
            $table->foreign('idprofil')->references('idProfil')->on('profils')->onDelete('cascade');
            $table->foreign('idinteret')->references('idInteret')->on('centreinterets')->onDelete('cascade');
            $table->primary(['idprofil', 'idinteret']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('choisirinterets');
    }
};
