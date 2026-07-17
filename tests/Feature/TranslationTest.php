<?php

use App\Models\User;
use App\Models\Connection;
use App\Models\ChatMessage;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('premium users can translate messages using gemini api', function () {
    // 1. Create premium user (discussion_score >= 100)
    $alice = User::factory()->create([
        'name' => 'Alice Premium',
        'discussion_score' => 100,
    ]);

    // 2. Create another user (sender)
    $bob = User::factory()->create([
        'name' => 'Bob Sender',
        'discussion_score' => 0,
    ]);

    // 3. Create connection
    $connection = Connection::create([
        'user_id' => $alice->id,
        'friend_id' => $bob->id,
        'status' => 'accepted',
    ]);

    // 4. Create message from Bob to Alice
    $message = ChatMessage::create([
        'connection_id' => $connection->id,
        'sender_id' => $bob->id,
        'message' => 'Hello world',
    ]);

    // 5. Fake Gemini 2.0 API response
    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'Bonjour le monde']
                        ]
                    ]
                ]
            ]
        ], 200)
    ]);

    // 6. Act as Alice and test Livewire Volt chat-box component
    $this->actingAs($alice);

    Volt::test('chat-box')
        ->set('connectionId', $connection->id)
        ->call('translateMessage', $message->id)
        ->assertSet('translatedMessages.' . $message->id, 'Bonjour le monde');

    // 7. Verify Gemini API was called with the correct model and payload
    Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        return str_contains($request->url(), 'gemini-2.0-flash') &&
            str_contains($request->body(), 'Hello world');
    });
});

test('non premium users cannot translate messages', function () {
    // 1. Create non-premium user (discussion_score < 100, no active subscription)
    $charlie = User::factory()->create([
        'name' => 'Charlie Standard',
        'discussion_score' => 10,
    ]);

    // 2. Create another user
    $bob = User::factory()->create([
        'name' => 'Bob Sender',
        'discussion_score' => 0,
    ]);

    // 3. Create connection
    $connection = Connection::create([
        'user_id' => $charlie->id,
        'friend_id' => $bob->id,
        'status' => 'accepted',
    ]);

    // 4. Create message
    $message = ChatMessage::create([
        'connection_id' => $connection->id,
        'sender_id' => $bob->id,
        'message' => 'Hello world',
    ]);

    Http::fake();

    $this->actingAs($charlie);

    Volt::test('chat-box')
        ->set('connectionId', $connection->id)
        ->call('translateMessage', $message->id)
        ->assertNotSet('translatedMessages.' . $message->id, 'Bonjour le monde');

    // HTTP client should not be called at all
    Http::assertNothingSent();
});
