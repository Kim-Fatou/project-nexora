(() => {
  const marquee = document.querySelector('[data-marquee="testimonials"]');
  if (!marquee) return;

  const viewport = marquee.querySelector('.nx-marquee-viewport');
  const track = marquee.querySelector('.nx-marquee-track');
  if (!viewport || !track) return;

  const prefersReduced = window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches;
  if (prefersReduced) {
    marquee.classList.add('nx-marquee--reduced');
    return;
  }

  let pausedByClick = false;
  let activeCard = null;

  function setPaused(paused) {
    marquee.classList.toggle('is-paused', paused);
  }

  function stopForInteraction() {
    setPaused(true);
  }

  function resumeIfAllowed() {
    if (pausedByClick) return;
    setPaused(false);
  }

  // Pause on hover / pointer interactions
  marquee.addEventListener('mouseenter', stopForInteraction);
  marquee.addEventListener('mouseleave', resumeIfAllowed);
  marquee.addEventListener('pointerdown', stopForInteraction);
  marquee.addEventListener('pointerup', resumeIfAllowed);

  // Click / keyboard highlight on a card
  function setActiveCard(card) {
    if (activeCard === card) {
      activeCard.classList.remove('is-active');
      activeCard = null;
      pausedByClick = false;
      resumeIfAllowed();
      return;
    }

    if (activeCard) activeCard.classList.remove('is-active');
    activeCard = card;
    activeCard.classList.add('is-active');
    pausedByClick = true;
    setPaused(true);
  }

  track.addEventListener('click', (e) => {
    const card = e.target.closest('.nx-tcard');
    if (!card) return;
    setActiveCard(card);
  });

  track.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    const card = e.target.closest('.nx-tcard');
    if (!card) return;
    e.preventDefault();
    setActiveCard(card);
  });

  // Duplicate content to ensure seamless infinite loop
  function duplicateUntilFilled() {
    // Clear existing clones (if any)
    track.querySelectorAll('[data-clone="1"]').forEach((n) => n.remove());

    const originals = Array.from(track.children);
    const originalWidth = track.scrollWidth;
    if (!originals.length || originalWidth === 0) return;

    // Ensure at least 2 sets, plus enough to cover big screens
    let neededWidth = viewport.clientWidth * 2.2;
    let currentWidth = originalWidth;

    while (currentWidth < neededWidth) {
      for (const node of originals) {
        const clone = node.cloneNode(true);
        clone.setAttribute('data-clone', '1');
        clone.setAttribute('tabindex', '-1'); // avoid tabbing into clones
        track.appendChild(clone);
      }
      currentWidth = track.scrollWidth;
      if (currentWidth > viewport.clientWidth * 6) break; // safety
    }

    return currentWidth;
  }

  function setDuration(pxPerSecond = 70) {
    // Duration based on half of the track width (one "cycle")
    // We animate translateX from 0 to -50% (CSS), so we map to actual px.
    const full = track.scrollWidth;
    const half = full / 2;
    const seconds = Math.max(18, Math.min(60, half / pxPerSecond));
    marquee.style.setProperty('--nx-marquee-duration', `${seconds}s`);
  }

  function setup() {
    const fullWidth = duplicateUntilFilled();
    if (!fullWidth) return;
    setDuration(72);
    setPaused(false);
  }

  // Recompute on resize for responsiveness
  let raf = 0;
  function onResize() {
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(setup);
  }

  window.addEventListener('resize', onResize, { passive: true });
  window.addEventListener('load', setup);
  setup();
})();

