@extends('layouts.app')

@section('content')
<div class="auth-shell">
  <div class="auras">
    <span class="aura a1"></span>
    <span class="aura a2"></span>
    <span class="aura a3"></span>
  </div>

  <div class="pills" aria-hidden="true">
    <div class="pill tinted" style="top:8%;left:6%;--r:-8deg"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l2 5 5 2-5 2-2 5-2-5-5-2 5-2 2-5z"/></svg>Salut !</div>
    <div class="pill" style="top:10%;left:22%;--r:-3deg"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>Musique</div>
    <div class="pill tinted" style="top:26%;right:3%;--r:4deg"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 12h4M10 10v4M16 11h.01M18 13h.01"/></svg>Gaming</div>
    <div class="pill tinted" style="top:64%;right:14%;--r:-7deg"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>Chat</div>
  </div>

  <main class="main">
    <div class="card-wrap">
      <div class="card">
        <div class="brand">
          <div class="brand-badge">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <h1 class="font-display">Rejoins Nexora</h1>
          <p>Crée ton compte en quelques secondes</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
          @csrf
          <div class="social-row">
            <a href="{{ route('social.redirect', 'google') }}" class="social" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
              <svg viewBox="0 0 24 24" width="16" height="16"><path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.4-1.6 4.1-5.5 4.1-3.3 0-6-2.7-6-6.1s2.7-6.1 6-6.1c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.7 3.4 14.6 2.5 12 2.5 6.8 2.5 2.6 6.8 2.6 12s4.2 9.5 9.4 9.5c5.4 0 9-3.8 9-9.2 0-.6-.1-1.1-.2-1.6H12z"/></svg>
              Google
            </a>
            <a href="{{ route('social.redirect', 'apple') }}" class="social" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16.4 12.7c0-2.4 2-3.6 2.1-3.6-1.1-1.7-2.9-1.9-3.5-1.9-1.5-.2-2.9.9-3.7.9-.8 0-1.9-.9-3.1-.8-1.6 0-3.1.9-3.9 2.4-1.7 2.9-.4 7.2 1.2 9.6.8 1.2 1.7 2.5 3 2.4 1.2 0 1.7-.8 3.1-.8 1.4 0 1.9.8 3.1.8 1.3 0 2.1-1.2 2.9-2.4.6-.9 1-1.7 1.4-2.7-2.6-1-2.6-3.9-2.6-3.9zM14 4.8c.7-.8 1.1-2 1-3.1-1 0-2.2.7-2.9 1.5-.6.7-1.2 1.9-1 3 1.1.1 2.3-.6 2.9-1.4z"/></svg>
              Apple
            </a>
          </div>

          <div class="divider"><span>ou avec email</span></div>

          <label class="field">
            <span class="label">Nom complet ou Pseudo</span>
            <input class="input" type="text" name="name" value="{{ old('name') }}" placeholder="Jean" autocomplete="name" required autofocus />
            @error('name')
              <span class="error-css" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
            @enderror
          </label>

          <label class="field">
            <span class="label">Adresse email</span>
            <input class="input" type="email" name="email" value="{{ old('email') }}" placeholder="toi@exemple.com" autocomplete="email" required />
            @error('email')
              <span class="error-css" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
            @enderror
          </label>

          <label class="field">
            <span class="label">Mot de passe</span>
            <div class="input-wrap">
              <input type="password" name="password" placeholder="8 caractères minimum" autocomplete="new-password" required />
              <button type="button" class="toggle-eye" aria-label="Afficher">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-10-7-10-7a19.6 19.6 0 0 1 4.22-5.94"/><path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 10 7 10 7a19.5 19.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
              </button>
            </div>
            @error('password')
              <span class="error-css" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
            @enderror
          </label>

          <label class="field">
            <span class="label">Confirmer le mot de passe</span>
            <div class="input-wrap">
              <input type="password" name="password_confirmation" placeholder="Répète ton mot de passe" required />
            </div>
          </label>

          <label class="terms">
            <input type="checkbox" required />
            <span>J'accepte les <a href="#">conditions d'utilisation</a></span>
          </label>

          <button type="submit" class="cta">
            Créer mon compte
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </button>
        </form>

        <p class="foot">Déjà membre ? <a href="{{ route('login') }}">Se connecter</a></p>
      </div>
    </div>
  </main>
</div>
@endsection