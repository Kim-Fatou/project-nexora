<?php

namespace Database\Seeders;

use App\Models\Interest;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Crée un salon de démarrage pour quelques passions déjà en base,
     * pour que "Salons" ne soit pas vide au premier lancement.
     */
    public function run(): void
    {
        $creator = User::first();
        if (!$creator) {
            return; // pas d'utilisateur -> pas de créateur possible, on ne force rien
        }

        $defaults = [
            'tech' => ['name' => 'Technologie', 'icon' => '💻', 'description' => 'Dev, outils, actualité tech.'],
            'gaming' => ['name' => 'Jeux Vidéo', 'icon' => '🎮', 'description' => 'Discussions et sessions entre joueurs.'],
            'musique' => ['name' => 'Musique', 'icon' => '🎵', 'description' => 'Partage de sons, compos, découvertes.'],
            'entrepreneuriat' => ['name' => 'Entrepreneuriat', 'icon' => '🚀', 'description' => 'Lancer et faire grandir ses projets.'],
            'cuisine' => ['name' => 'Cuisine', 'icon' => '🍜', 'description' => 'Recettes et bons plans culinaires.'],
            'photo' => ['name' => 'Photographie', 'icon' => '📷', 'description' => 'Techniques, sorties, retouches.'],
        ];

        foreach ($defaults as $slug => $data) {
            $interest = Interest::where('slug', $slug)->first();

            $room = Room::firstOrCreate(
                ['slug' => Room::uniqueSlugFor($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $data['icon'],
                    'interest_id' => $interest?->id,
                    'creator_id' => $creator->id,
                    'is_public' => true,
                ]
            );

            // Le créateur "de référence" rejoint automatiquement son propre salon
            if (!$room->hasMember($creator->id)) {
                $room->members()->attach($creator->id, ['joined_at' => now()]);
            }
        }
    }
}