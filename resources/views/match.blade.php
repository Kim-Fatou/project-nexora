<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nexora - Match</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/splash.css">
  <link rel="stylesheet" href="/css/match.css">
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

  <div class="particles-container" aria-hidden="true"></div>
  <div class="morph-shape morph-1" aria-hidden="true"></div>
  <div class="morph-shape morph-2" aria-hidden="true"></div>
  <div class="morph-shape morph-3" aria-hidden="true"></div>

  <main class="match-container">
    <section class="match-hero">
      <a class="back-link" href="{{ route('matching') }}" aria-label="Retour au matching">
        <span class="back-icon">←</span>
        Retour
      </a>

      <div class="match-title-wrap">
        <h1 class="match-title" id="matchTitle">Match</h1>
        <p class="match-subtitle" id="matchSubtitle">Personnes qui partagent la même passion que toi</p>
      </div>

      <div class="match-stats">
        <div class="stat-chip">
          <span class="stat-number" id="matchCount">20</span>
          <span class="stat-label">personnes</span>
        </div>
        <div class="stat-chip subtle">
          <span class="stat-number" id="matchCondition">Musique</span>
          <span class="stat-label">condition</span>
        </div>
      </div>
    </section>

    <section class="match-list-section" aria-label="Liste des profils">
      <div class="match-list" id="matchList"></div>
    </section>
  </main>

  <div class="loading-overlay" id="loadingOverlay" aria-hidden="true">
    <div class="loading-card">
      <div class="nx-spinner" aria-hidden="true"></div>
      <div class="loading-title">Chargement des profils…</div>
      <div class="loading-sub">Analyse des connexions et affinités</div>
    </div>
  </div>

  <div class="profile-modal-overlay" id="profileModal" aria-hidden="true">
    <div class="profile-modal" role="dialog" aria-modal="true" aria-labelledby="profileModalTitle">
      <button class="modal-close" id="modalCloseBtn" aria-label="Fermer">✕</button>
      <div class="modal-head">
        <div class="modal-avatar" id="modalAvatar">A</div>
        <div class="modal-head-text">
          <h2 class="modal-title" id="profileModalTitle">Profil</h2>
          <p class="modal-subtitle" id="modalSubtitle">—</p>
        </div>
        <div class="modal-score" id="modalScore">0%</div>
      </div>
      <div class="modal-body" id="modalBody"></div>
      <div class="modal-actions">
        <button class="btn-primary-wide" id="modalSelectBtn">Discuter avec la personne maintenant</button>
        <button class="btn-ghost-wide" id="modalBackBtn">Retour à la liste</button>
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
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2026 Nexora. Tous droits réservés.</p>
      </div>
    </div>
  </footer>
<script></script>
  <script src="{{ asset('js/match.js') }}"></script>
  <script>/* ========================================
   NEXORA - MATCH PAGE STYLES
======================================== */

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
  min-height: 100vh;
  background: linear-gradient(135deg, #E8D8C4 0%, #C7B7A3 50%, #E8D8C4 100%);
  background-size: 400% 400%;
  animation: gradientShift 15s ease infinite;
  overflow-x: hidden;
  position: relative;
}

@keyframes gradientShift {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

.particles-container {
  position: fixed;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
  z-index: 0;
}

.particle {
  position: absolute;
  width: 6px;
  height: 6px;
  background: radial-gradient(circle, rgba(86, 28, 36, 0.6) 0%, transparent 70%);
  border-radius: 50%;
  animation: floatParticle 8s ease-in-out infinite;
}

@keyframes floatParticle {
  0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
  10% { opacity: 1; }
  90% { opacity: 1; }
  100% { transform: translateY(-100px) rotate(720deg); opacity: 0; }
}

.morph-shape {
  position: fixed;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.3;
  z-index: 0;
}

.morph-1 {
  width: 420px;
  height: 420px;
  background: #561C24;
  top: -120px;
  right: -120px;
  animation: morph1 20s ease-in-out infinite;
}

.morph-2 {
  width: 320px;
  height: 320px;
  background: #6D2932;
  bottom: -70px;
  left: -70px;
  animation: morph2 16s ease-in-out infinite;
}

.morph-3 {
  width: 260px;
  height: 260px;
  background: #C7B7A3;
  top: 55%;
  left: 55%;
  transform: translate(-50%, -50%);
  animation: morph3 18s ease-in-out infinite;
}

@keyframes morph1 {
  0%, 100% { transform: scale(1) rotate(0deg); border-radius: 50%; }
  33% { transform: scale(1.2) rotate(120deg); border-radius: 40% 60% 60% 40%; }
  66% { transform: scale(0.9) rotate(240deg); border-radius: 60% 40% 40% 60%; }
}

@keyframes morph2 {
  0%, 100% { transform: scale(1) rotate(0deg); }
  50% { transform: scale(1.3) rotate(180deg); }
}

@keyframes morph3 {
  0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.2; }
  50% { transform: translate(-50%, -50%) scale(1.5); opacity: 0.4; }
}

