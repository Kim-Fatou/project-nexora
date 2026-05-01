/**
 * NEXORA - Paramètres
 * JavaScript pour la gestion dynamique des utilisateurs et interactions
 */

// =====================================================
// GESTION DES UTILISATEURS (localStorage)
// =====================================================

class UserManager {
    constructor() {
        this.storageKey = 'nexora_user';
        this.currentUser = null;
    }

    // Récupérer l'utilisateur connecté
    getUser() {
        const userData = localStorage.getItem(this.storageKey);
        if (userData) {
            this.currentUser = JSON.parse(userData);
            return this.currentUser;
        }
        return null;
    }

    // Sauvegarder l'utilisateur
    saveUser(userData) {
        this.currentUser = {
            ...userData,
            createdAt: userData.createdAt || new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };
        localStorage.setItem(this.storageKey, JSON.stringify(this.currentUser));
        return this.currentUser;
    }

    // Mettre à jour l'utilisateur
    updateUser(updates) {
        if (this.currentUser) {
            this.currentUser = {
                ...this.currentUser,
                ...updates,
                updatedAt: new Date().toISOString()
            };
            localStorage.setItem(this.storageKey, JSON.stringify(this.currentUser));
            return this.currentUser;
        }
        return null;
    }

    // Déconnexion
    logout() {
        localStorage.removeItem(this.storageKey);
        this.currentUser = null;
    }

    // Obtenir les initiales
    getInitials() {
        if (!this.currentUser) return 'U';
        const { firstName, lastName } = this.currentUser;
        const firstInitial = firstName ? firstName.charAt(0).toUpperCase() : '';
        const lastInitial = lastName ? lastName.charAt(0).toUpperCase() : '';
        return firstInitial + lastInitial || 'U';
    }

    // Obtenir le nom complet
    getFullName() {
        if (!this.currentUser) return 'Utilisateur';
        const { firstName, lastName } = this.currentUser;
        return `${firstName || ''} ${lastName || ''}`.trim() || 'Utilisateur';
    }
}

// Instance globale
const userManager = new UserManager();

// =====================================================
// ÉLÉMENTS DOM
// =====================================================

const elements = {
    // Modal
    loginModal: document.getElementById('loginModal'),
    loginForm: document.getElementById('loginForm'),
    
    // Settings Page
    settingsPage: document.getElementById('settingsPage'),
    
    // Sidebar
    sidebar: document.querySelector('.sidebar'),
    menuToggle: document.getElementById('menuToggle'),
    navItems: document.querySelectorAll('.nav-item'),
    logoutBtn: document.getElementById('logoutBtn'),
    
    // Header
    headerAvatar: document.getElementById('headerAvatar'),
    headerUserName: document.getElementById('headerUserName'),
    
    // Profile Section
    profileAvatar: document.getElementById('profileAvatar'),
    profileFullName: document.getElementById('profileFullName'),
    profileEmail: document.getElementById('profileEmail'),
    profileForm: document.getElementById('profileForm'),
    editFirstName: document.getElementById('editFirstName'),
    editLastName: document.getElementById('editLastName'),
    editEmail: document.getElementById('editEmail'),
    editBio: document.getElementById('editBio'),
    editPhone: document.getElementById('editPhone'),
    editLocation: document.getElementById('editLocation'),
    bioCount: document.getElementById('bioCount'),
    
    // Sections
    sections: document.querySelectorAll('.settings-section'),
    
    // Password
    passwordForm: document.getElementById('passwordForm'),
    newPassword: document.getElementById('newPassword'),
    passwordStrength: document.getElementById('passwordStrength'),
    togglePasswordBtns: document.querySelectorAll('.toggle-password'),
    
    // Appearance
    themeOptions: document.querySelectorAll('.theme-option'),
    colorOptions: document.querySelectorAll('.color-option'),
    fontSizeSlider: document.getElementById('fontSizeSlider'),
    fontPreview: document.getElementById('fontPreview'),
    
    // Toast
    toast: document.getElementById('toast'),
    toastMessage: document.querySelector('.toast-message')
};

// =====================================================
// INITIALISATION
// =====================================================

document.addEventListener('DOMContentLoaded', () => {
    initApp();
});

