<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Connection extends Model
{
    protected $fillable = ['user_id', 'friend_id', 'status', 'blocked_by'];

    // Une connexion appartient à l'utilisateur qui a fait la demande
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Une connexion appartient aussi à l'ami qui a reçu la demande
    public function friend(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    // Une connexion (discussion) contient plusieurs messages de chat
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    public function unreadMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)
            ->whereNull('read_at')
            ->where('sender_id', '!=', auth()->id());
    }

    public function blockedBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'blocked_by');
}

    public function isBlocked(): bool
    {
        return !is_null($this->blocked_by);
    }
}