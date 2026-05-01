/* ========================================
   NEXORA - MATCH PAGE SCRIPT
   Renders profiles based on condition from matching.js
======================================== */

function qs(key, fallback = '') {
  const params = new URLSearchParams(window.location.search);
  return params.get(key) ?? fallback;
}

function toInt(value, fallback) {
  const n = Number.parseInt(String(value), 10);
  return Number.isFinite(n) ? n : fallback;
}

function createParticles() {
  const container = document.querySelector('.particles-container');
  if (!container) return;
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

function setupMobileMenu() {
  const header = document.getElementById('header');
  const menuToggle = document.getElementById('menuToggle');
  const mobileMenu = document.getElementById('mobileMenu');
  if (!header || !menuToggle || !mobileMenu) return;

  function updateHeader() {
    if (window.scrollY > 10) header.classList.add('scrolled');
    else header.classList.add('scrolled');
  }

  updateHeader();
  window.addEventListener('scroll', updateHeader);

  menuToggle.addEventListener('click', () => {
    menuToggle.classList.toggle('active');
    mobileMenu.classList.toggle('active');
  });

  const mobileLinks = mobileMenu.querySelectorAll('a');
  mobileLinks.forEach(link => {
    link.addEventListener('click', () => {
      menuToggle.classList.remove('active');
      mobileMenu.classList.remove('active');
    });
  });
}

const DATA_BY_INTEREST = {
  sport: {
    label: 'Sport',
    cities: ['Paris', 'Lyon', 'Marseille', 'Lille', 'Toulouse', 'Nice'],
    traits: ['Discipliné(e)', 'Motivé(e)', 'Team spirit', 'Énergique', 'Compétitif(ve)'],
    likes: ['Entraînements', 'Défis', 'Matches', 'Nutrition', 'Objectifs'],
    opinions: [
      '« J’adore progresser avec une team. »',
      '« Les petits défis, c’est ce qui me motive. »',
      '« La régularité > la motivation. »'
    ]
  },
  technologie: {
    label: 'Technologie',
    cities: ['Paris', 'Bordeaux', 'Nantes', 'Lyon', 'Montpellier', 'Grenoble'],
    traits: ['Curieux(se)', 'Builder', 'Analytique', 'Créatif(ve)', 'Geek assumé(e)'],
    likes: ['Hackathons', 'Side projects', 'IA', 'Open source', 'Startups'],
    opinions: [
      '« J’aime apprendre en construisant. »',
      '« Une idée, un prototype, et on voit. »',
      '« L’IA, oui, mais avec du sens. »'
    ]
  },
  musique: {
    label: 'Musique',
    cities: ['Paris', 'Bruxelles', 'Lyon', 'Marseille', 'Genève', 'Lille'],
    traits: ['Créatif(ve)', 'Sensible', 'Rythmé(e)', 'Expressif(ve)', 'Passionné(e)'],
    likes: ['Concerts', 'Studio', 'Playlists', 'Jam sessions', 'Découvertes'],
    opinions: [
      '« La musique dit ce que les mots n’osent pas. »',
      '« Un bon son peut sauver une journée. »',
      '« Je cherche des gens avec la même vibe. »'
    ]
  },
  mode: {
    label: 'Mode',
    cities: ['Paris', 'Milan', 'Lyon', 'Marseille', 'Bruxelles', 'Nice'],
    traits: ['Stylé(e)', 'Audacieux(se)', 'Créatif(ve)', 'Détaillé(e)', 'Inspiré(e)'],
    likes: ['Looks', 'Création', 'Vintage', 'Défilés', 'Shooting'],
    opinions: [
      '« Le style, c’est une signature. »',
      '« J’aime les pièces qui racontent une histoire. »',
      '« Minimal ou extravagant, l’essentiel c’est toi. »'
    ]
  },
  danse: {
    label: 'Danse',
    cities: ['Paris', 'Lyon', 'Marseille', 'Lille', 'Toulouse', 'Strasbourg'],
    traits: ['Énergique', 'Expressif(ve)', 'Créatif(ve)', 'Chill', 'Endurant(e)'],
    likes: ['Workshops', 'Battles', 'Chorés', 'Freestyle', 'Performances'],
    opinions: [
      '« La danse, c’est la liberté. »',
      '« Rien ne vaut une bonne session. »',
      '« Le flow, ça se partage. »'
    ]
  }
};

const SUB_LABELS = {
  football: 'Football',
  fitness: 'Fitness',
  basketball: 'Basketball',
  programmation: 'Programmation',
  ia: 'IA',
  startup: 'Startup',
  chanter: 'Chanter',
  produire: 'Produire',
  ecouter: 'Écouter',
  stylisme: 'Stylisme',
  shopping: 'Shopping',
  creation: 'Création',
  hiphop: 'Hip-hop',
  afro: 'Afro',
  moderne: 'Moderne'
};

const FIRST_NAMES = [
  'Amina', 'Kofi', 'Mina', 'Ryan', 'Tina', 'Sofia', 'Noah', 'Lina', 'Yanis', 'Chloé',
  'Maya', 'Adam', 'Inès', 'Sam', 'Leïla', 'Nina', 'Ismaël', 'Sara', 'Nabil', 'Aya'
];

function seededHash(str) {
  let h = 2166136261;
  for (let i = 0; i < str.length; i++) {
    h ^= str.charCodeAt(i);
    h = Math.imul(h, 16777619);
  }
  return h >>> 0;
}

function pick(arr, idx) {
  if (!arr.length) return '';
  return arr[idx % arr.length];
}

function clamp(n, min, max) {
  return Math.min(max, Math.max(min, n));
}

function buildProfiles({ interestKey, subKey, count }) {
  const base = DATA_BY_INTEREST[interestKey] ?? DATA_BY_INTEREST.musique;
  const seedBase = `${interestKey}:${subKey || ''}:${count}`;
  const seed = seededHash(seedBase);

  const profiles = [];
  for (let i = 0; i < count; i++) {
    const localSeed = seededHash(`${seed}:${i}`);
    const name = `${pick(FIRST_NAMES, localSeed)} ${String.fromCharCode(65 + (localSeed % 26))}.`;
    const city = pick(base.cities, localSeed >>> 3);
    const age = 18 + (localSeed % 17); // 18-34

    const connection = clamp(92 - i * Math.floor(50 / Math.max(1, count - 1)) + (localSeed % 5), 42, 97);
    const traitA = pick(base.traits, localSeed >>> 5);
    const traitB = pick(base.traits, localSeed >>> 7);
    const likeA = pick(base.likes, localSeed >>> 9);
    const likeB = pick(base.likes, localSeed >>> 11);
    const opinion = pick(base.opinions, localSeed >>> 13);

    const bioBits = [
      `Basé(e) à ${city}`,
      subKey ? `spécialité: ${SUB_LABELS[subKey] ?? subKey}` : base.label,
      `toujours partant(e) pour échanger`
    ];

    const bio = `${bioBits.join(' • ')}.`;
    const avatarLetter = name[0].toUpperCase();

    profiles.push({
      id: `${interestKey}-${subKey}-${i}`,
      name,
      age,
      city,
      connection,
      traits: Array.from(new Set([traitA, traitB])).slice(0, 2),
      likes: Array.from(new Set([likeA, likeB])).slice(0, 2),
      opinion,
      bio,
      avatarLetter
    });
  }

  profiles.sort((a, b) => b.connection - a.connection);
  return { base, profiles };
}

function renderList(profiles) {
  const list = document.getElementById('matchList');
  if (!list) return;
  list.innerHTML = '';

  profiles.forEach((p) => {
    const card = document.createElement('article');
    card.className = 'profile-card';
    card.tabIndex = 0;
    card.dataset.profileId = p.id;

    const tags = [
      ...p.traits.slice(0, 2).map(t => `<span class="tag">${t}</span>`),
      ...p.likes.slice(0, 2).map(t => `<span class="tag dark">❤️ ${t}</span>`)
    ].join('');

    card.innerHTML = `
      <div class="profile-head">
        <div class="avatar" aria-hidden="true">${p.avatarLetter}</div>
        <div class="profile-meta">
          <div class="profile-name">${escapeHtml(p.name)} <span style="opacity:.65; font-weight:800;">• ${p.age}</span></div>
          <div class="profile-loc">📍 ${escapeHtml(p.city)}</div>
        </div>
        <div class="score-pill">${p.connection}%</div>
      </div>
      <div class="profile-bio">${escapeHtml(p.bio)}</div>
      <div class="tags">${tags}</div>
    `;

    card.addEventListener('click', () => openProfileModal(p));
    card.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openProfileModal(p);
      }
    });

    list.appendChild(card);
  });
}

