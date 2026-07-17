<?php

use function Livewire\Volt\{state, layout};
use Illuminate\Support\Facades\Auth;

layout('layouts.app');

state([
    'userName' => fn () => Auth::user()->display_name,
    'userInterests' => fn () => Auth::user()->interests()->pluck('name')->toArray(),
]);

?>

<div class="companion-shell" id="companion-component" wire:id="companion">
  <link rel="stylesheet" href="{{ asset('css/companion.css') }}">

  <div class="bg-video-layer" id="bgVideoLayer">
    <video muted loop playsinline preload="auto" src="{{ asset('videos/companion-bg-1.mp4') }}"></video>
    <video muted loop playsinline preload="auto" src="{{ asset('videos/companion-bg-2.mp4') }}"></video>
    <video muted loop playsinline preload="auto" src="{{ asset('videos/companion-bg-3.mp4') }}"></video>
  </div>
  <div class="bg-video-tint"></div>

  <div class="auras" aria-hidden="true">
    <span class="aura a1"></span>
    <span class="aura a2"></span>
  </div>

  <a href="{{ route('socialnet') }}" class="skip-link">
    Passer
    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
  </a>

  <div class="main-container card-enter">
    <section class="companion-section">
      <div class="companion-avatar-wrapper">
        <div class="companion-glow"></div>
        <div class="orbit-ring orbit-ring-1"><div class="orbit-dot"></div></div>
        <div class="orbit-ring orbit-ring-2"><div class="orbit-dot"></div></div>
        <div class="companion-avatar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
        </div>
      </div>
      <div class="companion-status">
        <span class="status-dot"></span>
        <span>Ton compagnon Nexora</span>
      </div>
      <div class="step-progress" id="stepProgress">
        <span class="step-dot" data-step="1"></span>
        <span class="step-dot" data-step="2"></span>
        <span class="step-dot" data-step="3"></span>
        <span class="step-dot" data-step="4"></span>
        <span class="step-dot" data-step="5"></span>
      </div>
    </section>

    <div class="chat-zone">
      <div class="chat-messages" id="chatMessages"></div>
      <div class="flow-actions-container" id="flowActionsContainer"></div>
    </div>
  </div>

<script>
    window.companionData = {
      userName: @json($userName),
      userInterests: @json($userInterests),
      chatUrl: @json(route('socialnet')),
    };
  </script>
  <script src="{{ asset('js/companion.js') }}"></script>
</div>

</div>