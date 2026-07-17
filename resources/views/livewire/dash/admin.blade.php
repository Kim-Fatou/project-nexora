<?php

use function Livewire\Volt\{state, mount, layout};
use Illuminate\Support\Facades\Auth;

layout('layouts.app');

state([
    'totalMembers' => fn () => \App\Models\User::count(),
    'activeRoomsCount' => fn () => \App\Models\Room::count(),
    'messagesToday' => fn () => \App\Models\ChatMessage::whereDate('created_at', today())->count() + 
        \App\Models\RoomMessage::whereDate('created_at', today())->count(),
    'openReportsCount' => fn () => \App\Models\Report::where('status', 'open')->count(),
    'latestUsers' => fn () => \App\Models\User::with('expertiseInterest')
        ->when($this->userSearchQuery, function ($q) {
            $q->where('username', 'like', '%' . $this->userSearchQuery . '%')
              ->orWhere('email', 'like', '%' . $this->userSearchQuery . '%');
        })
        ->orderBy('created_at', 'desc')
        ->get(),
    'activeRooms' => fn () => \App\Models\Room::withCount('members')->orderByDesc('members_count')->take(3)->get(),
    'recentReports' => fn () => \App\Models\Report::with(['reporter', 'reportedUser'])->orderBy('created_at', 'desc')->take(3)->get(),
    'revenue' => fn () => \App\Models\Subscription::where('status', 'active')->where('plan_type', 'premium')->count() * 4.99,
    
    // CRUD state
    'userSearchQuery' => '',
    'interests' => fn () => \App\Models\Interest::all(),
    'showUserModal' => false,
    'modalUserId' => null,
    'modalUsername' => '',
    'modalEmail' => '',
    'modalPassword' => '',
    'modalLevel' => 1,
    'modalIsAdmin' => false,
    'modalIsPremium' => false,
    'modalIsExpert' => false,
    'modalExpertiseInterestId' => null,
]);

mount(function () {
    if (!Auth::check() || !Auth::user()->is_admin) {
        return redirect()->route('dashboard');
    }
});

$openCreateModal = function () {
    $this->modalUserId = null;
    $this->modalUsername = '';
    $this->modalEmail = '';
    $this->modalPassword = '';
    $this->modalLevel = 1;
    $this->modalIsAdmin = false;
    $this->modalIsPremium = false;
    $this->modalIsExpert = false;
    $this->modalExpertiseInterestId = \App\Models\Interest::first()?->id;
    $this->showUserModal = true;
};

$openEditModal = function ($userId) {
    $user = \App\Models\User::findOrFail($userId);
    $this->modalUserId = $user->id;
    $this->modalUsername = $user->username;
    $this->modalEmail = $user->email;
    $this->modalPassword = '';
    $this->modalLevel = $user->level ?? 1;
    $this->modalIsAdmin = (bool)$user->is_admin;
    $this->modalIsPremium = (bool)$user->is_premium;
    $this->modalIsExpert = (bool)$user->is_expert;
    $this->modalExpertiseInterestId = $user->expertise_interest_id ?: \App\Models\Interest::first()?->id;
    $this->showUserModal = true;
};

