(function () {
  const userName = window.companionData.userName;
  const userInterests = window.companionData.userInterests;
  const chatUrl = window.companionData.chatUrl;

  const chatMessages = document.getElementById('chatMessages');
  const flowActions = document.getElementById('flowActionsContainer');
  const stepDots = document.querySelectorAll('#stepProgress .step-dot');

  // ===== Fond vidéo qui alterne =====
  const videos = document.querySelectorAll('#bgVideoLayer video');
  let currentVideo = 0;
  let videosAvailable = false;

  videos.forEach((v, i) => {
    v.addEventListener('loadeddata', () => {
      videosAvailable = true;
      if (i === 0) {
        v.classList.add('is-active');
        v.play().catch(() => {});
      }
    });
    v.addEventListener('error', () => { /* fichier absent : on ignore, le dégradé reste visible */ });
  });

  function cycleVideo() {
    if (!videosAvailable || videos.length < 2) return;
    const next = (currentVideo + 1) % videos.length;
    videos[next].currentTime = 0;
    videos[next].play().catch(() => {});
    videos[next].classList.add('is-active');
    videos[currentVideo].classList.remove('is-active');
    currentVideo = next;
  }
  setInterval(cycleVideo, 9000);

  // ===== Logique de conversation =====
  const avatarIcon = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>`;

  function scrollBottom() { chatMessages.scrollTop = chatMessages.scrollHeight; }

  function addCompanionMessage(text) {
    const div = document.createElement('div');
    div.className = 'message message-companion';
    div.innerHTML = `<span class="message-avatar">${avatarIcon}</span><div class="message-bubble">${text}</div>`;
    chatMessages.appendChild(div);
    scrollBottom();
  }

  function addUserMessage(text) {
    const div = document.createElement('div');
    div.className = 'message message-user';
    div.innerHTML = `<div class="message-bubble">${text}</div>`;
    chatMessages.appendChild(div);
    scrollBottom();
  }

  function showTyping() {
    const div = document.createElement('div');
    div.className = 'message message-companion';
    div.id = 'typingIndicator';
    div.innerHTML = `<span class="message-avatar">${avatarIcon}</span><div class="message-bubble typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>`;
    chatMessages.appendChild(div);
    scrollBottom();
  }
  function hideTyping() {
    const t = document.getElementById('typingIndicator');
    if (t) t.remove();
  }

  function showActions(buttons) {
    flowActions.innerHTML = '';
    flowActions.classList.add('active');
    buttons.forEach((btn, i) => {
      const el = document.createElement('button');
      el.type = 'button';
      el.className = `action-btn ${btn.variant || ''}`;
      el.textContent = btn.label;
      el.style.animationDelay = `${i * 0.08}s`;
      el.addEventListener('click', () => btn.onClick());
      flowActions.appendChild(el);
    });
  }
  function hideActions() {
    flowActions.innerHTML = '';
    flowActions.classList.remove('active');
  }

  function markStep(step) {
    stepDots.forEach(dot => {
      if (parseInt(dot.dataset.step, 10) <= step) dot.classList.add('is-done');
    });
  }

  function say(text, delay = 900) {
    return new Promise(resolve => {
      showTyping();
      setTimeout(() => {
        hideTyping();
        addCompanionMessage(text);
        resolve();
      }, delay);
    });
  }

  function goToChat() {
    window.location.href = chatUrl;
  }

  // ===== Les 5 étapes =====

  async function step1() {
    markStep(1);
    await say(`Bonjour ${userName} ! 👋 Ravi de te rencontrer.`, 700);

    const interestsList = userInterests.length ? userInterests.join(', ') : null;
    if (interestsList) {
      await say(`Je vois que tu as choisi : <strong>${interestsList}</strong>. Excellent choix, ça va te faire de belles rencontres ! ✨`);
    }

    await say(`Pour commencer, dis-moi : qu'est-ce qui te ferait le plus plaisir sur Nexora en ce moment ?`);
    showActions([
      { label: 'Discuter avec des gens', variant: 'primary', onClick: () => { hideActions(); addUserMessage('Discuter avec des gens'); step2('discuter'); } },
      { label: 'Découvrir du contenu', variant: 'secondary', onClick: () => { hideActions(); addUserMessage('Découvrir du contenu'); step2('decouvrir'); } },
    ]);
  }

  async function step2(wish) {
    markStep(2);
    if (wish === 'discuter') {
      await say(`Parfait, c'est exactement ce que Nexora fait de mieux ! Des gens qui partagent tes passions t'attendent déjà.`);
    } else {
      await say(`Top ! Tu vas pouvoir explorer plein de contenus liés à tes centres d'intérêt directement dans ton espace.`);
    }

    await say(`Tu préfères plutôt papoter en petit groupe, ou en tête-à-tête ?`);
    showActions([
      { label: 'En petit groupe', variant: 'primary', onClick: () => { hideActions(); addUserMessage('En petit groupe'); step3('groupe'); } },
      { label: 'En tête-à-tête', variant: 'secondary', onClick: () => { hideActions(); addUserMessage('En tête-à-tête'); step3('tete-a-tete'); } },
    ]);
  }

  async function step3(pref) {
    markStep(3);
    if (pref === 'groupe') {
      await say(`Bien noté ! Les salons de discussion par centre d'intérêt vont te plaire alors.`);
    } else {
      await say(`Compris ! Les échanges en privé, c'est aussi ce qu'on fait de mieux.`);
    }

    await say(`Une dernière chose : tu es plutôt actif(ve) le matin, l'après-midi, ou le soir ?`);
    showActions([
      { label: 'Matin', variant: 'secondary', onClick: () => { hideActions(); addUserMessage('Matin'); step4('matin'); } },
      { label: 'Après-midi', variant: 'secondary', onClick: () => { hideActions(); addUserMessage('Après-midi'); step4('apres-midi'); } },
      { label: 'Soir', variant: 'primary', onClick: () => { hideActions(); addUserMessage('Soir'); step4('soir'); } },
    ]);
  }

  async function step4(moment) {
    markStep(4);
    const moments = { matin: 'le matin', 'apres-midi': "l'après-midi", soir: 'le soir' };
    await say(`Noté, ${moments[moment]} alors ! Tu croiseras plein de monde connecté à ce moment-là.`);

    await say(`Tu es prêt(e) à voir qui partage vraiment tes passions ?`);
    showActions([
      { label: 'Oui, j\u2019ai hâte !', variant: 'primary', onClick: () => { hideActions(); addUserMessage('Oui, j\u2019ai hâte !'); step5(); } },
    ]);
  }

  async function step5() {
    markStep(5);
    await say(`Alors n'attendons plus... 🚀`);
    await say(`Ton chat t'attend, ${userName}. À tout de suite !`, 700);
    showActions([
      { label: 'Rejoindre mon chat', variant: 'primary', onClick: goToChat },
    ]);
  }

  step1();
})();