function initApp() {
    const user = userManager.getUser();
    
    if (user) {
        // Utilisateur connecté - afficher les paramètres
        showSettingsPage();
        updateUIWithUserData();
    } else {
        // Pas d'utilisateur - afficher le formulaire de connexion
        showLoginModal();
    }
    
    setupEventListeners();
    setupAnimations();
}

// =====================================================
// AFFICHAGE DES PAGES
// =====================================================

function showLoginModal() {
    elements.loginModal.classList.remove('hidden');
    elements.settingsPage.classList.add('hidden');
    
    // Focus sur le premier champ
    setTimeout(() => {
        document.getElementById('firstName').focus();
    }, 300);
}

function showSettingsPage() {
    elements.loginModal.classList.add('hidden');
    elements.settingsPage.classList.remove('hidden');
    
    // Animation d'entrée
    elements.settingsPage.style.animation = 'fadeIn 0.4s ease-out';
}

// =====================================================
// MISE À JOUR DE L'UI AVEC LES DONNÉES UTILISATEUR
// =====================================================

function updateUIWithUserData() {
    const user = userManager.currentUser;
    if (!user) return;
    
    const initials = userManager.getInitials();
    const fullName = userManager.getFullName();
    
    // Header
    if (elements.headerAvatar) {
        elements.headerAvatar.textContent = initials;
    }
    if (elements.headerUserName) {
        elements.headerUserName.textContent = fullName;
    }
    
    // Profile Section
    if (elements.profileAvatar) {
        elements.profileAvatar.textContent = initials;
    }
    if (elements.profileFullName) {
        elements.profileFullName.textContent = fullName;
    }
    if (elements.profileEmail) {
        elements.profileEmail.textContent = user.email || '';
    }
    
    // Form fields
    if (elements.editFirstName) {
        elements.editFirstName.value = user.firstName || '';
    }
    if (elements.editLastName) {
        elements.editLastName.value = user.lastName || '';
    }
    if (elements.editEmail) {
        elements.editEmail.value = user.email || '';
    }
    if (elements.editBio) {
        elements.editBio.value = user.bio || '';
        updateBioCount();
    }
    if (elements.editPhone) {
        elements.editPhone.value = user.phone || '';
    }
    if (elements.editLocation) {
        elements.editLocation.value = user.location || '';
    }
}

// =====================================================
// EVENT LISTENERS
// =====================================================

function setupEventListeners() {
    // Login Form
    if (elements.loginForm) {
        elements.loginForm.addEventListener('submit', handleLogin);
    }
    
    // Navigation
    elements.navItems.forEach(item => {
        item.addEventListener('click', handleNavigation);
    });
    
    // Menu Toggle (Mobile)
    if (elements.menuToggle) {
        elements.menuToggle.addEventListener('click', toggleSidebar);
    }
    
    // Logout
    if (elements.logoutBtn) {
        elements.logoutBtn.addEventListener('click', handleLogout);
    }
    
    // Profile Form
    if (elements.profileForm) {
        elements.profileForm.addEventListener('submit', handleProfileUpdate);
    }
    
    // Bio character count
    if (elements.editBio) {
        elements.editBio.addEventListener('input', updateBioCount);
    }
    
    // Password Form
    if (elements.passwordForm) {
        elements.passwordForm.addEventListener('submit', handlePasswordChange);
    }
    
    // Password visibility toggles
    elements.togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', togglePasswordVisibility);
    });
    
    // Password strength
    if (elements.newPassword) {
        elements.newPassword.addEventListener('input', updatePasswordStrength);
    }
    
    // Theme options
    elements.themeOptions.forEach(option => {
        option.addEventListener('click', handleThemeChange);
    });
    
    // Color options
    elements.colorOptions.forEach(option => {
        option.addEventListener('click', handleColorChange);
    });
    
    // Font size slider
    if (elements.fontSizeSlider) {
        elements.fontSizeSlider.addEventListener('input', handleFontSizeChange);
    }
    
    // Close sidebar on outside click (mobile)
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 1024) {
            if (!elements.sidebar.contains(e.target) && 
                !elements.menuToggle.contains(e.target) &&
                elements.sidebar.classList.contains('open')) {
                elements.sidebar.classList.remove('open');
            }
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', handleKeyboard);
}

// =====================================================
// HANDLERS
// =====================================================

