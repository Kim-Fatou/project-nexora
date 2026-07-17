<?php

//page chat-box.blade.php

use function Livewire\Volt\{state, mount, on, uses};
use Livewire\WithFileUploads;
use App\Models\ChatMessage;
use App\Models\Connection;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
//use Throwable;

uses([WithFileUploads::class]);

state([
    // ----- Discussions entre humains -----
    'connectionId' => null,
    'messages' => [],
    'textMessage' => '',
    'conversations' => [],
    'attachments' => [],   // fichiers en attente d'envoi (upload temporaire Livewire, plusieurs possibles)
    'vocalAttachment' => null,
    'contactInfoOpen' => false,   // panneau "Infos du contact" (profil, médias, bloquer, signaler)

    // ----- Assistant IA (panneau flottant) -----
    'aiPanelOpen' => false,
    'aiMessages' => [],   // historique local {role: 'user'|'assistant', content: '...'}
    'aiInput' => '',
    'aiLoading' => false,

    // ----- Panel "Ajouter des passionnés" -----
    'addFriendsOpen' => false,
    'addFilter' => 'all',

    // ----- Canal personnel (pour rafraîchir la liste en temps réel) -----
    // Volt interpole les noms de canaux avec les propriétés d'état ({myUserId}),
    // donc on stocke l'ID de l'utilisateur connecté comme état, pas juste comme variable PHP.
    'myUserId' => null,
    'translatedMessages' => [],
    'editingMessageId' => null,
    'editingText' => '',
    'replyingToMessageId' => null,
]);

mount(function () {
    $this->myUserId = Auth::id();
    $this->loadConversations();
});

// -----------------------------------------------------------------------
// Charge la liste des discussions acceptées de l'utilisateur connecté.
// activeConversations() (défini dans User.php) charge déjà en une seule
// requête les relations user/friend + le dernier message (with(...)),
// ce qui évite le problème classique du "N+1" (une requête par ligne).
// -----------------------------------------------------------------------
$loadConversations = function () {
    $this->conversations = Auth::user()->activeConversations();
};

// Ouvre une discussion (appelable depuis ici, ou depuis un autre composant
// via $dispatch('openDiscussion', { id: ... }))
$openDiscussion = function ($id) {
    $this->connectionId = $id;
    $this->fetchMessages();
    $this->dispatch('discussion-opened', connectionId: $id);
};

// Écoute l'événement 'openDiscussion' et appelle directement l'action
// $openDiscussion ci-dessus (référencée par son nom, pas via une closure
// en ligne, pour éviter un bug connu de Volt qui génère un nom de méthode
// invalide comme "1Handler" quand une closure est passée directement).
on(['openDiscussion' => 'openDiscussion']);

$closeDiscussion = function () {
    $this->connectionId = null;
    $this->messages = [];
    $this->loadConversations(); // rafraîchit les aperçus de la liste
    $this->dispatch('discussion-closed');
};

// ==========================================================================
// PANNEAU "Infos du contact" — profil, médias partagés, bloquer, signaler
// ==========================================================================
$openContactInfo = function () {
    $this->contactInfoOpen = true;
};

$closeContactInfo = function () {
    $this->contactInfoOpen = false;
};

// Récupère les images partagées dans CETTE discussion, les plus récentes
// en premier, pour la mini-galerie du panneau "Infos du contact".
$getSharedMediaProperty = function () {
    if (!$this->connectionId) {
        return collect();
    }

    return ChatMessage::where('connection_id', $this->connectionId)
        ->where('attachment_mime', 'like', 'image/%')
        ->latest()
        ->limit(30)
        ->get();
};

// Bloque l'autre participant : plus aucun message ne peut être envoyé
// ni reçu tant que le blocage est actif (vérifié aussi au niveau du
// canal WebSocket dans routes/channels.php, pas seulement ici).
$blockUser = function () {
    $connection = Connection::find($this->connectionId);
    if ($connection) {
        $connection->update(['blocked_by' => Auth::id()]);
    }
};

// Seul celui qui a posé le blocage peut le lever
$unblockUser = function () {
    $connection = Connection::find($this->connectionId);
    if ($connection && $connection->blocked_by === Auth::id()) {
        $connection->update(['blocked_by' => null]);
    }
};

$reportUser = function () {
    $connection = Connection::find($this->connectionId);
    if (!$connection) return;

    $otherId = $connection->user_id === Auth::id() ? $connection->friend_id : $connection->user_id;

    \App\Models\Report::create([
        'reporter_id' => Auth::id(),
        'reported_user_id' => $otherId,
    ]);

    session()->flash('info', 'Signalement envoyé, merci — notre équipe va l\'examiner.');
};

// Supprime la discussion et tous ses messages. Simplification assumée :
// suppression pour LES DEUX participants (pas de suppression "juste pour
// moi" façon WhatsApp — hors scope pour l'instant).
$deleteDiscussion = function () {
    $connection = Connection::find($this->connectionId);
    if ($connection) {
        $connection->messages()->delete();
        $connection->delete();
    }

    $this->contactInfoOpen = false;
    $this->closeDiscussion();
};

// Écoute les nouveaux messages en temps réel via Reverb (WebSocket).
// Le nom du canal est dynamique : "chat.{connectionId}" est résolu avec
// la valeur actuelle de l'état $connectionId.
// (même remarque que ci-dessus : mapping direct vers fetchMessages,
// pas de closure en ligne, pour éviter le bug de compilation Volt.)
on(['echo-private:chat.{connectionId},MessageSent' => 'fetchMessages']);

// Écoute le canal PERSONNEL de l'utilisateur (App.Models.User.{id}, autorisé
// dans routes/channels.php). Ce canal reçoit une notification pour CHAQUE
// nouveau message qui le concerne, peu importe la discussion — c'est ce qui
// permet à la LISTE des discussions de se mettre à jour et de se réordonner
// en temps réel (le dernier message reçu remonte en haut), même quand on
// n'a pas la discussion concernée ouverte à l'écran.
on(['echo-private:App.Models.User.{myUserId},MessageSent' => 'loadConversations']);

