<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nexora - Matching Compagnon Virtuel</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/splash.css">
  <link rel="stylesheet" href="/css/matching.css">
</head>
<body>
  <header class="header" id="header">
    <div class="header-container">
      <a href="{{ route('ecran-demarrage') }}" class="logo">
        <div class="logo-icon">
          <img src="/Images/lg-removebg-preview.png" alt="Nexora">
        </div>
        <img src="" alt=""><span class="logo-text">Nexora</span>
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

  <!-- Particules animees -->
  <div class="particles-container"></div>

  <!-- Formes morphing en arriere-plan -->
  <div class="morph-shape morph-1"></div>
  <div class="morph-shape morph-2"></div>
  <div class="morph-shape morph-3"></div>

  <!-- Container principal -->
  <div class="main-container">
    
    <!-- Section Compagnon -->
    <section class="companion-section">
      <div class="companion-avatar-wrapper">
        <div class="companion-glow"></div>
        <div class="orbit-ring orbit-ring-1">
          <div class="orbit-dot"></div>
        </div>
        <div class="orbit-ring orbit-ring-2">
          <div class="orbit-dot"></div>
        </div>
        <img src="/Images/cpv.jpeg" alt="Compagnon Nexora" class="companion-avatar">
      </div>
      <div class="companion-status">
        <span class="status-dot"></span>
        <span>Compagnon actif</span>
      </div>
    </section>

    <!-- Zone de chat -->
    <div class="chat-zone">
      <div class="chat-messages" id="chatMessages">
        <!-- Les messages seront ajoutes dynamiquement -->
      </div>

      <!-- Boutons centres d'interet -->
      <div class="interests-container" id="interestsContainer" style="display: none;">
        <!-- Generes par JS -->
      </div>

      <!-- Sous-choix -->
      <div class="sub-choices-container" id="subChoicesContainer">
        <!-- Generes par JS -->
      </div>
    </div>

  </div>

  <footer class="footer">
    <div class="footer-accent"></div>
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="#" class="logo footer-logo">
            <div class="logo-icon">
              <img src="/Images/lg-removebg-preview.png" alt="Nexora">
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

  <!-- Overlay resultat matching -->
  <div class="match-result-overlay" id="matchResultOverlay">
    <div class="match-result-card">
      <button class="close-card-btn" onclick="closeMatchResult()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </button>

      <div class="match-icon">🎉</div>
      <h2 class="match-title">Match trouvé !</h2>
      <p class="match-subtitle" id="matchSubtitle">passionné(e)s de Football</p>
      <div class="match-count" id="matchCount">12</div>
      <p class="match-description">personnes partagent cette passion près de chez toi</p>

      <!-- Apercu des profils -->
      <div class="match-profiles-preview">
        <div class="profile-preview"><img src="/Images/cpv.jpeg" alt="Profil 1"></div>
        <div class="profile-preview"><img src="/Images/cpv.jpeg" alt="Profil 2"></div>
        <div class="profile-preview"><img src="/Images/cpv.jpeg" alt="Profil 3"></div>
        <div class="profile-preview more" id="matchMoreProfiles">+9</div>
      </div>

      <button class="view-profiles-btn" onclick="viewProfiles()">
        Voir les profils
      </button>
    </div>
  </div>

  <script src="{{ asset('js/matching.js') }}"></script>
  <script src="{{ asset('js/script.js') }}"></script>
