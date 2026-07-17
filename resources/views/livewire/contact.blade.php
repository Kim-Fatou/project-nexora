<?php

use function Livewire\Volt\{state, layout};
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

layout('layouts.app');

state([
    'name' => fn () => Auth::check() ? (Auth::user()->username ?? '') : '',
    'email' => fn () => Auth::check() ? Auth::user()->email : '',
    'subject' => '',
    'message' => '',
    'sent' => false,
]);

$send = function () {
    // Protection anti-spam : max 3 envois / 10 minutes par IP,
    // même logique que le rate limiting déjà utilisé pour NEXIA.
    $key = 'contact-form:' . request()->ip();
    if (RateLimiter::tooManyAttempts($key, 3)) {
        $this->addError('form', 'Trop de messages envoyés récemment. Réessaie dans quelques minutes.');
        return;
    }
    RateLimiter::hit($key, 600);

    $validated = $this->validate([
        'name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'email', 'max:255'],
        'subject' => ['required', 'string', 'max:150'],
        'message' => ['required', 'string', 'min:10', 'max:2000'],
    ]);

    ContactMessage::create([
        ...$validated,
        'user_id' => Auth::id(),
    ]);

    $this->reset('subject', 'message');
    $this->sent = true;
};

?>

<link rel="stylesheet" href="{{ asset('css/acceuil.css') }}">
<link rel="stylesheet" href="{{ asset('css/fonctionnalites.css') }}">
<link rel="stylesheet" href="{{ asset('css/contact.css') }}">

<div class="acceuil-shell features-shell contact-shell">

    <div class="bubbles" aria-hidden="true">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <header class="nav-wrap">
        <nav class="glass nav" id="mainNav">
            <a href="{{ url('/') }}" class="brand">
                <img src="{{ asset('images/logos/logonexora.png') }}" alt="Nexora Logo" class="nav-logo-img" />
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="{{ url('/') }}">Accueil</a></li>
                <li><a href="{{ route('fonctionnalites') }}">Fonctionnalités</a></li>
                <li><a href="{{ route('propos') }}">À propos</a></li>
                <li><a href="{{ route('communaute') }}">Communauté</a></li>
                <li><a href="{{ route('contact') }}" class="active">Contact</a></li>
            </ul>
            <div class="nav-cta">
                @auth
                    <a href="{{ route('socialnet') }}" class="btn-primary">Mon espace</a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">Connexion</a>
                    <a href="{{ route('register') }}" class="btn-primary">Rejoindre</a>
                @endauth
            </div>
            <button class="nav-burger" id="navBurger" aria-label="Ouvrir le menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </nav>
    </header>

    <main class="contact-main">

        <div class="contact-grid">

            {{-- ===== Colonne gauche : intro + infos ===== --}}
            <div class="contact-intro" data-cm-reveal>
                <h1 class="contact-title">Une question, une idée, <em>un souci</em> ?</h1>
                <p class="contact-lead">Écris-nous — une vraie personne lit chaque message, pas un robot de tri automatique.</p>

                <div class="contact-info-list">
                    <div class="contact-info-item">
                        <span class="contact-info-ic"><i class="fa-regular fa-envelope"></i></span>
                        <div>
                            <div class="contact-info-label">Email</div>
                            <div class="contact-info-value">contact@nexora.app</div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <span class="contact-info-ic"><i class="fa-regular fa-comment"></i></span>
                        <div>
                            <div class="contact-info-label">Déjà membre ?</div>
                            <div class="contact-info-value">Rejoins le salon <strong>Support</strong> directement sur Nexora</div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <span class="contact-info-ic"><i class="fa-regular fa-clock"></i></span>
                        <div>
                            <div class="contact-info-label">Délai de réponse</div>
                            <div class="contact-info-value">Sous 24 à 48h en général</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Colonne droite : formulaire ===== --}}
            <div class="contact-form-card glass" data-cm-reveal>
                @if ($sent)
                    <div class="contact-success">
                        <span class="contact-success-ic"><i class="fa-solid fa-wand-magic-sparkles" style="color: #6b1f2a;"></i></span>
                        <h2>Message envoyé !</h2>
                        <p>Merci, on te répond très vite. En attendant, n'hésite pas à explorer les salons.</p>
                        <a href="{{ route('fonctionnalites') }}" class="btn-ghost">Découvrir les fonctionnalités</a>
                    </div>
                @else
                    <form wire:submit.prevent="send" class="contact-form">
                        @error('form')
                            <div class="contact-alert">{{ $message }}</div>
                        @enderror

                        <div class="contact-field-row">
                            <label class="contact-field">
                                <span>Nom</span>
                                <input type="text" wire:model="name" placeholder="Ton nom ou pseudo" />
                                @error('name') <small class="contact-error">{{ $message }}</small> @enderror
                            </label>
                            <label class="contact-field">
                                <span>Email</span>
                                <input type="email" wire:model="email" placeholder="toi@exemple.com" />
                                @error('email') <small class="contact-error">{{ $message }}</small> @enderror
                            </label>
                        </div>

                        <label class="contact-field">
                            <span>Sujet</span>
                            <input type="text" wire:model="subject" placeholder="De quoi veux-tu parler ?" />
                            @error('subject') <small class="contact-error">{{ $message }}</small> @enderror
                        </label>

                        <label class="contact-field">
                            <span>Message</span>
                            <textarea wire:model="message" rows="6" placeholder="Écris-nous en détail…"></textarea>
                            @error('message') <small class="contact-error">{{ $message }}</small> @enderror
                        </label>

                        <button type="submit" class="btn-primary contact-submit" wire:loading.attr="disabled" wire:target="send">
                            <span wire:loading.remove wire:target="send">Envoyer le message →</span>
                            <span wire:loading wire:target="send">Envoi en cours…</span>
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </main>
</div>

<script src="{{ asset('js/acceuil.js') }}"></script>
<script src="{{ asset('js/contact.js') }}"></script>