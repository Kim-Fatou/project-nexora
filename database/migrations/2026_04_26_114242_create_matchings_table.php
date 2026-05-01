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
        Schema::create('matchings', function (Blueprint $table) {
            $table->id('idmatch');
            $table->integer('scoreaffinite');
            $table->dateTime('datematch')->useCurrent();
            $table->unsignedBigInteger('idutil1');
            $table->unsignedBigInteger('idutil2');
            
            $table->foreign('idutil1')->references('idUtilisateur')->on('utilisateurs')->onDelete('cascade');
            $table->foreign('idutil2')->references('idUtilisateur')->on('utilisateurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matchings');
    }
};
