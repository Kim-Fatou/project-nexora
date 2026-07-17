<?php
//page socialnet.blade.php

use function Livewire\Volt\{state, mount, on, uses, layout, with};
use Livewire\WithFileUploads;
use App\Models\Room;
use App\Models\RoomMessage;
use App\Models\Interest;
use App\Models\Story;
use App\Models\StoryView;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

uses([WithFileUploads::class]);

layout('layouts.app');

function compressImage($sourcePath, $destinationPath, $quality = 80) {
    $info = @getimagesize($sourcePath);
    if ($info === false) {
        return false;
    }

    if ($info['mime'] == 'image/jpeg') {
        $image = @imagecreatefromjpeg($sourcePath);
    } elseif ($info['mime'] == 'image/gif') {
        $image = @imagecreatefromgif($sourcePath);
    } elseif ($info['mime'] == 'image/png') {
        $image = @imagecreatefrompng($sourcePath);
        if ($image) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }
    } elseif ($info['mime'] == 'image/webp') {
        $image = @imagecreatefromwebp($sourcePath);
    } else {
        return false;
    }

    if (!$image) {
        return false;
    }

    $result = imagewebp($image, $destinationPath, $quality);
    imagedestroy($image);
    return $result;
}

state([
    // ----- Liste des salons -----
    'roomFilter' => 'all', // 'all' ou 'joined'
    'activeTab' => 'center', // 'left', 'center' or 'right'

    // ----- Salon actuellement ouvert -----
    'activeRoomId' => null,
    'roomTextMessage' => '',
    'roomAttachment' => null,

    // ----- Modale "Créer un salon" -----
    'showCreateRoomModal' => false,
    'newRoomName' => '',
    'newRoomDescription' => '',
    'newRoomInterestId' => null,
    'availableInterests' => [],

    // ----- Stories : barre horizontale (liste des amis ayant une story active) -----
    'storiesFeed' => [],

    // ----- Stories : modale de création -----
    'showCreateStoryModal' => false,
    'newStoryType' => 'text', // 'text' ou 'image'
    'newStoryText' => '',
    'newStoryColor' => '#6b1f2a',
    'newStoryImage' => null,

    // ----- Stories : visionnage -----
    'viewingUserId' => null,   // si non-null, la modale de visionnage est ouverte
    'viewingStories' => [],    // toutes les stories actives de cet utilisateur (ordre chronologique)
    'viewingIndex' => 0,       // position actuelle dans viewingStories

    // ----- Posts : flux public -----
    'newPostContent' => '',
    'newPostImage' => null,

    // ----- Posts : commentaires -----
    'openCommentsFor' => [],   // ids des posts dont le panneau "commentaires" est déplié
    'commentDrafts' => [],     // brouillon de commentaire racine par post : ['<postId>' => 'texte']
    'replyDrafts' => [],       // brouillon de réponse par commentaire : ['<commentId>' => 'texte']
    'replyingTo' => [],        // id du commentaire actuellement "en réponse" par post : ['<postId>' => commentId]
    
    // ----- Premium Tab -----
    'leftTab' => 'rooms',       // 'rooms' or 'premium'

    // ----- Salon Message Editing & Replying -----
    'editingRoomMessageId' => null,
    'editingRoomText' => '',
    'replyingToRoomMessageId' => null,

    // ----- Search Filter -----
    'searchQuery' => '',

    // ----- Settings Modal -----
    'showSettingsModal' => false,
    'showDeleteConfirm' => false,
    'settingsBio' => '',
    'settingsCity' => '',
    
    // ----- Notifications -----
    'unreadNotificationsCount' => 0,
    'myUserId' => null,
]);

mount(function () {
    $this->myUserId = Auth::id();
    $this->availableInterests = Interest::orderBy('name')->get(['id', 'name'])->toArray();
    $this->loadStories();
});

// -----------------------------------------------------------------------
// Charge tous les salons publics, en marquant ceux déjà rejoints par
// l'utilisateur connecté (propriété runtime 'joined', pas persistée).
// -----------------------------------------------------------------------
$loadRooms = function () {
    $userId = Auth::id();

    $query = Room::with('interest')
        ->withCount('members')
        ->where('is_public', true)
        ->orderBy('name');

    if (!empty($this->searchQuery)) {
        $qText = '%' . trim($this->searchQuery) . '%';
        $query->where(function ($sub) use ($qText) {
            $sub->where('name', 'like', $qText)
                ->orWhere('description', 'like', $qText)
                ->orWhereHas('interest', function ($iSub) use ($qText) {
                    $iSub->where('name', 'like', $qText);
                });
        });
    }

    $rooms = $query->get();
    $joinedRoomIds = Auth::user()->rooms()->pluck('rooms.id')->toArray();

    return $rooms->map(function ($room) use ($joinedRoomIds) {
        $room->joined = in_array($room->id, $joinedRoomIds);
        return $room;
    });
};

$setRoomFilter = function ($filter) {
    $this->roomFilter = $filter;
};

$setLeftTab = function ($tab) {
    $this->leftTab = $tab;
    $this->activeTab = 'left';
};

// ----- Notifications & Realtime bell -----
$loadNotifications = function () {
    $user = Auth::user();
    if ($user) {
        $this->unreadNotificationsCount = $user->unreadNotifications()->count();
        return $user->notifications()->latest()->take(20)->get();
    }
    return [];
};

$markAllNotificationsAsRead = function () {
    $user = Auth::user();
    if ($user) {
        $user->unreadNotifications->markAsRead();
    }
};

$readNotification = function ($id) {
    $user = Auth::user();
    if ($user) {
        $notif = $user->notifications()->find($id);
        if ($notif) {
            $notif->markAsRead();

            // Redirect or trigger action based on type
            $data = $notif->data;
            if (($data['type'] ?? '') === 'message' && isset($data['extra_data']['connection_id'])) {
                $this->activeTab = 'right';
                $this->dispatch('openDiscussion', id: $data['extra_data']['connection_id']);
            } elseif (($data['type'] ?? '') === 'room_message' && isset($data['extra_data']['room_id'])) {
                $this->activeTab = 'left';
                $this->openRoom($data['extra_data']['room_id']);
            } elseif (isset($data['url'])) {
                // If it has a url, redirect or handle tab change
                if (str_contains($data['url'], 'requests=1')) {
                    $this->activeTab = 'right';
                    $this->dispatch('openAddFriends'); // Open friend requests panel
                }
            }
        }
    }
};

on(['echo-private:App.Models.User.{myUserId},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated' => 'loadNotifications']);

// Rejoindre un salon (ouvre directement la discussion une fois rejoint)
$joinRoom = function ($roomId) {
    $room = Room::findOrFail($roomId);

    if (!$room->hasMember(Auth::id())) {
        $room->members()->attach(Auth::id(), ['joined_at' => now()]);
        Auth::user()->addXp(20);
    }

    $this->openRoom($roomId);
};

$openRoom = function ($roomId) {
    $room = Room::findOrFail($roomId);

    // Sécurité : on ne peut ouvrir la discussion que si on est membre
    if (!$room->hasMember(Auth::id())) {
        return;
    }

    $this->activeRoomId = $roomId;
    $this->dispatch('room-opened', roomId: $roomId);
};

$closeRoom = function () {
    $this->activeRoomId = null;
    $this->dispatch('room-closed');
};

// Écoute temps réel des nouveaux messages du salon ouvert (Reverb)
on(['echo-private:room.{activeRoomId},RoomMessageSent' => 'fetchRoomMessages']);

$fetchRoomMessages = function () {
    if ($this->activeRoomId) {
        return RoomMessage::where('room_id', $this->activeRoomId)
            ->orderBy('created_at', 'asc')
            ->get();
    }
    return [];
};

$broadcastRoomCallIncoming = function () {
    $room = Room::findOrFail($this->activeRoomId);
    if (!$room->hasMember(Auth::id())) {
        return;
    }
    broadcast(new \App\Events\CallIncoming(Auth::id(), Auth::user()->display_name, 'room', $this->activeRoomId))->toOthers();
};

$broadcastRoomCallOffer = function ($offer) {
    $room = Room::findOrFail($this->activeRoomId);
    if (!$room->hasMember(Auth::id())) {
        return;
    }
    broadcast(new \App\Events\CallOffer(Auth::id(), 'room', $this->activeRoomId, $offer))->toOthers();
};

$broadcastRoomCallAnswer = function ($answer) {
    $room = Room::findOrFail($this->activeRoomId);
    if (!$room->hasMember(Auth::id())) {
        return;
    }
    broadcast(new \App\Events\CallAnswer(Auth::id(), 'room', $this->activeRoomId, $answer))->toOthers();
};

$broadcastRoomIceCandidate = function ($candidate) {
    $room = Room::findOrFail($this->activeRoomId);
    if (!$room->hasMember(Auth::id())) {
        return;
    }
    broadcast(new \App\Events\IceCandidate(Auth::id(), 'room', $this->activeRoomId, $candidate))->toOthers();
};

$broadcastRoomCallEnded = function () {
    $room = Room::findOrFail($this->activeRoomId);
    if (!$room->hasMember(Auth::id())) {
        return;
    }
    broadcast(new \App\Events\CallEnded(Auth::id(), 'room', $this->activeRoomId))->toOthers();
};

$sendRoomMessage = function () {
    $room = Room::findOrFail($this->activeRoomId);

    // Un utilisateur qui n'est pas (ou plus) membre ne peut pas poster
    abort_unless($room->hasMember(Auth::id()), 403);

    Validator::make(
        ['roomTextMessage' => $this->roomTextMessage, 'roomAttachment' => $this->roomAttachment],
        [
            'roomTextMessage' => 'nullable|string|max:2000',
            'roomAttachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,zip,mp3,mp4,txt,webm,ogg,wav,m4a,aac',
        ]
    )->validate();

    $hasText = trim($this->roomTextMessage) !== '';
    $hasFile = $this->roomAttachment !== null;

    if ((!$hasText && !$hasFile) || !$this->activeRoomId) return;

    $attachmentData = [];

    if ($hasFile) {
        // Même logique de sécurité que le chat privé : stockage hors public/,
        // servi uniquement via RoomAttachmentController (vérifie l'appartenance).
        $path = $this->roomAttachment->store('room-attachments/' . $this->activeRoomId, 'local');

        $attachmentData = [
            'attachment_path' => $path,
            'attachment_name' => $this->roomAttachment->getClientOriginalName(),
            'attachment_mime' => $this->roomAttachment->getMimeType(),
            'attachment_size' => $this->roomAttachment->getSize(),
        ];
    }

    $msg = RoomMessage::create(array_merge([
        'room_id' => $this->activeRoomId,
        'sender_id' => Auth::id(),
        'message' => $hasText ? trim($this->roomTextMessage) : null,
        'parent_id' => $this->replyingToRoomMessageId,
    ], $attachmentData));

    broadcast(new \App\Events\RoomMessageSent($msg))->toOthers();

    // Notify other room members
    $room = Room::findOrFail($this->activeRoomId);
    $members = $room->members()->where('users.id', '!=', Auth::id())->get();
    $senderName = Auth::user()->display_name;
    $preview = $hasText ? trim($this->roomTextMessage) : "Fichier joint";
    foreach ($members as $m) {
        $m->notify(new \App\Notifications\AppNotification(
            "Message dans {$room->name}",
            "{$senderName} : " . \Illuminate\Support\Str::limit($preview, 60),
            "room_message",
            "/socialnet?tab=left&room=" . $this->activeRoomId,
            ['room_id' => $this->activeRoomId]
        ));
    }

    $this->roomTextMessage = '';
    $this->roomAttachment = null;
    $this->replyingToRoomMessageId = null;
};

$startRoomEdit = function ($messageId) {
    $message = RoomMessage::findOrFail($messageId);
    abort_unless($message->sender_id === Auth::id(), 403);
    $this->editingRoomMessageId = $messageId;
    $this->editingRoomText = $message->message;
};

$cancelRoomEdit = function () {
    $this->editingRoomMessageId = null;
    $this->editingRoomText = '';
};

$updateRoomMessage = function () {
    if (!$this->editingRoomMessageId) return;
    
    $message = RoomMessage::findOrFail($this->editingRoomMessageId);
    abort_unless($message->sender_id === Auth::id(), 403);
    
    Validator::make(
        ['editingRoomText' => $this->editingRoomText],
        ['editingRoomText' => 'required|string|max:2000']
    )->validate();
    
    $message->update([
        'message' => trim($this->editingRoomText),
        'is_edited' => true
    ]);
    
    $this->cancelRoomEdit();
};

$startRoomReply = function ($messageId) {
    $this->replyingToRoomMessageId = $messageId;
};

$cancelRoomReply = function () {
    $this->replyingToRoomMessageId = null;
};

// Supprime UN message de salon — seul l'expéditeur peut supprimer son propre message.
$deleteRoomMessage = function ($messageId) {
    $message = RoomMessage::findOrFail($messageId);
    abort_unless($message->sender_id === Auth::id(), 403);

    if ($message->hasAttachment() && \Illuminate\Support\Facades\Storage::disk('local')->exists($message->attachment_path)) {
        \Illuminate\Support\Facades\Storage::disk('local')->delete($message->attachment_path);
    }

    $message->delete();
};

$removeRoomAttachment = function () {
    $this->roomAttachment = null;
};

// -----------------------------------------------------------------------
// Création d'un nouveau salon par l'utilisateur
// -----------------------------------------------------------------------
$openCreateRoomModal = function () {
    $this->showCreateRoomModal = true;
};

$closeCreateRoomModal = function () {
    $this->showCreateRoomModal = false;
    $this->newRoomName = '';
    $this->newRoomDescription = '';
    $this->newRoomInterestId = null;
};

