<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostComment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'parent_id', 'content'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    // L'auteur du commentaire
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Le commentaire auquel celui-ci répond (null si c'est un commentaire racine)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PostComment::class, 'parent_id');
    }

    // Les réponses directes à ce commentaire
    public function replies(): HasMany
    {
        return $this->hasMany(PostComment::class, 'parent_id')->oldest();
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }
}
