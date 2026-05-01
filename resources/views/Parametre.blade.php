<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora — Paramètres</title>
    <link rel="stylesheet" href="{{ asset('css/parametre.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Modal de connexion -->
    <div id="loginModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <div class="logo-container">
                    <div class="logo-icon">N</div>
                    <span class="logo-text">Nexora</span>
                </div>
                <h2>Bienvenue sur Nexora</h2>
                <p>Entrez vos informations pour personnaliser votre espace</p>
            </div>
            <form id="loginForm" class="login-form">
                <div class="form-group">
                    <label for="firstName">Prénom</label>
                    <input type="text" id="firstName" placeholder="Votre prénom" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Nom</label>
                    <input type="text" id="lastName" placeholder="Votre nom" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="votre@email.com" required>
                </div>
                <div class="form-group">
                    <label for="bio">Bio (optionnel)</label>
                    <textarea id="bio" placeholder="Parlez-nous de vous..." rows="3"></textarea>
                </div>
                <button type="submit" class="btn-primary">
                    <span>Accéder aux paramètres</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Page Paramètres -->
    <div id="settingsPage" class="settings-page hidden">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-icon pulse">N</div>
                    <span class="logo-text">Nexora</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="#" class="nav-item" data-section="profile">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Profil</span>
                </a>
                <a href="#" class="nav-item" data-section="security">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Sécurité</span>
                </a>
                <a href="#" class="nav-item" data-section="notifications">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span>Notifications</span>
                </a>
                <a href="#" class="nav-item" data-section="appearance">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                    </svg>
                    <span>Apparence</span>
                </a>
                <a href="#" class="nav-item" data-section="privacy">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>Confidentialité</span>
                </a>
                <a href="#" class="nav-item" data-section="data">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <ellipse cx="12" cy="5" rx="9" ry="3"/>
                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                    </svg>
                    <span>Données</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <button id="logoutBtn" class="logout-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span>Déconnexion</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <div class="header-left">
                    <button id="menuToggle" class="menu-toggle">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>
                    <h1 class="page-title">Paramètres</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <div class="user-avatar" id="headerAvatar"></div>
                        <span class="user-name" id="headerUserName"></span>
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- Section Profil -->
                <section id="profile" class="settings-section active">
                    <div class="section-header">
                        <h2>Informations du profil</h2>
                        <p>Gérez vos informations personnelles</p>
                    </div>

                    <div class="profile-card animate-in">
                        <div class="profile-header">
                            <div class="avatar-large" id="profileAvatar"></div>
                            <div class="profile-info">
                                <h3 id="profileFullName"></h3>
                                <p id="profileEmail"></p>
                                <span class="badge">Membre Premium</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-card animate-in" style="animation-delay: 0.1s;">
                        <h3>Modifier le profil</h3>
                        <form id="profileForm" class="settings-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="editFirstName">Prénom</label>
                                    <input type="text" id="editFirstName">
                                </div>
                                <div class="form-group">
                                    <label for="editLastName">Nom</label>
                                    <input type="text" id="editLastName">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="editEmail">Email</label>
                                <input type="email" id="editEmail">
                            </div>
                            <div class="form-group">
                                <label for="editBio">Bio</label>
                                <textarea id="editBio" rows="4"></textarea>
                                <span class="char-count"><span id="bioCount">0</span>/200</span>
                            </div>
                            <div class="form-group">
                                <label for="editPhone">Téléphone</label>
                                <input type="tel" id="editPhone" placeholder="+33 6 00 00 00 00">
                            </div>
                            <div class="form-group">
                                <label for="editLocation">Localisation</label>
                                <input type="text" id="editLocation" placeholder="Paris, France">
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-secondary">Annuler</button>
                                <button type="submit" class="btn-primary">
                                    <span>Sauvegarder</span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Section Sécurité -->
                <section id="security" class="settings-section">
                    <div class="section-header">
                        <h2>Sécurité du compte</h2>
                        <p>Protégez votre compte avec ces paramètres</p>
                    </div>

                    <div class="security-grid">
                        <div class="security-card animate-in">
                            <div class="security-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <div class="security-info">
                                <h4>Mot de passe</h4>
                                <p>Dernière modification il y a 30 jours</p>
                            </div>
                            <button class="btn-outline">Modifier</button>
                        </div>

                        <div class="security-card animate-in" style="animation-delay: 0.1s;">
                            <div class="security-icon success">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <polyline points="9 12 12 15 16 10"/>
                                </svg>
                            </div>
                            <div class="security-info">
                                <h4>Authentification 2FA</h4>
                                <p>Activée via application</p>
                            </div>
                            <span class="status-badge active">Actif</span>
                        </div>

                        <div class="security-card animate-in" style="animation-delay: 0.2s;">
                            <div class="security-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                    <line x1="8" y1="21" x2="16" y2="21"/>
                                    <line x1="12" y1="17" x2="12" y2="21"/>
                                </svg>
                            </div>
                            <div class="security-info">
                                <h4>Sessions actives</h4>
                                <p>3 appareils connectés</p>
                            </div>
                            <button class="btn-outline">Gérer</button>
                        </div>
                    </div>

                    <div class="form-card animate-in" style="animation-delay: 0.3s;">
                        <h3>Changer le mot de passe</h3>
                        <form id="passwordForm" class="settings-form">
                            <div class="form-group">
                                <label for="currentPassword">Mot de passe actuel</label>
                                <div class="password-input">
                                    <input type="password" id="currentPassword">
                                    <button type="button" class="toggle-password">
                                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="newPassword">Nouveau mot de passe</label>
                                <div class="password-input">
                                    <input type="password" id="newPassword">
                                    <button type="button" class="toggle-password">
                                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="password-strength" id="passwordStrength">
                                    <div class="strength-bar"></div>
                                    <span class="strength-text">Force du mot de passe</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirmer le mot de passe</label>
                                <div class="password-input">
                                    <input type="password" id="confirmPassword">
                                    <button type="button" class="toggle-password">
                                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Mettre à jour</button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Section Notifications -->
                <section id="notifications" class="settings-section">
                    <div class="section-header">
                        <h2>Préférences de notification</h2>
                        <p>Choisissez comment vous souhaitez être notifié</p>
                    </div>

                    <div class="notification-groups">
                        <div class="notification-group animate-in">
                            <div class="group-header">
                                <h3>Notifications Push</h3>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="pushNotifications" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="notification-items">
                                <div class="notification-item">
                                    <div class="item-info">
                                        <h4>Messages directs</h4>
                                        <p>Recevez une notification pour chaque nouveau message</p>
                                    </div>
                                    <label class="toggle-switch small">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="notification-item">
                                    <div class="item-info">
                                        <h4>Mentions</h4>
                                        <p>Quand quelqu'un vous mentionne dans une publication</p>
                                    </div>
                                    <label class="toggle-switch small">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="notification-item">
                                    <div class="item-info">
                                        <h4>Nouveaux abonnés</h4>
                                        <p>Quand quelqu'un commence à vous suivre</p>
                                    </div>
                                    <label class="toggle-switch small">
                                        <input type="checkbox">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="notification-group animate-in" style="animation-delay: 0.1s;">
                            <div class="group-header">
                                <h3>Notifications Email</h3>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="emailNotifications" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="notification-items">
                                <div class="notification-item">
                                    <div class="item-info">
                                        <h4>Résumé hebdomadaire</h4>
                                        <p>Un récapitulatif de votre activité chaque semaine</p>
                                    </div>
                                    <label class="toggle-switch small">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="notification-item">
                                    <div class="item-info">
                                        <h4>Mises à jour produit</h4>
                                        <p>Nouvelles fonctionnalités et améliorations</p>
                                    </div>
                                    <label class="toggle-switch small">
                                        <input type="checkbox">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section Apparence -->
                <section id="appearance" class="settings-section">
                    <div class="section-header">
                        <h2>Personnalisation</h2>
                        <p>Adaptez l'apparence de Nexora à vos préférences</p>
                    </div>

                    <div class="appearance-options">
                        <div class="theme-selector animate-in">
                            <h3>Thème</h3>
                            <div class="theme-options">
                                <button class="theme-option active" data-theme="light">
                                    <div class="theme-preview light">
                                        <div class="preview-header"></div>
                                        <div class="preview-content">
                                            <div class="preview-line"></div>
                                            <div class="preview-line short"></div>
                                        </div>
                                    </div>
                                    <span>Clair</span>
                                </button>
                                <button class="theme-option" data-theme="dark">
                                    <div class="theme-preview dark">
                                        <div class="preview-header"></div>
                                        <div class="preview-content">
                                            <div class="preview-line"></div>
                                            <div class="preview-line short"></div>
                                        </div>
                                    </div>
                                    <span>Sombre</span>
                                </button>
                                <button class="theme-option" data-theme="auto">
                                    <div class="theme-preview auto">
                                        <div class="preview-header"></div>
                                        <div class="preview-content">
                                            <div class="preview-line"></div>
                                            <div class="preview-line short"></div>
                                        </div>
                                    </div>
                                    <span>Automatique</span>
                                </button>
                            </div>
                        </div>

                        <div class="color-selector animate-in" style="animation-delay: 0.1s;">
                            <h3>Couleur d'accent</h3>
                            <div class="color-options">
                                <button class="color-option active" data-color="#561C24" style="--color: #561C24;"></button>
                                <button class="color-option" data-color="#1E3A5F" style="--color: #1E3A5F;"></button>
                                <button class="color-option" data-color="#1D4E2C" style="--color: #1D4E2C;"></button>
                                <button class="color-option" data-color="#5C3D2E" style="--color: #5C3D2E;"></button>
                                <button class="color-option" data-color="#4A1942" style="--color: #4A1942;"></button>
                                <button class="color-option" data-color="#2D3436" style="--color: #2D3436;"></button>
                            </div>
                        </div>

                        <div class="font-selector animate-in" style="animation-delay: 0.2s;">
                            <h3>Taille de police</h3>
                            <div class="font-slider">
                                <span class="font-label small">A</span>
                                <input type="range" id="fontSizeSlider" min="12" max="20" value="16">
                                <span class="font-label large">A</span>
                            </div>
                            <p class="font-preview" id="fontPreview">Aperçu de la taille de texte</p>
                        </div>
                    </div>
                </section>

                <!-- Section Confidentialité -->
                <section id="privacy" class="settings-section">
                    <div class="section-header">
                        <h2>Confidentialité</h2>
                        <p>Contrôlez qui peut voir vos informations</p>
                    </div>

                    <div class="privacy-options">
                        <div class="privacy-card animate-in">
                            <div class="privacy-header">
                                <h3>Visibilité du profil</h3>
                            </div>
                            <div class="privacy-options-list">
                                <label class="radio-option">
                                    <input type="radio" name="profileVisibility" value="public" checked>
                                    <span class="radio-custom"></span>
                                    <div class="option-content">
                                        <h4>Public</h4>
                                        <p>Tout le monde peut voir votre profil</p>
                                    </div>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="profileVisibility" value="friends">
                                    <span class="radio-custom"></span>
                                    <div class="option-content">
                                        <h4>Amis uniquement</h4>
                                        <p>Seuls vos amis peuvent voir votre profil</p>
                                    </div>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="profileVisibility" value="private">
                                    <span class="radio-custom"></span>
                                    <div class="option-content">
                                        <h4>Privé</h4>
                                        <p>Personne ne peut voir votre profil</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="privacy-card animate-in" style="animation-delay: 0.1s;">
                            <div class="privacy-header">
                                <h3>Options de confidentialité</h3>
                            </div>
                            <div class="privacy-toggles">
                                <div class="privacy-toggle-item">
                                    <div class="toggle-info">
                                        <h4>Afficher le statut en ligne</h4>
                                        <p>Les autres peuvent voir quand vous êtes en ligne</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="privacy-toggle-item">
                                    <div class="toggle-info">
                                        <h4>Confirmations de lecture</h4>
                                        <p>Montrer quand vous avez lu les messages</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="privacy-toggle-item">
                                    <div class="toggle-info">
                                        <h4>Suggestions de profil</h4>
                                        <p>Apparaître dans les suggestions d'amis</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section Données -->
                <section id="data" class="settings-section">
                    <div class="section-header">
                        <h2>Gestion des données</h2>
                        <p>Téléchargez ou supprimez vos données</p>
                    </div>

                    <div class="data-options">
                        <div class="data-card animate-in">
                            <div class="data-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                            </div>
                            <div class="data-info">
                                <h3>Télécharger vos données</h3>
                                <p>Obtenez une copie de toutes vos données Nexora</p>
                            </div>
                            <button class="btn-outline">Télécharger</button>
                        </div>

                        <div class="data-card animate-in" style="animation-delay: 0.1s;">
                            <div class="data-icon warning">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18"/>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                </svg>
                            </div>
                            <div class="data-info">
                                <h3>Supprimer le compte</h3>
                                <p>Supprimer définitivement votre compte et toutes vos données</p>
                            </div>
                            <button class="btn-danger">Supprimer</button>
                        </div>

                        <div class="storage-card animate-in" style="animation-delay: 0.2s;">
                            <h3>Utilisation du stockage</h3>
                            <div class="storage-bar">
                                <div class="storage-progress" style="--progress: 45%;"></div>
                            </div>
                            <div class="storage-details">
                                <span>4.5 Go utilisés sur 10 Go</span>
                                <a href="#" class="upgrade-link">Augmenter le stockage</a>
                            </div>
                            <div class="storage-breakdown">
                                <div class="breakdown-item">
                                    <span class="breakdown-color" style="--color: #561C24;"></span>
                                    <span>Photos (2.1 Go)</span>
                                </div>
                                <div class="breakdown-item">
                                    <span class="breakdown-color" style="--color: #6D2932;"></span>
                                    <span>Vidéos (1.8 Go)</span>
                                </div>
                                <div class="breakdown-item">
                                    <span class="breakdown-color" style="--color: #C7B7A3;"></span>
                                    <span>Documents (0.6 Go)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <div class="toast-content">
            <svg class="toast-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span class="toast-message"></span>
        </div>
    </div>
    <script src="{{ asset('js/parametre.js') }}" defer></script>
</body>
</html>
