@extends('layouts.app')

@section('content')
<div class="auth-shell">
  <div class="auras">
    <span class="aura a1"></span>
    <span class="aura a2"></span>
    <span class="aura a3"></span>
  </div>

  <div class="pills" aria-hidden="true">
    <div class="pill tinted" style="top:12%;left:8%;--r:-6deg"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l2 5 5 2-5 2-2 5-2-5-5-2 5-2 2-5z"/></svg>Bon retour !</div>
    <div class="pill" style="top:22%;right:10%;--r:5deg"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>Chat</div>
  </div>

  <main class="main">
    <div class="card-wrap">
      <div class="card">
        <div class="brand">
          <div class="brand-badge">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h1 class="font-display">Connexion</h1>
          <p>Ravi de vous revoir ! Connectez-vous à votre compte Nexora.</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
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
            <span class="label">Adresse email</span>
            <input class="input" type="email" name="email" value="{{ old('email') }}" placeholder="toi@exemple.com" required autofocus />
            @error('email')
              <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
            @enderror
          </label>

          <label class="field">
            <span class="label">Mot de passe</span>
            <div class="input-wrap">
              <input type="password" name="password" placeholder="Rentre ton mot de passe" required />
            </div>
            @error('password')
              <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
            @enderror
          </label>

          <button type="submit" class="cta">
            Se connecter
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </button>
        </form>

        <p class="foot">Nouveau ? <a href="{{ route('register') }}">Créer un compte</a></p>
      </div>
    </div>
  </main>
</div>
@endsection