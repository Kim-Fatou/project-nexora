<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Language::query()->delete();

        \App\Models\Language::create(['code' => 'fr', 'name' => 'Français']);
        \App\Models\Language::create(['code' => 'en', 'name' => 'Anglais']);
        \App\Models\Language::create(['code' => 'bm', 'name' => 'Bambara']);
        \App\Models\Language::create(['code' => 'wo', 'name' => 'Wolof']);
        \App\Models\Language::create(['code' => 'ff', 'name' => 'Peul (Fulfulde)']);
        \App\Models\Language::create(['code' => 'sn', 'name' => 'Soninké']);
        \App\Models\Language::create(['code' => 'dgn', 'name' => 'Dogon']);
        \App\Models\Language::create(['code' => 'snf', 'name' => 'Sénoufo']);
        \App\Models\Language::create(['code' => 'mnk', 'name' => 'Malinké']);
        \App\Models\Language::create(['code' => 'son', 'name' => 'Songhaï']);
        \App\Models\Language::create(['code' => 'ar', 'name' => 'Arabe']);
        \App\Models\Language::create(['code' => 'es', 'name' => 'Espagnol']);
        \App\Models\Language::create(['code' => 'pt', 'name' => 'Portugais']);
    }
}