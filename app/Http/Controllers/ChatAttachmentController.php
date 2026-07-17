<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatAttachmentController extends Controller
{
    /**
     * Sert le fichier joint à un message de chat.
     *
     * SÉCURITÉ : les fichiers sont stockés sur le disque "local" (privé,
     * hors du dossier public/), donc AUCUNE URL directe ne peut y accéder.
     * La seule façon d'obtenir le fichier est de passer par cette méthode,
     * qui vérifie d'abord que l'utilisateur connecté fait bien partie des
     * deux participants de la discussion concernée.
     */
    public function show(ChatMessage $message): StreamedResponse
    {
        abort_unless($message->hasAttachment(), 404);

        $connection = $message->connection;
        $userId = Auth::id();

        abort_unless(
            $userId === $connection->user_id || $userId === $connection->friend_id,
            403,
            "Tu n'as pas accès à ce fichier."
        );

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