<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //  POUR VIDER LA TABLE AVANT D'INSÉRER
        \App\Models\Interest::query()->delete();
        //
        \App\Models\Interest::create(['slug' => 'musique', 'name' => 'Musique', 'category' => 'Art']);
        \App\Models\Interest::create(['slug' => 'tech', 'name' => 'Technologie', 'category' => 'Science']);
        \App\Models\Interest::create(['slug' => 'gaming', 'name' => 'Jeux Vidéo', 'category' => 'Loisir']);
        \App\Models\Interest::create(['slug' => 'cuisine', 'name' => 'Cuisine', 'category' => 'Loisir']);
        \App\Models\Interest::create(['slug' => 'voyage', 'name' => 'Voyage', 'category' => 'Loisir']);
        \App\Models\Interest::create(['slug' => 'lecture', 'name' => 'Lecture', 'category' => 'Culture']);
        \App\Models\Interest::create(['slug' => 'sport', 'name' => 'Sport', 'category' => 'Santé']);
        \App\Models\Interest::create(['slug' => 'cinema', 'name' => 'Cinéma', 'category' => 'Art']);
        \App\Models\Interest::create(['slug' => 'photo', 'name' => 'Photographie', 'category' => 'Art']);
        \App\Models\Interest::create(['slug' => 'mode', 'name' => 'Mode', 'category' => 'Style']);
        \App\Models\Interest::create(['slug' => 'technologie', 'name' => 'Technologie', 'category' => 'Science & Tech']);
        \App\Models\Interest::create(['slug' => 'jeux-video', 'name' => 'Jeux Vidéo', 'category' => 'Loisirs']);
        \App\Models\Interest::create(['slug' => 'art-design', 'name' => 'Art & Design', 'category' => 'Art & Culture']);
        \App\Models\Interest::create(['slug' => 'entrepreneuriat', 'name' => 'Entrepreneuriat', 'category' => 'Business']);
        \App\Models\Interest::create(['slug' => 'ecriture', 'name' => 'Écriture', 'category' => 'Art & Culture']);
        \App\Models\Interest::create(['slug' => 'nature', 'name' => 'Nature', 'category' => 'Environnement']);
        \App\Models\Interest::create(['slug' => 'animaux', 'name' => 'Animaux', 'category' => 'Environnement']);
        \App\Models\Interest::create(['slug' => 'fitness', 'name' => 'Fitness', 'category' => 'Santé & Bien-être']);
        \App\Models\Interest::create(['slug' => 'danse', 'name' => 'Danse', 'category' => 'Art & Culture']);
        
    
    }
}
