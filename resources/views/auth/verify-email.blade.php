@extends('layouts.app')

@section('content')
<div class="auth-shell">
  <div class="auras">
    <span class="aura a1"></span>
    <span class="aura a2"></span>
    <span class="aura a3"></span>
  </div>

  <div class="pills" aria-hidden="true">
    <div class="pill tinted" style="top:15%;left:10%;--r:-4deg">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      Presque là !
    </div>
  </div>

  <main class="main">
    <div class="card-wrap">
      <div class="card">
        <div class="brand">
          <div class="brand-badge">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h1 class="font-display" style="font-size: 1.8rem; line-height: 1.2; margin-bottom: 0.5rem; text-align: center;">Vérification d'email</h1>
          <p style="text-align: center;">Merci de vous être inscrit ! Avant de continuer, veuillez valider votre adresse email en cliquant sur le lien que nous venons de vous envoyer.</p>
        </div>

        @if (session('status') == 'verification-link-sent')
          <div class="success-message" style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #166534; font-size: 0.8rem; padding: 0.75rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: center;">
            Un nouveau lien de vérification a été envoyé à l'adresse email fournie lors de l'inscription.
          </div>
        @endif

        @if(config('app.env') === 'local')
          <div style="background: rgba(107, 31, 42, 0.05); border: 1px dashed rgba(107, 31, 42, 0.2); color: #6b1f2a; font-size: 0.75rem; padding: 0.75rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: left; line-height: 1.4;">
            <strong>🛠️ Mode Développeur (Laragon) :</strong> Aucun email réel n'est envoyé sur internet. Le lien de vérification a été écrit dans votre fichier de log :
            <code style="display: block; background: rgba(0,0,0,0.05); padding: 4px 8px; border-radius: 6px; margin-top: 4px; font-family: monospace; font-size: 10px; word-break: break-all;">Nexo1/storage/logs/laravel.log</code>
            Ouvrez ce fichier avec votre éditeur et copiez le lien d'activation situé tout en bas pour le coller dans la barre d'adresse de votre navigateur.
          </div>
        @endif

        <div style="display: flex; flex-direction: column; gap: 1rem; width: 100%;">
          <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="cta w-full" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
              Renvoyer l'email de vérification
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            </button>
          </form>

          <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
            @csrf
            <button type="submit" class="social w-full" style="border: 1px solid var(--border); background: transparent; padding: 0.75rem; border-radius: 9999px; color: var(--ink); font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
              Se déconnecter
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </button>
          </form>
        </div>

        <p class="foot" style="margin-top: 1.5rem; text-align: center;">Besoin d'aide ? Contactez notre support.</p>
      </div>
    </div>
  </main>
</div>
@endsection
