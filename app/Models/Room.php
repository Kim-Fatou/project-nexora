<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Room extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'interest_id', 'creator_id', 'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    // Le salon peut être rattaché à une passion (ex: "Technologie")
    public function interest(): BelongsTo
    {
        return $this->belongsTo(Interest::class);
    }

    // L'utilisateur qui a créé le salon
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // Tous les membres du salon
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_user')->withPivot('joined_at');
    }

    // Tous les messages du salon
    public function messages(): HasMany
    {
        return $this->hasMany(RoomMessage::class);
    }

    public function hasMember(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->members()->where('users.id', $userId)->exists();
    }

    // Génère un slug unique à partir du nom choisi par l'utilisateur
    // (ex: "Vélo de route" -> "velo-de-route", puis "-2" si déjà pris).
    public static function uniqueSlugFor(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}