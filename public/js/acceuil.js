/* ============================================================
   NEXORA — Accueil : interactions
   ============================================================ */

document.addEventListener("DOMContentLoaded", () => {

  /* ---------- 1. Menu mobile (burger) ----------
     En dessous de 760px (voir acceuil.css), les liens de nav sont
     masqués et remplacés par ce bouton "burger". On bascule la
     classe "is-open" au clic pour afficher/masquer le menu.
  ------------------------------------------------------------- */
  const nav = document.getElementById("mainNav");
  const burger = document.getElementById("navBurger");
  const navLinks = document.getElementById("navLinks");

  if (burger && nav && navLinks) {
    burger.addEventListener("click", () => {
      const isOpen = navLinks.classList.toggle("is-open");
      nav.classList.toggle("is-open", isOpen); // anime le burger en croix
      burger.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });

    // Ferme le menu automatiquement après avoir cliqué un lien (mobile)
    navLinks.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        navLinks.classList.remove("is-open");
        nav.classList.remove("is-open");
        burger.setAttribute("aria-expanded", "false");
      });
    });
  }

  /* ---------- 2. Scroll fluide vers les ancres (#hero, etc.) ---------- */
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener("click", (e) => {
      const target = document.querySelector(link.getAttribute("href"));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  });

  /* ---------- 3. Démo interactive NEXIA (API Gemini) ----------
     Sur la page d'accueil (visiteur non connecté), on appelle l'API
     publique sécurisée et limitée en taux (rate-limited) de NEXIA,
     qui utilise Gemini pour répondre de manière dynamique.
     Après 3 messages, on invite à créer un compte pour continuer.
  ------------------------------------------------------------- */
  const form = document.getElementById("nexiaForm");
  const input = document.getElementById("nexiaInput");
  const chat = document.getElementById("nexiaChat");
  const hint = document.getElementById("nexiaHint");

  const REGISTER_URL = window.NEXORA_REGISTER_URL || "/register";

  let exchanges = 0; // compte le nombre d'échanges
  let history = []; // stocke l'historique local pour le contexte

  function addBubble(text, from) {
    const bubble = document.createElement("div");
    bubble.className = `nexia-bubble from-${from}`; // from-user ou from-nexia
    bubble.innerHTML = text.replace(/\n/g, '<br>'); // permet des sauts de ligne simples
    chat.appendChild(bubble);
    chat.scrollTop = chat.scrollHeight; // auto-scroll vers le bas
    return bubble;
  }

  if (form && input && chat) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const question = input.value.trim();
      if (!question) return;

      if (hint) hint.remove(); // retire le message d'accroche initial

      addBubble(question, "user");
      input.value = "";
      exchanges += 1;

      // Désactiver le champ pendant la saisie et afficher un indicateur de chargement
      input.disabled = true;
      const submitBtn = form.querySelector("button[type='submit']");
      if (submitBtn) submitBtn.disabled = true;

      const loadingBubble = document.createElement("div");
      loadingBubble.className = "nexia-bubble from-nexia loading";
      loadingBubble.innerHTML = '<span style="opacity: 0.6">NEXIA réfléchit...</span>';
      chat.appendChild(loadingBubble);
      chat.scrollTop = chat.scrollHeight;

      // CSRF Token
      const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
      const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute("content") : "";

      fetch("/nexia/ask", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
          message: question,
          history: history
        })
      })
        .then(response => {
          if (!response.ok) {
            throw new Error("Erreur réseau");
          }
          return response.json();
        })
        .then(data => {
          loadingBubble.remove();
          const reply = data.reply || "Désolé, je n'ai pas pu formuler de réponse.";
          addBubble(reply, "nexia");

          // Enregistrer l'historique
          history.push({ role: "user", content: question });
          history.push({ role: "model", content: reply });

          // Toujours réactiver l'input pour continuer la discussion indéfiniment
          input.disabled = false;
          if (submitBtn) submitBtn.disabled = false;
          input.focus();

          // Après 3 échanges, afficher une invitation discrète de création de compte sans bloquer le chat
          if (exchanges === 3) {
            const cta = document.createElement("a");
            cta.href = REGISTER_URL;
            cta.className = "btn-primary";
            cta.style.marginTop = "8px";
            cta.style.fontSize = "12.5px";
            cta.style.padding = "8px 14px";
            cta.style.display = "inline-block";
            cta.style.textAlign = "center";
            cta.style.whiteSpace = "normal";
            cta.textContent = "Rejoindre Nexora pour en voir plus →";
            chat.appendChild(cta);
            chat.scrollTop = chat.scrollHeight;
          }
        })
        .catch(error => {
          loadingBubble.remove();
          addBubble("Une erreur est survenue lors de la communication avec NEXIA. Réessaie dans un instant !", "nexia");
          input.disabled = false;
          if (submitBtn) submitBtn.disabled = false;
          input.focus();
        });
    });
  }
});