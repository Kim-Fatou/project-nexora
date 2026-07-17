<?php

namespace App\Models;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Connection;
use App\Models\Interest;
use App\Models\Room;
use App\Models\Story; // pour la relation vers les salons rejoints
use App\Models\Post; // pour la relation vers les publications du flux
use App\Models\Subscription;
use App\Casts\SafeEncryptCast;

#[Fillable(['name', 'email', 'password', 'username', 'location', 'bio', 'goals', 'ideal_partner', 'discussion_score', 'is_expert', 'expertise_interest_id', 'level', 'xp', 'profile_completed_xp_awarded', 'is_admin', 'is_premium'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_expert' => 'boolean',
            'is_premium' => 'boolean',
            'premium_expires_at' => 'datetime',
            'profile_completed_xp_awarded' => 'boolean',
            'name' => SafeEncryptCast::class,
            'bio' => SafeEncryptCast::class,
            'location' => SafeEncryptCast::class,
            'goals' => SafeEncryptCast::class,
            'ideal_partner' => SafeEncryptCast::class,
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function expertiseInterest(): BelongsTo
    {
        return $this->belongsTo(Interest::class, 'expertise_interest_id');
    }

    public function isPremium(): bool
    {
        // DDIA / Business logic: Access granted if level threshold is reached (level >= 5)
        if ($this->level >= 5) {
            return true;
        }

        // OR discussion score >= 100 (fallback compatibility)
        if ($this->discussion_score >= 100) {
            return true;
        }

        // OR if user has an active is_premium subscription in the database
        if ($this->is_premium) {
            if (is_null($this->premium_expires_at) || $this->premium_expires_at->isFuture()) {
                return true;
            }
        }

        // Fallback backward compatibility with subscriptions table
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->exists();
    }

    public function discussionLevel(): int
    {
        // Simple formula: Level starts at 1, goes up by 1 level every 20 discussion points
        return floor($this->discussion_score / 20) + 1;
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class);
    }


     // Nouvelle relation, même pattern que interests()
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class)->withPivot('is_native');
    }

    
        // 1. Récupérer toutes les connexions que CET utilisateur a initiées
    public function initiatedConnections(): HasMany
    {
        return $this->hasMany(Connection::class, 'user_id');
    }

    // 2. Récupérer toutes les connexions que CET utilisateur a reçues
    public function receivedConnections(): HasMany
    {
        return $this->hasMany(Connection::class, 'friend_id');
    }

    // -------------------------------------------------------------------
    // Récupère toutes les discussions ACTIVES (acceptées) de l'utilisateur,
    // avec le dernier message de chacune préchargé (évite le N+1), et les
    // trie pour que la conversation avec le message le plus récent soit
    // toujours en premier (comportement "WhatsApp").
    // -------------------------------------------------------------------
    public function activeConversations()
    {
        $userId = $this->id;

        return Connection::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->orWhere('friend_id', $userId);
            })
            ->where('status', 'accepted')
            ->with(['user', 'friend', 'latestMessage'])
            ->withCount('unreadMessages')
            ->get()
            ->sortByDesc(fn ($conversation) => $conversation->latestMessage?->created_at ?? $conversation->created_at)
            ->values();
    }



        // Salons (rooms) que cet utilisateur a rejoints
    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_user')->withPivot('joined_at');
    }

    // Toutes les stories publiées par cet utilisateur
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    // Toutes les publications (posts) créées par cet utilisateur
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // Liste des IDs des AMIS (connexions acceptées), dans les 2 sens.
    // Sert à savoir quelles stories afficher (les miennes + celles de mes amis).
    public function friendIds()
    {
        $sent = $this->initiatedConnections()->where('status', 'accepted')->pluck('friend_id');
        $received = $this->receivedConnections()->where('status', 'accepted')->pluck('user_id');

        return $sent->merge($received)->unique()->values();
    }



    public function suggestedUsers()
    {
        $myInterestIds = $this->interests()->pluck('interests.id');

        $alreadyConnectedIds = Connection::where('user_id', $this->id)->pluck('friend_id')
            ->merge(Connection::where('friend_id', $this->id)->pluck('user_id'))
            ->unique();

        return User::where('id', '!=', $this->id)
            ->whereNotIn('id', $alreadyConnectedIds)
            ->whereHas('interests', function($query) use ($myInterestIds) {
                $query->whereIn('interests.id', $myInterestIds);
            })->with('interests')->get();
     }

     public function getDisplayNameAttribute(): string
     {
         return $this->username ?: 'Membre_' . $this->id;
     }

     public function getInitialsAttribute(): string
     {
         $name = $this->username ?: ('Membre_' . $this->id);
         return strtoupper(mb_substr($name, 0, 2));
     }

     public function addXp(int $amount): void
     {
         $this->increment('xp', $amount);
         $newLevel = $this->calculateLevelFromXp($this->xp);
         if ($newLevel !== $this->level) {
             $this->update(['level' => $newLevel]);
         }
     }

     public function calculateLevelFromXp(int $xp): int
     {
         if ($xp >= 1000) return 5;
         if ($xp >= 600) return 4;
         if ($xp >= 300) return 3;
         if ($xp >= 100) return 2;
         return 1;
     }

     public function checkAndAwardProfileCompletionXp(): void
     {
          if ($this->profile_completed_xp_awarded) {
              return;
          }

          if (!empty($this->bio) && !empty($this->username) && $this->languages()->exists() && $this->interests()->exists()) {
              $this->update(['profile_completed_xp_awarded' => true]);
              $this->addXp(30);
          }
     }

     public function getIsAdminAttribute($value): bool
     {
         if (config('app.env') === 'local' && str_contains(strtolower($this->email ?? ''), 'admin')) {
             return true;
         }
         return (bool)$value;
     }
}
