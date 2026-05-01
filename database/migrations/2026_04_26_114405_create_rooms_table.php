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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id('idroom');
            $table->string('nomroom');
            $table->string('codeacces')->nullable();
            $table->integer('capacitemax');
            $table->unsignedBigInteger('idconversation');
            $table->unsignedBigInteger('idcreateur');
            
            $table->foreign('idconversation')->references('idConversation')->on('conversations')->onDelete('cascade');
            $table->foreign('idcreateur')->references('idProfil')->on('profils')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
