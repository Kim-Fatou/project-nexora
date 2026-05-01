<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nexora - Créer un compte</title>
  <meta name="description" content="Rejoins Nexora et connecte-toi avec des personnes qui partagent tes passions">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="{{ asset('CSS/splash.css') }}">
  <link rel="stylesheet" href="{{ asset('CSS/onboarding.css') }}">
  <style>
    .onboarding-card { max-width: 720px; }
    .signup-form { max-width: 620px; margin: 0 auto; gap: 1rem; }
    .form-group { margin-bottom: 0.95rem; }
  </style>
</head>
<body>
  <header class="header" id="header">
    <div class="header-container">
      <a href="/ecran-demarrage" class="logo">
        <div class="logo-icon">
          <img src="/Images/lg-removebg-preview.png" alt="">
        </div>
        <img src="" alt=""><span class="logo-text">Nexora</span>
      </a>

      <nav class="nav-desktop">
        <a href="/ecran-demarrage" class="nav-link">Accueil</a>
        <a href="/ecran-demarrage#features" class="nav-link">Fonctionnalités</a>
        <a href="/apropos" class="nav-link">À propos</a>
        <a href="/ecran-demarrage#community" class="nav-link">Communauté</a>
        <a href="/contact" class="nav-link">Contact</a>
      </nav>

      <div class="header-actions">
        <a href="/ecran-demarrage" class="btn-ghost">Connexion</a>
        <a href="/integration1" class="btn-primary">Rejoindre</a>
      </div>

      <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
        <span class="menu-icon"></span>
      </button>
    </div>

    <nav class="nav-mobile" id="mobileMenu">
      <a href="/ecran-demarrage" class="nav-link-mobile">Accueil</a>
      <a href="/ecran-demarrage#features" class="nav-link-mobile">Fonctionnalités</a>
      <a href="/apropos" class="nav-link-mobile">À propos</a>
      <a href="/ecran-demarrage#community" class="nav-link-mobile">Communauté</a>
      <a href="/contact" class="nav-link-mobile">Contact</a>
      <div class="mobile-actions">
        <a href="{{ route('ecran-demarrage') }}" class="btn-ghost-mobile">Connexion</a>
        <a href="{{ route('integration1') }}" class="btn-primary-mobile">Rejoindre</a>
      </div>
    </nav>
  </header>

  <!-- Background Shapes -->
  <div class="bg-shape bg-shape-1"></div>
  <div class="bg-shape bg-shape-2"></div>
  <div class="bg-shape bg-shape-3"></div>

  <!-- Back Button -->
  <a href="{{ route('integration2') }}" class="btn-back">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    <span>Retour</span>
  </a>

  <div class="onboarding-container">
    <div class="onboarding-card page-transition">
      <!-- Progress -->
      <div class="progress-container">
        <div class="progress-bar">
          <div class="progress-fill" style="width: 100%"></div>
        </div>
        <span class="progress-step">3/3</span>
      </div>

      <!-- Header -->
      <div class="card-header">
        <div class="card-icon animate-scale-in">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <h1 class="card-title animate-fade-in-up opacity-0">
          Crée ton <span class="gradient-text">compte</span>
        </h1>
        <p class="card-subtitle animate-fade-in-up opacity-0 delay-200">
          Plus qu'une étape pour rejoindre la communauté Nexora
        </p>
      </div>

      <!-- Signup Form -->
      <form id="signupForm" class="signup-form" action="{{ route('matching') }}" method="GET">
        <div class="form-group animate-fade-in-up opacity-0 delay-300">
          <label class="form-label" for="name">Prénom</label>
          <input 
            type="text" 
            id="name" 
            class="form-input" 
            placeholder="Ton prénom"
            required
            minlength="2"
            autocomplete="given-name"
          >
        </div>

        <div class="form-group animate-fade-in-up opacity-0 delay-400">
          <label class="form-label" for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            class="form-input" 
            placeholder="ton@email.com"
            required
            autocomplete="email"
          >
        </div>

        <div class="form-group animate-fade-in-up opacity-0 delay-500">
          <label class="form-label" for="password">Mot de passe</label>
          <input 
            type="password" 
            id="password" 
            class="form-input" 
            placeholder="Minimum 6 caractères"
            required
            minlength="6"
            autocomplete="new-password"
          >
          <div class="password-strength">
            <div class="strength-bar"></div>
            <div class="strength-bar"></div>
            <div class="strength-bar"></div>
            <div class="strength-bar"></div>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="submitBtn" class="btn btn-primary btn-block animate-fade-in-up opacity-0 delay-600">
          <span>Créer mon compte</span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </button>
      </form>

      <!-- Terms -->
      <p class="card-subtitle animate-fade-in-up opacity-0 delay-700" style="font-size: 0.75rem; margin-top: 1rem;">
        Deja un compte ? <a href="#" style="color: var(--primary); text-decoration: underline;">Connecte-toi</a> ou consulte nos <a href="#" style="color: var(--primary); text-decoration: underline;">Conditions d'utilisation</a> et notre <a href="#" style="color: var(--primary); text-decoration: underline;">Politique de confidentialité</a>.
      </p>
    </div>
  </div>

  <footer class="footer">
    <div class="footer-accent"></div>
    <div class="footer-container">
      <div class="footer-grid">
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
<script src="{{ asset('js/script.js') }}"></script>
<script src="{{ asset('js/inscription.js') }}"></script>
</body>
</html>
