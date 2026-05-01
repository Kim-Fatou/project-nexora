<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nexora - Connecte-toi par centres d'intérêt</title>
  <meta name="description" content="Nexora est le réseau social qui te connecte avec des personnes partageant tes passions. Découvre, partage et crée des liens authentiques.">
 <link rel="stylesheet" href="{{ asset('css/splash.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
 
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

</head>
<body>

 <section class="hero hero-dark" id="splash-hero">
  <div class="splash-bubbles" aria-hidden="true">
    <span class="splash-bubble b1"></span>
    <span class="splash-bubble b2"></span>
    <span class="splash-bubble b3"></span>
    <span class="splash-bubble b4"></span>
    <span class="splash-bubble b5"></span>
    <span class="splash-bubble b6"></span>
    <span class="splash-bubble b7"></span>
    <span class="splash-bubble b8"></span>
    <span class="splash-bubble b9"></span>
    <span class="splash-bubble b10"></span>
    <span class="splash-bubble b11"></span>
    <span class="splash-bubble b12"></span>
  </div>

  <div class="hero-container">
    <div class="splash-hero-layout">
      <!-- Lateral brand -->
      <aside class="splash-side" aria-label="Brand latéral">
        <div class="splash-side-brand">
          <div class="splash-side-brand-word">N</div>
          <div class="splash-side-brand-word">E</div>
          <div class="splash-side-brand-word">X</div>
          <div class="splash-side-brand-word">O</div>
          <div class="splash-side-brand-word">R</div>
          <div class="splash-side-brand-word">A</div>
          <div class="splash-side-sub">Réseau social par centres d'intérêt</div>
        </div>

        <div class="splash-side-follow">
          <div class="splash-side-follow-title">SUIVEZ-NOUS</div>
          <p class="splash-side-follow-text">
            <strong>Nouveau réseau social</strong><br>
            Nexora est une plateforme qui te connecte avec des personnes partageant tes passions,
            pour des échanges authentiques et des liens durables.
          </p>
          <div class="splash-side-social" aria-label="Réseaux sociaux">
            <a class="splash-side-social-link" href="#" aria-label="LinkedIn">in</a>
            <a class="splash-side-social-link" href="#" aria-label="Facebook">f</a>
            <a class="splash-side-social-link" href="#" aria-label="Instagram">◎</a>
          </div>
        </div>
      </aside>

      <!-- Main frame -->
      <div class="splash-frame" role="region" aria-label="Hero">
        <div class="splash-frame-top">
          <div class="splash-tabs" aria-label="Navigation visuelle">
            <a class="splash-tab is-active" href="{{ route('ecran-demarrage') }}">Accueil</a>
            <a class="splash-tab" href="{{ route('ecran-demarrage') }}#features">Fonctionnalités</a>
            <a class="splash-tab" href="{{ route('ecran-demarrage') }}#community">Communauté</a>
            <a class="splash-tab" href="{{ route('apropos') }}">À propos</a>
            <a class="splash-tab" href="{{ route('contact') }}">Contact</a>
          </div>

          <div class="splash-frame-actions">
            <a class="splash-cta-top" href="{{ route('login') }}">Connexion</a>
            <a class="splash-cta-top splash-cta-secondary" href="{{ route('integration1') }}">Rejoindre</a>
          </div>
        </div>

        <div class="splash-frame-grid">
          <div class="splash-frame-left">
            <div class="splash-kicker">NOUS</div>
            <h1 class="splash-title">CONNECTONS<br>LES PASSIONS</h1>
            <p class="splash-desc">
              Une expérience sociale moderne, pensée pour la connexion authentique, la confidentialité et le partage de centres d'intérêt communs.
            </p>

            <div class="splash-actions">
              <a class="splash-action-primary" href="{{ route('integration1') }}">Voir la démo</a>
              <div class="splash-metric" aria-label="Engagement">
                <div class="splash-metric-badge" aria-hidden="true"></div>
                <div class="splash-metric-text">7,3k</div>
              </div>
            </div>
          </div>

          <div class="splash-frame-right">
            <div class="splash-profiles" id="splashProfiles" aria-label="Avis utilisateurs">
              <div class="splash-profiles-row" role="tablist" aria-label="Sélecteur de profils">
                <button class="splash-profile is-active" type="button" data-name="Amandine" data-role="Utilisatrice" data-comment="« J’adore le fait de pouvoir discuter tout en gardant mes données chez moi. »">
                  <span class="splash-profile-initial">A</span>
                </button>
                <button class="splash-profile" type="button" data-name="Kofi" data-role="Communauté" data-comment="« L’interface est super fluide, et les groupes sont hyper pratiques. »">
                  <span class="splash-profile-initial">K</span>
                </button>
                <button class="splash-profile" type="button" data-name="Mina" data-role="Team" data-comment="« On a connecté l'app à notre communauté en quelques minutes. »">
                  <span class="splash-profile-initial">M</span>
                </button>
              </div>

              <div class="splash-profile-card" role="tabpanel" aria-live="polite">
                <div class="splash-profile-card-head">
                  <div class="splash-profile-card-avatar" aria-hidden="true">A</div>
                  <div class="splash-profile-card-meta">
                    <div class="splash-profile-card-name" id="splashProfileName">Amandine</div>
                    <div class="splash-profile-card-role" id="splashProfileRole">Utilisatrice</div>
                  </div>
                </div>
                <p class="splash-profile-card-comment" id="splashProfileComment">
                  « J’adore le fait de pouvoir discuter tout en gardant mes données chez moi. »
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="module" src="https://unpkg.com/@splinetool/viewer@1.12.87/build/spline-viewer.js"></script>
  <!-- Features Section -->
  <section class="features" id="features">
    <div class="features-container">
      <div class="features-header">
        <h2 class="features-title">
          Nexora, le choix qui te connecte.
        </h2>
        <p class="features-description">
          Des rencontres authentiques, des avis réels et une expérience qui donne envie de rester.
        </p>
      </div>

      <!-- Témoignages (scroll horizontal infini) -->
      <section class="nx-testimonials" aria-label="Témoignages">
        <div class="nx-testimonials-head">
          <h3 class="nx-testimonials-title">Ils restent sur <span class="gradient-text">Nexora</span></h3>
          <p class="nx-testimonials-subtitle">Des avis courts, concrets et vrais — qui défilent en continu.</p>
        </div>

        <div class="nx-marquee" data-marquee="testimonials" aria-label="Défilement de témoignages">
          <div class="nx-marquee-fade nx-marquee-fade--left" aria-hidden="true"></div>
          <div class="nx-marquee-fade nx-marquee-fade--right" aria-hidden="true"></div>

          <div class="nx-marquee-viewport" id="nxMarqueeViewport">
            <div class="nx-marquee-track" id="nxMarqueeTrack">
              <!-- Set 1 (JS duplique automatiquement pour boucle infinie) -->
              <article class="nx-tcard" tabindex="0" data-name="Ryan">
                <header class="nx-tcard-head">
                  <span class="nx-tavatar" aria-hidden="true">R</span>
                  <div class="nx-tmeta">
                    <div class="nx-tname">Ryan</div>
                    <div class="nx-trole">Membre Nexora</div>
                  </div>
                </header>
                <p class="nx-tcomment">« J’ai trouvé des amis passionnés par la danse. L’ambiance est simple et naturelle. »</p>
                <footer class="nx-trating" aria-label="Note 5 étoiles">
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                </footer>
              </article>

              <article class="nx-tcard" tabindex="0" data-name="Amadine">
                <header class="nx-tcard-head">
                  <span class="nx-tavatar" aria-hidden="true">A</span>
                  <div class="nx-tmeta">
                    <div class="nx-tname">Amadine</div>
                    <div class="nx-trole">Communauté Live</div>
                  </div>
                </header>
                <p class="nx-tcomment">« Les rencontres sont plus vraies ici. J’ai rejoint un live incroyable en 2 minutes. »</p>
                <footer class="nx-trating" aria-label="Note 4 étoiles">
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star" aria-hidden="true">★</span>
                </footer>
              </article>

              <article class="nx-tcard" tabindex="0" data-name="Mina">
                <header class="nx-tcard-head">
                  <span class="nx-tavatar" aria-hidden="true">M</span>
                  <div class="nx-tmeta">
                    <div class="nx-tname">Mina</div>
                    <div class="nx-trole">Match & discussions</div>
                  </div>
                </header>
                <p class="nx-tcomment">« Les échanges sont fluides. Je découvre des personnes qui me ressemblent vraiment. »</p>
                <footer class="nx-trating" aria-label="Note 5 étoiles">
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                </footer>
              </article>

              <article class="nx-tcard" tabindex="0" data-name="Kofi">
                <header class="nx-tcard-head">
                  <span class="nx-tavatar" aria-hidden="true">K</span>
                  <div class="nx-tmeta">
                    <div class="nx-tname">Kofi</div>
                    <div class="nx-trole">Gaming</div>
                  </div>
                </header>
                <p class="nx-tcomment">« Pas de spam. Juste des discussions qui matchent avec mes centres d’intérêt. »</p>
                <footer class="nx-trating" aria-label="Note 4 étoiles">
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star" aria-hidden="true">★</span>
                </footer>
              </article>

              <article class="nx-tcard" tabindex="0" data-name="Tina">
                <header class="nx-tcard-head">
                  <span class="nx-tavatar" aria-hidden="true">T</span>
                  <div class="nx-tmeta">
                    <div class="nx-tname">Tina</div>
                    <div class="nx-trole">Photo • Art</div>
                  </div>
                </header>
                <p class="nx-tcomment">« L’interface est clean et moderne. J’ai envie de rester et discuter. »</p>
                <footer class="nx-trating" aria-label="Note 5 étoiles">
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                  <span class="nx-star is-on" aria-hidden="true">★</span>
                </footer>
              </article>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats Banner -->
      <div class="stats-banner glass">
        <div class="stat-item">
          <span class="stat-number">50K+</span>
          <span class="stat-desc">Utilisateurs</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <span class="stat-number">120+</span>
          <span class="stat-desc">Communautés</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <span class="stat-number">1M+</span>
          <span class="stat-desc">Connexions</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <span class="stat-number">4.9</span>
          <span class="stat-desc">Note App Store</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-accent"></div>
    <div class="footer-container">
      <div class="footer-grid">
        <!-- Brand -->
        <div class="footer-brand">
          <a href="#" class="logo footer-logo">
            <div class="logo-icon">
             <img src="/Images/lg-removebg-preview.png" alt="">
            </div>
            <img src="" alt=""><span class="logo-text">Nexora</span>
          </a>
          <p class="footer-tagline">
            Connecte-toi avec des personnes qui partagent tes passions.
          </p>
          <div class="social-links">
            <a href="#" class="social-link" aria-label="Twitter">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
              </svg>
            </a>
            <a href="#" class="social-link" aria-label="Instagram">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
              </svg>
            </a>
            <a href="#" class="social-link" aria-label="LinkedIn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                <rect width="4" height="12" x="2" y="9"></rect>
                <circle cx="4" cy="4" r="2"></circle>
              </svg>
            </a>
          </div>
        </div>

        <!-- Links -->
        <div class="footer-links">
          <div class="footer-column">
            <h4 class="footer-heading">Produit</h4>
            <a href="#" class="footer-link">Fonctionnalités</a>
            <a href="#" class="footer-link">Tarifs</a>
            <a href="#" class="footer-link">FAQ</a>
          </div>
          <div class="footer-column">
            <h4 class="footer-heading">Entreprise</h4>
            <a href="#" class="footer-link">À propos</a>
            <a href="#" class="footer-link">Blog</a>
            <a href="#" class="footer-link">Carrières</a>
          </div>
          <div class="footer-column">
            <h4 class="footer-heading">Légal</h4>
            <a href="#" class="footer-link">Confidentialité</a>
            <a href="#" class="footer-link">CGU</a>
            <a href="#" class="footer-link">Cookies</a>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2026 Nexora. Tous droits réservés.</p>
      </div>
    </div>
  </footer>

  <script>
