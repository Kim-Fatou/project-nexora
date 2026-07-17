<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeChatController extends Controller
{
    public function index()
    {
        // 1. Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Sécurité : si personne n'est connecté, retour à la page de connexion
        if (!$user) {
            return redirect()->route('login');
        }

        // 2. Récupérer les suggestions (Profils avec intérêts communs)
        $suggestions = $user->suggestedUsers();

        // 3. Récupérer les discussions actives (Le chat)
        $conversations = $user->activeConversations();

        // 4. Envoyer le tout à la vue principale
        return view('homechat', compact('suggestions', 'conversations'));
    }
}