$createRoom = function () {
    $validator = validator([
        'name' => $this->newRoomName,
        'description' => $this->newRoomDescription,
        'interest_id' => $this->newRoomInterestId,
    ], [
        'name' => ['required', 'string', 'min:3', 'max:100'],
        'description' => ['nullable', 'string', 'max:280'],
        'interest_id' => ['nullable', 'integer', 'exists:interests,id'],
    ]);

    if ($validator->fails()) {
        return;
    }

    $room = Room::create([
        'name' => $this->newRoomName,
        'slug' => Room::uniqueSlugFor($this->newRoomName),
        'description' => $this->newRoomDescription ?: null,
        'interest_id' => $this->newRoomInterestId ?: null,
        'creator_id' => Auth::id(),
        'is_public' => true,
    ]);

    // Le créateur rejoint automatiquement son propre salon
    $room->members()->attach(Auth::id(), ['joined_at' => now()]);

    $this->closeCreateRoomModal();
    $this->openRoom($room->id);
};

// =========================================================================
// STORIES
// =========================================================================

// -----------------------------------------------------------------------
// Construit la barre du haut : une entrée par ami (+ moi-même) ayant au
// moins une story active (< 24h). Triée : moi en premier, puis les amis
// dont je n'ai pas encore vu toutes les stories, puis les autres.
// -----------------------------------------------------------------------
$loadStories = function () {
    $userId = Auth::id();
    $ids = Auth::user()->friendIds()->push($userId)->unique();

    $storiesByUser = Story::active()
        ->whereIn('user_id', $ids)
        ->orderBy('created_at', 'asc')
        ->get()
        ->groupBy('user_id');

    $feed = [];
    foreach ($ids as $uid) {
        if (!isset($storiesByUser[$uid])) {
            continue; // cet utilisateur n'a aucune story active en ce moment
        }

        $userStories = $storiesByUser[$uid];
        $hasUnseen = $userStories->contains(fn ($s) => !$s->viewedBy($userId));

        $feed[] = [
            'user_id' => $uid,
            'user_name' => $userStories->first()->user->username,
            'has_unseen' => $hasUnseen,
            'is_me' => $uid === $userId,
        ];
    }

    // Tri : moi d'abord, puis non-vues avant vues
    usort($feed, function ($a, $b) {
        if ($a['is_me'] !== $b['is_me']) {
            return $a['is_me'] ? -1 : 1;
        }
        if ($a['has_unseen'] !== $b['has_unseen']) {
            return $a['has_unseen'] ? -1 : 1;
        }
        return 0;
    });

    $this->storiesFeed = $feed;
};

// ----- Création -----

$openCreateStoryModal = function () {
    $this->showCreateStoryModal = true;
};

$closeCreateStoryModal = function () {
    $this->showCreateStoryModal = false;
    $this->newStoryType = 'text';
    $this->newStoryText = '';
    $this->newStoryColor = '#6b1f2a';
    $this->newStoryImage = null;
};

$setNewStoryType = function ($type) {
    $this->newStoryType = $type;
};

$publishStory = function () {
    if ($this->newStoryType === 'image') {
        Validator::make(
            ['newStoryImage' => $this->newStoryImage],
            ['newStoryImage' => 'required|file|max:20480|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm']
        )->validate();

        // Fichier hors public/, servi uniquement via StoryAttachmentController
        // (vérifie que le visiteur est bien un ami avant de le montrer).
        $path = $this->newStoryImage->store('story-attachments/' . Auth::id(), 'local');
        $mime = $this->newStoryImage->getMimeType();
        
        if (str_starts_with($mime, 'image/')) {
            $fullPath = storage_path('app/private/' . $path);
            $compressedPath = $fullPath . '.webp';
            if (compressImage($fullPath, $compressedPath, 80)) {
                @unlink($fullPath);
                $path = $path . '.webp';
                $mime = 'image/webp';
            }
        }
        
        $type = str_starts_with($mime, 'video/') ? 'video' : 'image';

        Story::create([
            'user_id' => Auth::id(),
            'type' => $type,
            'media_path' => $path,
            'media_mime' => $mime,
            'expires_at' => now()->addDay(), // la story expire dans 24h
        ]);
    } else {
        Validator::make(
            ['newStoryText' => $this->newStoryText],
            ['newStoryText' => 'required|string|max:200']
        )->validate();

        Story::create([
            'user_id' => Auth::id(),
            'type' => 'text',
            'text_content' => trim($this->newStoryText),
            'background_color' => $this->newStoryColor,
            'expires_at' => now()->addDay(),
        ]);
    }

    $this->closeCreateStoryModal();
    $this->loadStories();
};

// ----- Visionnage -----

$openStory = function ($userId) {
    // Sécurité : on ne peut ouvrir que ses propres stories ou celles d'un ami
    $allowedIds = Auth::user()->friendIds()->push(Auth::id());
    abort_unless($allowedIds->contains($userId), 403);

    $this->viewingStories = Story::active()
        ->where('user_id', $userId)
        ->orderBy('created_at', 'asc')
        ->get();

    if ($this->viewingStories->isEmpty()) {
        return; // plus aucune story active pour cette personne (expirée entre-temps)
    }

    $this->viewingUserId = $userId;
    $this->viewingIndex = 0;
    $this->markCurrentStoryViewed();
};

// Marque la story actuellement affichée comme "vue" (sauf si c'est la mienne)
$markCurrentStoryViewed = function () {
    $story = $this->viewingStories[$this->viewingIndex] ?? null;

    if ($story && $story->user_id !== Auth::id()) {
        StoryView::firstOrCreate(
            ['story_id' => $story->id, 'viewer_id' => Auth::id()],
            ['viewed_at' => now()]
        );
    }
};

$toggleStoryLike = function ($storyId) {
    $userId = Auth::id();
    $view = StoryView::where('story_id', $storyId)->where('viewer_id', $userId)->first();
    
    if ($view) {
        $view->update(['liked' => !$view->liked]);
    } else {
        StoryView::create([
            'story_id' => $storyId,
            'viewer_id' => $userId,
            'viewed_at' => now(),
            'liked' => true
        ]);
    }
    
    if ($this->viewingUserId) {
        $this->viewingStories = Story::active()
            ->where('user_id', $this->viewingUserId)
            ->orderBy('created_at', 'asc')
            ->get();
    }
};

$nextStory = function () {
    if ($this->viewingIndex < count($this->viewingStories) - 1) {
        $this->viewingIndex++;
        $this->markCurrentStoryViewed();
    } else {
        $this->closeStoryViewer(); // dernière story -> on ferme, comme sur Snap/Insta
    }
};

$prevStory = function () {
    if ($this->viewingIndex > 0) {
        $this->viewingIndex--;
    }
};

$closeStoryViewer = function () {
    $this->viewingUserId = null;
    $this->viewingStories = [];
    $this->viewingIndex = 0;
    $this->loadStories(); // rafraîchit les indicateurs "vu / pas vu" dans la barre
};

// =========================================================================
// POSTS — flux public (texte + image optionnelle, likes, commentaires imbriqués)
// =========================================================================

// -----------------------------------------------------------------------
// Charge le flux : les publications les plus récentes d'abord, avec le
// nombre de likes, si JE l'ai déjà liké, et les commentaires (racines +
// réponses) préchargés pour éviter le N+1 à l'affichage.
$loadPosts = function () {
    $userId = Auth::id();

    $query = Post::with(['user', 'rootComments'])
        ->withCount('likes')
        ->latest()
        ->limit(50);

    if (!empty($this->searchQuery)) {
        $qText = '%' . trim($this->searchQuery) . '%';
        $query->where(function ($sub) use ($qText) {
            $sub->where('content', 'like', $qText)
                ->orWhereHas('user', function ($uSub) use ($qText) {
                    $uSub->where('username', 'like', $qText);
                });
        });
    }

    $posts = $query->get();
    $likedPostIds = \App\Models\PostLike::where('user_id', $userId)
        ->whereIn('post_id', $posts->pluck('id'))
        ->pluck('post_id')
        ->toArray();

    return $posts->map(function ($post) use ($likedPostIds) {
        $post->liked_by_me = in_array($post->id, $likedPostIds);
        return $post;
    });
};

// ----- Publication -----

$publishPost = function () {
    Validator::make(
        ['newPostContent' => $this->newPostContent, 'newPostImage' => $this->newPostImage],
        [
            'newPostContent' => 'nullable|string|max:2000',
            'newPostImage' => 'nullable|file|max:20480|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm',
        ]
    )->validate();

    $hasText = trim((string) $this->newPostContent) !== '';
    $hasImage = $this->newPostImage !== null;

    // Un post doit contenir au moins un texte ou une image
    if (!$hasText && !$hasImage) {
        return;
    }

    $imageData = [];

    if ($hasImage) {
        // Fichier hors public/, servi uniquement via PostAttachmentController
        // (même logique que story-attachments / room-attachments).
        $path = $this->newPostImage->store('post-attachments/' . Auth::id(), 'local');
        $mime = $this->newPostImage->getMimeType();

        if (str_starts_with($mime, 'image/')) {
            $fullPath = storage_path('app/private/' . $path);
            $compressedPath = $fullPath . '.webp';
            if (compressImage($fullPath, $compressedPath, 80)) {
                @unlink($fullPath);
                $path = $path . '.webp';
                $mime = 'image/webp';
            }
        }

        $imageData = [
            'image_path' => $path,
            'image_mime' => $mime,
        ];
    }

    Post::create(array_merge([
        'user_id' => Auth::id(),
        'content' => $hasText ? trim($this->newPostContent) : null,
    ], $imageData));

    $this->newPostContent = '';
    $this->newPostImage = null;
};

// Suppression : seul l'auteur peut supprimer sa propre publication
$deletePost = function ($postId) {
    $post = Post::findOrFail($postId);
    abort_unless($post->user_id === Auth::id(), 403);

    $post->delete();
};

// ----- Likes -----

$toggleLike = function ($postId) {
    $userId = Auth::id();

    $existing = PostLike::where('post_id', $postId)->where('user_id', $userId)->first();

    if ($existing) {
        $existing->delete();
    } else {
        $like = PostLike::create(['post_id' => $postId, 'user_id' => $userId]);
        // Notify post author
        $post = Post::find($postId);
        if ($post && $post->user_id !== $userId) {
            $senderName = Auth::user()->display_name;
            $post->user->notify(new \App\Notifications\AppNotification(
                "Nouveau j'aime",
                "{$senderName} a aimé ton post",
                "like",
                "/socialnet?post=" . $postId,
                ['post_id' => $postId]
            ));
        }
    }
};

// ----- Commentaires -----

// Ouvre/ferme le panneau "commentaires" sous une publication
$toggleComments = function ($postId) {
    if (in_array($postId, $this->openCommentsFor)) {
        $this->openCommentsFor = array_values(array_diff($this->openCommentsFor, [$postId]));
    } else {
        $this->openCommentsFor[] = $postId;
    }
};

// Ajoute un commentaire racine (pas une réponse)
$addComment = function ($postId) {
    $text = trim((string) ($this->commentDrafts[$postId] ?? ''));

    Validator::make(
        ['comment' => $text],
        ['comment' => 'required|string|max:1000']
    )->validate();

    $comment = PostComment::create([
        'post_id' => $postId,
        'user_id' => Auth::id(),
        'parent_id' => null,
        'content' => $text,
    ]);

    // Notify post author
    $post = Post::find($postId);
    if ($post && $post->user_id !== Auth::id()) {
        $senderName = Auth::user()->display_name;
        $post->user->notify(new \App\Notifications\AppNotification(
            "Nouveau commentaire",
            "{$senderName} a commenté ton post",
            "comment",
            "/socialnet?post=" . $postId,
            ['post_id' => $postId, 'comment_id' => $comment->id]
        ));
    }

    $this->commentDrafts[$postId] = '';
};

// Ouvre le champ de réponse sous un commentaire précis
$startReply = function ($postId, $commentId) {
    $this->replyingTo[$postId] = $commentId;
};

$cancelReply = function ($postId) {
    unset($this->replyingTo[$postId]);
};

// Ajoute une réponse (parent_id = le commentaire auquel on répond)
$addReply = function ($postId, $parentId) {
    $text = trim((string) ($this->replyDrafts[$parentId] ?? ''));

    Validator::make(
        ['reply' => $text],
        ['reply' => 'required|string|max:1000']
    )->validate();

    $reply = PostComment::create([
        'post_id' => $postId,
        'user_id' => Auth::id(),
        'parent_id' => $parentId,
        'content' => $text,
    ]);

    // Notify parent comment author
    $parentComment = PostComment::find($parentId);
    if ($parentComment && $parentComment->user_id !== Auth::id()) {
        $senderName = Auth::user()->display_name;
        $parentComment->user->notify(new \App\Notifications\AppNotification(
            "Nouveau commentaire",
            "{$senderName} a répondu à ton commentaire",
            "comment",
            "/socialnet?post=" . $postId,
            ['post_id' => $postId, 'comment_id' => $reply->id]
        ));
    }

    $this->replyDrafts[$parentId] = '';
    unset($this->replyingTo[$postId]);
};

// Suppression : seul l'auteur du commentaire peut le supprimer (ses réponses
// partent aussi grâce au cascadeOnDelete sur parent_id).
$deleteComment = function ($commentId) {
    $comment = PostComment::findOrFail($commentId);
    abort_unless($comment->user_id === Auth::id(), 403);

    $comment->delete();
};

$openPremiumTab = function () {
    $this->leftTab = 'premium';
};

