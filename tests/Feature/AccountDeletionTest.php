<?php

use App\Models\User;
use App\Models\Post;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can delete their account and it cascades to posts', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'email' => 'test@example.com',
    ]);

    $post = Post::create([
        'user_id' => $user->id,
        'content' => 'Hello World',
    ]);

    $this->actingAs($user);

    Volt::test('socialnet')
        ->call('deleteAccount')
        ->assertRedirect('/');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});
