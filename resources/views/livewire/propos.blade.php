<?php

use function Livewire\Volt\{layout};

layout('layouts.app');

?>

<link rel="stylesheet" href="{{ asset('css/acceuil.css') }}">
<link rel="stylesheet" href="{{ asset('css/propos.css') }}">

<div class="acceuil-shell propos-shell">

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
                <li><a href="{{ route('propos') }}" class="active">À propos</a></li>
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

    {{-- ==================================================================
         SECTION 1 — HERO / MANIFESTE
         Grand titre éditorial, à la Locomotive : typographie massive,
         peu de texte, beaucoup de respiration.
    =================================================================== --}}
    <section class="po-section po-hero" data-po-reveal>
        <h1 class="po-hero-title">
            Rapprocher par les<br>
            <em>passions</em>,<br>
            pas par des <span class="po-strike">algorithmes</span>.
        </h1>
        <p class="po-hero-lead">
            Nexora est né d'une idée simple : redonner le contrôle des relations
            humaines à ceux qui les vivent. Pas d'IA manipulatrice, pas de fil
            d'actualité addictif. Juste des humains reliés par ce qu'ils aiment.
        </p>
        <div class="po-scroll-cue" aria-hidden="true">
            <span></span>
            <small>Défile</small>
        </div>
    </section>

    {{-- ==================================================================
         SECTION 2 — LE CONSTAT (bloc sombre plein écran, contraste fort)
    =================================================================== --}}
    <section class="po-section po-dark" data-po-reveal>
        <span class="po-index">01</span>
        <h2 class="po-statement">
            Les réseaux sociaux d'aujourd'hui te connaissent mieux que tes amis
            — et pourtant, tu t'y sens <em>plus seul</em> que jamais.
        </h2>
        <p class="po-statement-sub">
            Fils infinis, algorithmes de rétention, profils basés sur l'apparence :
            le modèle actuel optimise ton attention, pas tes relations. Nous avons
            voulu faire l'inverse.
        </p>
    </section>

    {{-- ==================================================================
         SECTION 3 — NOTRE VISION
    =================================================================== --}}
    <section class="po-section po-vision" data-po-reveal>
        <span class="po-index po-index-dark">02</span>
        <div class="po-vision-grid">
            <h2 class="po-title-lg">Notre <em>vision</em></h2>
            <div class="po-vision-text">
                <p>
                    Et si on se rencontrait par ce qu'on aime vraiment, plutôt que
                    par ce qu'on montre ? Nexora organise les discussions autour de
                    centres d'intérêt réels — pas de photos, pas de likes, pas de
                    scroll infini.
                </p>
                <p>
                    Chaque utilisateur existe d'abord à travers ses passions et un
                    identifiant qu'il choisit. Le reste — l'apparence, l'âge, le nom
                    — vient après, si et quand la personne le décide.
                </p>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         SECTION 4 — LES PILIERS (liste indexée façon Locomotive,
         plutôt que 3 cartes côte à côte)
    =================================================================== --}}
    <section class="po-section po-pillars" data-po-reveal>
        <span class="po-index po-index-dark">03</span>
        <h2 class="po-title-lg po-pillars-title">Nos <em>piliers</em></h2>

        <div class="po-pillar-row" data-po-reveal>
            <span class="po-pillar-num">01</span>
            <div class="po-pillar-body">
                <h3>Anonymat absolu</h3>
                <p>Pas de revente de données, pas de profilage. Tu existes à travers tes passions et le pseudonyme que tu choisis — rien d'autre n'est exigé.</p>
            </div>
            <span class="po-pillar-icon"><i class="fa-solid fa-lock"></i></span>
        </div>
        <div class="po-pillar-row" data-po-reveal>
            <span class="po-pillar-num">02</span>
            <div class="po-pillar-body">
                <h3>Connexion naturelle</h3>
                <p>Salons et discussions organisés par centres d'intérêt réels. Trouve instantanément ceux qui résonnent avec toi, sans algorithme entre vous.</p>
            </div>
            <span class="po-pillar-icon"><i class="fa-solid fa-handshake"></i></span>
        </div>
        <div class="po-pillar-row" data-po-reveal>
            <span class="po-pillar-num">03</span>
            <div class="po-pillar-body">
                <h3>Échanges libres</h3>
                <p>Messagerie chiffrée, notes vocales, appels et partage de fichiers — une communication complète, pensée pour être simple, pas surveillée.</p>
            </div>
            <span class="po-pillar-icon"><i class="fa-solid fa-comments"></i></span>
        </div>
    </section>

    {{-- ==================================================================
         SECTION 5 — LE PARCOURS (comment ça marche, étapes numérotées)
    =================================================================== --}}
    <section class="po-section po-journey" data-po-reveal>
        <span class="po-index">04</span>
        <h2 class="po-title-lg">Comment ça <em>marche</em></h2>

        <div class="po-journey-grid">
            <div class="po-step" data-po-reveal>
                <span class="po-step-num">1</span>
                <h3>Choisis tes passions</h3>
                <p>Sélectionne les centres d'intérêt qui te définissent vraiment.</p>
            </div>
            <div class="po-step" data-po-reveal>
                <span class="po-step-num">2</span>
                <h3>Compose ton profil</h3>
                <p>Un identifiant unique, une bio, tes préférences — sans nom ni âge affichés.</p>
            </div>
            <div class="po-step" data-po-reveal>
                <span class="po-step-num">3</span>
                <h3>Rejoins des salons</h3>
                <p>Discute avec des passionnés qui partagent réellement tes centres d'intérêt.</p>
            </div>
            <div class="po-step" data-po-reveal>
                <span class="po-step-num">4</span>
                <h3>Connecte, en confiance</h3>
                <p>Chat, appels, notes vocales — à ton rythme, en toute discrétion.</p>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         SECTION 6 — CHIFFRES CLÉS (bloc sombre, gros compteurs animés)
    =================================================================== --}}
    <section class="po-section po-dark po-stats" data-po-reveal>
        <span class="po-index">05</span>
        <h2 class="po-title-lg po-stats-title">Nexora en <em>chiffres</em></h2>
        <div class="po-stats-grid">
            <div class="po-stat">
                <span class="po-stat-num" data-count-to="2000">0</span>
                <span class="po-stat-plus">+</span>
                <span class="po-stat-label">Membres actifs</span>
            </div>
            <div class="po-stat">
                <span class="po-stat-num" data-count-to="30">0</span>
                <span class="po-stat-plus">+</span>
                <span class="po-stat-label">Centres d'intérêt</span>
            </div>
            <div class="po-stat">
                <span class="po-stat-num" data-count-to="0">0</span>
                <span class="po-stat-label">Donnée revendue</span>
            </div>
            <div class="po-stat">
                <span class="po-stat-num" data-count-to="100">0</span>
                <span class="po-stat-plus">%</span>
                <span class="po-stat-label">Anonymat garanti</span>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         SECTION 7 — CTA FINAL
    =================================================================== --}}
    <section class="po-section po-cta" data-po-reveal>
        <span class="po-index po-index-dark">06</span>
        <h2 class="po-cta-title">Prêt(e) à te reconnecter<br><em>autrement</em> ?</h2>
        <p class="po-cta-lead">Rejoins une communauté qui te découvre par ce que tu aimes.</p>
        <div class="po-cta-actions">
            @auth
                <a href="{{ route('socialnet') }}" class="btn-primary po-cta-btn">Retourner à Nexora →</a>
            @else
                <a href="{{ route('register') }}" class="btn-primary po-cta-btn">Rejoindre Nexora →</a>
                <a href="{{ route('fonctionnalites') }}" class="btn-ghost po-cta-btn">Voir les fonctionnalités</a>
            @endauth
        </div>
    </section>
</div>

<script src="{{ asset('js/acceuil.js') }}"></script>
<script src="{{ asset('js/propos.js') }}"></script>