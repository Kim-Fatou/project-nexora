<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Room;
use App\Models\Connection;
use App\Models\ChatMessage;
use App\Models\RoomMessage;
use App\Models\Language;
use App\Models\Interest;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('level is calculated correctly from xp', function () {
    $user = User::factory()->create(['xp' => 0, 'level' => 1]);

    expect($user->calculateLevelFromXp(50))->toBe(1);
    expect($user->calculateLevelFromXp(100))->toBe(2);
    expect($user->calculateLevelFromXp(300))->toBe(3);
    expect($user->calculateLevelFromXp(600))->toBe(4);
    expect($user->calculateLevelFromXp(1000))->toBe(5);
});

test('joining a room awards 20 xp', function () {
    $user = User::factory()->create(['xp' => 0, 'level' => 1]);
    $room = Room::create([
        'name' => 'Tech Room',
        'slug' => 'tech-room',
        'is_public' => true,
        'creator_id' => $user->id,
    ]);

    $this->actingAs($user);

    Volt::test('socialnet')
        ->call('joinRoom', $room->id);

    $user->refresh();
    expect($user->xp)->toBe(20);
});

test('sending messages awards xp up to a daily limit of 50 xp', function () {
    $alice = User::factory()->create(['xp' => 0, 'level' => 1]);
    $bob = User::factory()->create();

    $connection = Connection::create([
        'user_id' => $alice->id,
        'friend_id' => $bob->id,
        'status' => 'accepted',
    ]);

    // Send 25 messages = 50 XP
    for ($i = 0; $i < 25; $i++) {
        ChatMessage::create([
            'connection_id' => $connection->id,
            'sender_id' => $alice->id,
            'message' => 'Message ' . $i,
        ]);
    }

    $alice->refresh();
    expect($alice->xp)->toBe(50);

    // 26th message shouldn't award XP
    ChatMessage::create([
        'connection_id' => $connection->id,
        'sender_id' => $alice->id,
        'message' => 'Message 26',
    ]);

    $alice->refresh();
    expect($alice->xp)->toBe(50); // capped at 50 XP
});

test('completing profile awards 30 xp once', function () {
    $user = User::factory()->create([
        'xp' => 0,
        'level' => 1,
        'bio' => null,
        'username' => null,
        'profile_completed_xp_awarded' => false,
    ]);

    $lang = Language::create(['name' => 'French', 'slug' => 'french', 'code' => 'fr']);
    $interest = Interest::create(['name' => 'Vidéos', 'slug' => 'videos', 'category' => 'Loisirs']);

    $this->actingAs($user);

    // Save profile via Volt profile component
    Volt::test('profile')
        ->call('saveProfile', [
            'location' => 'Paris',
            'handle' => 'alice_test',
            'bio' => 'Ceci est ma super bio de test longue de plus de dix caractères.',
            'goals' => 'Apprendre',
            'idealPartner' => 'Sympa',
            'nativeLanguageId' => $lang->id,
            'otherLanguageIds' => [],
        ]);

    // Also attach interest so profile completes
    $user->interests()->attach($interest);
    $user->checkAndAwardProfileCompletionXp();

    $user->refresh();
    expect($user->xp)->toBe(30);
    expect($user->profile_completed_xp_awarded)->toBeTrue();

    // Triggering again shouldn't double award
    $user->addXp(10); // user has 40 now
    $user->checkAndAwardProfileCompletionXp();
    $user->refresh();
    expect($user->xp)->toBe(40);
});