$fetchMessages = function () {
    if ($this->connectionId) {
        $this->messages = ChatMessage::where('connection_id', $this->connectionId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark incoming messages as read
        ChatMessage::where('connection_id', $this->connectionId)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Refresh conversation previews and unread badges
        $this->loadConversations();
    }
};

$submitMessage = function () {
    $this->attachments = is_array($this->attachments) ? $this->attachments : ($this->attachments ? [$this->attachments] : []);

    Validator::make(
        [
            'textMessage' => $this->textMessage, 
            'attachments' => $this->attachments,
            'vocalAttachment' => $this->vocalAttachment
        ],
        [
            'textMessage' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array|max:10', // 10 fichiers max par envoi
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,zip,mp3,mp4,txt',
            'vocalAttachment' => 'nullable|file|max:10240|mimes:webm,ogg,wav,mp3,mp4,m4a,aac',
        ]
    )->validate();

    $hasText = trim($this->textMessage) !== '';
    $hasFiles = count($this->attachments) > 0;
    $hasVocal = !empty($this->vocalAttachment);

    if ((!$hasText && !$hasFiles && !$hasVocal) || !$this->connectionId) return;

    $connection = Connection::findOrFail($this->connectionId);
    $recipientId = $connection->user_id === Auth::id() ? $connection->friend_id : $connection->user_id;
    $recipient = User::find($recipientId);

    if ($hasText) {
        $msg = ChatMessage::create([
            'connection_id' => $this->connectionId,
            'sender_id' => Auth::id(),
            'message' => trim($this->textMessage),
            'parent_id' => $this->replyingToMessageId,
        ]);
        broadcast(new MessageSent($msg))->toOthers();
        if ($recipient) {
            $senderName = Auth::user()->display_name;
            $recipient->notify(new \App\Notifications\AppNotification(
                "Nouveau message",
                "{$senderName} : " . \Illuminate\Support\Str::limit($msg->message, 60),
                "message",
                "/socialnet?tab=right&connection=" . $this->connectionId,
                ['connection_id' => $this->connectionId]
            ));
        }
    }

    // Message vocal direct (envoi instantané type WhatsApp)
    if ($hasVocal) {
        $file = $this->vocalAttachment;
        $path = $file->store('chat-attachments/' . $this->connectionId, 'local');

        $msg = ChatMessage::create([
            'connection_id' => $this->connectionId,
            'sender_id' => Auth::id(),
            'message' => null,
            'attachment_path' => $path,
            'attachment_name' => 'voice-message.webm',
            'attachment_mime' => 'audio/webm',
            'attachment_size' => $file->getSize(),
            'parent_id' => $this->replyingToMessageId,
        ]);
        broadcast(new MessageSent($msg))->toOthers();
        if ($recipient) {
            $senderName = Auth::user()->display_name;
            $recipient->notify(new \App\Notifications\AppNotification(
                "Nouveau message vocal",
                "{$senderName} vous a envoyé un message vocal",
                "message",
                "/socialnet?tab=right&connection=" . $this->connectionId,
                ['connection_id' => $this->connectionId]
            ));
        }
        $this->vocalAttachment = null;
    }

    // Un message séparé par fichier, envoyé dans l'ordre de sélection.
    foreach ($this->attachments as $file) {
        $path = $file->store('chat-attachments/' . $this->connectionId, 'local');

        $msg = ChatMessage::create([
            'connection_id' => $this->connectionId,
            'sender_id' => Auth::id(),
            'message' => null,
            'attachment_path' => $path,
            'attachment_name' => $file->getClientOriginalName(),
            'attachment_mime' => $file->getMimeType(),
            'attachment_size' => $file->getSize(),
            'parent_id' => $this->replyingToMessageId,
        ]);
        broadcast(new MessageSent($msg))->toOthers();
        if ($recipient) {
            $senderName = Auth::user()->display_name;
            $recipient->notify(new \App\Notifications\AppNotification(
                "Nouveau message",
                "{$senderName} : Fichier joint",
                "message",
                "/socialnet?tab=right&connection=" . $this->connectionId,
                ['connection_id' => $this->connectionId]
            ));
        }
    }

    $this->textMessage = '';
    $this->attachments = [];
    $this->replyingToMessageId = null;
    $this->fetchMessages();
};

$deleteMessage = function ($messageId) {
    $msg = ChatMessage::findOrFail($messageId);
    abort_unless($msg->sender_id === Auth::id(), 403);
    
    if ($msg->hasAttachment() && \Illuminate\Support\Facades\Storage::disk('local')->exists($msg->attachment_path)) {
        \Illuminate\Support\Facades\Storage::disk('local')->delete($msg->attachment_path);
    }
    
    $msg->delete();
    $this->fetchMessages();
};

$startEdit = function ($messageId) {
    $msg = ChatMessage::findOrFail($messageId);
    abort_unless($msg->sender_id === Auth::id(), 403);
    $this->editingMessageId = $messageId;
    $this->editingText = $msg->message;
};

$cancelEdit = function () {
    $this->editingMessageId = null;
    $this->editingText = '';
};

$updateMessage = function () {
    if (!$this->editingMessageId) return;
    
    $msg = ChatMessage::findOrFail($this->editingMessageId);
    abort_unless($msg->sender_id === Auth::id(), 403);
    
    Validator::make(
        ['editingText' => $this->editingText],
        ['editingText' => 'required|string|max:2000']
    )->validate();
    
    $msg->update([
        'message' => trim($this->editingText),
        'is_edited' => true
    ]);
    
    $this->cancelEdit();
    $this->fetchMessages();
};

$startReply = function ($messageId) {
    $this->replyingToMessageId = $messageId;
};

$cancelReply = function () {
    $this->replyingToMessageId = null;
};

$removeAttachment = function ($index) {
    unset($this->attachments[$index]);
    $this->attachments = array_values($this->attachments); // réindexe le tableau
};

    on(['echo-private:App.Models.User.' . auth()->id() . ',MessageSent' => 'loadConversations']);

$broadcastCallIncoming = function () {
    $connection = Connection::findOrFail($this->connectionId);
    if ($connection->status !== 'accepted' || !in_array(Auth::id(), [$connection->user_id, $connection->friend_id])) {
        return;
    }
    broadcast(new \App\Events\CallIncoming(Auth::id(), Auth::user()->display_name, 'chat', $this->connectionId))->toOthers();
};

$broadcastCallOffer = function ($offer) {
    $connection = Connection::findOrFail($this->connectionId);
    if ($connection->status !== 'accepted' || !in_array(Auth::id(), [$connection->user_id, $connection->friend_id])) {
        return;
    }
    broadcast(new \App\Events\CallOffer(Auth::id(), 'chat', $this->connectionId, $offer))->toOthers();
};

$broadcastCallAnswer = function ($answer) {
    $connection = Connection::findOrFail($this->connectionId);
    if ($connection->status !== 'accepted' || !in_array(Auth::id(), [$connection->user_id, $connection->friend_id])) {
        return;
    }
    broadcast(new \App\Events\CallAnswer(Auth::id(), 'chat', $this->connectionId, $answer))->toOthers();
};

$broadcastIceCandidate = function ($candidate) {
    $connection = Connection::findOrFail($this->connectionId);
    if ($connection->status !== 'accepted' || !in_array(Auth::id(), [$connection->user_id, $connection->friend_id])) {
        return;
    }
    broadcast(new \App\Events\IceCandidate(Auth::id(), 'chat', $this->connectionId, $candidate))->toOthers();
};

$broadcastCallEnded = function () {
    $connection = Connection::findOrFail($this->connectionId);
    if ($connection->status !== 'accepted' || !in_array(Auth::id(), [$connection->user_id, $connection->friend_id])) {
        return;
    }
    broadcast(new \App\Events\CallEnded(Auth::id(), 'chat', $this->connectionId))->toOthers();
};

// =========================================================================
// ASSISTANT IA — panneau flottant à côté de la barre de saisie
// =========================================================================
$toggleAiPanel = function () {
    $this->aiPanelOpen = !$this->aiPanelOpen;
};

$askAI = function () {
    $question = trim($this->aiInput);
    if ($question === '') return;

    // ---------------------------------------------------------------
    // STRATÉGIE : protection contre la surcharge (rate limiting).
    // On limite le nombre d'appels IA par utilisateur/minute pour éviter
    // qu'un seul compte ne consomme tout le budget API ou ne sature le
    // service. Le compteur vit dans le cache (rapide), pas en base SQL.
    // ---------------------------------------------------------------
    $rateLimitKey = 'ai-chat:' . Auth::id();
    if (RateLimiter::tooManyAttempts($rateLimitKey, 15)) {
        $this->aiMessages[] = ['role' => 'assistant', 'content' => 'Tu poses des questions un peu trop vite, attends quelques secondes 😊'];
        return;
    }
    RateLimiter::hit($rateLimitKey, 60); // fenêtre glissante de 60 secondes, max 15 questions

    $this->aiMessages[] = ['role' => 'user', 'content' => $question];
    $this->aiInput = '';
    $this->aiLoading = true;

    $apiKey = config('services.gemini.key');
    if (!$apiKey || $apiKey === 'votre_cle_api_gemini_ici') {
        \Illuminate\Support\Facades\Log::error("Chat AI : Clé API Gemini manquante ou configurée par défaut dans services.php.");
        $this->aiMessages[] = ['role' => 'assistant', 'content' => "L'assistant IA de Nexora n'est pas encore configuré (clé API manquante)."];
        $this->aiLoading = false;
        return;
    }

    // Map history to Gemini format: user -> user, assistant -> model
    $contents = collect($this->aiMessages)
        ->map(fn ($m) => [
            'role' => $m['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $m['content']]]
        ])
        ->toArray();

    try {
        $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey,
            ])
            ->timeout(20) // on ne bloque jamais l'utilisateur trop longtemps
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent', [
                'contents' => $contents,
                'systemInstruction' => [
                    'parts' => [['text' => "Tu es l'assistant IA intégré au chat de Nexora, une application sociale basée sur la connexion par passions. Réponds de façon brève, chaleureuse et utile, en français."]]
                ]
            ]);

        if ($response->successful()) {
            $jsonData = $response->json();
            $text = $jsonData['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!$text) {
                \Illuminate\Support\Facades\Log::warning('Chat AI : Réponse JSON Gemini invalide ou structure inattendue.', ['json' => $jsonData]);
                $text = "Désolé, je n'ai pas pu décoder la réponse de l'IA.";
            }
        } else {
            \Illuminate\Support\Facades\Log::warning('Chat AI (Gemini) a échoué', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            $text = "L'assistant IA est momentanément indisponible, réessaie dans un instant.";
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Chat AI (Gemini) exception', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        $text = "L'assistant IA est momentanément indisponible, réessaie dans un instant. (Erreur: " . $e->getMessage() . ")";
    }

    $this->aiMessages[] = ['role' => 'assistant', 'content' => $text];
    $this->aiLoading = false;
};