.match-container {
  position: relative;
  z-index: 1;
  max-width: 980px;
  margin: 0 auto;
  padding: 110px 20px 40px;
  min-height: 100vh;
}

.match-hero {
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.45);
  border-radius: 26px;
  padding: 22px 22px 18px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(18, 18, 18, 0.08);
}

.match-hero::before {
  content: '';
  position: absolute;
  inset: -80px;
  background: conic-gradient(
    from 0deg,
    transparent,
    rgba(86, 28, 36, 0.16),
    transparent,
    rgba(109, 41, 50, 0.16),
    transparent
  );
  animation: rotateGlow 6s linear infinite;
  opacity: 0.7;
}

@keyframes rotateGlow { to { transform: rotate(360deg); } }

.match-hero > * { position: relative; z-index: 1; }

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  color: #561C24;
  text-decoration: none;
  font-weight: 600;
  padding: 10px 14px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.65);
  border: 1px solid rgba(86, 28, 36, 0.15);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.back-link:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(86, 28, 36, 0.18);
}

.back-icon { font-size: 18px; }

.match-title-wrap { margin-top: 14px; }

.match-title {
  font-size: 34px;
  letter-spacing: -0.02em;
  color: #121212;
  font-weight: 800;
}

.match-subtitle {
  margin-top: 8px;
  color: rgba(18, 18, 18, 0.72);
  font-size: 15px;
  line-height: 1.4;
}

.match-stats {
  margin-top: 16px;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.stat-chip {
  display: inline-flex;
  align-items: baseline;
  gap: 10px;
  padding: 12px 14px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.72);
  border: 1px solid rgba(255, 255, 255, 0.55);
}

.stat-chip.subtle {
  background: rgba(18, 18, 18, 0.08);
  border: 1px solid rgba(18, 18, 18, 0.08);
}

.stat-number {
  font-weight: 900;
  color: #561C24;
  font-size: 22px;
}

.stat-chip.subtle .stat-number { color: #121212; }

.stat-label {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: rgba(18, 18, 18, 0.55);
}

.match-list-section { margin-top: 18px; }

.match-list {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

@media (max-width: 860px) {
  .match-list { grid-template-columns: 1fr; }
  .match-container { padding-top: 95px; }
  .match-title { font-size: 30px; }
}

.profile-card {
  background: rgba(255, 255, 255, 0.28);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
  border: 1px solid rgba(255, 255, 255, 0.50);
  border-radius: 22px;
  padding: 16px;
  position: relative;
  overflow: hidden;
  transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
  cursor: pointer;
}

.profile-card::after {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 20% 20%, rgba(86, 28, 36, 0.18), transparent 50%),
              radial-gradient(circle at 80% 10%, rgba(109, 41, 50, 0.14), transparent 55%);
  opacity: 0;
  transition: opacity 0.22s ease;
  pointer-events: none;
}

.profile-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 18px 55px rgba(86, 28, 36, 0.18);
  border-color: rgba(86, 28, 36, 0.18);
}

.profile-card:hover::after { opacity: 1; }

