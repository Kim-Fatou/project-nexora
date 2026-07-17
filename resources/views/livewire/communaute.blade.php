<?php

use function Livewire\Volt\{layout};

layout('layouts.app');

?>

<link rel="stylesheet" href="{{ asset('css/acceuil.css') }}">
<link rel="stylesheet" href="{{ asset('css/communaute.css') }}">

<div class="acceuil-shell communaute-shell">

    <div class="bubbles" aria-hidden="true">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    {{-- Nav identique à toutes les autres pages --}}
    <header class="nav-wrap">
        <nav class="glass nav" id="mainNav">
            <a href="{{ url('/') }}" class="brand">
                <img src="{{ asset('images/logos/logonexora.png') }}" alt="Nexora Logo" class="nav-logo-img" />
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="{{ url('/') }}">Accueil</a></li>
                <li><a href="{{ route('fonctionnalites') }}">Fonctionnalités</a></li>
                <li><a href="{{ route('propos') }}">À propos</a></li>
                <li><a href="{{ route('communaute') }}" class="active">Communauté</a></li>
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

    {{-- Halo qui suit la souris (esprit Lusion), purement décoratif --}}
    <div class="cm-glow" id="cmGlow" aria-hidden="true"></div>

    {{-- ==================================================================
         SECTION 1 — HERO : titre géant révélé mot par mot
    =================================================================== --}}
    <section class="cm-section cm-hero" data-cm-reveal>
        <h1 class="cm-hero-title" id="cmHeroTitle">
            Des milliers de passionnés,<br>
            <em>une seule</em> vraie communauté.
        </h1>
        <p class="cm-hero-lead">
            Pas de followers, pas de likes à collectionner — juste des gens qui
            partagent ce qui te fait vibrer, prêts à en discuter avec toi.
        </p>
    </section>

    {{-- ==================================================================
         SECTION 2 — BANDEAU DÉFILANT (ticker infini, esprit Lusion)
    =================================================================== --}}
    <section class="cm-ticker-wrap" data-cm-reveal>
        <div class="cm-ticker">
            <div class="cm-ticker-track">
                <span>TECHNOLOGIE</span><span class="cm-dot">✦</span>
                <span>MUSIQUE</span><span class="cm-dot">✦</span>
                <span>VOYAGE</span><span class="cm-dot">✦</span>
                <span>CUISINE</span><span class="cm-dot">✦</span>
                <span>ENTREPRENEURIAT</span><span class="cm-dot">✦</span>
                <span>ART</span><span class="cm-dot">✦</span>
                <span>JEUX VIDÉO</span><span class="cm-dot">✦</span>
                <span>CINÉMA</span><span class="cm-dot">✦</span>
                <!-- dupliqué pour la boucle infinie -->
                <span>TECHNOLOGIE</span><span class="cm-dot">✦</span>
                <span>MUSIQUE</span><span class="cm-dot">✦</span>
                <span>VOYAGE</span><span class="cm-dot">✦</span>
                <span>CUISINE</span><span class="cm-dot">✦</span>
                <span>ENTREPRENEURIAT</span><span class="cm-dot">✦</span>
                <span>ART</span><span class="cm-dot">✦</span>
                <span>JEUX VIDÉO</span><span class="cm-dot">✦</span>
                <span>CINÉMA</span><span class="cm-dot">✦</span>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         SECTION 3 — SALONS EN VEDETTE (galerie horizontale, glisser pour défiler)
    =================================================================== --}}
    <section class="cm-section cm-gallery-section" data-cm-reveal>
        <div class="cm-section-head">
            <span class="cm-index">01</span>
            <h2 class="cm-title-lg">Des salons pour <em>chaque passion</em></h2>
            <p class="cm-section-sub">Glisse pour explorer — chaque salon est un espace vivant, animé par sa communauté.</p>
        </div>
        <div class="cm-gallery" id="cmGallery">
            <div class="cm-gallery-track" id="cmGalleryTrack">
                <div class="cm-gallery-card">
                    <span class="cm-gallery-icon"><i class="fa-solid fa-laptop-code"></i></span>
                    <h3>Technologie</h3>
                    <p>128 membres actifs</p>
                </div>
                <div class="cm-gallery-card">
                    <span class="cm-gallery-icon"><i class="fa-solid fa-music"></i></span>
                    <h3>Musique</h3>
                    <p>212 membres actifs</p>
                </div>
                <div class="cm-gallery-card">
                    <span class="cm-gallery-icon"><i class="fa-solid fa-earth-americas"></i></span>
                    <h3>Voyage</h3>
                    <p>64 membres actifs</p>
                </div>
                <div class="cm-gallery-card">
                    <span class="cm-gallery-icon"><i class="fa-solid fa-utensils"></i></span>
                    <h3>Cuisine</h3>
                    <p>201 membres actifs</p>
                </div>
                <div class="cm-gallery-card">
                    <span class="cm-gallery-icon"><i class="fa-solid fa-rocket"></i></span>
                    <h3>Entrepreneuriat</h3>
                    <p>97 membres actifs</p>
                </div>
                <div class="cm-gallery-card">
                    <span class="cm-gallery-icon"><i class="fa-solid fa-palette"></i></span>
                    <h3>Art</h3>
                    <p>73 membres actifs</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         SECTION 4 — TÉMOIGNAGES
    =================================================================== --}}
    <section class="cm-section cm-testimonials" data-cm-reveal>
        <span class="cm-index">02</span>
        <h2 class="cm-title-lg">Ils en <em>parlent</em> mieux que nous</h2>
        <div class="cm-testi-grid">
            <div class="cm-testi-card" data-cm-reveal>
                <p>« J'ai trouvé des gens qui parlent vraiment de dev toute la journée, sans jugement. Le salon Technologie est devenu mon endroit préféré. »</p>
                <div class="cm-testi-name">@dev_anonyme</div>
            </div>
            <div class="cm-testi-card" data-cm-reveal>
                <p>« Aucune pression de montrer mon visage. Juste discuter de voyage avec des gens qui partagent la même envie de découvrir le monde. »</p>
                <div class="cm-testi-name">@wanderlust_92</div>
            </div>
            <div class="cm-testi-card" data-cm-reveal>
                <p>« NEXIA m'a aidé à composer mon profil sans stress. Trois jours après, j'avais déjà rejoint deux salons et discuté avec plein de monde. »</p>
                <div class="cm-testi-name">@musique_lover</div>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         SECTION 5 — CHIFFRES / PORTÉE GLOBALE
    =================================================================== --}}
    <section class="cm-section cm-dark cm-stats" data-cm-reveal>
        <span class="cm-index cm-index-light">03</span>
        <h2 class="cm-title-lg cm-title-light">Une communauté <em>qui grandit</em></h2>
        <div class="cm-stats-grid">
            <div class="cm-stat">
                <span class="cm-stat-num" data-count-to="2000">0</span><span class="cm-stat-plus">+</span>
                <span class="cm-stat-label">Membres actifs</span>
            </div>
            <div class="cm-stat">
                <span class="cm-stat-num" data-count-to="6">0</span>
                <span class="cm-stat-label">Langues parlées</span>
            </div>
            <div class="cm-stat">
                <span class="cm-stat-num" data-count-to="30">0</span><span class="cm-stat-plus">+</span>
                <span class="cm-stat-label">Salons actifs</span>
            </div>
            <div class="cm-stat">
                <span class="cm-stat-num" data-count-to="24">0</span><span class="cm-stat-plus">/7</span>
                <span class="cm-stat-label">Conversations en cours</span>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         SECTION 6 — REJOINDRE LA CONVERSATION (boutons "magnétiques")
    =================================================================== --}}
    <section class="cm-section cm-join" data-cm-reveal>
        <span class="cm-index">04</span>
        <h2 class="cm-title-lg cm-join-title">Trouve ta place<br><em>dans la conversation</em></h2>
        <div class="cm-magnet-row">
            <button class="cm-magnet" data-cm-magnet><i class="fa-solid fa-laptop-code"></i> Technologie</button>
            <button class="cm-magnet" data-cm-magnet><i class="fa-solid fa-music"></i> Musique</button>
            <button class="cm-magnet" data-cm-magnet><i class="fa-solid fa-earth-americas"></i> Voyage</button>
            <button class="cm-magnet" data-cm-magnet><i class="fa-solid fa-utensils"></i> Cuisine</button>
            <button class="cm-magnet" data-cm-magnet><i class="fa-solid fa-rocket"></i> Startups</button>
            <button class="cm-magnet" data-cm-magnet><i class="fa-solid fa-palette"></i> Art</button>
        </div>
    </section>

    {{-- ==================================================================
         SECTION 7 — CTA FINAL
    =================================================================== --}}
    <section class="cm-section cm-cta" data-cm-reveal>
        <h2 class="cm-cta-title">Ta communauté<br><em>t'attend déjà</em>.</h2>
        <div class="cm-cta-actions">
            @auth
                <a href="{{ route('socialnet') }}" class="btn-primary cm-cta-btn">Retourner à Nexora →</a>
            @else
                <a href="{{ route('register') }}" class="btn-primary cm-cta-btn">Rejoindre Nexora →</a>
            @endauth
        </div>
    </section>

</div>

<script src="{{ asset('js/acceuil.js') }}"></script>
<script src="{{ asset('js/communaute.js') }}"></script>