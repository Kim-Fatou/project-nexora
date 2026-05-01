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
        Schema::create('possederbadges', function (Blueprint $table) {
            $table->unsignedBigInteger('idprofil');
            $table->unsignedBigInteger('idbadge');
            $table->dateTime('dateobtention')->useCurrent();
            
            $table->foreign('idprofil')->references('idProfil')->on('profils')->onDelete('cascade');
            $table->foreign('idbadge')->references('idBadge')->on('badges')->onDelete('cascade');
            $table->primary(['idprofil', 'idbadge']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('possederbadges');
    }
};
