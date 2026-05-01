<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nexora — Appels</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/animations.css">
  <link rel="stylesheet" href="/css/chat.css">
</head>
<body>
  <div class="nx-shell" id="shell">
    <div class="nx-bg" aria-hidden="true">
      <div class="nx-shape nx-shape-1 u-bg-drift"></div>
      <div class="nx-shape nx-shape-2 u-bg-drift"></div>
      <div class="nx-shape nx-shape-3 u-bg-drift"></div>
      <div class="nx-grid-glow"></div>
    </div>

    <!-- Rail -->
    <aside class="nx-rail glass" aria-label="Navigation">
      <a class="nx-rail-logo" href="{{ route('dashboard') }}" aria-label="Dashboard Nexora">
        <span class="nx-brand-mark" aria-hidden="true">
          <img src="{{ asset('images/lg-removebg-preview.png') }}" alt="" />
        </span>
      </a>

      <nav class="nx-rail-nav">
        <a class="nx-rail-btn" href="{{ route('chat') }}" aria-label="Chats">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
          </svg>
        </a>
        <a class="nx-rail-btn is-active" href="{{ route('appel') }}" aria-label="Appels">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.15 10.8 19.86 19.86 0 0 1 .08 2.18 2 2 0 0 1 2.06 0h3a2 2 0 0 1 2 1.72c.12.86.33 1.7.62 2.5a2 2 0 0 1-.45 2.11L6.1 7.1a16 16 0 0 0 6.8 6.8l.77-1.13a2 2 0 0 1 2.11-.45c.8.29 1.64.5 2.5.62A2 2 0 0 1 22 16.92z"></path>
          </svg>
        </a>
        <a class="nx-rail-btn" href="{{ route('Parametre') }}" aria-label="Réglages">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"></path>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.33 1.82V22a2 2 0 0 1-4 0v-.08A1.65 1.65 0 0 0 8.6 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-.6-1 1.65 1.65 0 0 0-1.82-.33H2a2 2 0 0 1 0-4h.08A1.65 1.65 0 0 0 4.6 8.6a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 8.6 4.6a1.65 1.65 0 0 0 1-.6 1.65 1.65 0 0 0 .33-1.82V2a2 2 0 0 1 4 0v.08A1.65 1.65 0 0 0 15.4 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 8.6a1.65 1.65 0 0 0 .6 1 1.65 1.65 0 0 0 1.82.33H22a2 2 0 0 1 0 4h-.08A1.65 1.65 0 0 0 19.4 15z"></path>
          </svg>
        </a>
      </nav>

      <div class="nx-rail-bottom">
        <a class="nx-me" href="{{ route('dashboard') }}" aria-label="Dashboard">
          <span class="nx-initials nx-initials--me" aria-hidden="true">NX</span>
        </a>
      </div>
    </aside>

    <!-- Main -->
    <div class="nx-main">
      <header class="nx-header glass" aria-label="Barre supérieure">
        <div class="nx-header-left">
          <div class="nx-brand">
            <span class="nx-brand-logo" aria-hidden="true">
              <img src="/Images/lg-removebg-preview.png" alt="" />
            </span>
            <span class="nx-brand-name">Appels</span>
          </div>
        </div>

        <div class="nx-search">
          <svg class="nx-search-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.3-4.3"></path>
          </svg>
          <input type="search" placeholder="Rechercher un appel…" autocomplete="off" />
          <span class="nx-search-glow" aria-hidden="true"></span>
        </div>

        <div class="nx-header-right">
          <a class="nx-pill" href="{{ route('chat') }}" aria-label="Aller au chat">
            <span class="nx-pill-ico" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
              </svg>
            </span>
            <span class="nx-pill-text">Chat</span>
          </a>
          <a class="nx-me-mini" href="{{ route('Parametre') }}" aria-label="Paramètres">
            <span class="nx-initials nx-initials--me" aria-hidden="true">NX</span>
          </a>
        </div>
      </header>

      <section class="nx-grid" aria-label="Historique des appels">
        <aside class="nx-list glass" aria-label="Historique">
          <div class="nx-list-head">
            <div class="nx-list-title">
              <h2>Appels</h2>
              <span class="nx-badge">12</span>
            </div>
            <div class="nx-list-search" aria-label="Filtrer">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 4h18"></path>
                <path d="M7 12h10"></path>
                <path d="M10 20h4"></path>
              </svg>
              <input type="search" placeholder="Filtrer (manqués, entrants…)" autocomplete="off" />
            </div>
          </div>

          <div class="nx-convos" style="padding-top: 12px;">
            <div class="nx-day" style="margin: 0 0 10px;"><span>Aujourd’hui</span></div>
            <div class="nx-convo u-fade-in-up u-delay-1" role="listitem" style="cursor: default;">
              <span class="nx-initials nx-initials--peer" aria-hidden="true">A</span>
              <span class="nx-convo-body">
                <span class="nx-convo-top">
                  <span class="nx-convo-name">Amaya</span>
                  <span class="nx-time">09:20</span>
                </span>
                <span class="nx-convo-bottom">
                  <span class="nx-preview">Appel sortant • 02:14</span>
                  <span class="nx-chip nx-chip--online">OK</span>
                </span>
              </span>
            </div>

            <div class="nx-convo u-fade-in-up u-delay-2" role="listitem" style="cursor: default;">
              <span class="nx-initials nx-initials--peer" aria-hidden="true">G</span>
              <span class="nx-convo-body">
                <span class="nx-convo-top">
                  <span class="nx-convo-name">Gabin</span>
                  <span class="nx-time">08:03</span>
                </span>
                <span class="nx-convo-bottom">
                  <span class="nx-preview">Manqué</span>
                  <span class="nx-unread">!</span>
                </span>
              </span>
            </div>
          </div>

          <div class="nx-list-foot">
            <button class="nx-cta" type="button" aria-label="Démarrer un appel">
              <span>Nouvel appel</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.15 10.8 19.86 19.86 0 0 1 .08 2.18 2 2 0 0 1 2.06 0h3a2 2 0 0 1 2 1.72c.12.86.33 1.7.62 2.5a2 2 0 0 1-.45 2.11L6.1 7.1a16 16 0 0 0 6.8 6.8l.77-1.13a2 2 0 0 1 2.11-.45c.8.29 1.64.5 2.5.62A2 2 0 0 1 22 16.92z"></path>
              </svg>
            </button>
          </div>
        </aside>

        <main class="nx-chat glass" aria-label="Détails">
          <header class="nx-chat-head">
            <div class="nx-chat-peer" style="cursor: default;">
              <span class="nx-initials nx-initials--peer" aria-hidden="true">A</span>
              <span class="nx-chat-peer-meta">
                <span class="nx-chat-peer-name">Amaya</span>
                <span class="nx-chat-peer-status">
                  <span class="nx-dot" aria-hidden="true"></span>
                  Disponible
                </span>
              </span>
            </div>
            <div class="nx-chat-actions">
              <button class="nx-icon nx-icon--accent" type="button" aria-label="Appeler maintenant">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.15 10.8 19.86 19.86 0 0 1 .08 2.18 2 2 0 0 1 2.06 0h3a2 2 0 0 1 2 1.72c.12.86.33 1.7.62 2.5a2 2 0 0 1-.45 2.11L6.1 7.1a16 16 0 0 0 6.8 6.8l.77-1.13a2 2 0 0 1 2.11-.45c.8.29 1.64.5 2.5.62A2 2 0 0 1 22 16.92z"></path>
                </svg>
              </button>
            </div>
          </header>

          <section class="nx-thread" aria-label="Contenu" style="padding-bottom: 14px;">
            <div class="nx-day"><span>À venir</span></div>
            <div class="nx-msg nx-msg--peer">
              <div class="nx-bubble">
                <p>Cette page affichera les appels (audio/vidéo) et l’historique dès que tu branches la base de données.</p>
                <div class="nx-meta"><time>—</time></div>
              </div>
            </div>
          </section>
        </main>
      </section>
    </div>
  </div>
</body>
</html>
