<?php

use function Livewire\Volt\{state, layout};
use Illuminate\Support\Facades\Auth;

layout('layouts.app');

state([
    'user' => fn () => Auth::user(),
    'roomsCount' => fn () => Auth::user()->rooms()->count(),
    'connectionsCount' => fn () => \App\Models\Connection::where(function ($query) {
            $query->where('user_id', Auth::id())->orWhere('friend_id', Auth::id());
        })
        ->where('status', 'accepted')
        ->count(),
    'messagesCount' => fn () => \App\Models\ChatMessage::where('sender_id', Auth::id())->count() + 
        \App\Models\RoomMessage::where('sender_id', Auth::id())->count(),
    'interestsCount' => fn () => Auth::user()->interests()->count(),
    'progressPercent' => function () {
        $user = Auth::user();
        $xp = $user->xp;
        if ($user->level >= 5) {
            return 100;
        }
        $thresholds = [1 => 100, 2 => 300, 3 => 600, 4 => 1000];
        $currentLevel = $user->level;
        $nextThreshold = $thresholds[$currentLevel] ?? 1000;
        $prevThreshold = $currentLevel == 1 ? 0 : $thresholds[$currentLevel - 1];
        
        $range = $nextThreshold - $prevThreshold;
        $earnedInRange = $xp - $prevThreshold;
        
        return max(0, min(100, round(($earnedInRange / $range) * 100)));
    },
    'xpNeeded' => function () {
        $user = Auth::user();
        $xp = $user->xp;
        if ($user->level >= 5) {
            return 0;
        }
        $thresholds = [1 => 100, 2 => 300, 3 => 600, 4 => 1000];
        return ($thresholds[$user->level] ?? 1000) - $xp;
    },
    'activeRooms' => fn () => Auth::user()->rooms()
        ->withCount('messages')
        ->orderByDesc('messages_count')
        ->take(3)
        ->get(),
    'connections' => fn () => \App\Models\Connection::where(function ($query) {
            $query->where('user_id', Auth::id())->orWhere('friend_id', Auth::id());
        })
        ->where('status', 'accepted')
        ->with(['user', 'friend', 'latestMessage'])
        ->take(5)
        ->get()
]);

