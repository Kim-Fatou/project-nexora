<?php

use function Livewire\Volt\{layout};

layout('layouts.app');

?>

<link rel="stylesheet" href="{{ asset('css/acceuil.css') }}">
<link rel="stylesheet" href="{{ asset('css/fonctionnalites.css') }}">

<div class="acceuil-shell features-shell">

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
                <li><a href="{{ route('contact') }}">Contact</a></li>
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

    <nav class="feat-index" id="featIndex" aria-label="Sommaire des fonctionnalités">
        <ol>
            <li><a href="#f-interets" data-target="f-interets"><span class="fi-num">01</span><span class="fi-label">Centres d'intérêt</span></a></li>
            <li><a href="#f-salons" data-target="f-salons"><span class="fi-num">02</span><span class="fi-label">Salons thématiques</span></a></li>
            <li><a href="#f-chat" data-target="f-chat"><span class="fi-num">03</span><span class="fi-label">Discussions privées</span></a></li>
            <li><a href="#f-nexia" data-target="f-nexia"><span class="fi-num">04</span><span class="fi-label">NEXIA</span></a></li>
            <li><a href="#f-anonymat" data-target="f-anonymat"><span class="fi-num">05</span><span class="fi-label">Anonymat</span></a></li>
            <li><a href="#f-langues" data-target="f-langues"><span class="fi-num">06</span><span class="fi-label">Multilingue</span></a></li>
            <li><a href="#f-premium" data-target="f-premium"><span class="fi-num">07</span><span class="fi-label">Premium & Experts</span></a></li>
        </ol>
    </nav>

    <main class="feat-main">

        {{-- 01 — Centres d'intérêt --}}
        <section class="feat-section" id="f-interets">
            <div class="feat-num">01</div>
            <div class="feat-grid">
                <div class="feat-copy">
                    <h2 class="feat-title">Des rencontres par <em>centres d'intérêt</em>, jamais par algorithme.</h2>
                    <p class="feat-text">Choisis tes passions à l'inscription — technologie, musique, voyage, cuisine… Nexora te connecte avec des gens qui partagent vraiment ce que tu aimes, sans boîte noire qui décide à ta place.</p>
                </div>
                <div class="feat-shot">
                    <img src="{{ asset('images/features/interets.png') }}" alt="Sélection des centres d'intérêt sur Nexora">
                </div>
            </div>
        </section>

        {{-- 02 — Salons --}}
        <section class="feat-section" id="f-salons">
            <div class="feat-num">02</div>
            <div class="feat-grid feat-grid-reverse">
                <div class="feat-copy">
                    <h2 class="feat-title">Des <em>salons</em> pour discuter en groupe de ce qui te passionne.</h2>
                    <p class="feat-text">Rejoins des salons thématiques et échange en temps réel avec plusieurs passionnés à la fois — chaque salon est un espace dédié à un centre d'intérêt précis.</p>
                </div>
                <div class="feat-shot feat-shot-phone">
                    <img src="{{ asset('images/features/salons.png') }}" alt="Salon de discussion Entrepreneuriat sur Nexora">
                </div>
            </div>
        </section>

        {{-- 03 — Chat privé --}}
        <section class="feat-section" id="f-chat">
            <div class="feat-num">03</div>
            <div class="feat-grid">
                <div class="feat-copy">
                    <h2 class="feat-title">Des <em>discussions privées</em>, en texte et en audio.</h2>
                    <p class="feat-text">Une fois connecté avec quelqu'un, poursuis la conversation en privé — messages et notes vocales, pensés pour préserver ton anonymat du début à la fin.</p>
                </div>
                <div class="feat-shot feat-shot-phone">
                    <img src="{{ asset('images/features/chat.png') }}" alt="Discussion privée sur Nexora">
                </div>
            </div>
        </section>

        {{-- 04 — NEXIA --}}
        <section class="feat-section" id="f-nexia">
            <div class="feat-num">04</div>
            <div class="feat-grid feat-grid-reverse">
                <div class="feat-copy">
                    <h2 class="feat-title"><em>NEXIA</em>, ta compagne pour bien démarrer.</h2>
                    <p class="feat-text">Dès ton inscription, NEXIA t'accompagne pas à pas pour composer ton profil et comprendre comment tirer le meilleur de Nexora — comme une vraie conversation, pas un formulaire.</p>
                </div>
                <div class="feat-shot feat-shot-phone">
                    <img src="{{ asset('images/features/nexia.png') }}" alt="Assistante NEXIA sur Nexora">
                </div>
            </div>
        </section>

        {{-- 05 — Anonymat --}}
        <section class="feat-section" id="f-anonymat">
            <div class="feat-num">05</div>
            <div class="feat-grid">
                <div class="feat-copy">
                    <h2 class="feat-title">Ton <em>anonymat</em> respecté, du premier au dernier jour.</h2>
                    <p class="feat-text">Pas de nom, pas d'âge affiché : juste un identifiant unique que tu choisis toi-même. Nexora est pensé pour qu'on te découvre par ce que tu partages, pas par qui tu es à l'état civil.</p>
                </div>
                <div class="feat-shot">
                    <img src="{{ asset('images/features/anonymat.png') }}" alt="Formulaire de profil anonyme sur Nexora">
                </div>
            </div>
        </section>

        {{-- 06 — Multilingue (pas encore de capture réelle, on garde l'aperçu) --}}
        <section class="feat-section" id="f-langues">
            <div class="feat-num">06</div>
            <div class="feat-grid feat-grid-reverse">
                <div class="feat-copy">
                    <h2 class="feat-title">Une communauté <em>multilingue</em>, sans barrière.</h2>
                    <p class="feat-text">Indique ta langue native et celles que tu parles — Nexora t'aide à trouver des interlocuteurs avec qui la conversation coule naturellement.</p>
                </div>
                <div class="feat-visual glass">
                    <div class="mini-bubbles">
                        <span class="mini-chip is-on">Français</span>
                        <span class="mini-chip is-on">Bambara</span>
                        <span class="mini-chip">Anglais</span>
                        <span class="mini-chip">Wolof</span>
                    </div>
                </div>
            </div>
        </section>
        {{-- 07 — Premium & Experts --}}
        <section class="feat-section" id="f-premium">
            <div class="feat-num">07</div>
            <div class="feat-grid">
                <div class="feat-copy">
                    <h2 class="feat-title">Espace <em>Premium & Experts</em> pour aller plus loin.</h2>
                    <p class="feat-text">Accédez instantanément à l'espace Premium pour échanger en direct avec des experts certifiés dans vos passions. Les membres Premium bénéficient également de la traduction automatique des discussions en temps réel grâce à l'IA de Gemini et d'un badge exclusif. Le statut Premium est accessible gratuitement dès que votre score de discussion atteint le niveau 6 !</p>
                </div>
                <div class="feat-visual glass flex flex-col items-center justify-center p-6 gap-3">
                    <div class="w-12 h-12 rounded-full bg-[#6b1f2a]/10 flex items-center justify-center text-2xl animate-bounce">👑</div>
                    <span class="text-xs font-semibold text-[#6b1f2a]">Badge Premium Actif</span>
                    <span class="text-[10px] text-neutral-500 text-center">Accès aux experts & Traduction instantanée activée</span>
                </div>
            </div>
        </section>

    </main>
</div>

<script src="{{ asset('js/acceuil.js') }}"></script>
<script src="{{ asset('js/fonctionnalites.js') }}"></script>