$translateMessage = function ($messageId) {
    if (!Auth::user()->isPremium()) {
        return;
    }

    $msg = ChatMessage::findOrFail($messageId);
    $text = $msg->message;

    // Get the user's native language name
    $targetLang = Auth::user()->languages()->wherePivot('is_native', true)->first()?->name ?? 'French';

    $apiKey = config('services.gemini.key');
    if (!$apiKey || $apiKey === 'votre_cle_api_gemini_ici') {
        \Illuminate\Support\Facades\Log::error("Traduction Chat : Clé API Gemini manquante ou configurée par défaut dans services.php.");
        $this->translatedMessages[$messageId] = "Erreur de traduction : Clé API manquante.";
        return;
    }

    try {
        $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey,
            ])
            ->timeout(10)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent', [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => "Translate the following text to {$targetLang}. Provide ONLY the translation, with no extra text or explanations:\n\n{$text}"]]
                    ]
                ]
            ]);

        if ($response->successful()) {
            $jsonData = $response->json();
            $translated = $jsonData['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($translated) {
                $this->translatedMessages[$messageId] = trim($translated);
            } else {
                \Illuminate\Support\Facades\Log::warning('Traduction Chat : Réponse JSON Gemini invalide ou structure inattendue.', ['json' => $jsonData]);
                $this->translatedMessages[$messageId] = "Échec du décodage de la traduction.";
            }
        } else {
            \Illuminate\Support\Facades\Log::warning('Traduction Chat (Gemini) a échoué', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            $this->translatedMessages[$messageId] = "Échec de la traduction.";
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Traduction Chat (Gemini) exception', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        $this->translatedMessages[$messageId] = "Erreur de traduction.";
    }
};

// =========================================================================
// PANEL "Ajouter des passionnés"
// =========================================================================
$openAddFriends = function () {
    $this->addFriendsOpen = true;
};

$closeAddFriends = function () {
    $this->addFriendsOpen = false;
    $this->loadConversations();
};

