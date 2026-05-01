<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nexora — Chat</title>
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
        <a class="nx-rail-btn is-active" href="{{ route('chat') }}" aria-label="Chats">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
          </svg>
        </a>
        <a class="nx-rail-btn" href="{{ route('dashboard') }}" aria-label="Dashboard">
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

    <!-- Main -->
    <div class="nx-main">
      <!-- Global topbar -->
      <header class="nx-header glass" aria-label="Barre supérieure">
        <div class="nx-header-left">
          <div class="nx-brand">
            <span class="nx-brand-logo" aria-hidden="true">
              <img src="/Images/lg-removebg-preview.png" alt="" />
            </span>
            <span class="nx-brand-name">Nexora</span>
          </div>
        </div>

        <div class="nx-search">
          <svg class="nx-search-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.3-4.3"></path>
          </svg>
          <input id="globalSearch" type="search" placeholder="Rechercher un utilisateur, un groupe, un message…" autocomplete="off" />
          <span class="nx-search-glow" aria-hidden="true"></span>
        </div>

        <div class="nx-header-right">
          <a class="nx-pill" href="{{ route('dashboard') }}" aria-label="Dashboard">
            <span class="nx-pill-ico" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 13h8V3H3v10z"></path>
                <path d="M13 21h8v-6h-8v6z"></path>
                <path d="M13 3h8v8h-8V3z"></path>
                <path d="M3 21h8v-4H3v4z"></path>
              </svg>
            </span>
            <span class="nx-pill-text">Dashboard</span>
          </a>
          <a class="nx-me-mini" href="{{ route('Parametre') }}" aria-label="Paramètres">
            <span class="nx-initials nx-initials--me" aria-hidden="true">NX</span>
          </a>
        </div>
      </header>

      <!-- Grid -->
      <section class="nx-grid nx-grid--two" aria-label="Espace de discussion">
        <!-- Conversations -->
        <aside class="nx-list glass" aria-label="Conversations">
          <div class="nx-list-head">
            <div class="nx-list-title">
              <h2>Chat</h2>
              <span class="nx-badge" id="chatCount">6</span>
            </div>

            <label class="nx-list-search" aria-label="Rechercher un contact">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
              </svg>
              <input id="contactSearch" type="search" placeholder="Rechercher un contact…" autocomplete="off" />
            </label>
          </div>

          <div class="nx-tabs" role="tablist" aria-label="Filtre">
            <button class="nx-tab is-active" type="button" data-filter="all" role="tab" aria-selected="true">Messages</button>
            <button class="nx-tab" type="button" data-filter="unread" role="tab" aria-selected="false">Non lus</button>
          </div>

          <div class="nx-convos" id="convos">
            <button class="nx-convo is-active u-fade-in-up u-delay-1" type="button"
                    data-name="Amaya" data-initials="A" data-status="En ligne"
                    data-role="Dancehall • Étudiante" data-location="Pune, India"
                    data-about="Vibe Dancehall. Partage de playlists, challenges, et discussions chill."
                    data-unread="0">
              <span class="nx-initials nx-initials--peer" aria-hidden="true">A</span>
              <span class="nx-convo-body">
                <span class="nx-convo-top">
                  <span class="nx-convo-name">Amaya</span>
                  <span class="nx-time">09:14</span>
                </span>
                <span class="nx-convo-bottom">
                  <span class="nx-preview">On se fait une playlist partagée ?</span>
                  <span class="nx-chip nx-chip--online">online</span>
                </span>
              </span>
            </button>

            <button class="nx-convo u-fade-in-up u-delay-2" type="button"
                    data-name="Gabin" data-initials="G" data-status="Actif il y a 2 min"
                    data-role="Tech • Dev" data-location="Paris, FR"
                    data-about="Code, design systems et UI. Toujours chaud pour un projet."
                    data-unread="2">
              <span class="nx-initials nx-initials--peer" aria-hidden="true">G</span>
              <span class="nx-convo-body">
                <span class="nx-convo-top">
                  <span class="nx-convo-name">Gabin</span>
                  <span class="nx-time">08:57</span>
                </span>
                <span class="nx-convo-bottom">
                  <span class="nx-preview">Tu as avancé sur l’intégration ?</span>
                  <span class="nx-unread">2</span>
                </span>
              </span>
            </button>

            <button class="nx-convo u-fade-in-up u-delay-3" type="button"
                    data-name="Tina" data-initials="T" data-status="Hors ligne"
                    data-role="Photo • Art" data-location="Abidjan, CI"
                    data-about="Photographie, art et moodboards. J’adore les concepts créatifs."
                    data-unread="0">
              <span class="nx-initials nx-initials--peer" aria-hidden="true">T</span>
              <span class="nx-convo-body">
                <span class="nx-convo-top">
                  <span class="nx-convo-name">Tina</span>
                  <span class="nx-time">Hier</span>
                </span>
                <span class="nx-convo-bottom">
                  <span class="nx-preview">J’ai une idée pour ton projet.</span>
                </span>
              </span>
            </button>

            <button class="nx-convo u-fade-in-up u-delay-4" type="button"
                    data-name="Rayan" data-initials="R" data-status="En ligne"
                    data-role="Sport • Fitness" data-location="Dakar, SN"
                    data-about="Motivation, sport, nutrition. On s’encourage au quotidien."
                    data-unread="1">
              <span class="nx-initials nx-initials--peer" aria-hidden="true">R</span>
              <span class="nx-convo-body">
                <span class="nx-convo-top">
                  <span class="nx-convo-name">Rayan</span>
                  <span class="nx-time">07:31</span>
                </span>
                <span class="nx-convo-bottom">
                  <span class="nx-preview">Tu t’entraînes aujourd’hui ?</span>
                  <span class="nx-unread">1</span>
                </span>
              </span>
            </button>

            <button class="nx-convo u-fade-in-up u-delay-4" type="button"
                    data-name="Nina" data-initials="N" data-status="Actif hier"
                    data-role="Musique • Piano" data-location="Lyon, FR"
                    data-about="Classique, piano et covers. J’aime les discussions profondes."
                    data-unread="0">
              <span class="nx-initials nx-initials--peer" aria-hidden="true">N</span>
              <span class="nx-convo-body">
                <span class="nx-convo-top">
                  <span class="nx-convo-name">Nina</span>
                  <span class="nx-time">Lun</span>
                </span>
                <span class="nx-convo-bottom">
                  <span class="nx-preview">Merci pour la recommandation !</span>
                </span>
              </span>
            </button>

            <button class="nx-convo u-fade-in-up u-delay-4" type="button"
                    data-name="Kofi" data-initials="K" data-status="En ligne"
                    data-role="Gaming • Esport" data-location="Accra, GH"
                    data-about="Jeux compétitifs, stratégie et fun. Teamplay avant tout."
                    data-unread="0">
              <span class="nx-initials nx-initials--peer" aria-hidden="true">K</span>
              <span class="nx-convo-body">
                <span class="nx-convo-top">
                  <span class="nx-convo-name">Kofi</span>
                  <span class="nx-time">Dim</span>
                </span>
                <span class="nx-convo-bottom">
                  <span class="nx-preview">Let’s go duo ce soir ?</span>
                  <span class="nx-chip nx-chip--online">online</span>
                </span>
              </span>
            </button>
          </div>

          <div class="nx-list-foot">
            <button class="nx-cta" type="button" aria-label="Nouvelle discussion">
              <span>Nouveau message</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 5v14"></path>
                <path d="M5 12h14"></path>
              </svg>
            </button>
          </div>
        </aside>

        <!-- Thread -->
        <main class="nx-chat glass" aria-label="Discussion">
          <header class="nx-chat-head">
            <div class="nx-chat-peer" aria-label="Contact">
              <span class="nx-initials nx-initials--peer" id="peerInitials" aria-hidden="true">A</span>
              <span class="nx-chat-peer-meta">
                <span class="nx-chat-peer-name" id="peerName">Amaya</span>
                <span class="nx-chat-peer-status" id="peerStatus">
                  <span class="nx-dot" aria-hidden="true"></span>
                  En ligne
                </span>
              </span>
            </div>

            <div class="nx-chat-meta">
              <span class="nx-tag">Messages</span>
              <span class="nx-tag">Vocal</span>
              <span class="nx-tag">Envoyé</span>
            </div>

            <div class="nx-chat-actions">
              <button class="nx-icon" type="button" aria-label="Appel">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.15 10.8 19.86 19.86 0 0 1 .08 2.18 2 2 0 0 1 2.06 0h3a2 2 0 0 1 2 1.72c.12.86.33 1.7.62 2.5a2 2 0 0 1-.45 2.11L6.1 7.1a16 16 0 0 0 6.8 6.8l.77-1.13a2 2 0 0 1 2.11-.45c.8.29 1.64.5 2.5.62A2 2 0 0 1 22 16.92z"></path>
                </svg>
              </button>
              <button class="nx-icon" type="button" aria-label="Vidéo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M23 7l-7 5 7 5V7z"></path>
                  <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                </svg>
              </button>
              <button class="nx-icon nx-icon--accent" type="button" id="focusBtn" aria-label="Mode focus">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <circle cx="12" cy="12" r="3"></circle>
                  <path d="M19.4 15a7.8 7.8 0 0 0 .1-6"></path>
                  <path d="M4.6 9a7.8 7.8 0 0 0 0 6"></path>
                  <path d="M15 19.4a7.8 7.8 0 0 1-6 0"></path>
                  <path d="M9 4.6a7.8 7.8 0 0 1 6 0"></path>
                </svg>
              </button>
            </div>
          </header>

          <section class="nx-thread" id="thread" role="log" aria-live="polite" aria-relevant="additions">
            <div class="nx-day"><span>Aujourd’hui</span></div>

            <article class="nx-msg nx-msg--peer u-fade-in-up u-delay-1">
              <div class="nx-bubble">
                <p>Salut ! J’ai vu que tu aimes la <strong>danse</strong>. Tu pratiques quel style ?</p>
                <div class="nx-meta"><time>09:12</time></div>
              </div>
            </article>

            <article class="nx-msg nx-msg--me u-fade-in-up u-delay-2">
              <div class="nx-bubble">
                <p>Hey, plutôt Afro et un peu Hip-hop. Et toi ?</p>
                <div class="nx-meta">
                  <time>09:13</time>
                  <span class="nx-read" aria-label="Envoyé">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M20 6L9 17l-5-5"></path>
                    </svg>
                  </span>
                </div>
              </div>
            </article>

            <article class="nx-msg nx-msg--peer u-fade-in-up u-delay-3">
              <div class="nx-bubble">
                <p>Afro c’est une vibe ! Moi c’est Dancehall. On se fait une playlist partagée ?</p>
                <div class="nx-meta"><time>09:14</time></div>
              </div>
            </article>
          </section>

          <footer class="nx-composer" aria-label="Écrire un message">
            <button class="nx-tool" type="button" aria-label="Joindre">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66L9.46 17.37a2 2 0 0 1-2.83-2.83l8.49-8.49"></path>
              </svg>
            </button>
            <button class="nx-tool" type="button" id="emojiBtn" aria-label="Emojis">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                <path d="M9 9h.01"></path>
                <path d="M15 9h.01"></path>
              </svg>
            </button>
            <label class="nx-input-wrap">
              <input id="messageInput" type="text" placeholder="Écrire un message…" autocomplete="off" />
              <span class="nx-hint">Entrée pour envoyer</span>
              <span class="nx-record-status" id="recordStatus" aria-live="polite"></span>
            </label>
            <button class="nx-mic" type="button" id="recordBtn" aria-label="Enregistrer un message vocal">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                <path d="M12 19v4"></path>
                <path d="M8 23h8"></path>
              </svg>
              <span class="nx-mic-wave" aria-hidden="true"></span>
            </button>
            <button class="nx-send" type="button" id="sendBtn" aria-label="Envoyer un message">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 2L11 13"></path>
                <path d="M22 2l-7 20-4-9-9-4 20-7z"></path>
              </svg>
            </button>
          </footer>
        </main>
      </section>
    </div>
  </div>

  <script>
    function getParam(key) {
      const p = new URLSearchParams(window.location.search);
      return p.get(key) || '';
    }

    const shell = document.getElementById('shell');
    const convos = document.getElementById('convos');
    const contactSearch = document.getElementById('contactSearch');
    const tabs = Array.from(document.querySelectorAll('.nx-tab'));
    const chatCount = document.getElementById('chatCount');

    const peerName = document.getElementById('peerName');
    const peerInitials = document.getElementById('peerInitials');
    const peerStatus = document.getElementById('peerStatus');

    const thread = document.getElementById('thread');
    const input = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const focusBtn = document.getElementById('focusBtn');
    const recordBtn = document.getElementById('recordBtn');
    const recordStatus = document.getElementById('recordStatus');

    let mediaStream = null;
    let mediaRecorder = null;
    let audioChunks = [];
    let isRecording = false;

    async function getAudioStream() {
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        return null;
      }

      try {
        return await navigator.mediaDevices.getUserMedia({ audio: true });
      } catch (error) {
        console.warn('Enregistrement vocal impossible', error);
        return null;
      }
    }

    function createAudioMessage(blob) {
      const url = URL.createObjectURL(blob);
      const article = document.createElement('article');
      article.className = 'nx-msg nx-msg--me';
      article.innerHTML = `
        <div class="nx-bubble">
          <audio controls src="${url}"></audio>
          <div class="nx-meta">
            <time>${nowHHMM()}</time>
            <span class="nx-read" aria-label="Envoyé">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 6L9 17l-5-5"></path>
              </svg>
            </span>
          </div>
        </div>
      `;
      return article;
    }

    function startRecording() {
      if (!mediaStream || !MediaRecorder) return;

      audioChunks = [];
      mediaRecorder = new MediaRecorder(mediaStream);
      mediaRecorder.addEventListener('dataavailable', (event) => {
        if (event.data && event.data.size > 0) {
          audioChunks.push(event.data);
        }
      });

      mediaRecorder.addEventListener('stop', () => {
        const blob = new Blob(audioChunks, { type: 'audio/webm' });
        thread.appendChild(createAudioMessage(blob));
        scrollToBottom();
      });

      mediaRecorder.start();
      isRecording = true;
      recordBtn?.classList.add('is-recording');
      if (recordStatus) {
        recordStatus.textContent = 'Enregistrement en cours… cliquez de nouveau pour arrêter';
      }
    }

    function stopRecording() {
      if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
      }
      isRecording = false;
      recordBtn?.classList.remove('is-recording');
      if (recordStatus) {
        recordStatus.textContent = '';
      }
    }

    async function toggleRecording() {
      if (isRecording) {
        stopRecording();
        return;
      }

      if (!mediaStream) {
        mediaStream = await getAudioStream();
      }

      if (!mediaStream) {
        if (recordStatus) {
          recordStatus.textContent = 'Microphone indisponible';
        }
        return;
      }

      startRecording();
    }

    function pad2(n) { return String(n).padStart(2, '0'); }
    function nowHHMM() {
      const d = new Date();
      return `${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
    }

    function scrollToBottom() {
      thread?.scrollTo({ top: thread.scrollHeight, behavior: 'smooth' });
    }

    function setTabActive(btn) {
      tabs.forEach(t => {
        const isActive = t === btn;
        t.classList.toggle('is-active', isActive);
        t.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });
      filterConvos();
    }

    function filterConvos() {
      const q = (contactSearch?.value || '').trim().toLowerCase();
      const activeTab = tabs.find(t => t.classList.contains('is-active'))?.dataset.filter || 'all';

      let visible = 0;
      const items = Array.from(convos.querySelectorAll('.nx-convo'));
      for (const btn of items) {
        const name = (btn.dataset.name || '').toLowerCase();
        const preview = (btn.querySelector('.nx-preview')?.textContent || '').toLowerCase();
        const unread = Number(btn.dataset.unread || '0');

        const matchesText = !q || name.includes(q) || preview.includes(q);
        const matchesTab = activeTab === 'all' ? true : unread > 0;
        const show = matchesText && matchesTab;

        btn.style.display = show ? '' : 'none';
        if (show) visible++;
      }
      if (chatCount) chatCount.textContent = String(visible);
    }

    function selectConvo(btn) {
      const items = Array.from(convos.querySelectorAll('.nx-convo'));
      items.forEach(b => b.classList.toggle('is-active', b === btn));

      const name = btn.dataset.name || 'Contact';
      const initials = btn.dataset.initials || name.slice(0, 1).toUpperCase();
      const status = btn.dataset.status || '';

      peerName.textContent = name;
      peerInitials.textContent = initials;

      peerStatus.innerHTML = `<span class="nx-dot" aria-hidden="true"></span>${status}`;

      // Optional: clear unread visually on click.
      const unreadEl = btn.querySelector('.nx-unread');
      if (unreadEl) unreadEl.remove();
      btn.dataset.unread = '0';
      filterConvos();
    }

    function createMyMessage(text) {
      const article = document.createElement('article');
      article.className = 'nx-msg nx-msg--me';
      article.innerHTML = `
        <div class="nx-bubble">
          <p></p>
          <div class="nx-meta">
            <time>${nowHHMM()}</time>
            <span class="nx-read" aria-label="Envoyé">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 6L9 17l-5-5"></path>
              </svg>
            </span>
          </div>
        </div>
      `;
      article.querySelector('p').textContent = text;
      return article;
    }

    function send() {
      const text = (input?.value || '').trim();
      if (!text) return;
      thread.appendChild(createMyMessage(text));
      input.value = '';
      input.focus();
      scrollToBottom();
    }

    convos?.addEventListener('click', (e) => {
      const btn = e.target.closest('.nx-convo');
      if (!btn) return;
      selectConvo(btn);
    });

    contactSearch?.addEventListener('input', filterConvos);
    tabs.forEach(t => t.addEventListener('click', () => setTabActive(t)));

    sendBtn?.addEventListener('click', send);
    input?.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        send();
      }
    });

    focusBtn?.addEventListener('click', () => document.body.classList.toggle('nx-focus'));
    recordBtn?.addEventListener('click', toggleRecording);

    // Initial setup
    filterConvos();
    // If coming from Match: open/create the target peer
    const incomingPeer = getParam('peer').trim();
    if (incomingPeer && convos) {
      const incomingInitials = (getParam('initials') || incomingPeer.slice(0, 1)).trim().slice(0, 2).toUpperCase();
      const existing = Array.from(convos.querySelectorAll('.nx-convo')).find(b => (b.dataset.name || '') === incomingPeer);
      const btn = existing || (() => {
        const b = document.createElement('button');
        b.className = 'nx-convo u-fade-in-up u-delay-1';
        b.type = 'button';
        b.dataset.name = incomingPeer;
        b.dataset.initials = incomingInitials;
        b.dataset.status = 'En ligne';
        b.dataset.unread = '0';
        b.innerHTML = `
          <span class="nx-initials nx-initials--peer" aria-hidden="true">${incomingInitials}</span>
          <span class="nx-convo-body">
            <span class="nx-convo-top">
              <span class="nx-convo-name">${incomingPeer}</span>
              <span class="nx-time">now</span>
            </span>
            <span class="nx-convo-bottom">
              <span class="nx-preview">Démarrer la discussion…</span>
              <span class="nx-chip nx-chip--online">online</span>
            </span>
          </span>
        `;
        convos.prepend(b);
        return b;
      })();
      selectConvo(btn);
    }
    window.addEventListener('load', () => setTimeout(scrollToBottom, 250));
  </script>

</body>
</html>