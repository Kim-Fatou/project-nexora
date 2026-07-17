@extends('layouts.app')

@section('content')
<div class="auth-shell">
  <div class="auras">
    <span class="aura a1"></span>
    <span class="aura a2"></span>
    <span class="aura a3"></span>
  </div>

  <main class="main" style="max-width: 500px; width: 100%; margin: 0 auto; padding: 20px;">
    <div class="card-wrap">
      <div class="card" style="padding: 2.5rem; text-align: center;">
        <div class="brand">
          <div class="brand-badge" style="background: rgba(107,31,42,0.1); color: #6b1f2a;">
            @if($provider === 'google')
              <i class="fa-brands fa-google text-lg"></i>
            @else
              <i class="fa-brands fa-apple text-lg"></i>
            @endif
          </div>
          <h1 class="font-display" style="font-size: 1.8rem; line-height: 1.2; margin-bottom: 0.5rem;">Simulation {{ ucfirst($provider) }}</h1>
          <p>Les identifiants `{{ strtoupper($provider) }}_CLIENT_ID` ne sont pas configurés dans votre fichier `.env`.</p>
        </div>

        <div style="background: rgba(255,255,255,0.25); border: 1px solid var(--border); border-radius: 16px; padding: 1.25rem; margin: 1.5rem 0; text-align: left; font-size: 0.8rem; line-height: 1.5;">
          <p style="margin: 0 0 0.5rem; font-weight: 600; color: var(--wine);">💡 Mode Démonstration</p>
          Pour vous permettre de tester le flux de connexion sans avoir à paramétrer vos comptes développeurs, vous pouvez simuler une authentification instantanée en cliquant ci-dessous.
        </div>

        <form action="{{ route('social.callback.mock', $provider) }}" method="POST" id="socialForm" onsubmit="handleSocialSubmit(event)">
          @csrf
          <button type="submit" class="cta w-full" id="btnConnect" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
            <span>Simuler la connexion avec {{ ucfirst($provider) }}</span>
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </form>

        <div style="margin-top: 1.5rem;">
          <a href="{{ route('login') }}" style="color: #6b7280; font-size: 0.75rem; text-decoration: none; hover:underline;">← Retourner à la page de connexion</a>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
  function handleSocialSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('btnConnect');
    btn.disabled = true;
    btn.style.opacity = '0.7';
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Authentification simulée en cours...';
    
    setTimeout(() => {
      e.target.submit();
    }, 1500);
  }
</script>
@endsection
