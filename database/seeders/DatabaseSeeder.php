<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\InterestSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    

        // APPELLE TON SEEDER ICI POUR REMPLIR LES INTÉRÊTS
        $this->call([
            InterestSeeder::class, 
            LanguageSeeder::class, // Ajoute le seeder pour les langues
            RoomSeeder::class, // Salons de démarrage, un par passion (Tech, Gaming, Musique...)
        ]);

    }
}





