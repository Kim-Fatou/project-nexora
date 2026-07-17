/* =========================================================
   NEXORA — Profil (onboarding + vue carte)
   - Formulaire 2 étapes (Informations, Préférences)
   - Langues choisies dans une liste fournie (pas de saisie libre)
   - Sauvegarde finale via Livewire (component.call('saveProfile', ...))
   - Bascule vers la vue "carte bento" en lecture seule
   ========================================================= */

const data = window.profileData || { existing: {}, languages: [], userInterests: [], isOnboarding: true };

const STEPS = ["Informations", "Préférences"];

/* ---------- 1. État local du formulaire, pré-rempli si profil existant ---------- */
const form = {
  location: data.existing.location || "",
  handle: data.existing.handle || "",
  bio: data.existing.bio || "",
  goals: data.existing.goals || "",
  idealPartner: data.existing.idealPartner || "",
  nativeLanguageId: data.existing.nativeLanguageId || null,
  otherLanguageIds: (data.existing.otherLanguageIds || []).map(Number),
};
let step = 0;

/* ---------- 2. Bind des champs texte ---------- */
document.querySelectorAll("[data-k]").forEach((el) => {
  const k = el.getAttribute("data-k");
  el.value = form[k] ?? "";
  el.addEventListener("input", () => {
    form[k] = el.value;
    if (k === "bio") document.getElementById("bioCounter").textContent = `${el.value.length}/280`;
    updateNext();
  });
});
document.getElementById("bioCounter").textContent = `${form.bio.length}/280`;

/* ---------- 3. Stepper (2 étapes) ---------- */
const stepperEl = document.getElementById("stepper");
function renderStepper() {
  stepperEl.innerHTML = "";
  STEPS.forEach((label, i) => {
    const dot = document.createElement("div");
    dot.className = "pf-step-dot" + (i === step ? " is-on" : "") + (i < step ? " is-done" : "");
    dot.innerHTML = `<span class="pf-step-bubble">${i < step ? "✓" : i + 1}</span><span class="pf-step-label">${label}</span>`;
    stepperEl.appendChild(dot);
    if (i < STEPS.length - 1) {
      const line = document.createElement("span");
      line.className = "pf-step-line" + (i < step ? " is-done" : "");
      stepperEl.appendChild(line);
    }
  });
  document.getElementById("stepEyebrow").textContent = `Étape ${step + 1} sur ${STEPS.length}`;
  document.querySelectorAll(".pf-step-panel").forEach((p) => { p.hidden = Number(p.dataset.step) !== step; });
  document.getElementById("backBtn").hidden = step === 0;
  document.getElementById("formCta").classList.toggle("is-first", step === 0);
  const isLast = step === STEPS.length - 1;
  document.getElementById("nextLabel").textContent = isLast ? "Finaliser mon profil" : "Suivant";
  updateNext();
}

/* ---------- 4. Langues (sélection dans une liste fournie, pas de saisie libre) ---------- */
const nativeLangSelect = document.getElementById("nativeLangSelect");
const otherLangSelect = document.getElementById("otherLangSelect");
const langTags = document.getElementById("langTags");

if (form.nativeLanguageId) nativeLangSelect.value = form.nativeLanguageId;
nativeLangSelect.addEventListener("change", () => {
  form.nativeLanguageId = nativeLangSelect.value ? Number(nativeLangSelect.value) : null;
  // La langue native ne peut pas être aussi dans "autres langues"
  form.otherLanguageIds = form.otherLanguageIds.filter((id) => id !== form.nativeLanguageId);
  renderLangTags();
  updateNext();
});

function renderLangTags() {
  langTags.innerHTML = "";
  form.otherLanguageIds.forEach((id) => {
    const lang = data.languages.find((l) => l.id === id);
    if (!lang) return;
    const t = document.createElement("span");
    t.className = "pf-tag";
    t.innerHTML = `${lang.name}<button type="button" class="pf-tag-x" aria-label="Retirer">×</button>`;
    t.querySelector(".pf-tag-x").addEventListener("click", () => {
      form.otherLanguageIds = form.otherLanguageIds.filter((x) => x !== id);
      renderLangTags();
    });
    langTags.appendChild(t);
  });
}

document.getElementById("addLang").addEventListener("click", () => {
  const id = Number(otherLangSelect.value);
  if (!id) return;
  if (id === form.nativeLanguageId) { alert("Cette langue est déjà ta langue native."); return; }
  if (form.otherLanguageIds.includes(id)) return;
  form.otherLanguageIds.push(id);
  otherLangSelect.value = "";
  renderLangTags();
});
renderLangTags();

/* ---------- 5. Validation & navigation ---------- */
function isStepValid() {
  if (step === 0) return form.location.trim() !== "" && form.handle.trim() !== "" && form.bio.trim().length > 10;
  if (step === 1) return !!form.nativeLanguageId;
  return true;
}
function updateNext() { document.getElementById("nextBtn").disabled = !isStepValid(); }