$sendRequest = function ($userId) {
    // La contrainte unique(user_id, friend_id) empêche les doublons dans CE sens ;
    // on vérifie aussi le sens inverse pour éviter un conflit/duplication logique.
    $exists = Connection::where(function ($q) use ($userId) {
        $q->where('user_id', Auth::id())->where('friend_id', $userId);
    })->orWhere(function ($q) use ($userId) {
        $q->where('user_id', $userId)->where('friend_id', Auth::id());
    })->exists();

    if (!$exists) {
        $conn = Connection::create([
            'user_id' => Auth::id(),
            'friend_id' => $userId,
            'status' => 'pending',
        ]);

        $recipient = \App\Models\User::find($userId);
        if ($recipient) {
            $senderName = Auth::user()->display_name;
            $recipient->notify(new \App\Notifications\AppNotification(
                "Demande d'ami",
                "{$senderName} vous a envoyé une demande d'ami",
                "friend_request",
                "/socialnet?tab=right&requests=1",
                ['connection_id' => $conn->id]
            ));
        }
    }
};

$acceptRequest = function ($connectionId) {
    $connection = Connection::where('id', $connectionId)
        ->where('friend_id', Auth::id()) // seul le destinataire peut accepter
        ->first();

    if ($connection) {
        $connection->update(['status' => 'accepted']);
        $this->loadConversations();
    }
};

$declineRequest = function ($connectionId) {
    Connection::where('id', $connectionId)
        ->where('friend_id', Auth::id())
        ->delete();
};

// Discussions en attente reçues par l'utilisateur (à accepter/décliner)
$getReceivedRequestsProperty = function () {
    return Connection::with('user.interests', 'user.languages')
        ->where('friend_id', Auth::id())
        ->where('status', 'pending')
        ->latest()
        ->get();
};

// Suggestions filtrables : Tous / Mêmes passions / Même langue
$getSuggestionsProperty = function () {
    $me = Auth::user();
    $myInterestIds = $me->interests()->pluck('interests.id');
    $myLanguageIds = $me->languages()->pluck('languages.id');

    $alreadyLinkedIds = Connection::where('user_id', $me->id)->pluck('friend_id')
        ->merge(Connection::where('friend_id', $me->id)->pluck('user_id'))
        ->unique();

    $query = \App\Models\User::where('id', '!=', $me->id)
        ->whereNotIn('id', $alreadyLinkedIds)
        ->with('interests', 'languages');

    if ($this->addFilter === 'passions') {
        $query->whereHas('interests', fn($q) => $q->whereIn('interests.id', $myInterestIds));
    } elseif ($this->addFilter === 'lang') {
        $query->whereHas('languages', fn($q) => $q->whereIn('languages.id', $myLanguageIds));
    }

    return $query->get();
};

?>