?>

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<div class="dash-shell">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="dash-sidebar">
        <div class="dash-logo">
            <img src="{{ asset('images/logos/logonexora.png') }}" alt="Nexora">
        </div>

        <nav class="dash-nav">
            <a href="{{ route('dashboard') }}" class="dash-nav-item is-active">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
                Vue d'ensemble
            </a>
            <a href="{{ route('socialnet') }}" class="dash-nav-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12a9 9 0 11-3.9-7.4"/></svg>
                Mes salons
            </a>
            <a href="{{ route('socialnet') }}" class="dash-nav-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8z"/></svg>
                Mes connexions
            </a>

            @if($user->is_admin)
            <a href="{{ route('admin') }}" class="dash-nav-item" style="color: #a3273a;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Administration
            </a>
            @endif

            <div class="dash-nav-group">
                <div class="dash-nav-label">Compte</div>
                <a href="{{ route('profile') }}" class="dash-nav-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
                    Mon profil
                </a>
                <a href="#" class="dash-nav-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                    Paramètres
                </a>
            </div>
        </nav>

        <div class="dash-sidebar-foot">
            <div class="dash-avatar">{{ strtoupper(substr($user->username ?? 'N', 0, 2)) }}</div>
            <div>
                <div class="dash-sidebar-foot-name">{{ '@' . ($user->username ?? 'toi') }}</div>
                <div class="dash-sidebar-foot-sub">Niveau {{ $user->level ?? 1 }}</div>
            </div>
        </div>
    </aside>

    {{-- ===== CONTENU ===== --}}
    <main class="dash-main">

        <div class="dash-topbar">
            <div class="dash-breadcrumb">Nexora / <strong>Vue d'ensemble</strong></div>
            <div class="dash-topbar-actions">
                <a href="{{ route('socialnet') }}" class="btn-dash-ghost">← Retour au fil</a>
            </div>
        </div>

        <h1 class="dash-page-title">Bienvenue, {{ '@' . ($user->username ?? 'toi') }} 👋</h1>
        <p class="dash-page-sub">Voici un aperçu de ton activité sur Nexora.</p>

        {{-- ===== 1. Cartes statistiques ===== --}}
        <div class="dash-grid">
            <div class="dash-card col-span-3">
                <div class="dash-card-title">Salons rejoints</div>
                <div class="stat-num">{{ $roomsCount }}</div>
                <div class="mini-bars"><span style="height:30%"></span><span style="height:55%"></span><span style="height:40%"></span><span style="height:70%"></span><span style="height:50%"></span><span style="height:85%"></span></div>
            </div>
            <div class="dash-card col-span-3">
                <div class="dash-card-title">Connexions</div>
                <div class="stat-num">{{ $connectionsCount }}</div>
                <div class="mini-bars"><span style="height:20%"></span><span style="height:35%"></span><span style="height:60%"></span><span style="height:45%"></span><span style="height:75%"></span><span style="height:90%"></span></div>
            </div>
            <div class="dash-card col-span-3">
                <div class="dash-card-title">Messages envoyés</div>
                <div class="stat-num">{{ $messagesCount }}</div>
                <div class="mini-bars"><span style="height:50%"></span><span style="height:60%"></span><span style="height:40%"></span><span style="height:80%"></span><span style="height:65%"></span><span style="height:55%"></span></div>
            </div>
            <div class="dash-card col-span-3">
                <div class="dash-card-title">Centres d'intérêt</div>
                <div class="stat-num">{{ $interestsCount }}</div>
                <div class="mini-bars"><span style="height:100%"></span><span style="height:100%"></span><span style="height:100%"></span><span style="height:100%"></span><span style="height:100%"></span><span style="height:20%"></span></div>
            </div>
        </div>

        {{-- ===== 2. Progression de niveau ===== --}}
        <div class="dash-grid">
            <div class="dash-card col-span-8">
                <div class="dash-card-head">
                    <span class="dash-card-title">Progression</span>
                    <span class="badge badge-wine">Niveau {{ $user->level ?? 1 }} / 5</span>
                </div>
                <p style="font-size:13px;color:var(--muted);margin:0 0 4px;">Atteins le niveau 5 pour débloquer Nexora Premium gratuitement, ou passe premium directement.</p>
                <div class="level-track"><div class="level-fill" style="width:{{ $progressPercent }}%"></div></div>
                @if ($user->level >= 5)
                    <p style="font-size:12px;color:var(--muted);margin:0;">Niveau Maximum ({{ $user->xp }} XP) — Félicitations, tu as le grade ultime !</p>
                @else
                    <p style="font-size:12px;color:var(--muted);margin:0;">{{ $user->xp }} XP cumulés — encore {{ $xpNeeded }} XP pour débloquer le niveau suivant.</p>
                @endif
            </div>
            <div class="dash-card col-span-4">
                <div class="dash-card-title">Salons les plus actifs</div>
                <table>
                    <tbody>
                        @forelse ($activeRooms as $activeRoom)
                            <tr>
                                <td>{{ $activeRoom->icon }} {{ $activeRoom->name }}</td>
                                <td style="text-align:right;color:var(--muted)">{{ $activeRoom->messages_count }} msg</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align:center;color:var(--muted);font-size:12px;">Aucun salon actif</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== 3. Mes connexions récentes ===== --}}
        <div class="dash-grid">
            <div class="dash-card col-span-12">
                <div class="dash-card-head">
                    <span class="dash-card-title">Connexions récentes</span>
                    <a href="{{ route('socialnet') }}" class="dash-card-link">Voir tout →</a>
                </div>
                <table>
                    <thead><tr><th>Personne</th><th>Passion partagée</th><th>Statut</th><th>Dernier échange</th></tr></thead>
                    <tbody>
                        @forelse ($connections as $conn)
                            @php
                                $otherUser = $conn->user_id === $user->id ? $conn->friend : $conn->user;
                                $sharedInterest = $user->interests()->whereIn('interests.id', $otherUser->interests()->pluck('interests.id'))->first();
                                $sharedPassion = $sharedInterest ? $sharedInterest->name : 'Général';
                            @endphp
                            <tr>
                                <td><span class="table-avatar">{{ strtoupper(substr($otherUser->username ?? 'N', 0, 2)) }}</span>{{ '@' . $otherUser->username }}</td>
                                <td>{{ $sharedPassion }}</td>
                                <td><span class="badge badge-green">Actif</span></td>
                                <td>{{ $conn->latestMessage ? $conn->latestMessage->created_at->diffForHumans() : 'Pas de message' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;color:var(--muted);font-size:12px;padding:20px;">Aucune connexion pour le moment</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== 4. Premium (verrouillé tant que niveau < 5 / pas payé) ===== --}}
        <div class="dash-grid">
            <div class="dash-card col-span-12 {{ ($user->level < 5 && !$user->is_premium) ? 'premium-locked' : '' }}" id="premiumCard">
                <div class="dash-card-title">Fonctionnalités Premium</div>
                <div class="dash-grid" style="margin-top:12px;">
                    <div class="dash-card col-span-4"><div class="dash-card-title">Salons illimités</div><p style="font-size:13px;color:var(--muted)">Rejoins plus de 10 salons simultanément.</p></div>
                    <div class="dash-card col-span-4"><div class="dash-card-title">Appels HD</div><p style="font-size:13px;color:var(--muted)">Appels vocaux et vidéo en haute qualité.</p></div>
                    <div class="dash-card col-span-4"><div class="dash-card-title">Traduction en direct</div><p style="font-size:13px;color:var(--muted)">Messages traduits automatiquement, en temps réel.</p></div>
                </div>

                @if ($user->level < 5 && !$user->is_premium)
                    <div class="premium-overlay">
                        <span class="premium-overlay-ic">🔒</span>
                        <h4>Débloque Nexora Premium</h4>
                        <p>Atteins le niveau 5, ou passe premium dès maintenant pour profiter de tout, immédiatement.</p>
                        <a href="{{ route('premium.checkout') }}" class="btn-dash-primary">Passer Premium →</a>
                    </div>
                @endif
            </div>
        </div>

    </main>
</div>

<script src="{{ asset('js/dashboard.js') }}"></script>