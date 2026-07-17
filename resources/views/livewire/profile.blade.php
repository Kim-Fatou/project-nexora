<?php

use function Livewire\Volt\{state, mount, layout};
use Illuminate\Support\Facades\Auth;
use App\Models\Language;

layout('layouts.app');

state([
    // Drapeau : détermine si on affiche le formulaire (première visite,
    // profil pas encore complété) ou la vue "carte de profil" (profil déjà
    // rempli — utile plus tard si cette page sert aussi de "Mon profil").
    'isOnboarding' => fn () => is_null(Auth::user()->username),

    // Valeurs actuelles de l'utilisateur, pour pré-remplir le formulaire
    // s'il revient corriger son profil.
    'existing' => fn () => [
        'location' => Auth::user()->location,
        'handle' => Auth::user()->username,
        'bio' => Auth::user()->bio,
        'goals' => Auth::user()->goals,
        'idealPartner' => Auth::user()->ideal_partner,
        'nativeLanguageId' => Auth::user()->languages()->wherePivot('is_native', true)->first()?->id,
        'otherLanguageIds' => Auth::user()->languages()->wherePivot('is_native', false)->pluck('languages.id'),
    ],

    // Liste complète des langues disponibles (table de référence) : c'est
    // NOUS qui fournissons la liste, l'utilisateur choisit dedans — pas de
    // saisie libre, ça évite les doublons/fautes ("Anglais" vs "anglais").
    'languages' => fn () => Language::orderBy('name')->get(['id', 'name'])->toArray(),

    // Centres d'intérêt déjà choisis sur /interests — affichés en lecture
    // seule sur la page profil, jamais ressaisis ici.
    'userInterests' => fn () => Auth::user()->interests()->get(['interests.id', 'interests.name', 'interests.slug'])->toArray(),
]);

// -----------------------------------------------------------------------
// Sauvegarde le profil et synchronise les langues.
// Appelée depuis app.js via Livewire.find(...).call('saveProfile', data)
// -----------------------------------------------------------------------
$saveProfile = function ($data) {
    $user = Auth::user();
    $wasOnboarding = is_null($user->username);

    $validator = validator($data, [
        'location' => ['required', 'string', 'max:255'],
        'handle' => [
            'required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_\.]+$/',
            \Illuminate\Validation\Rule::unique('users', 'username')->ignore($user->id),
        ],
        'bio' => ['required', 'string', 'min:10', 'max:280'],
        'goals' => ['nullable', 'string', 'max:255'],
        'idealPartner' => ['nullable', 'string', 'max:280'],
        'nativeLanguageId' => ['required', 'integer', 'exists:languages,id'],
        'otherLanguageIds' => ['array'],
        'otherLanguageIds.*' => ['integer', 'exists:languages,id'],
    ]);

    if ($validator->fails()) {
        return ['success' => false, 'errors' => $validator->errors()->all()];
    }

    $validated = $validator->validated();

    $user->update([
        'location' => $validated['location'],
        'username' => $validated['handle'],
        'bio' => $validated['bio'],
        'goals' => $validated['goals'] ?? null,
        'ideal_partner' => $validated['idealPartner'] ?? null,
    ]);

    $syncData = [$validated['nativeLanguageId'] => ['is_native' => true]];
    foreach ($validated['otherLanguageIds'] ?? [] as $langId) {
        if ($langId != $validated['nativeLanguageId']) {
            $syncData[$langId] = ['is_native' => false];
        }
    }
    $user->languages()->sync($syncData);
    $user->checkAndAwardProfileCompletionXp();

    return [
        'success' => true,
        'redirect' => $wasOnboarding ? route('companion') : route('profile'),
    ];
};
?>

