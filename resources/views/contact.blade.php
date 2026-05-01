<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nexora - Contact</title>
  <meta name="description" content="Contacte l'équipe Nexora. Une question, une suggestion ou un problème ? On te répond rapidement." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  
  <link rel="stylesheet" href="{{ asset('CSS/splash.css') }}" />
  <link rel="stylesheet" href="{{ asset('CSS/onboarding.css') }}" />
  <style>
    .contact-card { max-width: 900px; }
    .contact-grid {
      display: grid;
      gap: 1.25rem;
      margin-top: 1rem;
    }
    @media (min-width: 900px) {
      .contact-grid { grid-template-columns: 1.15fr 0.85fr; align-items: start; }
    }
    .contact-aside {
      background: rgba(86, 28, 36, 0.03);
      border: 1px solid rgba(86, 28, 36, 0.08);
      border-radius: var(--radius);
      padding: 1rem;
    }
    .contact-item { display: flex; gap: 0.75rem; align-items: flex-start; padding: 0.75rem 0; }
    .contact-item + .contact-item { border-top: 1px solid rgba(86, 28, 36, 0.08); }
    .contact-badge {
      width: 38px;
      height: 38px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: var(--white);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .contact-item h3 { font-size: 0.95rem; font-weight: 700; margin-bottom: 0.15rem; }
    .contact-item p { font-size: 0.875rem; opacity: 0.75; }
    .helper-text { font-size: 0.8rem; opacity: 0.65; margin-top: -0.5rem; }
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
    <div class="onboarding-card contact-card page-transition">
      <div class="card-header">
        <div class="card-icon animate-scale-in">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
          </svg>
        </div>
        <h1 class="card-title animate-fade-in-up opacity-0">
          <span class="gradient-text">Contact</span>
        </h1>
        <p class="card-subtitle animate-fade-in-up opacity-0 delay-200" style="max-width: 560px;">
          Une question, un bug, une idée ? Envoie-nous un message et on te répond au plus vite.
        </p>
      </div>

      <div class="contact-grid">
        <form class="signup-form" id="contactForm">
          <div class="form-group animate-fade-in-up opacity-0 delay-300">
            <label class="form-label" for="fullname">Nom</label>
            <input class="form-input" type="text" id="fullname" name="fullname" placeholder="Ton nom" required minlength="2" autocomplete="name" />
          </div>

          <div class="form-group animate-fade-in-up opacity-0 delay-400">
            <label class="form-label" for="email">Email</label>
            <input class="form-input" type="email" id="email" name="email" placeholder="ton@email.com" required autocomplete="email" />
          </div>

          <div class="form-group animate-fade-in-up opacity-0 delay-500">
            <label class="form-label" for="subject">Sujet</label>
            <input class="form-input" type="text" id="subject" name="subject" placeholder="Ex: Problème de connexion" required minlength="3" />
          </div>

          <div class="form-group animate-fade-in-up opacity-0 delay-600">
            <label class="form-label" for="message">Message</label>
            <textarea class="form-input" id="message" name="message" placeholder="Décris ta demande..." required rows="6" style="resize: vertical; min-height: 140px;"></textarea>
          </div>
          <p class="helper-text animate-fade-in-up opacity-0 delay-600">Astuce: plus tu donnes de détails, plus on peut t’aider rapidement.</p>

          <button type="submit" class="btn btn-primary btn-block animate-fade-in-up opacity-0 delay-700">
            <span>Envoyer</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14M12 5l7 7-7 7"></path>
            </svg>
          </button>
        </form>

        <aside class="contact-aside animate-fade-in-up opacity-0 delay-500" aria-label="Informations de contact">
          <div class="contact-item">
            <div class="contact-badge" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
              </svg>
            </div>
            <div>
              <h3>Support</h3>
              <p>Réponse disponible 24h/24</p>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-badge" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16v16H4z"></path>
                <path d="m22 6-10 7L2 6"></path>
              </svg>
            </div>
            <div>
              <h3>Email</h3>
              <p><a href="mailto:contact@nexora.app" style="color: var(--primary); text-decoration: underline;">contact@nexora.app</a></p>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-badge" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10a7 7 0 0 1-14 0"></path>
                <path d="M12 17v4"></path>
                <path d="M8 21h8"></path>
                <path d="M12 3a7 7 0 0 0-7 7v0"></path>
                <path d="M19 10a7 7 0 0 0-7-7"></path>
              </svg>
            </div>
            <div>
              <h3>Réseaux</h3>
              <p>Retrouve-nous aussi sur nos réseaux ici.</p>
            </div>
          </div>
        </aside>
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

  <script src="script.js"></script>
  <script>
    // Fallback: empêcher l'envoi réel sans backend
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
      contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Message prêt à être envoyé. (Aucun backend n’est branché pour le moment)');
        contactForm.reset();
      });
    }
  </script>
</body>
</html>