<div class="glass rounded-2xl p-4 flex flex-col flex-1 min-h-0 relative">

    @if ($addFriendsOpen)
        {{-- ===== Vue "Ajouter des passionnés" ===== --}}
        <div class="flex flex-col flex-1 min-h-0">
            <header class="flex items-center gap-2 pb-3 border-b border-[#ead9d4]/70 shrink-0">
                <button wire:click="closeAddFriends" class="w-8 h-8 rounded-full glass flex items-center justify-center hover:bg-[#6b1f2a]/10" title="Retour">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="flex-1 min-w-0">
                    <div class="font-display text-lg leading-none truncate">Ajouter des passionnés</div>
                    <div class="text-[10px] text-neutral-500 mt-0.5">Trouve tes prochains alliés <i class="fa-solid fa-wand-magic-sparkles" style="color: #6b1f2a;"></i></div>
                </div>
            </header>

            <div class="flex flex-wrap gap-1.5 mt-3 shrink-0">
                <button wire:click="$set('addFilter', 'all')" class="chip {{ $addFilter === 'all' ? 'active' : '' }} px-2.5 py-1 rounded-full text-[10px] border border-[#ead9d4]">Tous</button>
                <button wire:click="$set('addFilter', 'passions')" class="chip {{ $addFilter === 'passions' ? 'active' : '' }} px-2.5 py-1 rounded-full text-[10px] border border-[#ead9d4] bg-[#fffdfb]/40">Mêmes passions</button>
                <button wire:click="$set('addFilter', 'lang')" class="chip {{ $addFilter === 'lang' ? 'active' : '' }} px-2.5 py-1 rounded-full text-[10px] border border-[#ead9d4] bg-[#fffdfb]/40">Même langue</button>
            </div>

            <div class="mt-3 flex-1 min-h-0 overflow-y-auto scroll-thin pr-1">
                @if ($this->receivedRequests->count() > 0)
                    <div class="mb-4">
                        <div class="text-[10px] uppercase tracking-widest text-neutral-500 mb-2 flex items-center justify-between">
                            <span>Demandes reçues</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full btn-wine font-semibold">{{ $this->receivedRequests->count() }}</span>
                        </div>
                        <ul class="space-y-2">
                            @foreach ($this->receivedRequests as $req)
                                @php $initials = $req->user->initials; @endphp
                                <li class="glass rounded-xl p-2.5 flex items-center gap-3" wire:key="req-{{ $req->id }}">
                                    <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] flex items-center justify-center text-white text-xs font-semibold">{{ $initials }}</div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium truncate">@​{{ $req->user->display_name }}</div>
                                        <div class="text-[11px] text-neutral-500 truncate">{{ $req->user->location ?? '' }}</div>
                                    </div>
                                    <button wire:click="acceptRequest({{ $req->id }})" class="btn-wine text-[10px] font-semibold px-2.5 py-1.5 rounded-full shrink-0">Accepter</button>
                                    <button wire:click="declineRequest({{ $req->id }})" class="glass text-[10px] font-medium px-2.5 py-1.5 rounded-full shrink-0 hover:bg-[#6b1f2a]/10">Décliner</button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <div class="text-[10px] uppercase tracking-widest text-neutral-500 mb-2">Suggestions</div>
                    <ul class="space-y-2">
                        @forelse ($this->suggestions as $sugg)
                            @php $initials = $sugg->initials; @endphp
                            <li class="glass rounded-xl p-2.5 flex items-center gap-3" wire:key="sugg-{{ $sugg->id }}">
                                <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-br from-[#f2d9c4] to-[#a83248] flex items-center justify-center text-white text-xs font-semibold">{{ $initials }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium truncate">@​{{ $sugg->display_name }}</div>
                                    <div class="text-[11px] text-neutral-500 truncate">{{ $sugg->location ?? '' }}</div>
                                </div>
                                <button wire:click="sendRequest({{ $sugg->id }})" class="btn-wine text-[10px] font-semibold px-3 py-1.5 rounded-full shrink-0">+ Ajouter</button>
                            </li>
                        @empty
                            <li class="text-center text-xs text-neutral-500 py-4">Aucune suggestion.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

    @elseif (!$connectionId)
        {{-- ===== Vue LISTE des discussions ===== --}}
        <div class="flex flex-col flex-1 min-h-0" wire:poll.visible.10s="loadConversations">
            <div class="flex items-center justify-between mb-3 shrink-0">
                <h2 class="font-display text-lg">Discussions</h2>
                <button wire:click="openAddFriends" class="w-8 h-8 rounded-full btn-wine flex items-center justify-center text-lg font-light hover:scale-105 transition" title="Ajouter">+</button>
            </div>

            @if (count($conversations) === 0)
                <div class="flex-1 flex items-center justify-center text-center p-6 text-sm text-[#2b1a1d]/60">
                    Tu n'as pas encore de discussion. Ajoute des passionnés pour commencer à discuter !
                </div>
            @else
                <ul class="list-scroll scroll-thin space-y-1 pr-1">
                    @foreach ($conversations as $conversation)
                        @php
                            $other = $conversation->user_id === auth()->id() ? $conversation->friend : $conversation->user;
                            $lastMessage = $conversation->latestMessage;
                            $initials = $other->initials;
                        @endphp
                        <li wire:click="openDiscussion({{ $conversation->id }})" wire:key="conv-{{ $conversation->id }}"
                            class="room-item flex items-center gap-3 p-2.5 rounded-xl cursor-pointer hover:bg-[#6b1f2a]/5 transition">
                            <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] flex items-center justify-center text-white font-semibold text-xs">
                                {{ $initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-sm truncate">@​{{ $other->display_name }}</div>
                                <div class="text-[11px] {{ $conversation->unread_messages_count > 0 ? 'text-[#6b1f2a] font-semibold' : 'text-neutral-500' }} truncate">
                                    @if ($lastMessage)
                                        {{ $lastMessage->sender_id === auth()->id() ? 'Toi : ' : '' }}{{ Str::limit($lastMessage->message, 32) }}
                                    @else
                                        Dis bonjour 👋
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col items-end shrink-0 gap-1">
                                @if ($lastMessage)
                                    <div class="text-[10px] text-neutral-400">{{ $lastMessage->created_at->format('H:i') }}</div>
                                @endif
                                @if ($conversation->unread_messages_count > 0)
                                    <span class="bg-[#a83248] text-white text-[9px] px-1.5 py-0.5 rounded-full font-semibold">{{ $conversation->unread_messages_count }}</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    @else
        {{-- ===== Vue CHAT actif ===== --}}
        @php
            $activeConversation = $conversations->firstWhere('id', $connectionId)
                ?? Connection::with(['user', 'friend'])->find($connectionId);
            $other = $activeConversation->user_id === auth()->id() ? $activeConversation->friend : $activeConversation->user;
            $initials = $other->initials;
        @endphp
        <div class="flex flex-col flex-1 min-h-0">
            <header class="flex items-center gap-2 pb-3 border-b border-[#ead9d4]/70 shrink-0">
                <button wire:click="closeDiscussion" class="w-8 h-8 rounded-full glass flex items-center justify-center hover:bg-[#6b1f2a]/10 shrink-0" title="Retour">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button wire:click="openContactInfo" class="flex items-center gap-2 flex-1 min-w-0 text-left" title="Voir les infos du contact">
                    <div class="relative shrink-0">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] flex items-center justify-center text-white font-semibold text-xs">{{ $initials }}</div>
                        <span class="absolute bottom-0 right-0 dot-live ring-2 ring-[#fbf6f0]"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm truncate">@​{{ $other->display_name }}</div>
                        <div class="text-[10px] text-neutral-500 truncate">{{ $activeConversation->isBlocked() ? 'Bloqué' : 'En ligne' }}</div>
                    </div>
                </button>

                {{-- Call feature removed --}}
            </header>

            @if ($contactInfoOpen)
                {{-- ===== Panneau "Infos du contact" ===== --}}
                <div class="flex flex-col flex-1 min-h-0 overflow-y-auto scroll-thin py-4">
                    <div class="flex flex-col items-center text-center px-4">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] flex items-center justify-center text-white font-display text-2xl font-semibold">{{ $initials }}</div>
                        <div class="font-display text-lg mt-3">@​{{ $other->username }}</div>
                        <div class="text-xs text-neutral-500 mt-0.5">{{ $other->bio ?? 'Aucune bio pour le moment' }}</div>
                        <button wire:click="closeContactInfo" class="mt-4 text-xs text-[#6b1f2a] font-medium">← Retour à la discussion</button>
                    </div>

                    <div class="mt-6 px-4">
                        <div class="text-[10px] uppercase tracking-widest text-neutral-500 mb-2">Médias partagés</div>
                        @if ($this->sharedMedia->count() > 0)
                            <div class="grid grid-cols-3 gap-1.5">
                                @foreach ($this->sharedMedia as $media)
                                    <a href="{{ route('chat.attachment', $media, false) }}" target="_blank" class="aspect-square rounded-lg overflow-hidden block" wire:key="media-{{ $media->id }}">
                                        <img src="{{ route('chat.attachment', $media, false) }}" class="w-full h-full object-cover" loading="lazy" />
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-xs text-neutral-500 text-center py-4">Aucune image partagée pour l'instant.</div>
                        @endif
                    </div>

                    <div class="mt-6 px-4 space-y-1">
                        @if ($activeConversation->isBlocked())
                            @if ($activeConversation->blocked_by === auth()->id())
                                <button wire:click="unblockUser" wire:confirm="Débloquer {{ '@' . $other->username }} ?" class="w-full text-left px-3 py-2.5 rounded-xl text-sm hover:bg-[#6b1f2a]/5">
                                    🔓 Débloquer {{ '@' . $other->username }}
                                </button>
                            @else
                                <div class="px-3 py-2.5 text-xs text-neutral-500">Ce contact vous a bloqué.</div>
                            @endif
                        @else
                            <button wire:click="blockUser" wire:confirm="Bloquer {{ '@' . $other->username }} ? Vous ne pourrez plus échanger de messages tant que le blocage est actif." class="w-full text-left px-3 py-2.5 rounded-xl text-sm hover:bg-[#6b1f2a]/5">
                                🚫 Bloquer {{ '@' . $other->username }}
                            </button>
                        @endif

                        <button wire:click="reportUser" wire:confirm="Signaler {{ '@' . $other->username }} à l'équipe Nexora ?" class="w-full text-left px-3 py-2.5 rounded-xl text-sm hover:bg-[#6b1f2a]/5">
                            🚩 Signaler {{ '@' . $other->username }}
                        </button>

                        <button wire:click="deleteDiscussion" wire:confirm="Supprimer définitivement cette discussion ? Cette action est irréversible." class="w-full text-left px-3 py-2.5 rounded-xl text-sm text-red-600 hover:bg-red-50">
                            🗑️ Supprimer la discussion
                        </button>
                    </div>
                </div>
            @else

            <div class="chat-messages scroll-thin py-3 space-y-3" wire:poll.visible.5s="fetchMessages" x-data x-init="$el.scrollTop = $el.scrollHeight" x-on:livewire:updated="$el.scrollTop = $el.scrollHeight">
                
                @forelse ($messages as $msg)
                    <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'gap-2 items-end' }} group relative" wire:key="msg-{{ $msg->id }}">
                        @if ($msg->sender_id !== auth()->id())
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] shrink-0 flex items-center justify-center text-[10px] text-white font-semibold">{{ mb_substr($other->initials, 0, 1) }}</div>
                        @endif
                        
                        <div class="flex items-center gap-1.5 {{ $msg->sender_id === auth()->id() ? 'flex-row-reverse' : '' }} max-w-[80%]">
                            <div class="{{ $msg->sender_id === auth()->id() ? 'msg-out rounded-2xl rounded-br-md' : 'msg-in rounded-2xl rounded-bl-md' }} {{ $msg->isImageAttachment() && !$msg->message && !$msg->parent ? 'p-1' : 'px-3.5 py-2.5' }} text-sm break-words">
                                
                                {{-- Threading preview --}}
                                @if ($msg->parent)
                                    <div class="text-[10px] opacity-75 border-l-2 border-[#6b1f2a] pl-2 mb-1.5 italic bg-black/5 rounded py-0.5 px-1.5 truncate">
                                        <b>{{ $msg->parent->sender_id === auth()->id() ? 'Moi' : $msg->parent->sender->display_name }}</b> : 
                                        {{ $msg->parent->message ?: '📎 Pièce jointe' }}
                                    </div>
                                @endif

                                @if ($msg->hasAttachment())
                                    @if ($msg->isImageAttachment())
                                        <a href="{{ route('chat.attachment', $msg, false) }}" target="_blank" class="block {{ $msg->message ? 'mb-2' : '' }}">
                                            <img src="{{ route('chat.attachment', $msg, false) }}" alt="{{ $msg->attachment_name }}" class="rounded-xl max-w-full block" style="max-height:220px;object-fit:cover" loading="lazy" />
                                        </a>
                                    @elseif ($msg->isAudioAttachment())
                                        <div class="block mt-1.5 mb-1 text-neutral-800">
                                            <div x-data="{
                                                playing: false,
                                                audio: null,
                                                duration: '0:00',
                                                currentTime: '0:00',
                                                progress: 0,
                                                init() {
                                                    this.audio = new Audio('{{ route('chat.attachment', $msg, false) }}');
                                                    this.audio.addEventListener('loadedmetadata', () => {
                                                        this.duration = this.formatTime(this.audio.duration);
                                                    });
                                                    this.audio.addEventListener('timeupdate', () => {
                                                        this.currentTime = this.formatTime(this.audio.currentTime);
                                                        this.progress = (this.audio.currentTime / this.audio.duration) * 100;
                                                    });
                                                    this.audio.addEventListener('ended', () => {
                                                        this.playing = false;
                                                        this.progress = 0;
                                                        this.currentTime = '0:00';
                                                    });
                                                },
                                                togglePlay() {
                                                    if (this.playing) {
                                                        this.audio.pause();
                                                        this.playing = false;
                                                    } else {
                                                        this.audio.play();
                                                        this.playing = true;
                                                    }
                                                },
                                                formatTime(secs) {
                                                    if (isNaN(secs)) return '0:00';
                                                    const m = Math.floor(secs / 60);
                                                    const s = Math.floor(secs % 60).toString().padStart(2, '0');
                                                    return `${m}:${s}`;
                                                }
                                            }" class="flex items-center gap-3 p-2.5 rounded-2xl bg-black/5 hover:bg-black/10 transition max-w-[280px]">
                                                <!-- Play/Pause Button -->
                                                <button @click="togglePlay" class="w-10 h-10 rounded-full bg-[#6b1f2a] text-white flex items-center justify-center hover:scale-105 transition shadow-md shrink-0 focus:outline-none">
                                                    <span class="text-sm"><i :class="playing ? 'fa-solid fa-pause' : 'fa-solid fa-play'"></i></span>
                                                </button>
                                                <!-- Waveform & Time -->
                                                <div class="flex-1 min-w-0 flex flex-col gap-1">
                                                    <div class="flex items-end gap-[3px] h-6 px-1 items-center">
                                                        <template x-for="(h, i) in [12,18,10,14,22,16,10,14,18,12,16,20,12,14,10,16]">
                                                            <div class="w-[3px] rounded-full transition-all duration-150" 
                                                                 :class="i / 16 * 100 < progress ? 'bg-[#6b1f2a]' : 'bg-[#6b1f2a]/20'"
                                                                 :style="`height: ${h}px`"
                                                                 style="height: 12px"></div>
                                                        </template>
                                                    </div>
                                                    <div class="flex items-center justify-between text-[9px] text-neutral-500 font-semibold leading-none">
                                                        <span x-text="playing ? currentTime : duration">0:00</span>
                                                        <span class="flex items-center gap-1"><i class="fa-solid fa-microphone"></i> vocal</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif (str_starts_with($msg->attachment_mime ?? '', 'video/'))
                                        <div class="block mt-1 mb-2 rounded-lg overflow-hidden max-w-xs">
                                            <video src="{{ route('chat.attachment', $msg, false) }}" controls class="w-full max-h-[220px] object-cover"></video>
                                        </div>
                                    @else
                                        <a href="{{ route('chat.attachment', $msg, false) }}" class="flex items-center gap-2 rounded-xl px-2.5 py-2 {{ $msg->message ? 'mb-2' : '' }}" style="background:rgba(0,0,0,.06)">
                                            <span class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-base" style="background:rgba(0,0,0,.08)"><i class="fa-regular fa-file"></i></span>
                                            <span class="flex-1 min-w-0">
                                                <span class="block truncate text-xs font-medium">{{ $msg->attachment_name }}</span>
                                                <span class="block text-[10px] opacity-70">{{ $msg->attachmentSizeForHumans() }}</span>
                                            </span>
                                        </a>
                                    @endif
                                @endif

                                @if ($editingMessageId === $msg->id)
                                    {{-- Inline Editor --}}
                                    <div class="flex flex-col gap-1.5 min-w-[200px]">
                                        <input type="text" wire:model="editingText" wire:keydown.enter="updateMessage" class="w-full bg-white/70 text-[#2b1a1d] rounded-xl px-2.5 py-1.5 text-xs outline-none border border-[#ead9d4]" />
                                        <div class="flex gap-2 justify-end">
                                            <button wire:click="cancelEdit" class="text-[10px] text-neutral-500 font-medium">Annuler</button>
                                            <button wire:click="updateMessage" class="text-[10px] text-[#6b1f2a] font-bold">Enregistrer</button>
                                        </div>
                                    </div>
                                @else
                                    @if ($msg->message)
                                        <div>{{ $msg->message }}</div>
                                        @if(auth()->user()->isPremium() && $msg->sender_id !== auth()->id())
                                            @if(isset($translatedMessages[$msg->id]))
                                                <div class="text-[11px] opacity-90 border-t border-[#ead9d4]/40 mt-1.5 pt-1.5 italic">
                                                    🌐 {{ $translatedMessages[$msg->id] }}
                                                </div>
                                            @else
                                                <button wire:click="translateMessage({{ $msg->id }})" class="translate-badge text-[9px] px-1.5 py-0.5 rounded-full mt-1.5 flex items-center gap-1 hover:scale-105 transition" title="Traduire le message">
                                                    🌐 Traduire
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                    
                                    @if($msg->is_edited)
                                        <span class="text-[9px] opacity-60 block mt-0.5 text-right">(modifié)</span>
                                    @endif
                                @endif
                            </div>

                            {{-- Hover Actions --}}
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-1 items-center shrink-0">
                                <button wire:click="startReply({{ $msg->id }})" class="text-neutral-400 hover:text-[#6b1f2a] text-xs p-1" title="Répondre"><i class="fa-solid fa-reply"></i></button>
                                @if ($msg->sender_id === auth()->id())
                                    <button wire:click="startEdit({{ $msg->id }})" class="text-neutral-400 hover:text-[#6b1f2a] text-xs p-1" title="Modifier"><i class="fa-solid fa-pen"></i></button>
                                    <button wire:click="deleteMessage({{ $msg->id }})" wire:confirm="Supprimer ce message ?" class="text-neutral-400 hover:text-[#a83248] text-xs p-1" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-[10px] text-neutral-500 mt-1 absolute bottom-[-15px] {{ $msg->sender_id === auth()->id() ? 'right-1' : 'left-9' }}">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                    
                    {{-- Spacer for absolute time rendering --}}
                    <div class="h-3"></div>
                @empty
                    <div class="flex-1 flex items-center justify-center text-center p-6 text-sm text-[#2b1a1d]/60">
                        C'est le début de votre discussion avec {{ '@' . $other->username }} 👋
                    </div>
                @endforelse
            </div>

            {{-- ===== Aperçu de la pièce jointe en attente d'envoi ===== --}}
           @if (count($attachments) > 0)
                <div class="mt-3 flex items-center gap-2 overflow-x-auto scroll-thin pb-1 shrink-0">
                    @foreach ($attachments as $index => $file)
                        <div class="relative shrink-0" wire:key="pending-{{ $index }}">
                            @if (str_starts_with($file->getMimeType() ?? '', 'image/'))
                                <img src="{{ $file->temporaryUrl() }}" class="w-14 h-14 rounded-lg object-cover" />
                            @else
                                <div class="w-14 h-14 rounded-lg flex flex-col items-center justify-center text-center px-1" style="background:rgba(107,31,42,.08)">
                                    <span class="text-lg">📄</span>
                                    <span class="text-[8px] truncate w-full">{{ $file->getClientOriginalName() }}</span>
                                </div>
                            @endif
                            <button wire:click="removeAttachment({{ $index }})" class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-[#2b1a1d] text-white flex items-center justify-center text-[10px]" title="Retirer">✕</button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- ===== Barre de saisie + icône IA ===== --}}
            @if ($activeConversation->isBlocked())
                <div class="pt-3 border-t border-[#ead9d4]/70 text-center text-xs text-neutral-500 py-3 shrink-0">
                    {{ $activeConversation->blocked_by === auth()->id() ? 'Vous avez bloqué ce contact. Débloquez-le depuis "Infos du contact" pour reprendre la discussion.' : 'Vous ne pouvez plus envoyer de messages à ce contact.' }}
                </div>
            @else
            {{-- ===== Aperçu du message auquel on répond ===== --}}
            @if ($replyingToMessageId)
                @php
                    $replyParent = App\Models\ChatMessage::find($replyingToMessageId);
                @endphp
                @if ($replyParent)
                    <div class="flex items-center justify-between px-3 py-1.5 bg-[#6b1f2a]/5 border-t border-[#ead9d4]/70 rounded-t-2xl shrink-0 text-xs text-neutral-600 gap-2">
                        <div class="truncate flex items-center gap-1.5 min-w-0">
                            <span class="text-neutral-400 shrink-0">En réponse à</span>
                            <b class="shrink-0">{{ $replyParent->sender_id === auth()->id() ? 'Moi' : ($replyParent->sender->username ?? $replyParent->sender->name) }}</b>
                            <span class="truncate italic text-neutral-500">: {{ $replyParent->message ?: '📎 Fichier joint' }}</span>
                        </div>
                        <button wire:click="cancelReply" class="text-neutral-400 hover:text-[#6b1f2a] text-[10px] shrink-0 font-semibold p-1">✕</button>
                    </div>
                @endif
            @endif

            {{-- ===== Barre de saisie + icône IA ===== --}}
            <div class="pt-3 border-t border-[#ead9d4]/70 flex items-center gap-2 shrink-0 relative"
                 x-data="{
                    recording: false,
                    mediaRecorder: null,
                    audioChunks: [],
                    startRecording() {
                        navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
                            this.recording = true;
                            this.audioChunks = [];
                            this.mediaRecorder = new MediaRecorder(stream);
                            this.mediaRecorder.ondataavailable = e => this.audioChunks.push(e.data);
                            this.mediaRecorder.onstop = () => {
                                const blob = new Blob(this.audioChunks, { type: 'audio/webm' });
                                const file = new File([blob], 'voice-message.webm', { type: 'audio/webm' });
                                @this.upload('vocalAttachment', file, () => {
                                    @this.submitMessage();
                                });
                            };
                            this.mediaRecorder.start();
                        }).catch(err => {
                            alert('Veuillez autoriser l\'accès au microphone.');
                        });
                    },
                    stopRecording() {
                        if (this.mediaRecorder) {
                            this.mediaRecorder.stop();
                            this.recording = false;
                            this.mediaRecorder.stream.getTracks().forEach(t => t.stop());
                        }
                    }
                 }">
                <!-- Media Actions (left side) -->
                <div class="flex items-center gap-1 shrink-0">
                    <label for="chat-attachment-{{ $connectionId }}" class="w-9 h-9 rounded-full glass flex items-center justify-center cursor-pointer hover:bg-[#6b1f2a]/10" title="Joindre un fichier">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a5 5 0 01-7.07-7.07l9.19-9.19a3 3 0 014.24 4.24l-9.2 9.19a1 1 0 01-1.41-1.41l8.49-8.48"/></svg>
                    </label>
                    <input type="file" id="chat-attachment-{{ $connectionId }}" wire:model="attachments" multiple class="hidden" />

                    <button type="button" @click="recording ? stopRecording() : startRecording()" :class="recording ? 'bg-[#a83248] text-white animate-pulse' : 'glass'" class="w-9 h-9 rounded-full flex items-center justify-center hover:bg-[#6b1f2a]/10" title="Message vocal">
                        <span x-html="recording ? '<i class=\'fa-solid fa-square\'></i>' : '<i class=\'fa-solid fa-microphone\'></i>'"></span>
                    </button>
                </div>

                <!-- Input area (center) -->
                <div class="flex-1 min-w-0 bg-[#fdf4ec]/40 border border-[#ead9d4]/70 rounded-full px-3.5 py-1.5 flex items-center gap-2">
                    <input type="text" wire:model="textMessage" wire:keydown.enter="submitMessage" placeholder="Écris un message…" class="flex-1 bg-transparent outline-none text-sm placeholder:text-neutral-500 min-w-0" />
                    
                    <!-- Emoji inside input on the right -->
                    <div x-data="{ open: false }" class="relative shrink-0 flex items-center">
                        <button type="button" @click="open = !open" class="w-6 h-6 hover:scale-105 transition flex items-center justify-center text-base" title="Emojis">
                            <i class="fa-regular fa-face-smile"></i>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute bottom-9 right-0 bg-[#fffdfb] border border-[#ead9d4] rounded-2xl shadow-xl p-2 grid grid-cols-6 gap-1.5 z-50 w-48">
                            <template x-for="emoji in ['😀', '😂', '😍', '👍', '🎉', '🔥', '💡', '🚀', '💬', '🌐', '👑', '🎤', '❤️', '👏', '🙌', '🌟', '✨', '✔️']">
                                <button type="button" @click="$wire.textMessage = ($wire.textMessage || '') + emoji; open = false;" class="w-6 h-6 hover:bg-[#6b1f2a]/10 rounded flex items-center justify-center text-sm" x-text="emoji"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Chat Actions (right side) -->
                <div class="flex items-center gap-1 shrink-0">
                    <button wire:click="toggleAiPanel" class="ai-toggle w-9 h-9 rounded-full flex items-center justify-center {{ $aiPanelOpen ? 'is-active' : '' }}" title="Demander à l'IA">
                        <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>

                    <button wire:click="submitMessage" class="w-9 h-9 rounded-full btn-wine flex items-center justify-center text-sm">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </div>

                {{-- ===== Panneau IA flottant ===== --}}
                @if ($aiPanelOpen)
                    <div class="ai-panel glass-strong rounded-2xl p-3 flex flex-col">
                        <div class="ai-panel-header flex items-center justify-between pb-2 mb-2 border-b border-[#ead9d4]/70 shrink-0">
                            <div class="flex items-center gap-2">
                                <span class="ai-dot"></span>
                                <span class="font-display text-sm">Assistant Nexora</span>
                            </div>
                            <button wire:click="toggleAiPanel" class="w-6 h-6 rounded-full glass flex items-center justify-center hover:bg-[#6b1f2a]/10" title="Fermer">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="ai-panel-messages scroll-thin flex-1 min-h-0 space-y-2 pr-1">
                            @forelse ($aiMessages as $aiMsg)
                                <div class="flex {{ $aiMsg['role'] === 'user' ? 'justify-end' : '' }}">
                                    <div class="{{ $aiMsg['role'] === 'user' ? 'msg-out rounded-2xl rounded-br-md' : 'msg-in rounded-2xl rounded-bl-md' }} px-3 py-2 text-xs max-w-[85%]">
                                        {{ $aiMsg['content'] }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-[11px] text-neutral-500 p-4">
                                    Pose-moi une question, je suis là pour t'aider <i class="fa-solid fa-wand-magic-sparkles" style="color: #6b1f2a;"></i>
                                </div>
                            @endforelse

                            @if ($aiLoading)
                                <div class="flex">
                                    <div class="msg-in rounded-2xl rounded-bl-md px-3 py-2 text-xs">
                                        <span class="ai-typing"><i></i><i></i><i></i></span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 mt-2 border-t border-[#ead9d4]/70 flex items-center gap-1.5 shrink-0">
                            <input type="text" wire:model="aiInput" wire:keydown.enter="askAI" placeholder="Ta question…" class="flex-1 bg-transparent outline-none text-xs placeholder:text-neutral-500 min-w-0" />
                            <button wire:click="askAI" class="w-7 h-7 shrink-0 rounded-full btn-wine flex items-center justify-center">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
            @endif
            @endif
        </div>
    @endif
    <script>
        window.chatBoxLivewire = @this;
    </script>
</div>