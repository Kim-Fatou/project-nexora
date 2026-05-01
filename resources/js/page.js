// ========== NEXORA ONBOARDING - MAIN SCRIPT ==========

// State Management
const state = {
  selectedInterests: [],
  currentStep: 1,
  totalSteps: 3
};

// ========== UTILITIES ==========

// Create ripple effect on click
function createRipple(event, element) {
  const circle = document.createElement('span');
  const diameter = Math.max(element.clientWidth, element.clientHeight);
  const radius = diameter / 2;

  const rect = element.getBoundingClientRect();
  circle.style.width = circle.style.height = `${diameter}px`;
  circle.style.left = `${event.clientX - rect.left - radius}px`;
  circle.style.top = `${event.clientY - rect.top - radius}px`;
  circle.classList.add('ripple');

  const existingRipple = element.querySelector('.ripple');
  if (existingRipple) existingRipple.remove();

  element.appendChild(circle);
}

// Smooth page transition
function navigateTo(url) {
  document.body.style.opacity = '0';
  document.body.style.transform = 'translateY(20px)';
  document.body.style.transition = 'all 0.4s ease-out';
  
  setTimeout(() => {
    window.location.href = url;
  }, 400);
}

// Initialize page with fade-in
function initPageTransition() {
  document.body.style.opacity = '0';
  document.body.style.transform = 'translateY(20px)';
  
  requestAnimationFrame(() => {
    document.body.style.transition = 'all 0.6s ease-out';
    document.body.style.opacity = '1';
    document.body.style.transform = 'translateY(0)';
  });
}

// ========== INTERESTS PAGE ==========

function initInterestsPage() {
  const pills = document.querySelectorAll('.interest-pill');
  const counter = document.querySelector('.counter-badge');
  const counterContainer = document.querySelector('.selection-counter');
  const continueBtn = document.getElementById('continueBtn');
  
  // Load saved interests from sessionStorage
  const saved = sessionStorage.getItem('nexora_interests');
  if (saved) {
    state.selectedInterests = JSON.parse(saved);
    updateInterestUI();
  }
  
  pills.forEach((pill, index) => {
    // Staggered animation
    pill.style.animationDelay = `${index * 50}ms`;
    
    pill.addEventListener('click', function(e) {
      createRipple(e, this);
      
      const interest = this.dataset.interest;
      
      if (this.classList.contains('selected')) {
        this.classList.remove('selected');
        state.selectedInterests = state.selectedInterests.filter(i => i !== interest);
      } else {
        this.classList.add('selected');
        state.selectedInterests.push(interest);
        
        // Add micro-interaction
        this.style.transform = 'scale(1.1)';
        setTimeout(() => {
          this.style.transform = '';
        }, 150);
      }
      
      updateInterestUI();
      sessionStorage.setItem('nexora_interests', JSON.stringify(state.selectedInterests));
    });
  });
  
  function updateInterestUI() {
    const count = state.selectedInterests.length;
    
    // Update counter
    if (counter) {
      counter.textContent = count;
      counter.classList.add('bounce');
      setTimeout(() => counter.classList.remove('bounce'), 500);
    }
    
    // Update counter container state
    if (counterContainer) {
      counterContainer.classList.toggle('active', count > 0);
    }
    
    // Update button state
    if (continueBtn) {
      continueBtn.disabled = count < 3;
      
      if (count >= 3) {
        continueBtn.classList.add('pulse-glow');
      } else {
        continueBtn.classList.remove('pulse-glow');
      }
    }
    
    // Update selected pills UI
    pills.forEach(pill => {
      if (state.selectedInterests.includes(pill.dataset.interest)) {
        pill.classList.add('selected');
      }
    });
  }
  
  // Continue button
  if (continueBtn) {
    continueBtn.addEventListener('click', () => {
      if (state.selectedInterests.length >= 3) {
        navigateTo('groups.html');
      }
    });
  }
}

// ========== GROUPS PAGE ==========

function initGroupsPage() {
  const continueBtn = document.getElementById('continueBtn');
  const featureItems = document.querySelectorAll('.feature-item');
  
  // Staggered animation for feature items
  featureItems.forEach((item, index) => {
    item.style.opacity = '0';
    item.style.animationDelay = `${300 + index * 150}ms`;
    item.classList.add('animate-fade-in-up');
  });
  
  if (continueBtn) {
    continueBtn.addEventListener('click', () => {
      navigateTo('signup.html');
    });
  }
}

// ========== SIGNUP PAGE ==========

