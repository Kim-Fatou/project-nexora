/* ============================================================
   NEXORA — Dashboards : petites animations d'entrée
   ============================================================ */

document.addEventListener("DOMContentLoaded", () => {
  // Anime légèrement les mini-graphiques en barres à l'affichage
  document.querySelectorAll(".mini-bars span").forEach((bar) => {
    const target = bar.style.height;
    bar.style.height = "0%";
    requestAnimationFrame(() => {
      setTimeout(() => { bar.style.height = target; }, 80);
    });
  });

  // Anime la barre de progression de niveau
  document.querySelectorAll(".level-fill").forEach((fill) => {
    const target = fill.style.width;
    fill.style.width = "0%";
    requestAnimationFrame(() => {
      setTimeout(() => { fill.style.width = target; }, 150);
    });
  });
});