(() => {
  const root = document.getElementById('splash-hero');
  if (!root) return;

  const profilesRoot = root.querySelector('#splashProfiles');
  if (!profilesRoot) return;

  const buttons = Array.from(profilesRoot.querySelectorAll('.splash-profile'));
  const nameEl = profilesRoot.querySelector('#splashProfileName');
  const roleEl = profilesRoot.querySelector('#splashProfileRole');
  const commentEl = profilesRoot.querySelector('#splashProfileComment');
  const avatarEl = profilesRoot.querySelector('.splash-profile-card-avatar');

  function setActive(btn) {
    buttons.forEach(b => b.classList.toggle('is-active', b === btn));
    if (nameEl) nameEl.textContent = btn.dataset.name || '';
    if (roleEl) roleEl.textContent = btn.dataset.role || '';
    if (commentEl) commentEl.textContent = btn.dataset.comment || '';
    if (avatarEl) avatarEl.textContent = (btn.dataset.name || '?').trim().slice(0, 1).toUpperCase();
    // Show the card
    const card = profilesRoot.querySelector('.splash-profile-card');
    if (card) card.style.display = 'block';
  }

  buttons.forEach(btn => {
    btn.addEventListener('click', () => setActive(btn));
    btn.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        setActive(btn);
      }
    });
  });
})();
</script>
 
  <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