$loadExperts = function () {
    $user = Auth::user();
    $myInterestIds = $user->interests()->pluck('interests.id');
    
    $query = \App\Models\User::where('is_expert', true)
        ->whereIn('expertise_interest_id', $myInterestIds)
        ->with('expertiseInterest');

    if (!empty($this->searchQuery)) {
        $qText = '%' . trim($this->searchQuery) . '%';
        $query->where(function ($sub) use ($qText) {
            $sub->where('username', 'like', $qText)
                ->orWhereHas('expertiseInterest', function ($iSub) use ($qText) {
                    $iSub->where('name', 'like', $qText);
                });
        });
    }

    $experts = $query->get();

    if ($experts->isEmpty() && empty($this->searchQuery)) {
        $experts = \App\Models\User::where('is_expert', true)
            ->with('expertiseInterest')
            ->limit(10)
            ->get();
    }
    
    return $experts;
};

$updatedSearchQuery = function () {
    // dynamically loaded by with() block on render
};

$activatePremium = function () {
    \App\Models\Subscription::updateOrCreate(
        ['user_id' => Auth::id()],
        [
            'status' => 'active',
            'plan_type' => 'monthly',
            'ends_at' => now()->addMonth()
        ]
    );
};

$contactExpert = function ($expertId) {
    $userId = Auth::id();
    $connection = \App\Models\Connection::where(function($q) use ($userId, $expertId) {
        $q->where('user_id', $userId)->where('friend_id', $expertId);
    })->orWhere(function($q) use ($userId, $expertId) {
        $q->where('user_id', $expertId)->where('friend_id', $userId);
    })->first();

    if (!$connection) {
        $connection = \App\Models\Connection::create([
            'user_id' => $userId,
            'friend_id' => $expertId,
            'status' => 'accepted'
        ]);
    } else if ($connection->status !== 'accepted') {
        $connection->update(['status' => 'accepted']);
    }

    $this->dispatch('openDiscussion', id: $connection->id);
    $this->activeTab = 'right';
    $this->dispatch('mobile-tab-change', tab: 'right');
};

$openSettingsModal = function () {
    $user = Auth::user();
    $this->settingsBio = $user->bio ?? '';
    $this->settingsCity = $user->city ?? '';
    $this->showDeleteConfirm = false;
    $this->showSettingsModal = true;
};

$closeSettingsModal = function () {
    $this->showDeleteConfirm = false;
    $this->showSettingsModal = false;
};

$saveSettings = function () {
    $user = Auth::user();
    
    Validator::make(
        ['settingsBio' => $this->settingsBio, 'settingsCity' => $this->settingsCity],
        [
            'settingsBio' => 'nullable|string|max:500',
            'settingsCity' => 'nullable|string|max:100',
        ]
    )->validate();
    
    $user->update([
        'bio' => trim($this->settingsBio),
        'city' => trim($this->settingsCity),
    ]);
    
    $this->closeSettingsModal();
    session()->flash('info', 'Réglages enregistrés !');
};

$logout = function () {
    Auth::logout();
    Session::invalidate();
    Session::regenerateToken();
    return redirect()->to('/');
};

$deleteAccount = function () {
    $user = Auth::user();
    if (!$user) return;

    // Déconnexion et invalidation de la session d'abord pour éviter que Auth::logout()
    // ne tente de sauvegarder le remember_token sur un utilisateur supprimé (ce qui le recréerait).
    Auth::logout();
    Session::invalidate();
    Session::regenerateToken();

    try {
        \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
            // Justification DDIA : Suppression réelle en cascade pour le respect du RGPD
            // Purge manuelle ordonnée des tables enfants pour contourner les verrous/contraintes de clés étrangères
            
            // 1. Likes
            \App\Models\PostLike::where('user_id', $user->id)->delete();
            
            // 2. Commentaires
            \App\Models\PostComment::where('user_id', $user->id)->delete();
            
            // 3. Publications & images associées
            $posts = \App\Models\Post::where('user_id', $user->id)->get();
            foreach ($posts as $post) {
                if ($post->image_path) {
                    @unlink(storage_path('app/private/' . $post->image_path));
                }
                $post->delete();
            }
            
            // 4. Stories & Story Views
            \App\Models\StoryView::where('user_id', $user->id)->delete();
            $stories = \App\Models\Story::where('user_id', $user->id)->get();
            foreach ($stories as $story) {
                if ($story->media_path) {
                    @unlink(storage_path('app/private/' . $story->media_path));
                }
                $story->delete();
            }
            
            // 5. Relations d'amis (connections)
            \App\Models\Connection::where('user_id', $user->id)
                ->orWhere('friend_id', $user->id)
                ->delete();
            
            // 6. Messages de chat (privés)
            $chatMsgs = \App\Models\ChatMessage::where('sender_id', $user->id)->get();
            foreach ($chatMsgs as $msg) {
                if ($msg->attachment_path) {
                    @unlink(storage_path('app/private/' . $msg->attachment_path));
                }
                $msg->delete();
            }
            
            // 7. Messages de salon
            $roomMsgs = \App\Models\RoomMessage::where('sender_id', $user->id)->get();
            foreach ($roomMsgs as $msg) {
                if ($msg->attachment_path) {
                    @unlink(storage_path('app/private/' . $msg->attachment_path));
                }
                $msg->delete();
            }
            
            // 8. Appartenance aux salons (pivot)
            $user->rooms()->detach();
            
            // 9. Notifications
            $user->notifications()->delete();
            
            // 10. Compte principal
            $user->delete();
        });
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error("Erreur lors de la suppression du compte: " . $e->getMessage());
    }

    return redirect()->to('/');
};

$getRoomsSearchResults = function () {
    if (empty($this->searchQuery)) return collect();
    $q = '%' . trim($this->searchQuery) . '%';
    $rooms = Room::with('interest')
        ->withCount('members')
        ->where('is_public', true)
        ->where(function ($sub) use ($q) {
            $sub->where('name', 'like', $q)
                ->orWhere('description', 'like', $q)
                ->orWhereHas('interest', function ($iSub) use ($q) {
                    $iSub->where('name', 'like', $q);
                });
        })
        ->take(10)
        ->get();

    $joinedRoomIds = Auth::user()->rooms()->pluck('rooms.id')->toArray();

    return $rooms->map(function ($room) use ($joinedRoomIds) {
        $room->joined = in_array($room->id, $joinedRoomIds);
        return $room;
    });
};

$getUsersSearchResults = function () {
    if (empty($this->searchQuery)) return collect();
    $q = '%' . trim($this->searchQuery) . '%';
    return User::where('id', '!=', Auth::id())
        ->where('username', 'like', $q)
        ->take(10)
        ->get();
};

$getPostsSearchResults = function () {
    if (empty($this->searchQuery)) return collect();
    
    // DDIA Justification: MATCH AGAINST for FULLTEXT search performance, fallback to LIKE for very short terms (< 3 chars)
    // or when running SQLite (testing environment)
    $term = trim($this->searchQuery);
    if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite' || strlen($term) < 3) {
        return Post::with('user')
            ->where('content', 'like', "%{$term}%")
            ->latest()
            ->take(10)
            ->get();
    }
    
    return Post::with('user')
        ->whereRaw("MATCH(content) AGAINST(? IN BOOLEAN MODE)", [$term])
        ->latest()
        ->take(10)
        ->get();
};

with(fn () => [
    'rooms' => $this->loadRooms(),
    'posts' => $this->loadPosts(),
    'roomMessages' => $this->fetchRoomMessages(),
    'notifications' => $this->loadNotifications(),
    'experts' => $this->loadExperts(),
]);
?>


