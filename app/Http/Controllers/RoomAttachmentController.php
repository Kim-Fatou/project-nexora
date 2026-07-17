<?php

namespace App\Http\Controllers;

use App\Models\RoomMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RoomAttachmentController extends Controller
{
    /**
     * Sert le fichier joint à un message de salon.
     *
     * SÉCURITÉ : mêmes règles que ChatAttachmentController — fichier stocké
     * hors de public/, accès uniquement si l'utilisateur connecté est membre
     * du salon concerné.
     */
    public function show(RoomMessage $message): StreamedResponse
    {
        abort_unless($message->hasAttachment(), 404);

        $room = $message->room;
        $userId = Auth::id();

        abort_unless($room->hasMember($userId), 403, "Tu n'as pas accès à ce fichier.");

        abort_unless(Storage::disk('local')->exists($message->attachment_path), 404);

        $mime = (string) $message->attachment_mime;
        $isPlayable = str_starts_with($mime, 'image/') || str_starts_with($mime, 'video/') || str_starts_with($mime, 'audio/');

        if ($isPlayable) {
            return Storage::disk('local')->response(
                $message->attachment_path,
                $message->attachment_name,
                ['Content-Type' => $message->attachment_mime]
            );
        }

        return Storage::disk('local')->download(
            $message->attachment_path,
            $message->attachment_name
        );
    }
}