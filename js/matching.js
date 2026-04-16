/* ========================================
   NEXORA - MATCHING COMPANION SCRIPT
   Logique interactive du chat guide
======================================== */

// Configuration des centres d'interet et sous-choix
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

// Elements DOM
const chatMessages = document.getElementById('chatMessages');
const interestsContainer = document.getElementById('interestsContainer');
const subChoicesContainer = document.getElementById('subChoicesContainer');
const matchResultOverlay = document.getElementById('matchResultOverlay');

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
  // Nombre aleatoire de matchs
  const matchCount = 8 + Math.floor(Math.random() * 15);
  
  document.getElementById('matchSubtitle').textContent = `passionné(e)s de ${choice.label}`;
  document.getElementById('matchCount').textContent = matchCount;

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
  
  initChat();
}

// Voir les profils (placeholder)
function viewProfiles() {
  alert('Redirection vers les profils... (fonctionnalité à implémenter)');
  closeMatchResult();
}
