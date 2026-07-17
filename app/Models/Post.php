<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = ['user_id', 'content', 'image_path', 'image_mime'];

    // L'auteur de la publication
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Tous les likes de cette publication
    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    // Tous les commentaires (racines + réponses) de cette publication
    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    // Seulement les commentaires "racine" (pas les réponses), les plus anciens
    // en premier — chaque commentaire racine charge déjà ses réponses (evite le N+1).
    public function rootComments(): HasMany
    {
        return $this->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->oldest();
    }

    public function hasImage(): bool
    {
        return !is_null($this->image_path);
    }

    // Cet utilisateur a-t-il déjà liké cette publication ?
    public function likedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->likes()->where('user_id', $userId)->exists();
    }
}
