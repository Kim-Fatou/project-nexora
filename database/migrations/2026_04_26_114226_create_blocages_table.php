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
        Schema::create('blocages', function (Blueprint $table) {
            $table->unsignedBigInteger('idprofilsource');
            $table->unsignedBigInteger('idprofilcible');
            
            $table->foreign('idprofilsource')->references('idProfil')->on('profils')->onDelete('cascade');
            $table->foreign('idprofilcible')->references('idProfil')->on('profils')->onDelete('cascade');
            $table->primary(['idprofilsource', 'idprofilcible']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocages');
    }
};
