/* ============================================================
   NEXORA — À propos : révélation au scroll + compteurs animés
   ============================================================ */

document.addEventListener("DOMContentLoaded", () => {

  /* ---------- 1. Révélation progressive des sections au scroll ----------
     Chaque élément [data-po-reveal] est invisible par défaut (voir
     propos.css). Dès qu'il entre dans le champ de vision, on ajoute
     .is-visible qui déclenche la transition CSS (fade + translateY). */
  const revealTargets = document.querySelectorAll("[data-po-reveal]");

  if ("IntersectionObserver" in window && revealTargets.length) {
    const revealObserver = new IntersectionObserver(
      (entries, observer) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target); // une seule fois, pas besoin de répéter
          }
        });
      },
      { threshold: 0.15, rootMargin: "0px 0px -60px 0px" }
    );

    revealTargets.forEach((el) => revealObserver.observe(el));
  } else {
    // Navigateur trop ancien : on affiche tout directement, pas d'animation.
    revealTargets.forEach((el) => el.classList.add("is-visible"));
  }

  /* ---------- 2. Compteurs animés (section "Chiffres clés") ----------
     Chaque [data-count-to] compte de 0 jusqu'à sa valeur cible, une
     seule fois, dès qu'il devient visible à l'écran. */
  const counters = document.querySelectorAll("[data-count-to]");

  function animateCounter(el) {
    const target = parseInt(el.dataset.countTo, 10) || 0;
    const duration = 1400; // ms
    const startTime = performance.now();

    function tick(now) {
      const progress = Math.min((now - startTime) / duration, 1);
      // easeOutCubic : démarre vite, ralentit en douceur vers la fin
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(eased * target);
      if (progress < 1) {
        requestAnimationFrame(tick);
      } else {
        el.textContent = target;
      }
    }
    requestAnimationFrame(tick);
  }

  if ("IntersectionObserver" in window && counters.length) {
    const counterObserver = new IntersectionObserver(
      (entries, observer) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animateCounter(entry.target);
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.5 }
    );
    counters.forEach((el) => counterObserver.observe(el));
  } else {
    counters.forEach((el) => { el.textContent = el.dataset.countTo; });
  }
});