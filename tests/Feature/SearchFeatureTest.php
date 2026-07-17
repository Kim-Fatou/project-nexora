<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Room;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('socialnet search query searches posts, rooms, and users', function () {
    $user = User::factory()->create([
        'username' => 'alice_smith',
    ]);
    
    $post = Post::create([
        'user_id' => $user->id,
        'content' => 'Livre de Kleppmann sur les bases de données distribuées',
    ]);

    $room = Room::create([
        'name' => 'Architecture Logicielle',
        'slug' => 'architecture-logicielle',
        'is_public' => true,
        'creator_id' => $user->id,
    ]);

    $this->actingAs($user);

    Volt::test('socialnet')
        ->set('searchQuery', 'Kleppmann')
        ->assertSee('Livre de Kleppmann')
        ->set('searchQuery', 'Architecture')
        ->assertSee('Architecture Logicielle');
});