function initSignupPage() {
  const form = document.getElementById('signupForm');
  const nameInput = document.getElementById('name');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const strengthBars = document.querySelectorAll('.strength-bar');
  const submitBtn = document.getElementById('submitBtn');
  
  // Password strength checker
  if (passwordInput) {
    passwordInput.addEventListener('input', function() {
      const password = this.value;
      const strength = calculatePasswordStrength(password);
      updateStrengthBars(strength);
    });
  }
  
  function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return Math.min(strength, 4);
  }
  
  function updateStrengthBars(strength) {
    strengthBars.forEach((bar, index) => {
      bar.classList.remove('weak', 'medium', 'strong');
      if (index < strength) {
        if (strength <= 2) bar.classList.add('weak');
        else if (strength === 3) bar.classList.add('medium');
        else bar.classList.add('strong');
      }
    });
  }
  
  // Form validation
  function validateForm() {
    const name = nameInput?.value.trim();
    const email = emailInput?.value.trim();
    const password = passwordInput?.value;
    
    const isValid = name?.length >= 2 && 
                    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && 
                    password?.length >= 6;
    
    if (submitBtn) {
      submitBtn.disabled = !isValid;
    }
    
    return isValid;
  }
  
  // Input listeners
  [nameInput, emailInput, passwordInput].forEach(input => {
    if (input) {
      input.addEventListener('input', validateForm);
      
      // Focus animation
      input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
      });
      
      input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
      });
    }
  });
  
  // Form submission
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (!validateForm()) return;
      
      // Save user data
      const userData = {
        name: nameInput.value.trim(),
        email: emailInput.value.trim(),
        interests: JSON.parse(sessionStorage.getItem('nexora_interests') || '[]')
      };
      
      sessionStorage.setItem('nexora_user', JSON.stringify(userData));
      
      // Show loading state
      submitBtn.innerHTML = `
        <svg class="animate-spin" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
          <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
        </svg>
        <span>Creating account...</span>
      `;
      submitBtn.disabled = true;
      
      // Simulate API call
      setTimeout(() => {
        showSuccess();
      }, 1500);
    });
  }
}

// ========== SUCCESS STATE ==========

function showSuccess() {
  const card = document.querySelector('.onboarding-card');
  const userData = JSON.parse(sessionStorage.getItem('nexora_user') || '{}');
  
  // Create confetti
  createConfetti();
  
  // Update card content
  card.innerHTML = `
    <div class="success-container page-transition">
      <div class="success-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
      </div>
      <h1 class="success-title">Welcome, ${userData.name || 'Friend'}!</h1>
      <p class="success-text">Your Nexora account has been created successfully. Get ready to connect with amazing people!</p>
      <button class="btn btn-primary btn-block" onclick="navigateTo('index.html')">
        <span>Start Exploring</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
      </button>
    </div>
  `;
}

function createConfetti() {
  const container = document.createElement('div');
  container.className = 'confetti-container';
  document.body.appendChild(container);
  
  const colors = ['#561C24', '#6D2932', '#C7B7A3', '#E8D8C4', '#22c55e'];
  
  for (let i = 0; i < 50; i++) {
    const confetti = document.createElement('div');
    confetti.className = 'confetti';
    confetti.style.left = `${Math.random() * 100}%`;
    confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
    confetti.style.animationDelay = `${Math.random() * 2}s`;
    confetti.style.animationDuration = `${2 + Math.random() * 2}s`;
    
    if (Math.random() > 0.5) {
      confetti.style.borderRadius = '50%';
    }
    
    container.appendChild(confetti);
  }
  
  // Clean up
  setTimeout(() => container.remove(), 5000);
}

// ========== BACK BUTTON ==========

function initBackButton() {
  const backBtn = document.querySelector('.btn-back');
  if (backBtn) {
    backBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const target = backBtn.dataset.target || document.referrer;
      if (target) {
        navigateTo(target);
      } else {
        window.history.back();
      }
    });
  }
}

// ========== INITIALIZATION ==========

document.addEventListener('DOMContentLoaded', () => {
  initPageTransition();
  initBackButton();
  
  // Detect current page and initialize
  const path = window.location.pathname;
  
  if (path.includes('interests') || path.endsWith('index.html') || path.endsWith('/')) {
    initInterestsPage();
  } else if (path.includes('groups')) {
    initGroupsPage();
  } else if (path.includes('signup')) {
    initSignupPage();
  }
});

// Keyboard navigation
document.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    const activeBtn = document.querySelector('.btn-primary:not(:disabled)');
    if (activeBtn && document.activeElement.tagName !== 'INPUT') {
      activeBtn.click();
    }
  }
});