<script>
const interestsData = {
  sport: {
    icon: '⚽',
    label: 'Sport',
    subChoices: [
      { id: 'football', label: 'Football', icon: '⚽' },
      { id: 'fitness', label: 'Fitness', icon: '💪' },
      { id: 'basketball', label: 'Basketball', icon: '🏀' }
    ]
  },
  technologie: {
    icon: '💻',
    label: 'Technologie',
    subChoices: [
      { id: 'programmation', label: 'Programmation', icon: '👨‍💻' },
      { id: 'ia', label: 'IA', icon: '🤖' },
      { id: 'startup', label: 'Startup', icon: '🚀' }
    ]
  },
  musique: {
    icon: '🎵',
    label: 'Musique',
    subChoices: [
      { id: 'chanter', label: 'Chanter', icon: '🎤' },
      { id: 'produire', label: 'Produire', icon: '🎹' },
      { id: 'ecouter', label: 'Écouter', icon: '🎧' }
    ]
  },
  mode: {
    icon: '👗',
    label: 'Mode',
    subChoices: [
      { id: 'stylisme', label: 'Stylisme', icon: '✂️' },
      { id: 'shopping', label: 'Shopping', icon: '🛍️' },
      { id: 'creation', label: 'Création', icon: '🎨' }
    ]
  },
  danse: {
    icon: '💃',
    label: 'Danse',
    subChoices: [
      { id: 'hiphop', label: 'Hip-hop', icon: '🕺' },
      { id: 'afro', label: 'Afro', icon: '🌍' },
      { id: 'moderne', label: 'Moderne', icon: '✨' }
    ]
  }
};

// Etat de l'application
let currentStep = 1;
let selectedInterest = null;
let selectedSubChoice = null;
const companionAvatarSrc = '/Images/cpv.jpeg';
let lastMatchCount = null;

// Elements DOM
const chatMessages = document.getElementById('chatMessages');
const interestsContainer = document.getElementById('interestsContainer');
const subChoicesContainer = document.getElementById('subChoicesContainer');
const matchResultOverlay = document.getElementById('matchResultOverlay');

const MATCH_COUNTS_BY_SUBCHOICE = {
  // Musique
  chanter: 20,
  produire: 18,
  ecouter: 20,
  // Sport
  football: 16,
  fitness: 14,
  basketball: 12,
  // Technologie
  programmation: 17,
  ia: 15,
  startup: 13,
  // Mode
  stylisme: 11,
  shopping: 19,
  creation: 10,
  // Danse
  hiphop: 18,
  afro: 16,
  moderne: 12
};

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
  createParticles();
  initChat();
});

// Creation des particules de fond
function createParticles() {
  const container = document.querySelector('.particles-container');
  const particleCount = 20;

  for (let i = 0; i < particleCount; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.animationDelay = Math.random() * 8 + 's';
    particle.style.animationDuration = (6 + Math.random() * 4) + 's';
    container.appendChild(particle);
  }
}

// Initialisation du chat
function initChat() {
  setTimeout(() => {
    addCompanionMessage("Bonjour ! 👋 Je suis ton compagnon Nexora.");
    
    setTimeout(() => {
      addCompanionMessage("Quel centre d'intérêt recherches-tu ?");
      
      setTimeout(() => {
        showInterestButtons();
      }, 500);
    }, 1000);
  }, 800);
}

// Ajouter un message du compagnon
function addCompanionMessage(text) {
  const messageDiv = document.createElement('div');
  messageDiv.className = 'message message-companion';
  messageDiv.innerHTML = `
    <img src="${companionAvatarSrc}" alt="Compagnon" class="message-avatar">
    <div class="message-bubble">${text}</div>
  `;
  chatMessages.appendChild(messageDiv);
  scrollToBottom();
}

// Ajouter un message utilisateur
function addUserMessage(text) {
  const messageDiv = document.createElement('div');
  messageDiv.className = 'message message-user';
  messageDiv.innerHTML = `
    <div class="message-bubble">${text}</div>
  `;
  chatMessages.appendChild(messageDiv);
  scrollToBottom();
}

// Afficher l'indicateur de frappe
function showTypingIndicator() {
  const typingDiv = document.createElement('div');
  typingDiv.className = 'message message-companion';
  typingDiv.id = 'typingIndicator';
  typingDiv.innerHTML = `
    <img src="${companionAvatarSrc}" alt="Compagnon" class="message-avatar">
    <div class="message-bubble typing-indicator">
      <div class="typing-dot"></div>
      <div class="typing-dot"></div>
      <div class="typing-dot"></div>
    </div>
  `;
  chatMessages.appendChild(typingDiv);
  scrollToBottom();
}

