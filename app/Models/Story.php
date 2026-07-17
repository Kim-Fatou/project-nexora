<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Story extends Model
{
    protected $fillable = [
        'user_id', 'type', 'media_path', 'media_mime',
        'text_content', 'background_color', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    // L'auteur de la story
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Toutes les "vues" de cette story (qui l'a regardée, et quand)
    public function views(): HasMany
    {
        return $this->hasMany(StoryView::class);
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    // Un utilisateur donné a-t-il déjà vu cette story ?
    public function viewedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->views()->where('viewer_id', $userId)->exists();
    }

    // Ne renvoie que les stories encore actives (moins de 24h)
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function likesCount(): int
    {
        return $this->views()->where('liked', true)->count();
    }

    public function likedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }
        return $this->views()->where('viewer_id', $userId)->where('liked', true)->exists();
    }
}