$saveUser = function () {
    $rules = [
        'modalUsername' => 'required|string|max:255|unique:users,username,' . ($this->modalUserId ?: 'NULL'),
        'modalEmail' => 'required|email|max:255|unique:users,email,' . ($this->modalUserId ?: 'NULL'),
    ];
    if (!$this->modalUserId) {
        $rules['modalPassword'] = 'required|string|min:6';
    } else {
        $rules['modalPassword'] = 'nullable|string|min:6';
    }
    
    $this->validate($rules, [
        'modalUsername.required' => 'Le nom d\'utilisateur est requis.',
        'modalUsername.unique' => 'Ce nom d\'utilisateur est déjà pris.',
        'modalEmail.required' => 'L\'adresse email est requise.',
        'modalEmail.unique' => 'Cet email est déjà pris.',
        'modalPassword.required' => 'Le mot de passe est requis.',
        'modalPassword.min' => 'Le mot de passe doit faire au moins 6 caractères.',
    ]);
    
    if ($this->modalUserId) {
        $user = \App\Models\User::findOrFail($this->modalUserId);
    } else {
        $user = new \App\Models\User();
    }
    
    $user->name = $this->modalUsername;
    $user->username = $this->modalUsername;
    $user->email = $this->modalEmail;
    if ($this->modalPassword) {
        $user->password = \Illuminate\Support\Facades\Hash::make($this->modalPassword);
    }
    
    $user->level = $this->modalLevel;
    $user->is_admin = $this->modalIsAdmin;
    $user->is_premium = $this->modalIsPremium;
    $user->is_expert = $this->modalIsExpert;
    $user->expertise_interest_id = $this->modalIsExpert ? $this->modalExpertiseInterestId : null;
    
    $user->save();
    
    $this->showUserModal = false;
    $this->latestUsers = \App\Models\User::with('expertiseInterest')
        ->when($this->userSearchQuery, function ($q) {
            $q->where('username', 'like', '%' . $this->userSearchQuery . '%')
              ->orWhere('email', 'like', '%' . $this->userSearchQuery . '%');
        })
        ->orderBy('created_at', 'desc')
        ->get();
};

$deleteUser = function ($userId) {
    $user = \App\Models\User::findOrFail($userId);
    if ($user->id === auth()->id()) {
        session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        return;
    }
    $user->delete();
    
    $this->latestUsers = \App\Models\User::with('expertiseInterest')
        ->when($this->userSearchQuery, function ($q) {
            $q->where('username', 'like', '%' . $this->userSearchQuery . '%')
              ->orWhere('email', 'like', '%' . $this->userSearchQuery . '%');
        })
        ->orderBy('created_at', 'desc')
        ->get();
};

$toggleExpert = function ($userId) {
    $user = \App\Models\User::findOrFail($userId);
    $user->is_expert = !$user->is_expert;
    
    // S'il devient expert et n'a pas d'intérêt défini, on lui affecte le premier de la base par défaut
    if ($user->is_expert && !$user->expertise_interest_id) {
        $user->expertise_interest_id = \App\Models\Interest::first()?->id;
    }
    
    $user->save();
    
    // Rafraîchir l'état
    $this->latestUsers = \App\Models\User::with('expertiseInterest')->orderBy('created_at', 'desc')->take(10)->get();
};

?>

