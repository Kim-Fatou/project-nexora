<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomMessage extends Model
{
    protected $fillable = [
        'room_id',
        'sender_id',
        'message',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'parent_id',
        'is_edited',
    ];

    protected function casts(): array
    {
        return [
            'message' => 'encrypted',
            'is_edited' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::created(function ($message) {
            $message->sender()->increment('discussion_score');
        });
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(RoomMessage::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(RoomMessage::class, 'parent_id');
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    public function isImageAttachment(): bool
    {
        return $this->hasAttachment() && str_starts_with((string) $this->attachment_mime, 'image/');
    }

    public function isAudioAttachment(): bool
    {
        return $this->hasAttachment() && (
            str_starts_with((string) $this->attachment_mime, 'audio/') 
            || str_contains((string) $this->attachment_name, 'voice-message')
        );
    }

    public function attachmentSizeForHumans(): ?string
    {
        if (!$this->attachment_size) {
            return null;
        }

        $bytes = $this->attachment_size;
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, $bytes < 10 ? 1 : 0) . ' ' . $units[$i];
    }
}