<div class="profile-shell" id="profile-component" wire:id="profile">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">

    {{-- Fond décoratif : mêmes "auras" que login/companion, pour rester cohérent visuellement --}}
    <div class="auras" aria-hidden="true">
        <span class="aura a1"></span>
        <span class="aura a2"></span>
    </div>

    {{-- Barre du haut : logo + navigation entre "form" et "profile" (purement visuel côté JS) --}}
    <header class="pf-top">
        <div class="pf-brand">
            <img src="{{ asset('images/logos/logonexora.png') }}" alt="Nexora Logo" style="height: 40px; width: auto; object-fit: contain; display: block;" />
        </div>
        <nav class="pf-top-nav">
            <span class="pf-chip {{ $isOnboarding ? 'is-on' : '' }}" data-chip="form">Onboarding</span>
            <span class="pf-chip {{ $isOnboarding ? '' : 'is-on' }}" data-chip="profile">Profil</span>
        </nav>
    </header>

    <main class="pf-main" id="pfMain">

        {{-- ============ FORMULAIRE (2 étapes) ============ --}}
        <section class="pf-glass pf-form" id="formView" {{ $isOnboarding ? '' : 'hidden' }}>

            @if (session()->has('error'))
                <div class="pf-alert">{{ session('error') }}</div>
            @endif

            <div class="pf-form-head">
                <span class="pf-eyebrow" id="stepEyebrow">Étape 1 sur 2</span>
                <h1 class="pf-h1">Compose ton profil <em>Nexora</em></h1>
                <p class="pf-sub">Prends le temps de te raconter — les bonnes rencontres commencent ici.</p>
                <div class="pf-stepper" id="stepper"></div>
            </div>

            {{-- Étape 1 : informations (pas de prénom/âge — anonymat) --}}
            <div class="pf-step-panel" data-step="0">
                <div class="pf-section-body">
                    <div class="pf-grid-2">
                        <label class="pf-field">
                            <span class="pf-field-label">Localisation</span>
                            <input class="pf-input" data-k="location" placeholder="Bamako, Mali" />
                        </label>
                        <label class="pf-field">
                            <span class="pf-field-label">Identifiant unique</span>
                            <input class="pf-input" data-k="handle" placeholder="mamoutou_diallo" />
                        </label>
                    </div>
                    <label class="pf-field">
                        <span class="pf-field-row">
                            <span class="pf-field-label">Parle-nous de toi</span>
                            <span class="pf-field-counter" id="bioCounter">0/280</span>
                        </span>
                        <textarea class="pf-input pf-textarea" maxlength="280" rows="3" data-k="bio"
                            placeholder="Sujets, passions, ce qui t'anime…"></textarea>
                    </label>
                    <label class="pf-field">
                        <span class="pf-field-label">Objectifs pro & apprentissage</span>
                        <input class="pf-input" data-k="goals" placeholder="Devenir ingénieur en IA…" />
                    </label>
                </div>
            </div>

            {{-- Étape 2 : préférences --}}
            <div class="pf-step-panel" data-step="1" hidden>
                <div class="pf-section-body">
                    <div>
                        <h2 class="pf-h3">Mode de communication</h2>
                        {{-- Un seul mode : Nexora est pensé pour l'anonymat, donc pas de
                             vidéo ni de rencontre "en personne" — juste texte & audio. --}}
                        <div class="pf-comm-mode">
                            <span class="pf-comm-ic"><i class="fa-solid fa-comments" style="color: #6b1f2a;"></i></span>
                            <span class="pf-comm-body">
                                <span class="pf-comm-title">Messages & audio</span>
                                <span class="pf-comm-desc">Tchat et notes vocales — pour préserver ton anonymat, c'est le seul mode d'échange sur Nexora.</span>
                            </span>
                        </div>
                    </div>

                    <label class="pf-field">
                        <span class="pf-field-label">Ton partenaire d'échange idéal ?</span>
                        <textarea class="pf-input pf-textarea" rows="3" data-k="idealPartner"
                            placeholder="Curieux, bienveillant, passionné par la tech…"></textarea>
                    </label>

                    <div>
                        <h2 class="pf-h3">Langues</h2>
                        <div class="pf-grid-2">
                            <label class="pf-field">
                                <span class="pf-field-label">Langue native</span>
                                <select class="pf-input" id="nativeLangSelect">
                                    <option value="">Choisir…</option>
                                    @foreach ($languages as $lang)
                                        <option value="{{ $lang['id'] }}">{{ $lang['name'] }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="pf-field">
                                <span class="pf-field-label">Ajouter une langue</span>
                                <div class="pf-inline">
                                    <select class="pf-input" id="otherLangSelect">
                                        <option value="">Choisir…</option>
                                        @foreach ($languages as $lang)
                                            <option value="{{ $lang['id'] }}" data-name="{{ $lang['name'] }}">{{ $lang['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="pf-btn pf-btn-ghost" id="addLang">Ajouter</button>
                                </div>
                            </label>
                        </div>
                        <div class="pf-tags" id="langTags" style="margin-top:12px"></div>
                    </div>
                </div>
            </div>

            <div class="pf-form-cta is-first" id="formCta">
                <div class="pf-form-error" id="formError" hidden></div>
                <button type="button" class="pf-btn pf-btn-ghost" id="backBtn" hidden>← Retour</button>
                <button type="button" class="pf-btn pf-btn-primary" id="nextBtn">
                    <span id="nextLabel">Suivant</span> <span>→</span>
                </button>
            </div>
        </section>

        {{-- ============ VUE PROFIL (bento) ============ --}}
        <section class="pf-profile" id="profileView" {{ $isOnboarding ? 'hidden' : '' }}>
            <div class="pf-glass pf-hero">
                <div class="pf-avatar-wrap">
                    <div class="pf-avatar"><span class="pf-avatar-fallback" id="avatarInitial">N</span></div>
                    <span class="pf-avatar-ring"></span>
                    <span class="pf-status"><span class="pf-status-dot"></span> En ligne</span>
                </div>
                {{-- Identifiant unique à la place de "prénom, âge" : l'anonymat d'abord --}}
                <h1 class="pf-h1 pf-hero-name" id="heroName"></h1>
                <p class="pf-sub pf-hero-handle" id="heroHandle"></p>
                <div class="pf-hero-cta">
                    <a href="{{ route('companion') }}" class="pf-btn pf-btn-primary" id="continueBtn">Continuer →</a>
                    <button class="pf-btn pf-btn-ghost" id="editBtn"><i class="fa-solid fa-pen-to-square"></i> Modifier le profil</button>
                </div>
            </div>

            <div class="pf-bento">
                <div class="pf-glass pf-cell pf-cell-about">
                    <h3 class="pf-h3">À propos</h3>
                    <ul class="pf-meta" id="aboutMeta"></ul>
                    <div class="pf-block"><span class="pf-label">De quoi j'aime parler</span>
                        <p class="pf-text" id="aboutBio"></p></div>
                    <div class="pf-block"><span class="pf-label">Objectifs</span>
                        <p class="pf-text" id="aboutGoals"></p></div>
                </div>

                <div class="pf-glass pf-cell pf-cell-interests">
                    <div class="pf-cell-head"><h3 class="pf-h3">Passions & intérêts</h3>
                        <span class="pf-count" id="interestsCount">0</span></div>
                    {{-- Rempli en JS depuis $userInterests (données réelles de /interests) --}}
                    <div class="pf-bubbles pf-bubbles-showcase" id="interestsShowcase"></div>
                </div>

                <div class="pf-glass pf-cell pf-cell-prefs">
                    <h3 class="pf-h3">Préférences</h3>
                    <div class="pf-pref-mini"><span class="pf-tag"><i class="fa-solid fa-comments"></i> Texte & audio</span></div>
                    <div class="pf-block"><span class="pf-label">Partenaire idéal</span>
                        <p class="pf-text" id="idealPartnerOut"></p></div>
                    <div class="pf-divider"></div>
                    <h3 class="pf-h3">Références</h3>
                    {{-- Pas encore de système de témoignages entre utilisateurs :
                         état vide honnête plutôt qu'une fausse démo. --}}
                    <div class="pf-ref-empty">
                        Personne n'a encore laissé de référence. Bientôt, les personnes avec qui tu échanges pourront écrire un mot sur toi ici.
                    </div>
                </div>

                <div class="pf-glass pf-cell pf-cell-lang">
                    <h3 class="pf-h3">Langues</h3>
                    <div class="pf-lang">
                        <div class="pf-lang-primary">
                            <span class="pf-label">Native</span>
                            <span class="pf-lang-name" id="langNative"></span>
                        </div>
                        <div class="pf-tags" id="langOthers"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Petit script inline : seulement des données, aucun texte ressemblant
         à une balise HTML, donc ça ne perturbe pas la détection Livewire
         "un seul élément racine" (on a eu ce bug sur companion.blade.php). --}}
    <script>
        window.profileData = {
            existing: @json($existing),
            languages: @json($languages),
            userInterests: @json($userInterests),
            isOnboarding: @json($isOnboarding),
        };
    </script>
    <script src="{{ asset('js/profile.js') }}"></script>
</div>