.profile-head {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 46px;
  height: 46px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  font-weight: 900;
  color: #fff;
  background: linear-gradient(135deg, #561C24, #6D2932);
  box-shadow: 0 12px 30px rgba(86, 28, 36, 0.22);
  flex-shrink: 0;
}

.profile-meta { flex: 1; min-width: 0; }

.profile-name {
  font-weight: 800;
  color: #121212;
  font-size: 16px;
  line-height: 1.2;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.profile-loc {
  margin-top: 4px;
  color: rgba(18, 18, 18, 0.7);
  font-size: 13px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.score-pill {
  padding: 10px 12px;
  border-radius: 999px;
  background: rgba(18, 18, 18, 0.08);
  color: #121212;
  font-weight: 900;
  font-size: 13px;
}

.profile-bio {
  margin-top: 12px;
  color: rgba(18, 18, 18, 0.72);
  font-size: 14px;
  line-height: 1.45;
}

.tags {
  margin-top: 12px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.tag {
  padding: 8px 10px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.65);
  border: 1px solid rgba(86, 28, 36, 0.10);
  color: #561C24;
  font-size: 12px;
  font-weight: 700;
}

.tag.dark {
  background: rgba(18, 18, 18, 0.08);
  color: rgba(18, 18, 18, 0.78);
  border-color: rgba(18, 18, 18, 0.08);
}

/* Loading overlay (black + red) */
.loading-overlay {
  position: fixed;
  inset: 0;
  background: rgba(10, 10, 10, 0.85);
  backdrop-filter: blur(10px);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 200;
}

.loading-overlay.active { display: flex; }

.loading-card {
  width: min(420px, 92vw);
  background: rgba(18, 18, 18, 0.55);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 26px;
  padding: 24px 22px;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.loading-card::before {
  content: '';
  position: absolute;
  inset: -120px;
  background: conic-gradient(from 0deg, transparent, rgba(199, 183, 163, 0.22), rgba(86, 28, 36, 0.35), transparent);
  animation: rotateGlow 3.2s linear infinite;
  opacity: 0.9;
}

.loading-card > * { position: relative; z-index: 1; }

.nx-spinner {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  margin: 2px auto 14px;
  background: conic-gradient(from 0deg, #561C24, #6D2932, #C7B7A3, #561C24);
  animation: spin 1.1s linear infinite;
  position: relative;
  box-shadow: 0 20px 60px rgba(0,0,0,0.45);
}

.nx-spinner::after {
  content: '';
  position: absolute;
  inset: 9px;
  border-radius: 50%;
  background: rgba(10, 10, 10, 0.85);
  border: 1px solid rgba(255,255,255,0.10);
}

@keyframes spin { to { transform: rotate(360deg); } }

.loading-title {
  color: #fff;
  font-weight: 900;
  font-size: 16px;
}

.loading-sub {
  margin-top: 6px;
  color: rgba(255,255,255,0.72);
  font-size: 13px;
}

/* Modal overlay (slightly black unfolding) */
.profile-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(18, 18, 18, 0.75);
  backdrop-filter: blur(10px);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 300;
  padding: 18px;
}

.profile-modal-overlay.active { display: flex; }

.profile-modal {
  width: min(720px, 96vw);
  max-height: min(82vh, 760px);
  overflow: auto;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(255, 255, 255, 0.45);
  border-radius: 26px;
  box-shadow: 0 30px 90px rgba(0, 0, 0, 0.45);
  transform: translateY(12px) scale(0.98);
  opacity: 0;
  animation: modalIn 0.25s ease forwards;
  position: relative;
}

@keyframes modalIn {
  to { transform: translateY(0) scale(1); opacity: 1; }
}

.modal-close {
  position: sticky;
  top: 12px;
  margin-left: auto;
  margin-right: 12px;
  display: grid;
  place-items: center;
  width: 42px;
  height: 42px;
  border-radius: 999px;
  border: 1px solid rgba(86, 28, 36, 0.12);
  background: rgba(255, 255, 255, 0.8);
  cursor: pointer;
  font-weight: 900;
  color: #561C24;
  z-index: 2;
}

.modal-head {
  padding: 18px 18px 0;
  display: flex;
  align-items: center;
  gap: 14px;
}

.modal-avatar {
  width: 60px;
  height: 60px;
  border-radius: 18px;
  display: grid;
  place-items: center;
  font-weight: 900;
  font-size: 22px;
  color: #fff;
  background: linear-gradient(135deg, #561C24, #6D2932);
  box-shadow: 0 18px 55px rgba(86, 28, 36, 0.25);
  flex-shrink: 0;
}

.modal-head-text { flex: 1; min-width: 0; }

.modal-title {
  font-size: 20px;
  font-weight: 900;
  color: #121212;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.modal-subtitle {
  margin-top: 6px;
  color: rgba(18, 18, 18, 0.7);
  font-size: 13px;
}

.modal-score {
  padding: 10px 12px;
  border-radius: 999px;
  background: rgba(86, 28, 36, 0.10);
  color: #561C24;
  font-weight: 900;
}

.modal-body {
  padding: 16px 18px 8px;
  display: grid;
  gap: 12px;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

@media (max-width: 700px) {
  .detail-grid { grid-template-columns: 1fr; }
}

.detail-card {
  background: rgba(255, 255, 255, 0.75);
  border: 1px solid rgba(18, 18, 18, 0.07);
  border-radius: 18px;
  padding: 12px 12px;
}

.detail-title {
  font-weight: 900;
  color: #121212;
  font-size: 13px;
  letter-spacing: 0.02em;
}

.detail-text {
  margin-top: 8px;
  color: rgba(18, 18, 18, 0.75);
  font-size: 13px;
  line-height: 1.45;
}

.modal-actions {
  padding: 12px 18px 18px;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-primary-wide,
.btn-ghost-wide {
  flex: 1;
  min-width: 220px;
  padding: 14px 16px;
  border-radius: 999px;
  cursor: pointer;
  font-weight: 800;
  border: none;
}

.btn-primary-wide {
  background: linear-gradient(135deg, #561C24, #6D2932);
  color: #fff;
  box-shadow: 0 14px 45px rgba(86, 28, 36, 0.25);
}

.btn-ghost-wide {
  background: rgba(18, 18, 18, 0.08);
  color: rgba(18, 18, 18, 0.88);
  border: 1px solid rgba(18, 18, 18, 0.08);
}

</script>
</body>
</html>

