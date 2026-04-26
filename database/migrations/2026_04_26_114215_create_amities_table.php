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
        Schema::create('amities', function (Blueprint $table) {
            $table->unsignedBigInteger('idprofil1');
            $table->unsignedBigInteger('idprofil2');
            $table->string('statut')->default('en_attente');
            $table->dateTime('dateami')->useCurrent();
            
            $table->foreign('idprofil1')->references('idProfil')->on('profils')->onDelete('cascade');
            $table->foreign('idprofil2')->references('idProfil')->on('profils')->onDelete('cascade');
            $table->primary(['idprofil1', 'idprofil2']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amities');
    }
};