function escapeHtml(s) {
  return String(s)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function setHero({ count, interestLabel, subLabel }) {
  const titleEl = document.getElementById('matchTitle');
  const subtitleEl = document.getElementById('matchSubtitle');
  const countEl = document.getElementById('matchCount');
  const condEl = document.getElementById('matchCondition');

  if (titleEl) titleEl.textContent = 'Match';
  if (countEl) countEl.textContent = String(count);

  const conditionText = subLabel ? `${interestLabel} • ${subLabel}` : interestLabel;
  if (condEl) condEl.textContent = conditionText;

  if (subtitleEl) {
    subtitleEl.textContent = `${count} personnes partagent la même passion que toi. Clique pour voir les détails de chaque profil.`;
  }
}

function showLoading(ms = 900) {
  const overlay = document.getElementById('loadingOverlay');
  if (!overlay) return Promise.resolve();
  overlay.classList.add('active');
  return new Promise((resolve) => {
    window.setTimeout(() => {
      overlay.classList.remove('active');
      resolve();
    }, ms);
  });
}

function openProfileModal(profile) {
  const overlay = document.getElementById('profileModal');
  if (!overlay) return;

  const avatar = document.getElementById('modalAvatar');
  const title = document.getElementById('profileModalTitle');
  const subtitle = document.getElementById('modalSubtitle');
  const score = document.getElementById('modalScore');
  const body = document.getElementById('modalBody');

  if (avatar) avatar.textContent = profile.avatarLetter;
  if (title) title.textContent = profile.name;
  if (subtitle) subtitle.textContent = `📍 ${profile.city} • ${profile.age} ans • ${profile.traits.join(' • ')}`;
  if (score) score.textContent = `${profile.connection}%`;

  if (body) {
    body.innerHTML = `
      <div class="detail-card">
        <div class="detail-title">Description</div>
        <div class="detail-text">${escapeHtml(profile.bio)}</div>
      </div>
      <div class="detail-grid">
        <div class="detail-card">
          <div class="detail-title">Ce(ux) que j’aime particulièrement</div>
          <div class="detail-text">- ${escapeHtml(profile.likes[0])}<br>- ${escapeHtml(profile.likes[1] ?? profile.likes[0])}</div>
        </div>
        <div class="detail-card">
          <div class="detail-title">Mon avis sur Nexora</div>
          <div class="detail-text">${escapeHtml(profile.opinion)}</div>
        </div>
      </div>
      <div class="detail-card">
        <div class="detail-title">Niveau de connexion</div>
        <div class="detail-text">
          ${profile.connection >= 85 ? 'Très forte connexion. Vous avez des goûts très proches.' :
            profile.connection >= 70 ? 'Bonne connexion. Beaucoup de points communs.' :
            profile.connection >= 55 ? 'Connexion moyenne. Quelques points communs utiles.' :
            'Connexion faible. Mais parfois, c’est là que ça devient intéressant.'}
        </div>
      </div>
    `;
  }

  overlay.classList.add('active');
  overlay.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';

  // Keep selection for "discuter maintenant"
  window.__nxSelectedProfile = {
    name: profile.name,
    initials: profile.avatarLetter,
    city: profile.city,
    interest: qs('interest', ''),
    interestLabel: qs('interestLabel', ''),
    sub: qs('sub', ''),
    subLabel: qs('subLabel', '')
  };
}

function closeProfileModal() {
  const overlay = document.getElementById('profileModal');
  if (!overlay) return;
  overlay.classList.remove('active');
  overlay.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

function wireModalControls() {
  const overlay = document.getElementById('profileModal');
  const closeBtn = document.getElementById('modalCloseBtn');
  const backBtn = document.getElementById('modalBackBtn');
  const selectBtn = document.getElementById('modalSelectBtn');

  function onSelect() {
    closeProfileModal();
    const p = window.__nxSelectedProfile;
    if (!p?.name) return;

    const qs2 = new URLSearchParams({
      peer: p.name,
      initials: p.initials || p.name.slice(0, 1).toUpperCase(),
      city: p.city || ''
    });
    window.location.href = `/chat?${qs2.toString()}`;
  }

  closeBtn?.addEventListener('click', closeProfileModal);
  backBtn?.addEventListener('click', closeProfileModal);
  selectBtn?.addEventListener('click', onSelect);

  overlay?.addEventListener('click', (e) => {
    if (e.target === overlay) closeProfileModal();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeProfileModal();
  });
}

document.addEventListener('DOMContentLoaded', async () => {
  createParticles();
  setupMobileMenu();
  wireModalControls();

  const interestKey = qs('interest', 'musique');
  const interestLabel = qs('interestLabel', DATA_BY_INTEREST[interestKey]?.label ?? 'Musique');
  const subKey = qs('sub', '');
  const subLabel = qs('subLabel', subKey ? (SUB_LABELS[subKey] ?? subKey) : '');
  const count = clamp(toInt(qs('count', '20'), 20), 1, 50);

  setHero({ count, interestLabel, subLabel });

  await showLoading(950);

  const { profiles } = buildProfiles({ interestKey, subKey, count });
  renderList(profiles);
});

