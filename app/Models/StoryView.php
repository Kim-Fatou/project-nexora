<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryView extends Model
{
    public $timestamps = false; // on a déjà 'viewed_at', pas besoin de created_at/updated_at

    protected $fillable = ['story_id', 'viewer_id', 'viewed_at', 'liked'];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'liked' => 'boolean',
        ];
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }
}