<div class="volt">
  <link rel="stylesheet" href="{{ asset('css/socialnet.css') }}"> 

  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>

  <div class="app-shell relative z-10">

    <!-- ===== HEADER ===== -->
    <header class="glass-strong rounded-2xl px-4 sm:px-6 py-3 flex items-center justify-between shrink-0 relative z-[70]">
      <div class="flex items-center gap-3 min-w-0">
        <a href="{{ url('/') }}" class="flex items-center">
          <img src="{{ asset('images/logos/logonexora.png') }}" alt="Nexora Logo" style="height: 36px; width: auto; object-fit: contain; display: block;" />
        </a>
        <div class="min-w-0 hidden sm:block">
          <div class="text-[10px] text-neutral-500 tracking-wider uppercase truncate">Le réseau des passionnés</div>
        </div>
      </div>

      <div class="hidden md:flex items-center gap-2 flex-1 max-w-lg mx-8">
        <div class="glass rounded-full px-4 py-2 flex items-center gap-2 w-full">
          <svg class="w-4 h-4 text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input wire:model.live.debounce.300ms="searchQuery" placeholder="Rechercher un salon, une passion, une personne…" class="bg-transparent outline-none text-sm w-full placeholder:text-neutral-500 min-w-0"/>
          <span class="kbd hidden lg:inline">⌘K</span>
        </div>
      </div>

      <div class="flex items-center gap-3 shrink-0 relative">
        <!-- Dashboard Link -->
        <a href="{{ route('dashboard') }}" class="glass rounded-full w-9 h-9 flex items-center justify-center hover:bg-[#6b1f2a]/10" title="Tableau de bord">
          <i class="fa-solid fa-gauge-high text-sm text-neutral-600"></i>
        </a>
        <!-- Notifications -->
        <div class="relative">
          <button id="btnNotif" class="glass rounded-full w-9 h-9 flex items-center justify-center relative">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-4-5.7V5a2 2 0 10-4 0v.3A6 6 0 006 11v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0"/></svg>
            @if($unreadNotificationsCount > 0)
              <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#a83248] text-white text-[9px] font-bold rounded-full ring-2 ring-[#fbf6f0] flex items-center justify-center">{{ $unreadNotificationsCount }}</span>
            @endif
          </button>
          <div id="dropdownNotif" class="dropdown glass-strong rounded-2xl p-3 hidden-x">
            <div class="flex items-center justify-between mb-2 px-2 gap-4 shrink-0">
              <div class="font-display text-sm">Notifications</div>
              @if($unreadNotificationsCount > 0)
                <button wire:click="markAllNotificationsAsRead" class="text-[10px] text-[#6b1f2a] font-medium hover:underline">Tout marquer lu</button>
              @endif
            </div>
            <ul class="space-y-1 max-h-80 overflow-y-auto scroll-thin">
              @forelse($notifications as $notif)
                @php
                  $icon = match($notif->data['type'] ?? '') {
                    'message' => '<i class="fa-solid fa-comment-dots"></i>',
                    'room_message' => '<i class="fa-solid fa-users"></i>',
                    'like' => '<i class="fa-solid fa-heart"></i>',
                    'comment' => '<i class="fa-solid fa-comment"></i>',
                    'friend_request' => '<i class="fa-solid fa-user-plus"></i>',
                    default => '<i class="fa-solid fa-bell"></i>'
                  };
                @endphp
                <li wire:click="readNotification('{{ $notif->id }}')" class="flex items-start gap-2.5 p-2 rounded-xl hover:bg-[#6b1f2a]/5 cursor-pointer transition {{ $notif->unread() ? 'bg-[#6b1f2a]/5' : '' }}">
                  <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#f2d9c4] to-[#a83248] flex items-center justify-center text-white text-base font-semibold shrink-0">{!! $icon !!}</div>
                  <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold">{{ $notif->data['title'] ?? 'Notification' }}</div>
                    <div class="text-[11px] text-neutral-600 mt-0.5 leading-snug">{{ $notif->data['message'] ?? '' }}</div>
                    <div class="text-[9px] text-neutral-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                  </div>
                </li>
              @empty
                <li class="text-center text-xs text-neutral-500 py-6">Aucune notification.</li>
              @endforelse
            </ul>
          </div>
        </div>

        <!-- Profil -->
        <div class="relative">
          <button id="btnProfile" class="w-9 h-9 rounded-full ring-gradient p-[2px] block">
            <div class="w-full h-full rounded-full bg-[#fbf6f0] flex items-center justify-center font-semibold text-sm">{{ auth()->user()->initials }}</div>
          </button>
          <div id="dropdownProfile" class="dropdown glass-strong rounded-2xl p-4 hidden-x" style="min-width:260px">
            <div class="flex items-center gap-3 pb-3 border-b border-[#ead9d4]/70">
              <div class="w-12 h-12 rounded-full ring-gradient p-[2px]">
                <div class="w-full h-full rounded-full bg-[#fbf6f0] flex items-center justify-center font-semibold">{{ auth()->user()->initials }}</div>
              </div>
              <div class="min-w-0">
                <div class="font-semibold text-sm truncate">@​{{ auth()->user()->username }}</div>
                <div class="text-[11px] text-neutral-500 truncate">{{ auth()->user()->city ?? 'Paris' }}</div>
                <div class="text-[10px] mt-1 inline-block px-1.5 py-0.5 rounded-full" style="background:rgba(107,31,42,.08);color:#6b1f2a">Passions : {{ auth()->user()->interests->pluck('name')->take(2)->join(' · ') }}</div>
              </div>
            </div>
            <div class="grid grid-cols-3 gap-2 py-3 text-center">
              <div><div class="font-display text-base">248</div><div class="text-[9px] uppercase text-neutral-500 tracking-wider">Amis</div></div>
              <div><div class="font-display text-base">42</div><div class="text-[9px] uppercase text-neutral-500 tracking-wider">Salons</div></div>
              <div><div class="font-display text-base">1.2k</div><div class="text-[9px] uppercase text-neutral-500 tracking-wider">J'aime</div></div>
            </div>
            <button onclick="goToProfile()" class="btn-wine w-full text-xs font-semibold py-2.5 rounded-full">Voir plus / Mon Profil →</button>
            <div class="flex gap-2 mt-2">
              <button wire:click="openSettingsModal" class="flex-1 glass rounded-full py-2 text-[11px] hover:bg-[#6b1f2a]/5"><i class="fa-solid fa-gear"></i> Réglages</button>
              <button wire:click="logout" class="flex-1 glass rounded-full py-2 text-[11px] hover:bg-[#6b1f2a]/5"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</button>
            </div>
            @if(auth()->user()->is_admin)
              <a href="{{ route('admin') }}" class="btn-wine w-full text-xs font-semibold py-2 rounded-full text-center block mt-2" style="background: #a3273a; color: white; text-decoration: none;">
                <i class="fa-solid fa-lock"></i> Administration
              </a>
            @endif
          </div>
        </div>
      </div>
    </header>

    <!-- ===== BODY (3 COLUMNS) ===== -->
    <div id="appBody" class="app-body">

      <!-- ========= LEFT COLUMN : NAVIGATION + SALONS ========= -->
      <aside id="colLeft" class="col-panel {{ $activeTab === 'left' ? 'mobile-visible' : '' }}">
        <div class="glass rounded-2xl p-4 flex flex-col flex-1 min-h-0">

          <div class="hidden lg:block shrink-0 pb-3 border-b border-[#ead9d4]/70 mb-3">
            <div class="text-[10px] uppercase tracking-widest text-neutral-500 mb-2">Navigation</div>
            <div class="flex flex-col gap-1">
              <button wire:click="$set('activeTab', 'center')" class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-left w-full {{ $activeTab === 'center' ? 'active' : '' }}">
                <span><i class="fa-solid fa-house"></i></span>
                <span>Flux</span>
              </button>
              <button wire:click="setLeftTab('rooms')" class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-left w-full {{ $leftTab === 'rooms' && $activeTab !== 'center' ? 'active' : '' }}">
                <span><i class="fa-solid fa-comments"></i></span>
                <span>Discussions</span>
              </button>
              <button wire:click="setLeftTab('premium')" class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-left w-full {{ $leftTab === 'premium' && $activeTab !== 'center' ? 'active' : '' }}">
                <span><i class="fa-solid fa-star"></i></span>
                <span>Premium</span>
              </button>
            </div>
          </div>

          @if($leftTab === 'rooms')
            <!-- Vue LISTE des salons -->
            <div id="roomsListView" class="flex flex-col flex-1 min-h-0 {{ $activeRoomId ? 'hidden lg:flex' : '' }}">
              <div class="flex items-center justify-between mb-2 shrink-0">
                <div class="text-[10px] uppercase tracking-widest text-neutral-500">Discussions unifiées</div>
                <button wire:click="openCreateRoomModal" class="text-[10px] text-[#6b1f2a] font-medium">+ Nouveau</button>
              </div>
              <!-- Filtres compacts en HAUT -->
              <div class="flex flex-wrap gap-1.5 mb-3 shrink-0" id="roomFilters">
                <button wire:click="setRoomFilter('all')" class="chip px-2.5 py-1 rounded-full text-[10px] border border-[#ead9d4] {{ $roomFilter === 'all' ? 'active' : 'bg-[#fffdfb]/40' }}">Tous</button>
                <button wire:click="setRoomFilter('joined')" class="chip px-2.5 py-1 rounded-full text-[10px] border border-[#ead9d4] {{ $roomFilter === 'joined' ? 'active' : 'bg-[#fffdfb]/40' }}">Rejoints</button>
              </div>
              <ul id="roomsList" class="list-scroll scroll-thin space-y-1 pr-1">
                @php
                  $visibleRooms = $roomFilter === 'joined' ? $rooms->where('joined', true) : $rooms;
                @endphp
                @forelse($visibleRooms as $room)
                  <li>
                    <button
                      wire:click="{{ $room->joined ? 'openRoom(' . $room->id . ')' : 'joinRoom(' . $room->id . ')' }}"
                      class="room-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm"
                    >
                      <span class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-base" style="background:rgba(107,31,42,0.08)">{{ $room->icon }}</span>
                      <span class="flex-1 min-w-0 text-left">
                        <span class="font-medium block truncate">{{ $room->name }}</span>
                        <span class="text-[11px] text-neutral-500 truncate block">{{ $room->interest?->name ?? 'Salon libre' }} · {{ $room->members_count }} membre(s)</span>
                      </span>
                      @unless($room->joined)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full btn-wine font-semibold shrink-0">Rejoindre</span>
                      @endunless
                    </button>
                  </li>
                @empty
                  <li class="text-center text-xs text-neutral-500 py-8">Aucun salon dans cette catégorie.</li>
                @endforelse
              </ul>
            </div>

            <!-- Vue CHAT d'un salon (visible seulement si un salon est ouvert) -->
            @if($activeRoomId)
              @php $activeRoom = $rooms->firstWhere('id', $activeRoomId); @endphp
              <div id="roomChatView" class="flex flex-col flex-1 min-h-0 lg:hidden">
                <header class="flex items-center gap-2 pb-3 border-b border-[#ead9d4]/70 shrink-0">
                  <button wire:click="closeRoom" class="w-8 h-8 rounded-full glass flex items-center justify-center hover:bg-[#6b1f2a]/10" title="Retour">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                  </button>
                  <span class="w-9 h-9 rounded-lg flex items-center justify-center text-lg shrink-0" style="background:rgba(107,31,42,0.08)"><i class="fa-solid fa-comments" style="color: #6b1f2a;"></i></span>
                  <div class="flex-1 min-w-0">
                    <div class="font-semibold text-sm truncate">{{ $activeRoom->name ?? '' }}</div>
                    <div class="text-[10px] text-neutral-500 truncate">{{ $activeRoom->members_count ?? 0 }} membre(s)</div>
                  </div>
                </header>

                <div id="roomChatMessages" class="chat-messages scroll-thin py-3 space-y-3">
                  @forelse($roomMessages as $m)
                    @if($m->sender_id === auth()->id())
                      <div class="flex justify-end items-center gap-1 group relative" wire:key="roommsg-{{ $m->id }}">
                        <div class="flex items-center gap-1.5 flex-row-reverse max-w-[80%]">
                          <div class="msg-out rounded-2xl rounded-br-md {{ $m->isImageAttachment() && !$m->message && !$m->parent ? 'p-1' : 'px-3.5 py-2.5' }} text-sm break-words">
                            
                            {{-- Threading preview --}}
                            @if ($m->parent)
                              <div class="text-[10px] opacity-75 border-l-2 border-[#fffdfb]/50 pl-2 mb-1.5 italic bg-black/10 rounded py-0.5 px-1.5 truncate">
                                <b>{{ $m->parent->sender_id === auth()->id() ? 'Moi' : $m->parent->sender->display_name }}</b> : 
                                <i class="fa-solid fa-paperclip"></i> {{ $m->parent->message ?: 'Fichier' }}
                              </div>
                            @endif

                            @if($m->hasAttachment())
                              @if($m->isImageAttachment())
                                <a href="{{ route('room.attachment', $m->id, false) }}" target="_blank"><img src="{{ route('room.attachment', $m->id, false) }}" class="rounded-xl max-w-full block" /></a>
                              @elseif($m->isAudioAttachment())
                                <div class="block mt-1.5 mb-1 text-neutral-800">
                                  <div x-data="{
                                      playing: false,
                                      audio: null,
                                      duration: '0:00',
                                      currentTime: '0:00',
                                      progress: 0,
                                      init() {
                                          this.audio = new Audio('{{ route('room.attachment', $m->id, false) }}');
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
                              @elseif(str_starts_with($m->attachment_mime ?? '', 'video/'))
                                <div class="block mt-1 mb-2 rounded-lg overflow-hidden max-w-xs">
                                  <video src="{{ route('room.attachment', $m->id, false) }}" controls class="w-full max-h-[220px] object-cover"></video>
                                </div>
                              @else
                                <a href="{{ route('room.attachment', $m->id, false) }}" class="msg-out rounded-2xl rounded-br-md px-3.5 py-2.5 text-sm block mb-1"><i class="fa-solid fa-paperclip"></i> {{ $m->attachment_name }}</a>
                              @endif
                            @endif

                            @if ($editingRoomMessageId === $m->id)
                              {{-- Inline Editor --}}
                              <div class="flex flex-col gap-1.5 min-w-[180px]">
                                <input type="text" wire:model="editingRoomText" wire:keydown.enter="updateRoomMessage" class="w-full bg-white/70 text-[#2b1a1d] rounded-xl px-2.5 py-1.5 text-xs outline-none border border-[#ead9d4]" />
                                <div class="flex gap-2 justify-end">
                                  <button wire:click="cancelRoomEdit" class="text-[10px] text-neutral-300 font-medium">Annuler</button>
                                  <button wire:click="updateRoomMessage" class="text-[10px] text-white font-bold">Enregistrer</button>
                                </div>
                              </div>
                            @else
                              @if($m->message)
                                <div>{{ $m->message }}</div>
                              @endif
                              @if($m->is_edited)
                                <span class="text-[9px] opacity-60 block mt-0.5 text-right">(modifié)</span>
                              @endif
                            @endif
                          </div>

                          {{-- Hover Actions --}}
                          <div class="opacity-0 group-hover:opacity-100 transition flex gap-1 items-center shrink-0">
                            <button wire:click="startRoomReply({{ $m->id }})" class="text-neutral-400 hover:text-[#6b1f2a] text-xs p-1" title="Répondre"><i class="fa-solid fa-reply"></i></button>
                            <button wire:click="startRoomEdit({{ $m->id }})" class="text-neutral-400 hover:text-[#6b1f2a] text-xs p-1" title="Modifier"><i class="fa-solid fa-pen"></i></button>
                            <button wire:click="deleteRoomMessage({{ $m->id }})" wire:confirm="Supprimer ce message ?" class="text-neutral-400 hover:text-[#a83248] text-xs p-1" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                          </div>
                        </div>

                        <div class="text-[10px] text-neutral-500 mt-1 mr-1 absolute bottom-[-15px] right-1">{{ $m->created_at->format('H:i') }}</div>
                      </div>
                      
                      {{-- Spacer for absolute time rendering --}}
                      <div class="h-3"></div>
                    @else
                      <div class="flex gap-2 items-end group relative" wire:key="roommsg-{{ $m->id }}">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] shrink-0 flex items-center justify-center text-[10px] text-white font-semibold">{{ mb_substr($m->sender->initials, 0, 1) }}</div>
                        
                        <div class="flex items-center gap-1.5 max-w-[80%]">
                          <div class="msg-in rounded-2xl rounded-bl-md px-3.5 py-2.5 text-sm break-words">
                            <div class="text-[10px] text-neutral-500 mb-1">@​{{ $m->sender->display_name }}</div>
                            
                            {{-- Threading preview --}}
                            @if ($m->parent)
                              <div class="text-[10px] opacity-75 border-l-2 border-[#6b1f2a] pl-2 mb-1.5 italic bg-black/5 rounded py-0.5 px-1.5 truncate">
                                <b>{{ $m->parent->sender_id === auth()->id() ? 'Moi' : $m->parent->sender->display_name }}</b> : 
                                {{ $m->parent->message ?: '📎 Fichier' }}
                              </div>
                            @endif

                            @if($m->hasAttachment())
                              @if($m->isImageAttachment())
                                <a href="{{ route('room.attachment', $m->id, false) }}" target="_blank" class="block mt-1.5"><img src="{{ route('room.attachment', $m->id, false) }}" class="rounded-xl max-w-full block" /></a>
                              @elseif($m->isAudioAttachment())
                                <div class="block mt-1.5 mb-1 text-neutral-800">
                                  <div x-data="{
                                      playing: false,
                                      audio: null,
                                      duration: '0:00',
                                      currentTime: '0:00',
                                      progress: 0,
                                      init() {
                                          this.audio = new Audio('{{ route('room.attachment', $m->id, false) }}');
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
                                          <span x-text="playing ? '⏸️' : '▶️'" class="text-sm"></span>
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
                                              <span class="flex items-center gap-0.5">🎤 vocal</span>
                                          </div>
                                      </div>
                                  </div>
                                </div>
                              @elseif(str_starts_with($m->attachment_mime ?? '', 'video/'))
                                <div class="block mt-1 mb-2 rounded-lg overflow-hidden max-w-xs">
                                  <video src="{{ route('room.attachment', $m->id, false) }}" controls class="w-full max-h-[220px] object-cover"></video>
                                </div>
                              @else
                                <a href="{{ route('room.attachment', $m->id, false) }}" class="msg-in rounded-2xl rounded-bl-md px-3.5 py-2.5 text-sm block mb-1">📎 {{ $m->attachment_name }}</a>
                              @endif
                            @endif

                            @if($m->message)
                              <div>{{ $m->message }}</div>
                            @endif
                            @if($m->is_edited)
                              <span class="text-[9px] opacity-60 block mt-0.5 text-right">(modifié)</span>
                            @endif
                          </div>

                          {{-- Hover Actions --}}
                          <div class="opacity-0 group-hover:opacity-100 transition flex gap-1 items-center shrink-0">
                            <button wire:click="startRoomReply({{ $m->id }})" class="text-neutral-400 hover:text-[#6b1f2a] text-xs p-1" title="Répondre"><i class="fa-solid fa-reply"></i></button>
                          </div>
                        </div>

                        <div class="text-[10px] text-neutral-500 mt-1 absolute bottom-[-15px] left-9">{{ $m->created_at->format('H:i') }}</div>
                      </div>
                      
                      {{-- Spacer for absolute time rendering --}}
                      <div class="h-3"></div>
                    @endif
                  @empty
                    <div class="text-center text-xs text-neutral-500 py-8">Aucun message pour l'instant.</div>
                  @endforelse
                </div>

                @if($roomAttachment)
                  <div class="flex items-center gap-2 text-[11px] px-1 pb-1 shrink-0">
                    📎 {{ $roomAttachment->getClientOriginalName() }}
                    <button wire:click="removeRoomAttachment" class="text-[#a83248]">✕</button>
                  </div>
                @endif

                {{-- ===== Aperçu du message auquel on répond ===== --}}
                @if ($replyingToRoomMessageId)
                  @php
                    $replyParent = App\Models\RoomMessage::find($replyingToRoomMessageId);
                  @endphp
                  @if ($replyParent)
                    <div class="flex items-center justify-between px-3 py-1.5 bg-[#6b1f2a]/5 border-t border-[#ead9d4]/70 rounded-t-2xl shrink-0 text-xs text-neutral-600 gap-2 w-full">
                      <div class="truncate flex items-center gap-1.5 min-w-0">
                        <span class="text-neutral-400 shrink-0">En réponse à</span>
                        <b class="shrink-0">{{ $replyParent->sender_id === auth()->id() ? 'Moi' : $replyParent->sender->display_name }}</b>
                        <span class="truncate italic text-neutral-500">: {{ $replyParent->message ?: '📎 Fichier joint' }}</span>
                      </div>
                      <button wire:click="cancelRoomReply" class="text-neutral-400 hover:text-[#6b1f2a] text-[10px] shrink-0 font-semibold p-1">✕</button>
                    </div>
                  @endif
                @endif

                <div class="w-full flex items-center gap-2 pt-3 border-t border-[#ead9d4]/70 shrink-0"
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
                                    @this.upload('roomAttachment', file, () => {
                                        @this.sendRoomMessage();
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
                    <label class="w-9 h-9 glass rounded-full flex items-center justify-center cursor-pointer hover:bg-[#6b1f2a]/10" title="Joindre un fichier">
                      <i class="fa-solid fa-paperclip"></i>
                      <input type="file" wire:model="roomAttachment" class="hidden" />
                    </label>

                    <button type="button" @click="recording ? stopRecording() : startRecording()" :class="recording ? 'bg-[#a83248] text-white animate-pulse' : 'glass'" class="w-9 h-9 rounded-full flex items-center justify-center hover:bg-[#6b1f2a]/10" title="Message vocal">
                      <span x-html="recording ? '<i class=\'fa-solid fa-square\'></i>' : '<i class=\'fa-solid fa-microphone\'></i>'"></span>
                    </button>
                  </div>

                  <!-- Input area (center) -->
                  <div class="flex-1 min-w-0 bg-[#fdf4ec]/40 border border-[#ead9d4]/70 rounded-full px-3.5 py-1.5 flex items-center gap-2">
                    <input wire:model="roomTextMessage" wire:keydown.enter="sendRoomMessage" placeholder="Message au salon…" class="flex-1 bg-transparent outline-none text-sm placeholder:text-neutral-500 min-w-0"/>
                    
                    <!-- Emoji inside input on the right -->
                    <div x-data="{ open: false }" class="relative shrink-0 flex items-center">
                      <button type="button" @click="open = !open" class="w-6 h-6 hover:scale-105 transition flex items-center justify-center text-base" title="Emojis">
                        <i class="fa-regular fa-face-smile"></i>
                      </button>
                      <div x-show="open" @click.outside="open = false" class="absolute bottom-9 right-0 bg-[#fffdfb] border border-[#ead9d4] rounded-2xl shadow-xl p-2 grid grid-cols-6 gap-1.5 z-50 w-48">
                        <template x-for="emoji in ['😀', '😂', '😍', '👍', '🎉', '🔥', '💡', '🚀', '💬', '🌐', '👑', '🎤', '❤️', '👏', '🙌', '🌟', '✨', '✔️']">
                          <button type="button" @click="$wire.roomTextMessage = ($wire.roomTextMessage || '') + emoji; open = false;" class="w-6 h-6 hover:bg-[#6b1f2a]/10 rounded flex items-center justify-center text-sm" x-text="emoji"></button>
                        </template>
                      </div>
                    </div>
                  </div>

                  <!-- Send Actions (right side) -->
                  <div class="flex items-center gap-1 shrink-0">
                    <button wire:click="sendRoomMessage" class="w-9 h-9 rounded-full btn-wine flex items-center justify-center text-sm">
                      <i class="fa-solid fa-paper-plane"></i>
                    </button>
                  </div>
                </div>
              </div>
            @endif
          @else
            <!-- Vue PREMIUM -->
            <div id="premiumListView" class="flex flex-col flex-1 min-h-0">
              <div class="text-[10px] uppercase tracking-widest text-neutral-500 mb-3">Espace Premium</div>
              @if(auth()->user()->isPremium())
                <div class="text-xs text-neutral-600 mb-4 bg-gradient-to-br from-[#6b1f2a]/10 to-transparent p-3 rounded-2xl border border-[#ead9d4]/50 leading-relaxed">
                  ✨ <b>Bienvenue dans l'espace Premium !</b><br>
                  Voici les experts passionnés qui correspondent à vos centres d'intérêt. Vous pouvez entamer une discussion directe avec eux.
                </div>

                <ul class="list-scroll scroll-thin space-y-2 pr-1">
                  @forelse($experts as $expert)
                    @php
                      $initials = $expert->initials;
                    @endphp
                    <li class="glass rounded-2xl p-3 flex items-center gap-3">
                      <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] flex items-center justify-center text-white text-xs font-semibold">
                        {{ $initials }}
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm truncate">@​{{ $expert->display_name }}</div>
                        <div class="text-[11px] text-neutral-500 truncate">Expert : {{ $expert->expertiseInterest?->name ?? 'Général' }}</div>
                      </div>
                      <button wire:click="contactExpert({{ $expert->id }})" class="btn-wine text-[10px] font-semibold px-3 py-1.5 rounded-full shrink-0">Discuter</button>
                    </li>
                  @empty
                    <li class="text-center text-xs text-neutral-500 py-8">Aucun expert disponible pour vos passions.</li>
                  @endforelse
                </ul>
              @else
                <div class="flex-1 flex flex-col items-center justify-center text-center p-4">
                  <div class="w-16 h-16 rounded-full bg-[#6b1f2a]/10 flex items-center justify-center text-2xl mb-4">👑</div>
                  <div class="font-display text-base font-semibold mb-2">Devenez Membre Premium</div>
                  <p class="text-xs text-neutral-500 max-w-[240px] leading-relaxed mb-6">
                    Accédez aux discussions directes avec les experts de vos passions et débloquez la traduction en temps réel de vos messages.
                  </p>
                  <div class="w-full space-y-2 bg-[#fffdfb]/40 border border-[#ead9d4] rounded-2xl p-3 text-left mb-6">
                    <div class="text-[11px] text-neutral-600 flex items-center gap-1.5">✓ Discussion avec les Experts</div>
                    <div class="text-[11px] text-neutral-600 flex items-center gap-1.5">✓ Traduction de chat automatique</div>
                    <div class="text-[11px] text-neutral-600 flex items-center gap-1.5">✓ Badge Premium unique</div>
                    <div class="text-[10px] text-neutral-400 mt-2 italic">Gratuit à partir du niveau 6 (100 messages) !</div>
                  </div>
                  <a href="{{ route('premium.checkout') }}" class="btn-wine w-full block text-center text-xs font-semibold py-2.5 rounded-full hover:scale-[1.02] transition">
                    Passer Premium (4,99€/mois) →
                  </a>
                </div>
              @endif
            </div>
          @endif
        </div>
      </aside>

      <!-- ===== MODALE : créer un salon ===== -->
      @if($showCreateRoomModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center modal-backdrop p-4">
          <div class="glass-strong rounded-3xl p-6 max-w-sm w-full relative">
            <div class="font-display text-lg mb-4">Créer un salon</div>
            <form wire:submit.prevent="createRoom" class="space-y-3">
              <input wire:model="newRoomName" placeholder="Nom du salon" class="w-full glass rounded-xl px-3 py-2.5 text-sm outline-none" />
              @error('name') <div class="text-[11px] text-[#a83248]">{{ $message }}</div> @enderror

              <textarea wire:model="newRoomDescription" placeholder="Description (optionnel)" rows="2" class="w-full glass rounded-xl px-3 py-2.5 text-sm outline-none"></textarea>

              <select wire:model="newRoomInterestId" class="w-full glass rounded-xl px-3 py-2.5 text-sm outline-none">
                <option value="">Passion associée (optionnel)</option>
                @foreach($availableInterests as $interest)
                  <option value="{{ $interest['id'] }}">{{ $interest['name'] }}</option>
                @endforeach
              </select>

              <div class="flex gap-2 pt-2">
                <button type="button" wire:click="closeCreateRoomModal" class="flex-1 glass rounded-full py-2.5 text-xs font-semibold">Annuler</button>
                <button type="submit" class="flex-1 btn-wine rounded-full py-2.5 text-xs font-semibold">Créer</button>
              </div>
            </form>
          </div>
        </div>
      @endif

      <!-- ===== MODALE : Réglages du profil ===== -->
      @if($showSettingsModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center modal-backdrop p-4">
          <div class="glass-strong rounded-3xl p-6 max-w-sm w-full relative">
            @if($showDeleteConfirm)
              <div class="text-center space-y-4">
                <div class="w-12 h-12 mx-auto rounded-full bg-red-100 flex items-center justify-center text-red-600 text-xl font-bold">⚠️</div>
                <div class="font-display text-lg text-[#6b1f2a]">Supprimer le compte</div>
                <p class="text-xs text-neutral-600 leading-relaxed text-center">
                  Cette action est définitive et irréversible. Toutes vos publications, likes, messages, stories et salons seront définitivement supprimés de la base de données.
                </p>
                <div class="flex flex-col gap-2 pt-2">
                  <button type="button" wire:click="deleteAccount" class="w-full py-2.5 rounded-full text-xs font-semibold text-white bg-red-600 hover:bg-red-700 shadow transition">
                    Oui, supprimer définitivement
                  </button>
                  <button type="button" wire:click="$set('showDeleteConfirm', false)" class="w-full py-2.5 rounded-full text-xs font-semibold glass hover:bg-neutral-100 transition">
                    Annuler
                  </button>
                </div>
              </div>
            @else
              <div class="font-display text-lg mb-4">Réglages du profil</div>
              <form wire:submit.prevent="saveSettings" class="space-y-4">
                <div>
                  <label class="block text-xs text-neutral-500 mb-1">Ma Bio</label>
                  <textarea wire:model="settingsBio" placeholder="Parle-nous de toi..." rows="3" class="w-full glass rounded-xl px-3 py-2 text-sm outline-none"></textarea>
                  @error('settingsBio') <div class="text-[11px] text-[#a83248]">{{ $message }}</div> @enderror
                </div>

                <div>
                  <label class="block text-xs text-neutral-500 mb-1">Ma Ville</label>
                  <input type="text" wire:model="settingsCity" placeholder="Ex: Paris, Tokyo..." class="w-full glass rounded-xl px-3 py-2 text-sm outline-none" />
                  @error('settingsCity') <div class="text-[11px] text-[#a83248]">{{ $message }}</div> @enderror
                </div>

                <div class="pt-2 border-t border-[#ead9d4]/50">
                  <button type="button" wire:click="$set('showDeleteConfirm', true)" class="w-full text-left text-xs text-red-600 hover:text-red-700 font-semibold flex items-center justify-between py-2 px-1 hover:bg-red-50/50 rounded-xl transition">
                    <span>⚠️ Zone de Danger</span>
                    <span class="text-[10px] underline">Supprimer le compte</span>
                  </button>
                </div>

                <div class="flex gap-2 pt-2">
                  <button type="button" wire:click="closeSettingsModal" class="flex-1 glass rounded-full py-2.5 text-xs font-semibold">Annuler</button>
                  <button type="submit" class="flex-1 btn-wine rounded-full py-2.5 text-xs font-semibold">Enregistrer</button>
                </div>
              </form>
            @endif
          </div>
        </div>
      @endif

      <!-- ========= CENTER COLUMN : FEED ========= -->
      <main id="colCenter" class="col-panel col-center {{ $activeTab === 'center' ? 'mobile-visible' : '' }}">

        @if($activeRoomId)
          @php $activeRoom = $rooms->firstWhere('id', $activeRoomId); @endphp
          <!-- Vue CHAT d'un salon sur Desktop -->
          <div id="roomChatViewDesktop" class="hidden lg:flex flex-col flex-1 min-h-0 glass rounded-2xl p-4">
            <header class="flex items-center gap-2 pb-3 border-b border-[#ead9d4]/70 shrink-0">
              <span class="w-9 h-9 rounded-lg flex items-center justify-center text-lg shrink-0" style="background:rgba(107,31,42,0.08)"><i class="fa-solid fa-comments" style="color: #6b1f2a;"></i></span>
              <div class="flex-1 min-w-0">
                <div class="font-semibold text-sm truncate">{{ $activeRoom->name ?? '' }}</div>
                <div class="text-[10px] text-neutral-500 truncate">{{ $activeRoom->members_count ?? 0 }} membre(s)</div>
              </div>
              <button wire:click="closeRoom" class="w-8 h-8 rounded-full glass flex items-center justify-center hover:bg-[#6b1f2a]/10" title="Fermer la discussion">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </header>

            <div id="roomChatMessagesDesktop" class="chat-messages scroll-thin py-3 space-y-3">
              @forelse($roomMessages as $m)
                @if($m->sender_id === auth()->id())
                  <div class="flex justify-end items-center gap-1 group relative" wire:key="roommsg-desk-{{ $m->id }}">
                    <div class="flex items-center gap-1.5 flex-row-reverse max-w-[80%]">
                      <div class="msg-out rounded-2xl rounded-br-md {{ $m->isImageAttachment() && !$m->message && !$m->parent ? 'p-1' : 'px-3.5 py-2.5' }} text-sm break-words">
                        @if ($m->parent)
                          <div class="text-[10px] opacity-75 border-l-2 border-[#fffdfb]/50 pl-2 mb-1.5 italic bg-black/10 rounded py-0.5 px-1.5 truncate">
                            <b>{{ $m->parent->sender_id === auth()->id() ? 'Moi' : $m->parent->sender->display_name }}</b> : 
                            <i class="fa-solid fa-reply"></i> {{ $m->parent->message ?: 'Fichier' }}
                          </div>
                        @endif
                        @if($m->hasAttachment())
                          @if($m->isImageAttachment())
                            <a href="{{ route('room.attachment', $m->id, false) }}" target="_blank"><img src="{{ route('room.attachment', $m->id, false) }}" class="rounded-xl max-w-full block" /></a>
                          @elseif($m->isAudioAttachment())
                            <div class="block mt-1.5 mb-1 text-neutral-800">
                               <div x-data="{ playing: false, audio: null, duration: '0:00', currentTime: '0:00', progress: 0, init() { this.audio = new Audio('{{ route('room.attachment', $m->id, false) }}'); this.audio.addEventListener('loadedmetadata', () => { this.duration = this.formatTime(this.audio.duration); }); this.audio.addEventListener('timeupdate', () => { this.currentTime = this.formatTime(this.audio.currentTime); this.progress = (this.audio.currentTime / this.audio.duration) * 100; }); this.audio.addEventListener('ended', () => { this.playing = false; this.progress = 0; this.currentTime = '0:00'; }); }, togglePlay() { if (this.playing) { this.audio.pause(); this.playing = false; } else { this.audio.play(); this.playing = true; } }, formatTime(secs) { if (isNaN(secs)) return '0:00'; const m = Math.floor(secs / 60); const s = Math.floor(secs % 60).toString().padStart(2, '0'); return `${m}:${s}`; } }" class="flex items-center gap-3 p-2.5 rounded-2xl bg-black/5 hover:bg-black/10 transition max-w-[280px]">
                                 <button @click="togglePlay" class="w-10 h-10 rounded-full bg-[#6b1f2a] text-white flex items-center justify-center hover:scale-105 transition shadow-md shrink-0 focus:outline-none">
                                   <span class="text-sm"><i :class="playing ? 'fa-solid fa-pause' : 'fa-solid fa-play'"></i></span>
                                 </button>
                                 <div class="flex-1 min-w-0 flex flex-col gap-1">
                                   <div class="flex items-end gap-[3px] h-6 px-1 items-center">
                                     <template x-for="(h, i) in [12,18,10,14,22,16,10,14,18,12,16,20,12,14,10,16]">
                                       <div class="w-[3px] rounded-full transition-all duration-150" :class="i / 16 * 100 < progress ? 'bg-[#6b1f2a]' : 'bg-[#6b1f2a]/20'" :style='`height: ${h}px`' style="height: 12px"></div>
                                     </template>
                                   </div>
                                   <div class="flex items-center justify-between text-[9px] text-neutral-500 font-semibold leading-none">
                                     <span x-text="playing ? currentTime : duration">0:00</span>
                                     <span class="flex items-center gap-1"><i class="fa-solid fa-microphone"></i> vocal</span>
                                   </div>
                                 </div>
                               </div>
                            </div>
                          @elseif(str_starts_with($m->attachment_mime ?? '', 'video/'))
                            <div class="block mt-1 mb-2 rounded-lg overflow-hidden max-w-xs">
                              <video src="{{ route('room.attachment', $m->id, false) }}" controls class="w-full max-h-[220px] object-cover"></video>
                            </div>
                          @else
                            <a href="{{ route('room.attachment', $m->id, false) }}" class="msg-out rounded-2xl rounded-br-md px-3.5 py-2.5 text-sm block mb-1"><i class="fa-solid fa-paperclip"></i> {{ $m->attachment_name }}</a>
                          @endif
                        @endif
                        @if ($editingRoomMessageId === $m->id)
                          <div class="flex flex-col gap-1.5 min-w-[180px]">
                            <input type="text" wire:model="editingRoomText" wire:keydown.enter="updateRoomMessage" class="w-full bg-white/70 text-[#2b1a1d] rounded-xl px-2.5 py-1.5 text-xs outline-none border border-[#ead9d4]" />
                            <div class="flex gap-2 justify-end">
                              <button wire:click="cancelRoomEdit" class="text-[10px] text-neutral-300 font-medium">Annuler</button>
                              <button wire:click="updateRoomMessage" class="text-[10px] text-white font-bold">Enregistrer</button>
                            </div>
                          </div>
                        @else
                          @if($m->message)
                            <div>{{ $m->message }}</div>
                          @endif
                          @if($m->is_edited)
                            <span class="text-[9px] opacity-60 block mt-0.5 text-right">(modifié)</span>
                          @endif
                        @endif
                      </div>
                      <div class="opacity-0 group-hover:opacity-100 transition flex gap-1 items-center shrink-0">
                        <button wire:click="startRoomReply({{ $m->id }})" class="text-neutral-400 hover:text-[#6b1f2a] text-xs p-1" title="Répondre"><i class="fa-solid fa-reply"></i></button>
                        <button wire:click="startRoomEdit({{ $m->id }})" class="text-neutral-400 hover:text-[#6b1f2a] text-xs p-1" title="Modifier"><i class="fa-solid fa-pen"></i></button>
                        <button wire:click="deleteRoomMessage({{ $m->id }})" wire:confirm="Supprimer ce message ?" class="text-neutral-400 hover:text-[#a83248] text-xs p-1" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                      </div>
                    </div>
                    <div class="text-[10px] text-neutral-500 mt-1 mr-1 absolute bottom-[-15px] right-1">{{ $m->created_at->format('H:i') }}</div>
                  </div>
                  <div class="h-3"></div>
                @else
                  <div class="flex gap-2 items-end group relative" wire:key="roommsg-desk-{{ $m->id }}">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] shrink-0 flex items-center justify-center text-[10px] text-white font-semibold">{{ mb_substr($m->sender->initials, 0, 1) }}</div>
                    <div class="flex items-center gap-1.5 max-w-[80%]">
                      <div class="msg-in rounded-2xl rounded-bl-md {{ $m->isImageAttachment() && !$m->message && !$m->parent ? 'p-1' : 'px-3.5 py-2.5' }} text-sm break-words">
                        @if ($m->parent)
                          <div class="text-[10px] opacity-75 border-l-2 border-[#6b1f2a] pl-2 mb-1.5 italic bg-black/5 rounded py-0.5 px-1.5 truncate">
                            <b>{{ $m->parent->sender_id === auth()->id() ? 'Moi' : $m->parent->sender->display_name }}</b> : 
                            <i class="fa-solid fa-reply"></i> {{ $m->parent->message ?: 'Fichier' }}
                          </div>
                        @endif
                        @if($m->hasAttachment())
                          @if($m->isImageAttachment())
                            <a href="{{ route('room.attachment', $m->id, false) }}" target="_blank"><img src="{{ route('room.attachment', $m->id, false) }}" class="rounded-xl max-w-full block" /></a>
                          @elseif($m->isAudioAttachment())
                            <div class="block mt-1.5 mb-1 text-neutral-800">
                               <div x-data="{ playing: false, audio: null, duration: '0:00', currentTime: '0:00', progress: 0, init() { this.audio = new Audio('{{ route('room.attachment', $m->id, false) }}'); this.audio.addEventListener('loadedmetadata', () => { this.duration = this.formatTime(this.audio.duration); }); this.audio.addEventListener('timeupdate', () => { this.currentTime = this.formatTime(this.audio.currentTime); this.progress = (this.audio.currentTime / this.audio.duration) * 100; }); this.audio.addEventListener('ended', () => { this.playing = false; this.progress = 0; this.currentTime = '0:00'; }); }, togglePlay() { if (this.playing) { this.audio.pause(); this.playing = false; } else { this.audio.play(); this.playing = true; } }, formatTime(secs) { if (isNaN(secs)) return '0:00'; const m = Math.floor(secs / 60); const s = Math.floor(secs % 60).toString().padStart(2, '0'); return `${m}:${s}`; } }" class="flex items-center gap-3 p-2.5 rounded-2xl bg-black/5 hover:bg-black/10 transition max-w-[280px]">
                                 <button @click="togglePlay" class="w-10 h-10 rounded-full bg-[#6b1f2a] text-white flex items-center justify-center hover:scale-105 transition shadow-md shrink-0 focus:outline-none">
                                   <span class="text-sm"><i :class="playing ? 'fa-solid fa-pause' : 'fa-solid fa-play'"></i></span>
                                 </button>
                                 <div class="flex-1 min-w-0 flex flex-col gap-1">
                                   <div class="flex items-end gap-[3px] h-6 px-1 items-center">
                                     <template x-for="(h, i) in [12,18,10,14,22,16,10,14,18,12,16,20,12,14,10,16]">
                                       <div class="w-[3px] rounded-full transition-all duration-150" :class="i / 16 * 100 < progress ? 'bg-[#6b1f2a]' : 'bg-[#6b1f2a]/20'" :style='`height: ${h}px`' style="height: 12px"></div>
                                     </template>
                                   </div>
                                   <div class="flex items-center justify-between text-[9px] text-neutral-500 font-semibold leading-none">
                                     <span x-text="playing ? currentTime : duration">0:00</span>
                                     <span class="flex items-center gap-1"><i class="fa-solid fa-microphone"></i> vocal</span>
                                   </div>
                                 </div>
                               </div>
                            </div>
                          @elseif(str_starts_with($m->attachment_mime ?? '', 'video/'))
                            <div class="block mt-1 mb-2 rounded-lg overflow-hidden max-w-xs">
                              <video src="{{ route('room.attachment', $m->id, false) }}" controls class="w-full max-h-[220px] object-cover"></video>
                            </div>
                          @else
                            <a href="{{ route('room.attachment', $m->id, false) }}" class="msg-in rounded-2xl rounded-bl-md px-3.5 py-2.5 text-sm block mb-1"><i class="fa-solid fa-paperclip"></i> {{ $m->attachment_name }}</a>
                          @endif
                        @endif
                        @if($m->message)
                          <div>{{ $m->message }}</div>
                        @endif
                        @if($m->is_edited)
                          <span class="text-[9px] opacity-60 block mt-0.5 text-right">(modifié)</span>
                        @endif
                      </div>
                      <div class="opacity-0 group-hover:opacity-100 transition flex gap-1 items-center shrink-0">
                        <button wire:click="startRoomReply({{ $m->id }})" class="text-neutral-400 hover:text-[#6b1f2a] text-xs p-1" title="Répondre"><i class="fa-solid fa-reply"></i></button>
                      </div>
                    </div>
                    <div class="text-[10px] text-neutral-500 mt-1 absolute bottom-[-15px] left-9">{{ $m->created_at->format('H:i') }}</div>
                  </div>
                  <div class="h-3"></div>
                @endif
              @empty
                <div class="text-center text-xs text-neutral-500 py-8">Aucun message pour l'instant.</div>
              @endforelse
            </div>

            <div class="pt-3 border-t border-[#ead9d4]/70 shrink-0 flex items-center gap-2 relative">
              <button class="w-9 h-9 rounded-full glass flex items-center justify-center hover:bg-[#6b1f2a]/10" onclick="document.getElementById('roomAttachmentInputDesk').click()"><i class="fa-solid fa-paperclip text-sm"></i></button>
              <input type="file" id="roomAttachmentInputDesk" wire:model="roomAttachment" class="hidden" />

              <div class="flex-1 relative flex items-center">
                <input type="text" wire:model="roomTextMessage" wire:keydown.enter="sendRoomMessage" placeholder="Écrire dans le salon..." class="w-full bg-white/40 rounded-full pl-4 pr-10 py-2.5 text-xs outline-none border border-[#ead9d4] focus:bg-white/60 transition" />
              </div>

              <button wire:click="sendRoomMessage" class="w-9 h-9 rounded-full btn-wine flex items-center justify-center hover:scale-105 transition shadow-md shrink-0"><i class="fa-solid fa-paper-plane text-xs"></i></button>
            </div>
          </div>
        @endif

        <div class="flex-1 flex flex-col min-h-0 {{ $activeRoomId ? 'lg:hidden' : '' }}">
        @if(!empty($searchQuery))
          <!-- ===== SECTION : RÉSULTATS DE RECHERCHE ===== -->
          <div class="flex-1 flex flex-col min-h-0 pt-3">
            <div class="flex items-center justify-between mb-4 shrink-0">
              <h2 class="font-display text-lg">Résultats pour "{{ $searchQuery }}"</h2>
              <button wire:click="$set('searchQuery', '')" class="text-xs text-[#6b1f2a] font-medium hover:underline">Effacer</button>
            </div>

            <div class="space-y-4 scroll-thin overflow-y-auto pr-1 flex-1 pb-4">
              
              <!-- 1. BLOC SALONS -->
              @php $srRooms = $this->getRoomsSearchResults(); @endphp
              <section class="glass rounded-2xl p-4">
                <h3 class="font-display text-xs uppercase tracking-wider text-neutral-500 mb-3"><i class="fa-solid fa-comments"></i> Salons ({{ $srRooms->count() }})</h3>
                @if($srRooms->isEmpty())
                  <div class="text-xs text-neutral-500 py-2">Aucun salon trouvé.</div>
                @else
                  <ul class="space-y-2">
                    @foreach($srRooms as $room)
                      <li class="flex items-center justify-between gap-3 p-2 rounded-xl hover:bg-[#6b1f2a]/5 transition">
                        <div class="flex items-center gap-3">
                          <span class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background:rgba(107,31,42,0.08)">{{ $room->icon }}</span>
                          <div class="text-left">
                            <span class="font-medium text-sm block">{{ $room->name }}</span>
                            <span class="text-[10px] text-neutral-500">{{ $room->interest?->name ?? 'Salon libre' }} · {{ $room->members_count }} membre(s)</span>
                          </div>
                        </div>
                        <button
                          wire:click="{{ $room->joined ? 'openRoom(' . $room->id . ')' : 'joinRoom(' . $room->id . ')' }}"
                          class="btn-wine text-[10px] font-semibold px-3 py-1.5 rounded-full"
                        >
                          {{ $room->joined ? 'Ouvrir' : 'Rejoindre' }}
                        </button>
                      </li>
                    @endforeach
                  </ul>
                @endif
              </section>

              <!-- 2. BLOC CONTACTS / DISCUSSIONS -->
              @php $srUsers = $this->getUsersSearchResults(); @endphp
              <section class="glass rounded-2xl p-4">
                <h3 class="font-display text-xs uppercase tracking-wider text-neutral-500 mb-3"><i class="fa-solid fa-user-group"></i> Membres & Contacts ({{ $srUsers->count() }})</h3>
                @if($srUsers->isEmpty())
                  <div class="text-xs text-neutral-500 py-2">Aucun membre correspondant.</div>
                @else
                  <ul class="space-y-2">
                    @foreach($srUsers as $usr)
                      <li class="flex items-center justify-between gap-3 p-2 rounded-xl hover:bg-[#6b1f2a]/5 transition">
                        <div class="flex items-center gap-3">
                          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] flex items-center justify-center text-white text-xs font-semibold shrink-0">{{ $usr->initials }}</div>
                          <div class="text-left">
                            <span class="font-medium text-sm block">{{ '@' . $usr->display_name }}</span>
                            <span class="text-[10px] text-neutral-500">{{ $usr->city ?? 'France' }}</span>
                          </div>
                        </div>
                        <button
                          wire:click="contactExpert({{ $usr->id }})"
                          class="btn-wine text-[10px] font-semibold px-3 py-1.5 rounded-full"
                        >
                          Discuter
                        </button>
                      </li>
                    @endforeach
                  </ul>
                @endif
              </section>

              <!-- 3. BLOC PUBLICATIONS -->
              @php $srPosts = $this->getPostsSearchResults(); @endphp
              <section class="glass rounded-2xl p-4">
                <h3 class="font-display text-xs uppercase tracking-wider text-neutral-500 mb-3"><i class="fa-solid fa-feather-pointed"></i> Publications ({{ $srPosts->count() }})</h3>
                @if($srPosts->isEmpty())
                  <div class="text-xs text-neutral-500 py-2">Aucun post correspondant.</div>
                @else
                  <div class="space-y-4">
                    @foreach($srPosts as $post)
                      <article class="post-card p-3 rounded-xl hover:bg-[#6b1f2a]/5 transition border border-[#ead9d4]/30" wire:key="search-post-{{ $post->id }}">
                        <header class="flex items-center gap-2 mb-2">
                          <div class="w-8 h-8 rounded-full bg-[#fbf6f0] flex items-center justify-center font-semibold text-xs border border-[#ead9d4]">{{ $post->user->initials }}</div>
                          <div class="flex-1 min-w-0 text-left">
                            <span class="font-semibold text-xs block">{{ '@' . $post->user->display_name }}</span>
                            <span class="text-[9px] text-neutral-500">{{ $post->created_at->diffForHumans() }}</span>
                          </div>
                        </header>
                        <p class="text-xs leading-relaxed mb-2 text-left">{{ $post->content }}</p>
                        @if($post->hasImage())
                          <div class="rounded-lg overflow-hidden border border-[#ead9d4] max-w-xs mb-2">
                            @if(str_starts_with($post->image_mime ?? '', 'video/'))
                              <video src="{{ route('post.attachment', $post->id, false) }}" class="w-full max-h-24 object-contain" controls></video>
                            @else
                              <img src="{{ route('post.attachment', $post->id, false) }}" class="w-full max-h-24 object-contain" />
                            @endif
                          </div>
                        @endif
                      </article>
                    @endforeach
                  </div>
                @endif
              </section>

            </div>
          </div>
        @else
          <!-- Stories (fixe) -->
          <section class="glass rounded-2xl p-4 shrink-0">
          <div class="flex items-center justify-between mb-3">
            <h2 class="font-display text-lg">Stories</h2>
          </div>
          <div class="flex gap-3 overflow-x-auto scroll-thin pb-1">
            <!-- Bouton "Ajouter une story" : toujours affiché en premier -->
            <button wire:click="openCreateStoryModal" class="story flex flex-col items-center gap-1.5 shrink-0 w-16">
              <div class="w-14 h-14 rounded-full p-[2px]">
                <div class="w-full h-full rounded-full bg-[#fbf6f0] flex items-center justify-center text-xl border-2 border-dashed border-[#6b1f2a]/40"><i class="fa-solid fa-plus text-[#6b1f2a]/60"></i></div>
              </div>
              <span class="text-[11px] truncate w-full text-center">Toi</span>
            </button>

            @foreach($storiesFeed as $entry)
              <button wire:click="openStory({{ $entry['user_id'] }})" class="story flex flex-col items-center gap-1.5 shrink-0 w-16">
                <!-- Anneau coloré (ring-gradient) seulement si il reste des stories non vues -->
                <div class="w-14 h-14 rounded-full {{ $entry['has_unseen'] ? 'ring-gradient' : '' }} p-[2px]">
                  <div class="w-full h-full rounded-full bg-[#fbf6f0] flex items-center justify-center text-sm font-semibold {{ $entry['has_unseen'] ? '' : 'opacity-50' }}">
                    {{ mb_substr($entry['user_name'], 0, 2) }}
                  </div>
                </div>
                <span class="text-[11px] truncate w-full text-center">{{ $entry['is_me'] ? 'Toi' : $entry['user_name'] }}</span>
              </button>
            @endforeach
          </div>
        </section>

        <!-- ===== MODALE : créer une story ===== -->
        @if($showCreateStoryModal)
          <div class="fixed inset-0 z-[100] flex items-center justify-center modal-backdrop p-4">
            <div class="glass-strong rounded-3xl p-6 max-w-sm w-full relative shadow-2xl">
              <div class="flex items-center justify-between mb-5">
                <div class="font-display text-lg"><i class="fa-solid fa-wand-magic-sparkles" style="color: #6b1f2a;"></i> Nouvelle story</div>
                <button type="button" wire:click="closeCreateStoryModal" class="w-8 h-8 rounded-full glass flex items-center justify-center hover:bg-[#6b1f2a]/10 text-sm">✕</button>
              </div>

              <!-- Choix du type : texte ou image -->
              <div class="flex gap-2 mb-4 p-1 rounded-full glass">
                <button type="button" wire:click="setNewStoryType('text')" class="flex-1 rounded-full py-2 text-xs font-semibold transition {{ $newStoryType === 'text' ? 'btn-wine shadow-md' : 'text-neutral-500 hover:text-[#6b1f2a]' }}"><i class="fa-solid fa-pen-to-square"></i> Texte</button>
                <button type="button" wire:click="setNewStoryType('image')" class="flex-1 rounded-full py-2 text-xs font-semibold transition {{ $newStoryType === 'image' ? 'btn-wine shadow-md' : 'text-neutral-500 hover:text-[#6b1f2a]' }}"><i class="fa-regular fa-image"></i> Image</button>
              </div>

              <form wire:submit.prevent="publishStory" class="space-y-4">
                @if($newStoryType === 'text')
                  <textarea wire:model="newStoryText" placeholder="Écris quelque chose…" rows="3" maxlength="200" class="w-full glass rounded-xl px-3.5 py-3 text-sm outline-none resize-none"></textarea>
                  @error('newStoryText') <div class="text-[11px] text-[#a83248] px-1">{{ $message }}</div> @enderror

                  <!-- Sélecteur de couleur de fond -->
                  <div>
                    <span class="text-[11px] text-neutral-500 block mb-2">Couleur de fond</span>
                    <div class="flex items-center gap-2.5">
                      @foreach(['#6b1f2a','#a83248','#2b4a3d','#2b3a4a','#4a3a2b'] as $color)
                        <button type="button" wire:click="$set('newStoryColor', '{{ $color }}')" class="w-7 h-7 rounded-full flex items-center justify-center transition {{ $newStoryColor === $color ? 'ring-2 ring-offset-2 ring-[#6b1f2a]' : '' }}" style="background:{{ $color }}">
                          @if($newStoryColor === $color)<span class="text-white text-xs">✓</span>@endif
                        </button>
                      @endforeach
                    </div>
                  </div>

                  <!-- Aperçu en direct -->
                  <div>
                    <span class="text-[11px] text-neutral-500 block mb-2">Aperçu</span>
                    <div class="rounded-2xl p-4 text-center text-white text-xs font-medium shadow-inner leading-relaxed" style="background:{{ $newStoryColor }}; aspect-ratio: 9/16; max-width: 150px; margin: 0 auto;">
                      <div class="h-full flex items-center justify-center overflow-hidden break-words">
                        {{ $newStoryText ?: 'Aperçu de ta story…' }}
                      </div>
                    </div>
                  </div>
                @else
                  <label class="flex flex-col items-center justify-center gap-2 w-full glass rounded-2xl py-8 px-4 cursor-pointer hover:bg-[#6b1f2a]/5 transition text-center">
                    <span class="text-3xl">🎥</span>
                    <span class="text-xs font-semibold">Choisir une image ou vidéo</span>
                    <span class="text-[10px] text-neutral-500">Images & Vidéos — 20 Mo maximum</span>
                    <input type="file" wire:model="newStoryImage" accept="image/*,video/*" class="hidden" />
                  </label>
                  @error('newStoryImage') <div class="text-[11px] text-[#a83248] px-1">{{ $message }}</div> @enderror
                  @if($newStoryImage)
                    <div class="relative rounded-2xl overflow-hidden" style="aspect-ratio: 9/16; max-width: 150px; margin: 0 auto;">
                      @if(str_starts_with($newStoryImage->getMimeType() ?? '', 'video/'))
                        <video src="{{ $newStoryImage->temporaryUrl() }}" class="w-full h-full object-contain bg-black/10" muted autoplay loop></video>
                      @else
                        <img src="{{ $newStoryImage->temporaryUrl() }}" class="w-full h-full object-contain bg-black/10" />
                      @endif
                      <button type="button" wire:click="$set('newStoryImage', null)" class="absolute top-2 right-2 w-7 h-7 rounded-full bg-black/50 text-white text-xs flex items-center justify-center backdrop-blur">✕</button>
                    </div>
                  @endif
                @endif

                <div class="flex gap-2 pt-2">
                  <button type="button" wire:click="closeCreateStoryModal" class="flex-1 glass rounded-full py-2.5 text-xs font-semibold hover:bg-black/5 transition">Annuler</button>
                  <button type="submit" wire:loading.attr="disabled" wire:target="publishStory,newStoryImage" class="flex-1 btn-wine rounded-full py-2.5 text-xs font-semibold shadow-md">Publier</button>
                </div>
              </form>
            </div>
          </div>
        @endif

        <!-- ===== MODALE : visionner une story ===== -->
        @if($viewingUserId)
          @php $currentStory = $viewingStories[$viewingIndex] ?? null; @endphp
          @if($currentStory)
            <div class="fixed inset-0 z-[100] flex items-center justify-center modal-backdrop p-4">
              <div class="glass-strong rounded-3xl overflow-hidden max-w-sm w-full relative" style="aspect-ratio:9/16" wire:key="story-frame-{{ $currentStory->id }}">

                <!-- Barres de progression (une par story de cette personne) -->
                <div class="absolute top-2 left-2 right-2 flex gap-1 z-10">
                  @foreach($viewingStories as $i => $s)
                    <div class="flex-1 h-1 rounded-full {{ $i <= $viewingIndex ? 'bg-white' : 'bg-white/30' }}"></div>
                  @endforeach
                </div>

                <button wire:click="closeStoryViewer" class="absolute top-4 right-3 z-10 text-white text-xl">✕</button>

                @if($currentStory->type === 'video')
                  <video src="{{ route('story.attachment', $currentStory->id, false) }}" class="w-full h-full object-contain bg-black/40" autoplay loop playsinline muted></video>
                @elseif($currentStory->isImage())
                  <img src="{{ route('story.attachment', $currentStory->id, false) }}" class="w-full h-full object-contain bg-black/40" onerror="this.closest('[wire\\:key]').querySelector('.story-fallback')?.classList.remove('hidden'); this.classList.add('hidden');" />
                  <div class="story-fallback hidden w-full h-full flex-col items-center justify-center p-8 text-white text-sm text-center gap-2" style="background:#6b1f2a">
                    <span class="text-2xl">🖼️</span>
                    <span>Impossible de charger ce média.</span>
                  </div>
                @else
                  <div class="w-full h-full flex items-center justify-center p-8 text-white text-lg text-center" style="background:{{ $currentStory->background_color ?? '#6b1f2a' }}">
                    {{ $currentStory->text_content }}
                  </div>
                @endif

                <!-- Zones cliquables invisibles gauche/droite pour naviguer (comme Snap/Insta) -->
                <button wire:click="prevStory" class="absolute inset-y-0 left-0 w-1/3" title="Précédent"></button>
                <button wire:click="nextStory" class="absolute inset-y-0 right-0 w-1/3" title="Suivant"></button>

                <!-- Bouton J'aime sur la story -->
                <div class="absolute bottom-4 right-4 z-20">
                  <button wire:click="toggleStoryLike({{ $currentStory->id }})" class="glass rounded-full px-3 py-2 flex items-center gap-1.5 text-xs text-white hover:scale-105 transition shadow-lg">
                    <span>{{ $currentStory->likedBy(auth()->id()) ? '❤️' : '🤍' }}</span>
                    <span>{{ $currentStory->likesCount() }}</span>
                  </button>
                </div>

                <!-- "Vu par X" : visible uniquement si c'est MA propre story -->
                @if($currentStory->user_id === auth()->id())
                  <div class="absolute bottom-4 left-4 z-20 text-white text-[11px] bg-black/30 px-2.5 py-1.5 rounded-full backdrop-blur">
                    <i class="fa-regular fa-eye"></i> Vu par {{ $currentStory->views()->count() }}
                  </div>
                @endif
              </div>
            </div>
          @endif
        @endif

        <!-- Composer (fixe) -->
        <section class="glass rounded-2xl p-3 flex flex-col gap-2 shrink-0">
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 shrink-0 rounded-full ring-gradient p-[2px]"><div class="w-full h-full rounded-full bg-[#fbf6f0] flex items-center justify-center text-sm font-semibold">{{ strtoupper(mb_substr(auth()->user()->username ?? auth()->user()->name, 0, 2)) }}</div></div>
            <input wire:model="newPostContent" wire:keydown.enter="publishPost" placeholder="Partage une idée…" class="flex-1 bg-transparent outline-none text-sm placeholder:text-neutral-500 min-w-0"/>
            <label class="w-9 h-9 shrink-0 glass rounded-full flex items-center justify-center cursor-pointer" title="Média">
              <i class="fa-regular fa-image"></i>
              <input type="file" wire:model="newPostImage" accept="image/*,video/*" class="hidden" />
            </label>
            <button wire:click="publishPost" wire:loading.attr="disabled" wire:target="publishPost,newPostImage" class="btn-wine shrink-0 text-xs font-semibold px-4 py-2 rounded-full">Publier</button>
          </div>
          @error('newPostContent') <div class="text-[11px] text-[#a83248] px-1">{{ $message }}</div> @enderror
          @error('newPostImage') <div class="text-[11px] text-[#a83248] px-1">{{ $message }}</div> @enderror
          @if($newPostImage)
            <div class="relative inline-block">
              @if(str_starts_with($newPostImage->getMimeType() ?? '', 'video/'))
                <video src="{{ $newPostImage->temporaryUrl() }}" class="rounded-xl max-h-40" muted autoplay loop></video>
              @else
                <img src="{{ $newPostImage->temporaryUrl() }}" class="rounded-xl max-h-40" />
              @endif
              <button wire:click="$set('newPostImage', null)" class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-[#6b1f2a] text-white text-xs flex items-center justify-center">✕</button>
            </div>
          @endif
        </section>

        <!-- Feed (SEUL élément qui défile) -->
        <div id="feed" class="feed-scroll scroll-thin space-y-4">
          @forelse($posts as $post)
            <article class="post-card glass rounded-2xl p-5" wire:key="post-{{ $post->id }}">
              <header class="flex items-center gap-3 mb-4">
                <div class="w-11 h-11 shrink-0 rounded-full ring-gradient p-[2px]"><div class="w-full h-full rounded-full bg-[#fbf6f0] flex items-center justify-center font-semibold">{{ $post->user->initials }}</div></div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-semibold text-sm">@​{{ $post->user->display_name }}</span>
                  </div>
                  <div class="text-[11px] text-neutral-500">{{ $post->created_at->diffForHumans() }}</div>
                </div>
                @if($post->user_id === auth()->id())
                  <button wire:click="deletePost({{ $post->id }})" wire:confirm="Supprimer cette publication ?" class="text-neutral-400 hover:text-[#6b1f2a] shrink-0" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                @endif
              </header>

              @if($post->content)
                <p class="text-sm leading-relaxed {{ $post->hasImage() ? 'mb-4' : 'mb-1' }}">{{ $post->content }}</p>
              @endif

              @if($post->hasImage())
                <div class="rounded-xl overflow-hidden border border-[#ead9d4]" wire:key="post-img-wrap-{{ $post->id }}">
                  @if(str_starts_with($post->image_mime ?? '', 'video/'))
                    <video src="{{ route('post.attachment', $post->id, false) }}" class="w-full max-h-[420px] object-contain bg-black/5" controls playsinline></video>
                  @else
                    <img src="{{ route('post.attachment', $post->id, false) }}" class="w-full max-h-[420px] object-contain bg-black/5" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');" />
                    <div class="hidden w-full py-10 flex-col items-center justify-center gap-2 text-neutral-400 text-xs">
                      <span class="text-2xl"><i class="fa-regular fa-image"></i></span>
                      <span>Image indisponible</span>
                    </div>
                  @endif
                </div>
              @endif

              <footer class="flex items-center gap-1 mt-4 pt-4 border-t border-[#ead9d4]/70 flex-wrap">
                <button wire:click="toggleLike({{ $post->id }})" class="like-btn flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs hover:bg-[#6b1f2a]/5 transition">
                  <span class="heart"><i class="{{ $post->liked_by_me ? 'fa-solid fa-heart' : 'fa-regular fa-heart' }}" style="{{ $post->liked_by_me ? 'color:#6b1f2a;' : '' }}"></i></span><span class="count">{{ $post->likes_count }}</span>
                </button>
                <button wire:click="toggleComments({{ $post->id }})" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs hover:bg-[#6b1f2a]/5">
                  <i class="fa-regular fa-comment"></i> <span>{{ $post->comments->count() }}</span>
                </button>
              </footer>

              <!-- Panneau commentaires (avec réponses imbriquées) -->
              @if(in_array($post->id, $openCommentsFor))
                <div class="mt-3 pt-3 border-t border-[#ead9d4]/70 space-y-3">
                  @forelse($post->rootComments as $comment)
                    <div class="flex items-start gap-2" wire:key="comment-{{ $comment->id }}">
                      <div class="w-7 h-7 shrink-0 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] flex items-center justify-center text-[10px] text-white font-semibold">{{ mb_substr($comment->user->initials, 0, 1) }}</div>
                      <div class="flex-1 min-w-0">
                        <div class="glass rounded-xl px-3 py-2">
                          <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold">@​{{ $comment->user->display_name }}</span>
                            <span class="text-[10px] text-neutral-500">{{ $comment->created_at->diffForHumans() }}</span>
                          </div>
                          <p class="text-xs leading-relaxed mt-0.5">{{ $comment->content }}</p>
                        </div>
                        <div class="flex items-center gap-3 mt-1 px-1">
                          <button wire:click="startReply({{ $post->id }}, {{ $comment->id }})" class="text-[10px] text-neutral-500 hover:text-[#6b1f2a]">Répondre</button>
                          @if($comment->user_id === auth()->id())
                            <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="Supprimer ce commentaire ?" class="text-[10px] text-neutral-500 hover:text-[#6b1f2a]">Supprimer</button>
                          @endif
                        </div>

                        <!-- Réponses imbriquées -->
                        @if($comment->replies->count())
                          <div class="mt-2 ml-2 pl-3 border-l-2 border-[#ead9d4] space-y-2">
                            @foreach($comment->replies as $reply)
                              <div class="flex items-start gap-2" wire:key="reply-{{ $reply->id }}">
                                <div class="w-6 h-6 shrink-0 rounded-full bg-gradient-to-br from-[#e0919b] to-[#6b1f2a] flex items-center justify-center text-[9px] text-white font-semibold">{{ mb_substr($reply->user->initials, 0, 1) }}</div>
                                <div class="flex-1 min-w-0">
                                  <div class="glass rounded-xl px-3 py-2">
                                    <div class="flex items-center gap-2">
                                      <span class="text-xs font-semibold">@​{{ $reply->user->display_name }}</span>
                                      <span class="text-[10px] text-neutral-500">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs leading-relaxed mt-0.5">{{ $reply->content }}</p>
                                  </div>
                                  @if($reply->user_id === auth()->id())
                                    <button wire:click="deleteComment({{ $reply->id }})" wire:confirm="Supprimer cette réponse ?" class="text-[10px] text-neutral-500 hover:text-[#6b1f2a] mt-1 ml-1">Supprimer</button>
                                  @endif
                                </div>
                              </div>
                            @endforeach
                          </div>
                        @endif

                        <!-- Champ de réponse -->
                        @if(($replyingTo[$post->id] ?? null) === $comment->id)
                          <div class="flex items-center gap-2 mt-2 ml-2">
                            <input wire:model="replyDrafts.{{ $comment->id }}" wire:keydown.enter="addReply({{ $post->id }}, {{ $comment->id }})" placeholder="Répondre à @​{{ $comment->user->display_name }}…" class="flex-1 glass rounded-full px-3 py-1.5 text-xs outline-none" />
                            <button wire:click="addReply({{ $post->id }}, {{ $comment->id }})" class="text-xs font-semibold text-[#6b1f2a] shrink-0">Envoyer</button>
                            <button wire:click="cancelReply({{ $post->id }})" class="text-xs text-neutral-400 shrink-0">Annuler</button>
                          </div>
                        @endif
                      </div>
                    </div>
                  @empty
                    <p class="text-xs text-neutral-500">Aucun commentaire pour le moment. Sois le premier à réagir !</p>
                  @endforelse

                  <!-- Nouveau commentaire racine -->
                  <div class="flex items-center gap-2 pt-1">
                    <input wire:model="commentDrafts.{{ $post->id }}" wire:keydown.enter="addComment({{ $post->id }})" placeholder="Écris un commentaire…" class="flex-1 glass rounded-full px-3 py-1.5 text-xs outline-none" />
                    <button wire:click="addComment({{ $post->id }})" class="text-xs font-semibold text-[#6b1f2a] shrink-0">Envoyer</button>
                  </div>
                </div>
              @endif
            </article>
          @empty
            <div class="glass rounded-2xl p-8 text-center text-sm text-neutral-500">
              Aucune publication pour le moment. Sois le premier à partager quelque chose ✨
            </div>
          @endforelse
        </div>
         </div>
        @endif
      </main>



      <!-- ========= RIGHT COLUMN : DISCUSSIONS ========= -->
      <aside id="colRight" class="col-panel {{ $activeTab === 'right' ? 'mobile-visible' : '' }}">
        @livewire('chat-box')
      </aside>
    </div>



    <!-- ===== BARRE DE NAVIGATION MOBILE (onglets) ===== -->
    <nav class="mobile-tabs glass-strong rounded-2xl px-2 py-1.5 shrink-0 items-center justify-around gap-1">
      <button wire:click="$set('activeTab', 'left')" class="tab-mobile {{ $activeTab === 'left' ? 'active' : '' }} flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl flex-1" data-tab="left">
        <span class="tab-icon w-8 h-8 rounded-lg glass flex items-center justify-center"><i class="fa-solid fa-users text-lg"></i></span>
        <span class="text-[10px] font-medium">Salons</span>
      </button>
      <button wire:click="$set('activeTab', 'center')" class="tab-mobile {{ $activeTab === 'center' ? 'active' : '' }} flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl flex-1" data-tab="center">
        <span class="tab-icon w-8 h-8 rounded-lg glass flex items-center justify-center"><i class="fa-solid fa-house text-lg"></i></span>
        <span class="text-[10px] font-medium">Story/Post</span>
      </button>
      <button wire:click="$set('activeTab', 'right')" class="tab-mobile {{ $activeTab === 'right' ? 'active' : '' }} flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl flex-1" data-tab="right">
        <span class="tab-icon w-8 h-8 rounded-lg glass flex items-center justify-center"><i class="fa-solid fa-comments text-lg"></i></span>
        <span class="text-[10px] font-medium">Chat</span>
      </button>
    </nav>
  </div>

  <!-- ===== MODAL D'APPEL ===== -->
  <div id="callModal" class="fixed inset-0 z-[100] hidden-x items-center justify-center modal-backdrop p-4">
    <div class="glass-strong rounded-3xl p-8 max-w-sm w-full text-center relative">
      <div id="callAvatar" class="w-24 h-24 mx-auto rounded-full ring-gradient p-[3px] pulse-call">
        <div class="w-full h-full rounded-full bg-[#fbf6f0] flex items-center justify-center font-display text-2xl font-semibold">JW</div>
      </div>
      <div id="callName" class="font-display text-2xl mt-4">James Whitfield</div>
      <div id="callStatus" class="text-sm text-neutral-500 mt-1">Appel en cours…</div>
      <div id="callType" class="text-[10px] uppercase tracking-widest text-[#6b1f2a] mt-2">Appel vocal</div>
      <div class="flex justify-center gap-4 mt-8">
        <button class="w-14 h-14 rounded-full glass flex items-center justify-center hover:bg-[#6b1f2a]/10" title="Mute"><i class="fa-solid fa-microphone-slash"></i></button>
        <button onclick="endCall()" class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl" style="background:linear-gradient(135deg,#c02b3a,#7d1a24);box-shadow:0 10px 30px -10px rgba(192,43,58,.6)">✕</button>
        <button class="w-14 h-14 rounded-full glass flex items-center justify-center hover:bg-[#6b1f2a]/10" title="Haut-parleur"><i class="fa-solid fa-volume-high"></i></button>
      </div>
    </div>
  </div>
  <script>
    window.socialnetLivewire = @this;
    window.rtcConfig = {
      iceServers: [
        { urls: 'stun:stun.l.google.com:19302' }
        @if(config('services.turn.url'))
        , {
          urls: '{{ config('services.turn.url') }}',
          username: '{{ config('services.turn.username') }}',
          credential: '{{ config('services.turn.credential') }}'
        }
        @endif
      ]
    };
  </script>
  <script src="{{ asset('js/socialnet.js') }}"></script>
</div>