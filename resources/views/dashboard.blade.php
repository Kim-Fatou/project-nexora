<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nexora — Dashboard</title>
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
        <a class="nx-rail-btn is-active" href="{{ route('dashboard') }}" aria-label="Dashboard">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 13h8V3H3v10z"></path>
            <path d="M13 21h8v-6h-8v6z"></path>
            <path d="M13 3h8v8h-8V3z"></path>
            <path d="M3 21h8v-4H3v4z"></path>
          </svg>
        </a>
        <a class="nx-rail-btn" href="{{ route('appel') }}" aria-label="Appels">
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

    <div class="nx-main">
      <header class="nx-header glass" aria-label="Barre supérieure">
        <div class="nx-header-left">
          <div class="nx-brand">
            <span class="nx-brand-logo" aria-hidden="true">
              <img src="/Images/lg-removebg-preview.png" alt="" />
            </span>
            <span class="nx-brand-name">Dashboard</span>
          </div>
        </div>
      </header>

      <main class="nx-chat glass" aria-label="Tableau de bord">
        <section class="nx-thread" style="padding: 24px; text-align: center; min-height: 320px;">
          <h1>Tableau de bord</h1>
          <p>Utilise la barre latérale pour naviguer vers Chat, Appels et Paramètres.</p>
        </section>
      </main>
    </div>
  </div>
</body>
</html>