<div class="dash-shell">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <aside class="dash-sidebar">
        <div class="dash-logo">
            <img src="{{ asset('images/logos/logonexora.png') }}" alt="Nexora">
        </div>

        <nav class="dash-nav">
            <a href="{{ route('admin') }}" class="dash-nav-item is-active">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
                Vue d'ensemble
            </a>
            <a href="#" class="dash-nav-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8z"/></svg>
                Utilisateurs
            </a>
            <a href="#" class="dash-nav-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12a9 9 0 11-3.9-7.4"/></svg>
                Salons
            </a>
            <a href="#" class="dash-nav-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                Signalements
                <span class="badge badge-red" style="margin-left:auto">{{ $openReportsCount }}</span>
            </a>

            <div class="dash-nav-group">
                <div class="dash-nav-label">Système</div>
                <a href="#" class="dash-nav-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Santé du système
                </a>
                <a href="#" class="dash-nav-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/></svg>
                    Paramètres
                </a>
            </div>
        </nav>

        <div class="dash-sidebar-foot">
            <div class="dash-avatar">AD</div>
            <div>
                <div class="dash-sidebar-foot-name">Administration</div>
                <div class="dash-sidebar-foot-sub">Accès complet</div>
            </div>
        </div>
    </aside>

    <main class="dash-main">

        <div class="dash-topbar">
            <div class="dash-breadcrumb">Nexora / <strong>Administration</strong></div>
            <div class="dash-topbar-actions">
                <a href="{{ route('socialnet') }}" class="btn-dash-ghost">← Retour au site</a>
            </div>
        </div>

        <h1 class="dash-page-title">Administration Nexora</h1>
        <p class="dash-page-sub">Vue globale de la plateforme.</p>

        {{-- ===== 1. Cartes statistiques globales ===== --}}
        <div class="dash-grid">
            <div class="dash-card col-span-3">
                <div class="dash-card-title">Membres totaux</div>
                <div class="stat-num">{{ number_format($totalMembers) }}</div>
                <div class="mini-bars"><span style="height:40%"></span><span style="height:55%"></span><span style="height:50%"></span><span style="height:70%"></span><span style="height:65%"></span><span style="height:90%"></span></div>
            </div>
            <div class="dash-card col-span-3">
                <div class="dash-card-title">Salons actifs</div>
                <div class="stat-num">{{ $activeRoomsCount }}</div>
                <div class="mini-bars"><span style="height:30%"></span><span style="height:45%"></span><span style="height:60%"></span><span style="height:55%"></span><span style="height:80%"></span><span style="height:75%"></span></div>
            </div>
            <div class="dash-card col-span-3">
                <div class="dash-card-title">Messages / jour</div>
                <div class="stat-num">{{ number_format($messagesToday) }}</div>
                <div class="mini-bars"><span style="height:50%"></span><span style="height:65%"></span><span style="height:40%"></span><span style="height:85%"></span><span style="height:70%"></span><span style="height:95%"></span></div>
            </div>
            <div class="dash-card col-span-3">
                <div class="dash-card-title">Signalements ouverts</div>
                <div class="stat-num" style="color:#a3273a">{{ $openReportsCount }}</div>
                <div class="mini-bars"><span style="height:70%"></span><span style="height:40%"></span><span style="height:60%"></span><span style="height:30%"></span><span style="height:50%"></span><span style="height:20%"></span></div>
            </div        {{-- ===== 2. Gestion des Utilisateurs (CRUD) ===== --}}
        <div class="dash-grid">
            <div class="dash-card col-span-12">
                <div class="dash-card-head" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap: 12px; margin-bottom: 16px;">
                    <span class="dash-card-title" style="margin:0">Gestion des Utilisateurs</span>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <input type="text" wire:model.live="userSearchQuery" placeholder="Rechercher..." class="glass" style="padding: 6px 12px; border-radius: 99px; outline:none; border: 1px solid rgba(0,0,0,0.1); font-size:12px;" />
                        <button wire:click="openCreateModal" class="btn-dash-primary" style="background:#6b1f2a; color:white; border:none; padding: 6px 16px; border-radius:99px; font-size:12px; font-weight:600; cursor:pointer;">
                            + Nouvel Utilisateur / Expert
                        </button>
                    </div>
                </div>
                
                @if (session()->has('error'))
                    <div class="badge badge-red" style="padding: 10px; width: 100%; display: block; margin-bottom: 12px; text-align: center;">{{ session('error') }}</div>
                @endif
                
                <table>
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Niveau</th>
                            <th>Rôles</th>
                            <th>Expertise</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestUsers as $u)
                            <tr>
                                <td>
                                    <span class="table-avatar">{{ strtoupper(substr($u->username ?? 'N', 0, 2)) }}</span>
                                    {{ '@' . $u->username }}
                                </td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->level }}</td>
                                <td>
                                    <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                        @if ($u->is_admin)
                                            <span class="badge" style="background:rgba(239, 68, 68, 0.1); color:#ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">Admin</span>
                                        @endif
                                        @if ($u->isPremium())
                                            <span class="badge badge-wine">Premium</span>
                                        @endif
                                        @if ($u->is_expert)
                                            <span class="badge" style="background:rgba(107, 31, 42, 0.1); color:#6b1f2a; border: 1px solid rgba(107, 31, 42, 0.2);">Expert</span>
                                        @else
                                            <span class="badge badge-green">Membre</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($u->is_expert)
                                        <span class="badge" style="background:rgba(0,0,0,0.05); color:#666;">{{ $u->expertiseInterest?->name ?? 'Général' }}</span>
                                    @else
                                        <span style="color:#aaa; font-size:11px;">—</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex; gap:8px; justify-content:flex-end;">
                                        <button wire:click="openEditModal({{ $u->id }})" class="badge badge-green" style="cursor:pointer; border:1px solid #166534; background:transparent; color:#166534;" title="Modifier l'utilisateur"><i class="fa-solid fa-pen"></i> Modifier</button>
                                        @if($u->id !== auth()->id())
                                            <button wire:click="deleteUser({{ $u->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer cet utilisateur ?" class="badge" style="cursor:pointer; border:1px solid #a83248; background:transparent; color:#a83248;" title="Supprimer l'utilisateur"><i class="fa-solid fa-trash"></i> Supprimer</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--muted);font-size:12px;padding:20px;">Aucun utilisateur trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
 
        {{-- ===== 3. Salons les plus actifs ===== --}}
        <div class="dash-grid">
            <div class="dash-card col-span-6">
                <div class="dash-card-head"><span class="dash-card-title">Salons les plus actifs</span></div>
                <table>
                    <tbody>
                        @forelse ($activeRooms as $activeRoom)
                            <tr>
                                <td>{{ $activeRoom->icon }} {{ $activeRoom->name }}</td>
                                <td style="text-align:right">{{ $activeRoom->members_count }} membres</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align:center;color:var(--muted);font-size:12px;padding:10px;">Aucun salon actif</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- ===== 4. Signalements récents ===== --}}
            <div class="dash-card col-span-6">
                <div class="dash-card-head"><span class="dash-card-title">Signalements récents</span></div>
                <table>
                    <tbody>
                        @forelse ($recentReports as $report)
                            <tr>
                                <td>
                                    <strong style="color:#a3273a">{{ $report->reason }}</strong><br>
                                    <span style="font-size:11px;color:var(--muted)">
                                        Signaleur : {{ '@' . ($report->reporter?->username ?? 'inconnu') }} | 
                                        Cible : {{ '@' . ($report->reportedUser?->username ?? 'inconnu') }}
                                    </span>
                                </td>
                                <td style="text-align:right">
                                    <span class="badge {{ $report->status === 'open' ? 'badge-red' : 'badge-green' }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align:center;color:var(--muted);font-size:12px;padding:20px;">Aucun signalement récent</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
 
        {{-- ===== 5. Répartition des paiements Premium ===== --}}
        <div class="dash-grid">
            <div class="dash-card col-span-12">
                <div class="dash-card-head"><span class="dash-card-title">Revenus Premium (30 derniers jours)</span></div>
                <div class="stat-num">{{ number_format($revenue, 2) }} €<span class="stat-delta" style="color:var(--muted)">— mensuel estimé</span></div>
                <div class="mini-bars"><span style="height:10%"></span><span style="height:10%"></span><span style="height:10%"></span><span style="height:10%"></span><span style="height:10%"></span><span style="height:10%"></span><span style="height:10%"></span></div>
            </div>
        </div>

        {{-- ===== MODALE : Créer / Modifier un Utilisateur ===== --}}
        @if ($showUserModal)
            <div class="fixed inset-0 z-[100] flex items-center justify-center modal-backdrop p-4" style="background: rgba(0,0,0,0.4); backdrop-filter: blur(8px); position: fixed; inset: 0; display: flex; align-items: center; justify-content: center;">
                <div class="glass-strong rounded-3xl p-8 max-w-md w-full relative" style="background: white; border-radius: 24px; padding: 32px; max-width: 450px; width: 100%; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15); text-align: left;">
                    <h3 class="font-display text-xl font-semibold mb-4" style="color: #6b1f2a; margin-bottom: 20px;">
                        {{ $modalUserId ? 'Modifier l\'utilisateur' : 'Créer un utilisateur / expert' }}
                    </h3>
                    
                    <form wire:submit.prevent="saveUser" style="display:flex; flex-direction:column; gap:16px;">
                        <div>
                            <label style="display:block; font-size:11px; color:#666; font-weight:600; margin-bottom: 4px;">Nom d'utilisateur</label>
                            <input type="text" wire:model="modalUsername" placeholder="ex: diallo_expert" class="glass" style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ccc; outline:none; font-size:13px;" />
                            @error('modalUsername') <span style="font-size:10px; color:#a83248; display:block; margin-top:2px;">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label style="display:block; font-size:11px; color:#666; font-weight:600; margin-bottom: 4px;">Adresse email</label>
                            <input type="email" wire:model="modalEmail" placeholder="ex: expert@nexora.com" class="glass" style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ccc; outline:none; font-size:13px;" />
                            @error('modalEmail') <span style="font-size:10px; color:#a83248; display:block; margin-top:2px;">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label style="display:block; font-size:11px; color:#666; font-weight:600; margin-bottom: 4px;">Mot de passe {{ $modalUserId ? '(laisser vide pour ne pas changer)' : '' }}</label>
                            <input type="password" wire:model="modalPassword" placeholder="******" class="glass" style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ccc; outline:none; font-size:13px;" />
                            @error('modalPassword') <span style="font-size:10px; color:#a83248; display:block; margin-top:2px;">{{ $message }}</span> @enderror
                        </div>

                        <div style="display:flex; gap:16px;">
                            <div style="flex:1;">
                                <label style="display:block; font-size:11px; color:#666; font-weight:600; margin-bottom: 4px;">Niveau (gamification)</label>
                                <input type="number" wire:model="modalLevel" class="glass" style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ccc; outline:none; font-size:13px;" />
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column; gap:8px; padding: 8px 0;">
                            <label style="display:flex; align-items:center; gap:8px; font-size:12px; cursor:pointer;">
                                <input type="checkbox" wire:model="modalIsAdmin" />
                                <span>Rôle : Administrateur</span>
                            </label>
                            <label style="display:flex; align-items:center; gap:8px; font-size:12px; cursor:pointer;">
                                <input type="checkbox" wire:model="modalIsPremium" />
                                <span>Statut : Membre Premium</span>
                            </label>
                            <label style="display:flex; align-items:center; gap:8px; font-size:12px; cursor:pointer;">
                                <input type="checkbox" wire:model.live="modalIsExpert" />
                                <span>Rôle : Expert passionné</span>
                            </label>
                        </div>

                        @if($modalIsExpert)
                            <div style="background: rgba(107,31,42,0.05); padding: 12px; border-radius: 12px; border: 1px solid rgba(107,31,42,0.1);">
                                <label style="display:block; font-size:11px; color:#6b1f2a; font-weight:600; margin-bottom: 6px;">Domaine d'expertise (Passion)</label>
                                <select wire:model="modalExpertiseInterestId" style="width:100%; padding:8px 12px; border-radius:10px; border:1px solid #6b1f2a; outline:none; font-size:12px; background:white;">
                                    @foreach($interests as $interest)
                                        <option value="{{ $interest->id }}">{{ $interest->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div style="display:flex; gap:12px; margin-top:16px;">
                            <button type="button" wire:click="$set('showUserModal', false)" class="btn-dash-ghost" style="flex:1; padding: 10px; border-radius:99px; font-size:12px; font-weight:600; cursor:pointer; text-align:center; border: 1px solid #ccc; background:transparent;">Annuler</button>
                            <button type="submit" class="btn-dash-primary" style="flex:1; padding: 10px; border-radius:99px; font-size:12px; font-weight:600; cursor:pointer; text-align:center; border:none; background:#6b1f2a; color:white;">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
 
    </main>
</div>
 
<script src="{{ asset('js/dashboard.js') }}"></script>