function handleLogin(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const userData = {
        firstName: document.getElementById('firstName').value.trim(),
        lastName: document.getElementById('lastName').value.trim(),
        email: document.getElementById('email').value.trim(),
        bio: document.getElementById('bio').value.trim()
    };
    
    // Validation
    if (!userData.firstName || !userData.lastName || !userData.email) {
        showToast('Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(userData.email)) {
        showToast('Veuillez entrer une adresse email valide', 'error');
        return;
    }
    
    // Save user
    userManager.saveUser(userData);
    
    // Animation de transition
    elements.loginModal.style.animation = 'fadeIn 0.3s ease-out reverse';
    
    setTimeout(() => {
        showSettingsPage();
        updateUIWithUserData();
        showToast(`Bienvenue ${userData.firstName} !`);
    }, 300);
}

function handleNavigation(e) {
    e.preventDefault();
    
    const sectionId = e.currentTarget.dataset.section;
    
    // Update active nav
    elements.navItems.forEach(item => item.classList.remove('active'));
    e.currentTarget.classList.add('active');
    
    // Show section
    elements.sections.forEach(section => {
        section.classList.remove('active');
        if (section.id === sectionId) {
            section.classList.add('active');
            // Re-trigger animations
            const animatedElements = section.querySelectorAll('.animate-in');
            animatedElements.forEach((el, index) => {
                el.style.animation = 'none';
                el.offsetHeight; // Trigger reflow
                el.style.animation = `fadeInUp 0.5s ease-out forwards`;
                el.style.animationDelay = `${index * 0.1}s`;
            });
        }
    });
    
    // Close sidebar on mobile
    if (window.innerWidth <= 1024) {
        elements.sidebar.classList.remove('open');
    }
}

function toggleSidebar() {
    elements.sidebar.classList.toggle('open');
}

function handleLogout() {
    // Confirmation
    if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
        userManager.logout();
        
        // Animation
        elements.settingsPage.style.animation = 'fadeIn 0.3s ease-out reverse';
        
        setTimeout(() => {
            showLoginModal();
            // Reset form
            elements.loginForm.reset();
        }, 300);
        
        showToast('Déconnexion réussie');
    }
}

