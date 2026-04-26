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
        Schema::create('messages', function (Blueprint $table) {
            $table->id('idmessage');
            $table->text('contenu');
            $table->dateTime('dateenvoi')->useCurrent();
            $table->string('typemedia')->nullable();
            $table->boolean('estanonyme')->default(false);
            $table->boolean('estlu')->default(false);
            $table->unsignedBigInteger('idconversation');
            $table->unsignedBigInteger('idexpediteur');
            
            $table->foreign('idconversation')->references('idConversation')->on('conversations')->onDelete('cascade');
            $table->foreign('idexpediteur')->references('idProfil')->on('profils')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
