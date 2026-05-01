<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nexora - À propos</title>
  <meta name="description" content="Découvre la mission de Nexora et ce qui nous motive à créer des connexions authentiques." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('CSS/splash.css') }}">
  <link rel="stylesheet" href="{{ asset('CSS/onboarding.css') }}">
  <style>
    .about-card { max-width: 860px; }
    .about-grid {
      display: grid;
      gap: 1rem;
      margin-top: 1.25rem;
    }
    @media (min-width: 768px) {
      .about-grid { grid-template-columns: repeat(3, 1fr); }
    }
    .about-panel {
      background: rgba(86, 28, 36, 0.03);
      border: 1px solid rgba(86, 28, 36, 0.08);
      border-radius: var(--radius);
      padding: 1rem;
      transition: transform 0.2s ease, background 0.2s ease;
    }
    .about-panel:hover {
      transform: translateY(-2px);
      background: rgba(86, 28, 36, 0.05);
    }
    .about-panel h3 {
      font-size: 0.95rem;
      font-weight: 700;
      margin-bottom: 0.35rem;
      color: var(--foreground);
    }
    .about-panel p {
      font-size: 0.875rem;
      opacity: 0.75;
    }
    .about-cta {
      display: flex;
      gap: 0.75rem;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 1.5rem;
    }
  </style>
</head>
<body>
  <header class="header" id="header">
    <div class="header-container">
      <a href="{{ route('ecran-demarrage') }}" class="logo">
        <div class="logo-icon">
          <img src="/Images/lg-removebg-preview.png" alt="" />
        </div>
        <img src="" alt="" /><span class="logo-text">Nexora</span>
      </a>

      <nav class="nav-desktop">
        <a href="{{ route('ecran-demarrage') }}" class="nav-link">Accueil</a>
        <a href="{{ route('ecran-demarrage') }}#features" class="nav-link">Fonctionnalités</a>
        <a href="{{ route('apropos') }}" class="nav-link">À propos</a>
        <a href="{{ route('ecran-demarrage') }}#community" class="nav-link">Communauté</a>
        <a href="{{ route('contact') }}" class="nav-link">Contact</a>
      </nav>

      <div class="header-actions">
        <a href="{{ route('ecran-demarrage') }}" class="btn-ghost">Connexion</a>
        <a href="{{ route('integration1') }}" class="btn-primary">Rejoindre</a>
      </div>

      <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
        <span class="menu-icon"></span>
      </button>
    </div>

    <nav class="nav-mobile" id="mobileMenu">
      <a href="{{ route('ecran-demarrage') }}" class="nav-link-mobile">Accueil</a>
      <a href="{{ route('ecran-demarrage') }}#features" class="nav-link-mobile">Fonctionnalités</a>
      <a href="{{ route('apropos') }}" class="nav-link-mobile">À propos</a>
      <a href="{{ route('ecran-demarrage') }}#community" class="nav-link-mobile">Communauté</a>
      <a href="{{ route('contact') }}" class="nav-link-mobile">Contact</a>
      <div class="mobile-actions">
        <a href="{{ route('ecran-demarrage') }}" class="btn-ghost-mobile">Connexion</a>
        <a href="{{ route('integration1') }}" class="btn-primary-mobile">Rejoindre</a>
      </div>
    </nav>
  </header>

  <div class="bg-shape bg-shape-1"></div>
  <div class="bg-shape bg-shape-2"></div>
  <div class="bg-shape bg-shape-3"></div>

  <div class="onboarding-container">
    <div class="onboarding-card about-card page-transition">
      <div class="card-header">
        <div class="card-icon animate-scale-in">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4z"></path>
            <path d="M9.5 12.5l1.5 1.5 3.5-3.5"></path>
          </svg>
        </div>
        <h1 class="card-title animate-fade-in-up opacity-0">
          À propos de <span class="gradient-text">Nexora</span>
        </h1>
        <p class="card-subtitle animate-fade-in-up opacity-0 delay-200" style="max-width: 560px;">
          Nexora connecte les personnes à partir de ce qu’elles aiment vraiment. Moins de bruit, plus de vraies affinités.
        </p>
      </div>

      <div class="about-grid">
        <div class="about-panel animate-fade-in-up opacity-0 delay-300">
          <h3>Notre mission</h3>
          <p>Créer des connexions authentiques basées sur les centres d’intérêt, pas sur les apparences.</p>
        </div>
        <div class="about-panel animate-fade-in-up opacity-0 delay-400">
          <h3>Notre promesse</h3>
          <p>Une expérience simple, bienveillante et privée, pensée pour des échanges de qualité.</p>
        </div>
        <div class="about-panel animate-fade-in-up opacity-0 delay-500">
          <h3>Notre communauté</h3>
          <p>Des groupes, des discussions et des événements autour de passions communes.</p>
        </div>
      </div>

      <div class="about-panel animate-fade-in-up opacity-0 delay-600" style="margin-top: 1rem;">
        <h3 style="margin-bottom: 0.5rem;">Ce que tu peux faire sur Nexora</h3>
        <p style="opacity: 0.75;">
          Personnaliser ton profil, rejoindre des communautés thématiques, discuter en privé ou en groupe et découvrir des profils compatibles près de chez toi.
        </p>
      </div>

      <div class="about-cta animate-fade-in-up opacity-0 delay-700">
        <a href="{{ route('integration1') }}" class="btn btn-primary">
          <span>Commencer</span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14M12 5l7 7-7 7"></path>
          </svg>
        </a>
        <a href="{{ route('contact') }}" class="btn btn-secondary">
          Nous contacter
        </a>
      </div>
    </div>
  </div>

  <footer class="footer">
    <div class="footer-accent"></div>
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="{{ route('ecran-demarrage') }}" class="logo footer-logo">
            <div class="logo-icon">
              <img src="/Images/lg-removebg-preview.png" alt="" />
            </div>
            <img src="" alt="" /><span class="logo-text">Nexora</span>
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

        <div class="footer-links">
          <div class="footer-column">
            <h4 class="footer-heading">Produit</h4>
            <a href="{{ route('ecran-demarrage') }}#features" class="footer-link">Fonctionnalités</a>
            <a href="#" class="footer-link">Tarifs</a>
            <a href="#" class="footer-link">FAQ</a>
          </div>
          <div class="footer-column">
            <h4 class="footer-heading">Entreprise</h4>
            <a href="{{ route('apropos') }}" class="footer-link">À propos</a>
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

  <script src="/js/script.js"></script>
</body>
</html>
