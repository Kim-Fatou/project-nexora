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
        Schema::create('partciperconvs', function (Blueprint $table) {
            $table->unsignedBigInteger('idprofil');
            $table->unsignedBigInteger('idconversation');
            
            $table->foreign('idprofil')->references('idProfil')->on('profils')->onDelete('cascade');
            $table->foreign('idconversation')->references('idConversation')->on('conversations')->onDelete('cascade');
            $table->primary(['idprofil', 'idconversation']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partciperconvs');
    }
};
