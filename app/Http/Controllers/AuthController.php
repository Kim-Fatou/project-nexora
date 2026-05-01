<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Affiche la vue de connexion/inscription
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Gère la connexion classique (Email / Mot de passe)
     */
    public function login(Request $request)
    {
        // Validation des données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Recherche de l'utilisateur par email
        $user = Utilisateur::where('email', $request->email)->first();

        // Vérification du mot de passe
        if ($user && $user->mot_de_passe && Hash::check($request->password, $user->mot_de_passe)) {
            // Connexion (Utilisation manuelle car la table est personnalisée)
            Auth::loginUsingId($user->idUtilisateur);
            
            return redirect('/')->with('success', 'Connecté avec succès !');
        }

        return back()->withErrors(['email' => 'Identifiants incorrects.']);
    }

    /**
     * Gère l'inscription classique
     */
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Utilisateur::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'mot_de_passe' => Hash::make($request->password),
        ]);

        Auth::loginUsingId($user->idUtilisateur);

        return redirect('/')->with('success', 'Compte créé avec succès !');
    }

    /**
     * Redirige l'utilisateur vers le fournisseur OAuth (Google, GitHub, etc.)
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Gère le retour après authentification sur le réseau social
     */
    public function handleProviderCallback($provider)
    {
        try {
            // Récupère les infos de l'utilisateur depuis le fournisseur
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Erreur lors de la connexion via ' . ucfirst($provider)]);
        }

        // Vérifie si un utilisateur existe déjà avec cet email ou cet ID provider
        $user = Utilisateur::where('email', $socialUser->getEmail())
            ->orWhere(function($query) use ($provider, $socialUser) {
                $query->where('provider_name', $provider)
                      ->where('provider_id', $socialUser->getId());
            })->first();

        if (!$user) {
            // S'il n'existe pas, on le crée
            // Tente de séparer le nom et prénom depuis le nom complet fourni par le réseau
            $nameParts = explode(' ', $socialUser->getName() ?? '');
            $prenom = array_shift($nameParts);
            $nom = count($nameParts) > 0 ? implode(' ', $nameParts) : null;

            $user = Utilisateur::create([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $socialUser->getEmail(),
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
                // 'mot_de_passe' reste null car c'est une connexion sociale
            ]);
        }

        // Connexion
        Auth::loginUsingId($user->idUtilisateur);

        return redirect('/')->with('success', 'Connecté avec succès via ' . ucfirst($provider) . ' !');
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
