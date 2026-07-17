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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('level')->default(1);
            $table->integer('xp')->default(0);
            $table->boolean('is_admin')->default(false)->index();
            $table->boolean('is_premium')->default(false)->index();
            $table->timestamp('premium_expires_at')->nullable();
            $table->boolean('profile_completed_xp_awarded')->default(false);
        });
 
        Schema::table('reports', function (Blueprint $table) {
            $table->string('status', 30)->default('open')->index();
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['level', 'xp', 'is_admin', 'is_premium', 'premium_expires_at', 'profile_completed_xp_awarded']);
        });
 
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
};
