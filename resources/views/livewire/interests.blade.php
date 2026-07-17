<?php

use function Livewire\Volt\{state, mount, layout};
use Illuminate\Support\Facades\Auth;
use App\Models\Interest;

state(['selectedInterests' => []]);
layout('layouts.app');

mount(function () {
    if (Auth::check()) {
        // Rediriger si l'utilisateur a déjà terminé son onboarding (3 intérêts ou plus choisis)
        if (Auth::user()->interests()->count() >= 3) {
            return redirect()->route('socialnet');
        }
    }
});

$saveInterests = function($interestsValues = []) {
    if (!Auth::check()) {
        session()->flash('error', 'Tu dois être connecté.');
        return redirect()->route('login');
    }
    
    if (empty($interestsValues) || count($interestsValues) < 3) {
        session()->flash('error', 'Sélectionne au moins 3 centres d’intérêt !');
        return;
    }

    // Récupère les vrais IDs numériques de la table interests
    $interestsIds = Interest::whereIn('slug', $interestsValues)
                            ->orWhereIn('name', $interestsValues)
                            ->pluck('id')
                            ->toArray();

    // S'il y a des IDs valides trouvés, on les synchronise
    if (!empty($interestsIds)) {
        Auth::user()->interests()->sync($interestsIds);
        Auth::user()->checkAndAwardProfileCompletionXp();
    }

    return redirect()->route('profile');
};

?>


<div class="interests-wrapper" id="interests-component" wire:id="interests">
    <link rel="stylesheet" href="{{ asset('css/interests.css') }}"> 

    <header>
        <nav class="justify-center">
            <div class="logo">
                <img src="{{ asset('images/logos/logonexora.png') }}" alt="Nexora Logo" style="height: 40px; width: auto; object-fit: contain; display: block;" />
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="badge"><span class="dot"></span>Étape 1 · Personnalise ton univers</div>
        <h1>Choisis tes <em>centres d'intérêt</em></h1>
        <p>Tape sur les bulles qui te ressemblent. Chaque passion s'anime à ton contact — choisis-en au moins 3.</p>
    </section>

    @if (session()->has('error'))
        <div style="max-width: 400px; margin: 20px auto; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 12px; border-radius: 12px; font-size: 0.85rem; text-align: center;">
            {{ session('error') }}
        </div>
    @endif

    <div class="container">
        <div class="filters" id="filters"></div>
    </div>

    <div class="grid" id="grid"></div>

    <div class="cta" id="cta">
        <div class="cta-inner">
            <div class="cta-left">
                <span class="count" id="count">0</span>
                <span class="cta-text" id="ctaText">Encore 3 pour continuer</span>
            </div>
            <button class="btn-continue" id="continue" disabled onclick="submitInterests()">
                Continuer
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </div>
    </div>

    <script src="{{ asset('js/interests.js') }}"></script>
</div>