// Masquer l'indicateur de frappe
function hideTypingIndicator() {
  const typing = document.getElementById('typingIndicator');
  if (typing) typing.remove();
}

// Scroll automatique vers le bas
function scrollToBottom() {
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Afficher les boutons des centres d'interet
function showInterestButtons() {
  interestsContainer.innerHTML = '';
  interestsContainer.style.display = 'flex';
  interestsContainer.style.opacity = '0';
  interestsContainer.style.animation = 'fadeInScale 0.5s ease forwards';

  Object.entries(interestsData).forEach(([key, data], index) => {
    const btn = document.createElement('button');
    btn.className = 'interest-btn';
    btn.dataset.interest = key;
    btn.innerHTML = `
      <span class="interest-icon">${data.icon}</span>
      <span>${data.label}</span>
    `;
    btn.style.animationDelay = `${index * 0.1}s`;
    btn.addEventListener('click', (e) => handleInterestClick(e, key));
    interestsContainer.appendChild(btn);
  });
}

// Gestion du clic sur un centre d'interet
function handleInterestClick(e, interestKey) {
  // Effet ripple
  createRipple(e);

  // Marquer comme selectionne
  document.querySelectorAll('.interest-btn').forEach(btn => btn.classList.remove('selected'));
  e.currentTarget.classList.add('selected');

  selectedInterest = interestKey;
  const interest = interestsData[interestKey];

  // Message utilisateur
  addUserMessage(`${interest.icon} ${interest.label}`);

  // Masquer les boutons principaux
  setTimeout(() => {
    interestsContainer.style.display = 'none';

    // Afficher typing indicator
    showTypingIndicator();

    setTimeout(() => {
      hideTypingIndicator();
      addCompanionMessage(`Super choix ! ${interest.icon} Précise un peu plus...`);
      
      setTimeout(() => {
        showSubChoices(interestKey);
      }, 500);
    }, 1200);
  }, 300);
}

// Creer l'effet ripple
function createRipple(e) {
  const btn = e.currentTarget;
  const ripple = document.createElement('span');
  ripple.className = 'ripple';
  
  const rect = btn.getBoundingClientRect();
  const size = Math.max(rect.width, rect.height);
  
  ripple.style.width = ripple.style.height = size + 'px';
  ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
  ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
  
  btn.appendChild(ripple);
  
  setTimeout(() => ripple.remove(), 600);
}

// Afficher les sous-choix
function showSubChoices(interestKey) {
  const interest = interestsData[interestKey];
  
  subChoicesContainer.innerHTML = '';
  subChoicesContainer.classList.add('active');

  interest.subChoices.forEach((choice, index) => {
    const btn = document.createElement('button');
    btn.className = 'sub-choice-btn';
    btn.dataset.subchoice = choice.id;
    btn.innerHTML = `${choice.icon} ${choice.label}`;
    btn.style.animationDelay = `${index * 0.1}s`;
    btn.addEventListener('click', (e) => handleSubChoiceClick(e, choice));
    subChoicesContainer.appendChild(btn);
  });
}

// Gestion du clic sur un sous-choix
function handleSubChoiceClick(e, choice) {
  // Marquer comme selectionne
  document.querySelectorAll('.sub-choice-btn').forEach(btn => btn.classList.remove('selected'));
  e.currentTarget.classList.add('selected');

  selectedSubChoice = choice;

  // Message utilisateur
  addUserMessage(`${choice.icon} ${choice.label}`);

  // Masquer les sous-choix
  setTimeout(() => {
    subChoicesContainer.classList.remove('active');

    // Typing indicator
    showTypingIndicator();

    setTimeout(() => {
      hideTypingIndicator();
      
      // Effet d'illumination
      triggerIllumination();
      
      // Afficher le resultat
      setTimeout(() => {
        showMatchResult(choice);
      }, 800);
    }, 1500);
  }, 300);
}

// Declencher l'effet d'illumination
function triggerIllumination() {
  const illumination = document.createElement('div');
  illumination.className = 'illumination-overlay';
  document.body.appendChild(illumination);

  // Sparkles
  createSparkles();

  setTimeout(() => {
    illumination.remove();
  }, 1500);
}

// Creer des etincelles
function createSparkles() {
  const count = 15;
  
  for (let i = 0; i < count; i++) {
    const sparkle = document.createElement('div');
    sparkle.className = 'sparkle';
    sparkle.style.left = (30 + Math.random() * 40) + '%';
    sparkle.style.top = (30 + Math.random() * 40) + '%';
    sparkle.style.animationDelay = Math.random() * 0.5 + 's';
    sparkle.style.background = ['#561C24', '#6D2932', '#C7B7A3', '#FFFFFF'][Math.floor(Math.random() * 4)];
    document.body.appendChild(sparkle);

    setTimeout(() => sparkle.remove(), 1500);
  }
}

// Afficher la carte de resultat
function showMatchResult(choice) {
  // Nombre de matchs selon la condition (fallback: aleatoire)
  const matchCount = MATCH_COUNTS_BY_SUBCHOICE[choice.id] ?? (8 + Math.floor(Math.random() * 15));
  lastMatchCount = matchCount;
  
  document.getElementById('matchSubtitle').textContent = `passionné(e)s de ${choice.label}`;
  document.getElementById('matchCount').textContent = matchCount;

  const moreEl = document.getElementById('matchMoreProfiles');
  if (moreEl) {
    moreEl.textContent = `+${Math.max(0, matchCount - 3)}`;
  }

  matchResultOverlay.classList.add('active');

  // Confettis
  setTimeout(() => {
    createConfetti();
  }, 400);
}

// Creer les confettis
function createConfetti() {
  const colors = ['#561C24', '#6D2932', '#C7B7A3', '#E8D8C4', '#FFFFFF'];
  const count = 50;

  for (let i = 0; i < count; i++) {
    const confetti = document.createElement('div');
    confetti.className = 'confetti';
    confetti.style.left = Math.random() * 100 + '%';
    confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
    confetti.style.animationDelay = Math.random() * 0.5 + 's';
    confetti.style.animationDuration = (2 + Math.random() * 2) + 's';
    
    // Forme aleatoire
    if (Math.random() > 0.5) {
      confetti.style.borderRadius = '50%';
    } else {
      confetti.style.width = '8px';
      confetti.style.height = '12px';
    }
    
    document.body.appendChild(confetti);

    setTimeout(() => confetti.remove(), 4000);
  }
}

// Fermer la carte de resultat
function closeMatchResult() {
  matchResultOverlay.classList.remove('active');
  
  // Reset et recommencer
  setTimeout(() => {
    resetChat();
  }, 300);
}

// Reinitialiser le chat
function resetChat() {
  chatMessages.innerHTML = '';
  selectedInterest = null;
  selectedSubChoice = null;
  currentStep = 1;
  lastMatchCount = null;
  
  initChat();
}

// Voir les profils (placeholder)
function viewProfiles() {
  const interestKey = selectedInterest ?? '';
  const interestLabel = interestsData?.[interestKey]?.label ?? '';
  const subChoiceId = selectedSubChoice?.id ?? '';
  const subChoiceLabel = selectedSubChoice?.label ?? '';
  const count = lastMatchCount ?? 20;

  const qs = new URLSearchParams({
    interest: interestKey,
    interestLabel,
    sub: subChoiceId,
    subLabel: subChoiceLabel,
    count: String(count)
  });

  window.location.href = `/match?${qs.toString()}`;
}
</script>
</body>
</html>
