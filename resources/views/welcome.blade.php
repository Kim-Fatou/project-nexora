@extends('layouts.app')

@section('content')
{{-- Feuille de style dédiée à la page d'accueil (voir public/css/acceuil.css) --}}
<link rel="stylesheet" href="{{ asset('css/acceuil.css') }}">

<div class="acceuil-shell">

    <!-- Bulles flottantes en fond (juste décoratif, CSS pur) -->
    <div class="bubbles" aria-hidden="true">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- ===== NAV ===== -->
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

            {{-- Bouton dynamique selon l'état de connexion --}}
            <div class="nav-cta">
                @auth
                    <a href="{{ route('socialnet') }}" class="btn-primary">Mon espace</a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">Connexion</a>
                    <a href="{{ route('register') }}" class="btn-primary">Rejoindre</a>
                @endauth
            </div>

            {{-- Bouton "burger" affiché uniquement en mobile (voir acceuil.css) --}}
            <button class="nav-burger" id="navBurger" aria-label="Ouvrir le menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </nav>

        {{-- Badge "100% anonyme" : occupe l'espace à droite de la nav --}}
        <div class="anon-badge" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>100% anonyme</span>
        </div>

    </header>

    <main class="corps">

        <!-- ===== Panneau NEXIA (mini assistante IA de démonstration) ===== -->
        <aside class="nexia-panel glass" id="nexiaPanel">
            <div class="nexia-head">
                <div class="nexia-avatar">N</div>
                <div>
                    <div class="nexia-name">NEXIA</div>
                    <p class="nexia-tagline">Ton assistante pour trouver des centres d'intérêt.</p>
                </div>
            </div>

            {{-- Zone où s'affichent les bulles de conversation (remplie par acceuil.js) --}}
            <div class="nexia-chat" id="nexiaChat">
                <p class="nexia-hint" id="nexiaHint">Pose-moi une question, je t'aide à explorer Nexora <i class="fa-solid fa-wand-magic-sparkles" style="color: #6b1f2a;"></i></p>
            </div>

            <form class="nexia-form" id="nexiaForm">
                <input
                    type="text"
                    id="nexiaInput"
                    class="search-bar"
                    placeholder="Rechercher un centre d'intérêt…"
                    autocomplete="off"
                />
                <button type="submit" class="btn-primary" id="send-btn" aria-label="Envoyer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
        </aside>

        <!-- ===== HERO (bloc principal) ===== -->
        <section class="hero glass" id="hero">
            <div class="hero-body">
                <div class="hero-left reveal">
                    <h1 class="title">
                        <span class="t-line">Connecte-toi</span>
                        <span class="t-line">par</span>
                        <span class="t-line t-accent">centres</span>
                        <span class="t-line t-accent">d'intérêt pas par algorithmes.</span>
                    </h1>

                    <p class="lead">
                        Nexora est le réseau social qui te connecte avec des personnes
                        partageant tes passions. Découvre, partage et crée des liens authentiques.
                    </p>

                    <div class="members">
                        <div class="avatars">
                            <span class="av av-a">A</span>
                            <span class="av av-b">B</span>
                            <span class="av av-c">C</span>
                            <span class="av av-more">+2k</span>
                        </div>
                        <div class="members-text"><strong>+2 000</strong> membres actifs</div>
                    </div>
                </div>

                <!-- Centre : tags d'intérêts flottants (décoratif, masqué sous 1100px) -->
                <div class="hero-center" aria-hidden="true">
                    <span class="chip chip-1 glass-light"><i class="fa-solid fa-palette"></i> Art</span>
                    <span class="chip chip-2 glass-light"><i class="fa-solid fa-earth-americas"></i> Voyage</span>
                    <span class="chip chip-3 glass-light"><i class="fa-regular fa-hand-spock"></i> Bonjour</span>
                    <span class="chip chip-4 glass-dark"><i class="fa-solid fa-comments"></i> Chat</span>
                    <span class="chip chip-5 glass-light"><i class="fa-solid fa-heart" style="color: #6b1f2a;"></i> +128</span>
                    <span class="chip chip-6 glass-light"><i class="fa-solid fa-utensils"></i> Cuisine</span>
                    <span class="chip chip-8 glass-light"><i class="fa-solid fa-leaf"></i> Nature</span>
                </div>

                <!-- Aperçu chat à droite (décoratif, illustration) -->
                <div class="hero-right">
                    <div class="chat-preview glass reveal">
                        <div class="cp-row">
                            <div class="cp-avatar"></div>
                            <div class="cp-bar"></div>
                        </div>
                        <div class="cp-msg cp-in">J'adore cette photo ! <i class="fa-solid fa-camera"></i></div>
                        <div class="cp-msg cp-out">On se retrouve demain ?</div>
                    </div>
                </div>
            </div>

            <div class="hero-foot">
                <div class="dotted-line">
                    <span>AUTHENTIQUE</span><i></i><span>COMMUNAUTÉ</span><i></i><span>PASSION</span>
                </div>
            </div>
        </section>

        <!-- ===== SECTION 1 : POURQUOI NEXORA (Statistiques/Valeurs) ===== -->
        <section class="home-extra-section">
            <h2 class="section-title">Pourquoi choisir Nexora ?</h2>
            <p class="section-subtitle">Découvrez une plateforme pensée pour le bien-être de ses membres et la qualité des échanges.</p>
            
            <div class="features-grid">
                <div class="feature-card glass">
                    <div class="feature-icon"><i class="fa-solid fa-brain"></i></div>
                    <h3 class="feature-title">Pas d'algorithmes</h3>
                    <p class="feature-desc">Vos connexions sont guidées uniquement par ce que vous aimez, pas par des robots cherchant à capturer votre attention de force.</p>
                </div>
                <div class="feature-card glass">
                    <div class="feature-icon"><i class="fa-solid fa-user-secret"></i></div>
                    <h3 class="feature-title">Anonymat Garanti</h3>
                    <p class="feature-desc">Échangez sans peur du jugement. Vous décidez de ce que vous partagez, votre identité réelle reste totalement privée.</p>
                </div>
                <div class="feature-card glass">
                    <div class="feature-icon"><i class="fa-solid fa-award"></i></div>
                    <h3 class="feature-title">Experts par Passion</h3>
                    <p class="feature-desc">Entrez en contact direct avec des mentors certifiés pour approfondir vos compétences et partager des projets concrets.</p>
                </div>
            </div>
        </section>

        <!-- ===== SECTION 2 : COMMENT ÇA MARCHE ===== -->
        <section class="home-extra-section glass" style="padding: 40px; margin-top: 56px;">
            <h2 class="section-title">Comment ça marche ?</h2>
            <p class="section-subtitle">Trois étapes simples pour rejoindre la communauté et commencer à partager.</p>
            
            <div class="steps-wrap">
                <div class="step-row">
                    <div class="step-num">01</div>
                    <div class="step-content">
                        <h3 class="step-title">Sélectionnez vos Passions</h3>
                        <p class="step-desc">Lors de votre inscription, choisissez parmi des dizaines d'intérêts : art, sport, technologie, voyage, cuisine, etc. Votre profil s'adapte instantanément à vos choix.</p>
                    </div>
                </div>
                <div class="step-row">
                    <div class="step-num">02</div>
                    <div class="step-content">
                        <h3 class="step-title">Rejoignez les Salons Unifiés</h3>
                        <p class="step-desc">Accédez à des salons de discussions thématiques en temps réel. Partagez des posts, des images, des vocaux ou des vidéos avec les autres membres passionnés.</p>
                    </div>
                </div>
                <div class="step-row">
                    <div class="step-num">03</div>
                    <div class="step-content">
                        <h3 class="step-title">Discutez avec des Experts</h3>
                        <p class="step-desc">Passez Premium pour débloquer la messagerie directe avec des experts dédiés, la traduction en temps réel et des badges exclusifs.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== FOOTER ===== -->
        <footer class="footer-wrap glass">
            <img src="{{ asset('images/logos/logonexora.png') }}" alt="Nexora Logo" class="footer-logo-img" />
            <p class="section-subtitle" style="margin-bottom: 16px;">La plateforme sociale qui vous connecte par centres d'intérêt, pas par algorithmes.</p>
            
            <ul class="footer-links">
                <li><a href="{{ url('/') }}">Accueil</a></li>
                <li><a href="{{ route('fonctionnalites') }}">Fonctionnalités</a></li>
                <li><a href="{{ route('propos') }}">À propos</a></li>
                <li><a href="{{ route('communaute') }}">Communauté</a></li>
                <li><a href="{{ route('contact') }}">Contact</a></li>
            </ul>

            <div class="footer-socials">
                <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="#" aria-label="Discord"><i class="fa-brands fa-discord"></i></a>
                <a href="#" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
            </div>

            <p class="footer-copy">&copy; {{ date('Y') }} Nexora. Tous droits réservés. Conçu avec passion.</p>
        </footer>

    </main>
</div>

{{-- On expose l'URL d'inscription en JS pour le CTA généré dynamiquement dans acceuil.js --}}
<script>window.NEXORA_REGISTER_URL = "{{ route('register') }}";</script>
<script src="{{ asset('js/acceuil.js') }}"></script>
@endsection