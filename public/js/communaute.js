/* ============================================================
   NEXORA — Communauté : halo curseur, ticker, galerie draggable,
   boutons magnétiques, compteurs animés, révélation au scroll
   ============================================================ */

document.addEventListener("DOMContentLoaded", () => {

  /* ---------- 1. Halo qui suit la souris (avec un léger retard) ---------- */
  const glow = document.getElementById("cmGlow");
  if (glow) {
    let mouseX = window.innerWidth / 2, mouseY = window.innerHeight / 2;
    let glowX = mouseX, glowY = mouseY;

    document.addEventListener("mousemove", (e) => {
      mouseX = e.clientX; mouseY = e.clientY;
    });

    function animateGlow() {
      // Interpolation douce : le halo "rattrape" la souris progressivement
      glowX += (mouseX - glowX) * 0.08;
      glowY += (mouseY - glowY) * 0.08;
      glow.style.transform = `translate(${glowX}px, ${glowY}px) translate(-50%, -50%)`;
      requestAnimationFrame(animateGlow);
    }
    requestAnimationFrame(animateGlow);
  }

  /* ---------- 2. Révélation des sections/cartes au scroll ---------- */
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) entry.target.classList.add("is-visible");
    });
  }, { threshold: 0.2 });
  document.querySelectorAll("[data-cm-reveal]").forEach((el) => revealObserver.observe(el));

  // Cascade légère sur les cartes de témoignages
  document.querySelectorAll(".cm-testi-card").forEach((card, i) => {
    card.style.transitionDelay = `${i * 0.1}s`;
  });

  /* ---------- 3. Galerie horizontale : glisser à la souris ---------- */
  const gallery = document.getElementById("cmGallery");
  if (gallery) {
    let isDown = false, startX, scrollLeft;

    gallery.addEventListener("mousedown", (e) => {
      isDown = true;
      gallery.classList.add("is-dragging");
      startX = e.pageX - gallery.offsetLeft;
      scrollLeft = gallery.scrollLeft;
    });
    ["mouseleave", "mouseup"].forEach((evt) =>
      gallery.addEventListener(evt, () => { isDown = false; gallery.classList.remove("is-dragging"); })
    );
    gallery.addEventListener("mousemove", (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - gallery.offsetLeft;
      gallery.scrollLeft = scrollLeft - (x - startX) * 1.4;
    });
  }

  /* ---------- 4. Boutons magnétiques (suivent le curseur dans leurs limites) ---------- */
  document.querySelectorAll("[data-cm-magnet]").forEach((btn) => {
    btn.addEventListener("mousemove", (e) => {
      const rect = btn.getBoundingClientRect();
      const x = e.clientX - rect.left - rect.width / 2;
      const y = e.clientY - rect.top - rect.height / 2;
      btn.style.transform = `translate(${x * 0.3}px, ${y * 0.4}px)`;
    });
    btn.addEventListener("mouseleave", () => {
      btn.style.transform = "translate(0, 0)";
    });
  });

  /* ---------- 5. Compteurs animés (0 → valeur finale) ---------- */
  function animateCount(el) {
    const target = parseInt(el.dataset.countTo, 10);
    const duration = 1400;
    const start = performance.now();
    function tick(now) {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(target * eased).toLocaleString("fr-FR");
      if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }
  const statsObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        animateCount(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.6 });
  document.querySelectorAll("[data-count-to]").forEach((el) => statsObserver.observe(el));
});