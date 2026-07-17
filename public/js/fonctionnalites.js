/* ============================================================
   NEXORA — Fonctionnalités : scroll actif + apparition douce
   ============================================================ */

document.addEventListener("DOMContentLoaded", () => {

  // Réutilise le scroll doux déjà préparé sur l'accueil (acceuil.js
  // ajoute déjà "nexora-snap" sur <html>, on l'active ici aussi
  // au cas où cette page est ouverte directement).
  document.documentElement.classList.add("nexora-snap");

  const sections = document.querySelectorAll(".feat-section");
  const indexLinks = document.querySelectorAll(".feat-index a");

  // ---------- 1. Fait apparaître chaque section en douceur à son entrée dans l'écran ----------
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) entry.target.classList.add("is-visible");
    });
  }, { threshold: 0.25 });
  sections.forEach((s) => revealObserver.observe(s));

  // ---------- 2. Met en surbrillance le lien actif dans l'index latéral ----------
  const activeObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const id = entry.target.id;
        indexLinks.forEach((a) => {
          a.classList.toggle("is-active", a.dataset.target === id);
        });
      }
    });
  }, { threshold: 0.5 });
  sections.forEach((s) => activeObserver.observe(s));

  // ---------- 3. Clic sur l'index → scroll fluide vers la section ----------
  indexLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      document.getElementById(link.dataset.target)?.scrollIntoView({ behavior: "smooth", block: "start" });
    });
  });
});