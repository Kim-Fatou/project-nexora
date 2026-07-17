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
      <div class="card" style="padding: 2.5rem;">
        <div class="brand">
          <div class="brand-badge" style="background: rgba(107,31,42,0.1); color: #6b1f2a;">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          </div>
          <h1 class="font-display" style="font-size: 1.8rem; line-height: 1.2; margin-bottom: 0.5rem; text-align: center;">Paiement Sécurisé</h1>
          <p style="text-align: center;">Nexora Premium — Accès illimité aux passions</p>
        </div>

        <div style="background: rgba(107,31,42,0.05); border: 1px dashed var(--border); border-radius: 16px; padding: 1rem; margin-bottom: 1.5rem; text-align: center;">
          <div style="font-size: 0.75rem; text-transform: uppercase; tracking-wider: 0.1em; color: var(--wine);">Abonnement Mensuel</div>
          <div style="font-size: 2rem; font-weight: 700; color: var(--wine); margin: 0.25rem 0;">4,99 €<span style="font-size: 0.9rem; font-weight: 500; color: var(--ink);">/mois</span></div>
          <div style="font-size: 0.7rem; color: #6b7280;">Annulable à tout moment, sans engagement.</div>
        </div>

        <form action="{{ route('premium.checkout.mock.post') }}" method="POST" id="paymentForm" onsubmit="handlePaymentSubmit(event)">
          @csrf

          <div style="margin-bottom: 1.25rem;">
            <label class="field">
              <span class="label">Nom sur la carte</span>
              <input class="input" type="text" name="card_name" placeholder="Marie Dupont" required />
            </label>
          </div>

          <div style="margin-bottom: 1.25rem;">
            <label class="field">
              <span class="label">Numéro de carte</span>
              <div style="position: relative; display: flex; align-items: center;">
                <input class="input" type="text" name="card_number" id="cardNumber" placeholder="4532 •••• •••• 1882" maxlength="19" required style="padding-right: 40px;" />
                <span style="position: absolute; right: 12px; color: #9ca3af;"><i class="fa-solid fa-credit-card"></i></span>
              </div>
            </label>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <label class="field">
              <span class="label">Date d'expiration</span>
              <input class="input" type="text" name="card_expiry" id="cardExpiry" placeholder="MM/AA" maxlength="5" required />
            </label>

            <label class="field">
              <span class="label">Code de sécurité (CVC)</span>
              <input class="input" type="password" name="card_cvc" placeholder="•••" maxlength="3" required />
            </label>
          </div>

          <button type="submit" class="cta w-full" id="btnPay" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
            <span>Payer et Activer Premium</span>
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
          <a href="{{ route('dashboard') }}" style="color: #6b7280; font-size: 0.75rem; text-decoration: none; hover:underline;">← Retourner au tableau de bord</a>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
  // Format card number with spaces
  document.getElementById('cardNumber').addEventListener('input', function (e) {
    let target = e.target;
    let position = target.selectionEnd;
    let length = target.value.length;
    
    let value = target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let matches = value.match(/\d{4,16}/g);
    let match = matches && matches[0] || '';
    let parts = [];

    for (let i = 0, len = match.length; i < len; i += 4) {
      parts.push(match.substring(i, i + 4));
    }

    if (parts.length > 0) {
      target.value = parts.join(' ');
    } else {
      target.value = value;
    }
  });

  // Format expiration date with slash
  document.getElementById('cardExpiry').addEventListener('input', function (e) {
    let target = e.target;
    let value = target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    if (value.length >= 2) {
      target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
    } else {
      target.value = value;
    }
  });

  function handlePaymentSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('btnPay');
    btn.disabled = true;
    btn.style.opacity = '0.7';
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Traitement sécurisé en cours...';
    
    setTimeout(() => {
      e.target.submit();
    }, 2000);
  }
</script>
@endsection
