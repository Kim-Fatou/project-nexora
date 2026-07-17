<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PostAttachmentController extends Controller
{
    /**
     * Sert l'image d'une publication.
     *
     * SÉCURITÉ : le flux est public (comme les Salons), donc tout utilisateur
     * connecté peut la voir — la vérification se limite à la route (middleware
     * 'auth') et à l'existence réelle du fichier sur le disque privé.
     */
    public function show(Post $post): StreamedResponse
    {
        abort_unless($post->hasImage(), 404);

        abort_unless(Storage::disk('local')->exists($post->image_path), 404);

        return Storage::disk('local')->response(
            $post->image_path,
            null,
            ['Content-Type' => $post->image_mime]
        );
    }
}
