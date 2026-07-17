<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\Story;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StoryAttachmentController extends Controller
{
    /**
     * Sert l'image d'une story.
     *
     * SÉCURITÉ : visible seulement par l'auteur lui-même ou par un ami
     * (connexion acceptée) — pas par n'importe quel utilisateur connecté.
     */
    public function show(Story $story): StreamedResponse
    {
        abort_unless(($story->isImage() || $story->type === 'video') && $story->media_path, 404);

        $userId = Auth::id();
        $isOwner = $story->user_id === $userId;

        $isFriend = Connection::where('status', 'accepted')
            ->where(function ($q) use ($userId, $story) {
                // (je suis user_id ET l'auteur est friend_id) OU l'inverse
                $q->where(function ($q2) use ($userId, $story) {
                    $q2->where('user_id', $userId)->where('friend_id', $story->user_id);
                })->orWhere(function ($q2) use ($userId, $story) {
                    $q2->where('user_id', $story->user_id)->where('friend_id', $userId);
                });
            })
            ->exists();

        abort_unless($isOwner || $isFriend, 403, "Tu n'as pas accès à cette story.");

        abort_unless(Storage::disk('local')->exists($story->media_path), 404);

        return Storage::disk('local')->response(
            $story->media_path,
            null,
            ['Content-Type' => $story->media_mime]
        );
    }
}
