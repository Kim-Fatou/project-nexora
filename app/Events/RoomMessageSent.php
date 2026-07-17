<?php

namespace App\Events;

use App\Models\RoomMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public RoomMessage $message;

    public function __construct(RoomMessage $message)
    {
        $this->message = $message->load('sender');
    }

    /**
     * Un seul canal ici : le salon lui-même. Contrairement au chat privé,
     * pas besoin de canal "personnel" par destinataire (un salon peut avoir
     * des centaines de membres) — la liste des salons se rafraîchit par un
     * simple poll côté front plutôt que par un événement par membre.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('room.' . $this->message->room_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'RoomMessageSent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'room_id' => $this->message->room_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'message' => $this->message->message,
            'has_attachment' => $this->message->hasAttachment(),
            'attachment_name' => $this->message->attachment_name,
            'created_at' => $this->message->created_at->format('H:i'),
        ];
    }
}