document.getElementById("nextBtn").addEventListener("click", () => {
  if (!isStepValid()) return;
  if (step < STEPS.length - 1) { step += 1; renderStepper(); }
  else submitProfile();
});
document.getElementById("backBtn").addEventListener("click", () => {
  if (step > 0) { step -= 1; renderStepper(); }
});
document.getElementById("editBtn").addEventListener("click", showForm);

/* ---------- 6. Sauvegarde finale via Livewire ---------- */
function submitProfile() {
  const btn = document.getElementById("nextBtn");
  const label = btn.querySelector("#nextLabel");
  btn.disabled = true;
  label.textContent = "Enregistrement…";

  const component = Livewire.find(
    document.querySelector('[wire\\:id]').getAttribute('wire:id')
  );
  if (!component) return;

  component.call('saveProfile', {
    location: form.location,
    handle: form.handle,
    bio: form.bio,
    goals: form.goals,
    idealPartner: form.idealPartner,
    nativeLanguageId: form.nativeLanguageId,
    otherLanguageIds: form.otherLanguageIds,
  }).then((result) => {
    if (result && result.success) {
      window.location.href = result.redirect;
    } else {
      btn.disabled = false;
      label.textContent = "Finaliser mon profil";
      const message = (result && result.errors && result.errors[0])
        || "Une erreur est survenue, vérifie tes informations.";
      alert(message);
    }
  }).catch((error) => {
    btn.disabled = false;
    label.textContent = "Finaliser mon profil";
    alert("Une erreur est survenue pendant l'enregistrement. Réessaie.");
    console.error(error);
  });
}

/* ---------- 7. Transitions entre vue formulaire et vue profil ---------- */
const main = document.getElementById("pfMain");
const chips = document.querySelectorAll("[data-chip]");
function setChip(name) { chips.forEach((c) => c.classList.toggle("is-on", c.dataset.chip === name)); }

function showProfile() {
  main.classList.add("is-fading");
  setTimeout(() => {
    document.getElementById("formView").hidden = true;
    document.getElementById("profileView").hidden = false;
    renderProfile();
    main.classList.remove("is-fading");
    setChip("profile");
    window.scrollTo({ top: 0, behavior: "smooth" });
  }, 380);
}
function showForm() {
  main.classList.add("is-fading");
  setTimeout(() => {
    document.getElementById("formView").hidden = false;
    document.getElementById("profileView").hidden = true;
    renderStepper();
    main.classList.remove("is-fading");
    setChip("form");
    window.scrollTo({ top: 0, behavior: "smooth" });
  }, 380);
}

/* ---------- 8. Rendu de la vue profil (bento), lecture seule ---------- */
function renderProfile() {
  document.getElementById("avatarInitial").textContent = (form.handle.replace('@', '')[0] || "N").toUpperCase();
  // Identifiant unique à la place de "prénom, âge" — anonymat respecté
  document.getElementById("heroName").textContent = form.handle;
  document.getElementById("heroHandle").textContent = form.location;

  const d = new Date();
  const localTime = `${String(d.getHours()).padStart(2, "0")}:${String(d.getMinutes()).padStart(2, "0")}`;
  document.getElementById("aboutMeta").innerHTML = `
    <li>📍 <span>${escapeHtml(form.location)}</span></li>
    <li>🕒 <span>Heure locale · ${localTime}</span></li>
    <li>@ <span>${escapeHtml(form.handle)}</span></li>`;
  document.getElementById("aboutBio").textContent = form.bio;
  document.getElementById("aboutGoals").textContent = form.goals || "—";

  // Passions & intérêts : données réelles venant de /interests, jamais ressaisies ici
  document.getElementById("interestsCount").textContent = data.userInterests.length;
  const show = document.getElementById("interestsShowcase");
  show.innerHTML = "";
  data.userInterests.forEach((it) => {
    const s = document.createElement("span");
    s.className = "pf-bubble is-on pf-bubble-lg";
    s.textContent = it.name;
    show.appendChild(s);
  });

  document.getElementById("idealPartnerOut").textContent = form.idealPartner || "—";

  const nativeLang = data.languages.find((l) => l.id === form.nativeLanguageId);
  document.getElementById("langNative").textContent = nativeLang ? nativeLang.name : "—";
  const others = document.getElementById("langOthers");
  others.innerHTML = "";
  form.otherLanguageIds.forEach((id) => {
    const lang = data.languages.find((l) => l.id === id);
    if (!lang) return;
    const s = document.createElement("span");
    s.className = "pf-tag";
    s.textContent = lang.name;
    others.appendChild(s);
  });
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
}

/* ---------- 9. Démarrage ---------- */
if (data.isOnboarding) {
  renderStepper();
} else {
  renderProfile();
}