function handleProfileUpdate(e) {
    e.preventDefault();
    
    const updates = {
        firstName: elements.editFirstName.value.trim(),
        lastName: elements.editLastName.value.trim(),
        email: elements.editEmail.value.trim(),
        bio: elements.editBio.value.trim(),
        phone: elements.editPhone.value.trim(),
        location: elements.editLocation.value.trim()
    };
    
    // Validation
    if (!updates.firstName || !updates.lastName || !updates.email) {
        showToast('Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }
    
    // Update user
    userManager.updateUser(updates);
    
    // Update UI
    updateUIWithUserData();
    
    // Feedback
    showToast('Profil mis à jour avec succès !');
    
    // Animation feedback
    const btn = e.target.querySelector('.btn-primary');
    btn.style.transform = 'scale(0.95)';
    setTimeout(() => {
        btn.style.transform = 'scale(1)';
    }, 150);
}

function updateBioCount() {
    if (elements.editBio && elements.bioCount) {
        const length = elements.editBio.value.length;
        elements.bioCount.textContent = length;
        
        // Visual feedback si dépassement
        if (length > 200) {
            elements.bioCount.style.color = '#dc2626';
        } else if (length > 180) {
            elements.bioCount.style.color = '#f59e0b';
        } else {
            elements.bioCount.style.color = '';
        }
    }
}

function handlePasswordChange(e) {
    e.preventDefault();
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validation
    if (!currentPassword || !newPassword || !confirmPassword) {
        showToast('Veuillez remplir tous les champs', 'error');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showToast('Les mots de passe ne correspondent pas', 'error');
        return;
    }
    
    if (newPassword.length < 8) {
        showToast('Le mot de passe doit contenir au moins 8 caractères', 'error');
        return;
    }
    
    // Simulate password change
    showToast('Mot de passe mis à jour avec succès !');
    e.target.reset();
    
    // Reset strength indicator
    updatePasswordStrength({ target: { value: '' } });
}

function togglePasswordVisibility(e) {
    const btn = e.currentTarget;
    const input = btn.parentElement.querySelector('input');
    const icon = btn.querySelector('.eye-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
        `;
    } else {
        input.type = 'password';
        icon.innerHTML = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        `;
    }
}

function updatePasswordStrength(e) {
    const password = e.target.value;
    const strengthBar = elements.passwordStrength.querySelector('.strength-bar');
    const strengthText = elements.passwordStrength.querySelector('.strength-text');
    
    let strength = 0;
    let strengthLabel = 'Faible';
    let strengthColor = '#dc2626';
    
    if (password.length >= 8) strength += 25;
    if (password.length >= 12) strength += 15;
    if (/[A-Z]/.test(password)) strength += 20;
    if (/[a-z]/.test(password)) strength += 10;
    if (/[0-9]/.test(password)) strength += 15;
    if (/[^A-Za-z0-9]/.test(password)) strength += 15;
    
    if (strength >= 80) {
        strengthLabel = 'Très fort';
        strengthColor = '#16a34a';
    } else if (strength >= 60) {
        strengthLabel = 'Fort';
        strengthColor = '#65a30d';
    } else if (strength >= 40) {
        strengthLabel = 'Moyen';
        strengthColor = '#f59e0b';
    }
    
    strengthBar.style.setProperty('--strength', `${strength}%`);
    strengthBar.style.setProperty('--strength-color', strengthColor);
    strengthText.textContent = password ? strengthLabel : 'Force du mot de passe';
    strengthText.style.color = password ? strengthColor : '';
}

function handleThemeChange(e) {
    const btn = e.currentTarget;
    const theme = btn.dataset.theme;
    
    elements.themeOptions.forEach(opt => opt.classList.remove('active'));
    btn.classList.add('active');
    
    // Apply theme
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    } else if (theme === 'light') {
        document.documentElement.classList.remove('dark');
    } else {
        // Auto - check system preference
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
    
    showToast(`Thème ${theme === 'light' ? 'clair' : theme === 'dark' ? 'sombre' : 'automatique'} activé`);
}

function handleColorChange(e) {
    const btn = e.currentTarget;
    const color = btn.dataset.color;
    
    elements.colorOptions.forEach(opt => opt.classList.remove('active'));
    btn.classList.add('active');
    
    // Apply accent color
    document.documentElement.style.setProperty('--bordeaux', color);
    
    showToast('Couleur d\'accent mise à jour');
}

function handleFontSizeChange(e) {
    const size = e.target.value;
    
    if (elements.fontPreview) {
        elements.fontPreview.style.fontSize = `${size}px`;
    }
    
    // Apply to body (optional)
    document.body.style.fontSize = `${size}px`;
}

function handleKeyboard(e) {
    // Escape to close sidebar on mobile
    if (e.key === 'Escape' && window.innerWidth <= 1024) {
        elements.sidebar.classList.remove('open');
    }
}

// =====================================================
// TOAST NOTIFICATION
// =====================================================

function showToast(message, type = 'success') {
    const toast = elements.toast;
    const toastMessage = elements.toastMessage;
    const toastIcon = toast.querySelector('.toast-icon');
    
    toastMessage.textContent = message;
    
    // Update icon based on type
    if (type === 'error') {
        toastIcon.innerHTML = `
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
        `;
        toastIcon.style.color = '#f87171';
    } else {
        toastIcon.innerHTML = `<polyline points="20 6 9 17 4 12"/>`;
        toastIcon.style.color = '#4ade80';
    }
    
    // Show toast
    toast.classList.remove('hide');
    toast.classList.add('show');
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        toast.classList.add('hide');
    }, 3000);
}

// =====================================================
// ANIMATIONS
// =====================================================

function setupAnimations() {
    // Intersection Observer for scroll animations
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        },
        { threshold: 0.1 }
    );
    
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
    
    // Ripple effect for buttons
    document.querySelectorAll('.btn-primary, .btn-outline').forEach(btn => {
        btn.addEventListener('click', createRipple);
    });
}

function createRipple(e) {
    const btn = e.currentTarget;
    const ripple = document.createElement('span');
    const rect = btn.getBoundingClientRect();
    
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
    `;
    
    btn.style.position = 'relative';
    btn.style.overflow = 'hidden';
    btn.appendChild(ripple);
    
    setTimeout(() => ripple.remove(), 600);
}

// =====================================================
// UTILITIES
// =====================================================

// Format date
function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export for potential module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { UserManager, userManager };
}
