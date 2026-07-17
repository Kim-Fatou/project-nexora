<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallIncoming implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public int $senderId;
    public string $senderName;
    public string $type; // 'chat' or 'room'
    public int $targetId; // connectionId or roomId

    public function __construct(int $senderId, string $senderName, string $type, int $targetId)
    {
        $this->senderId = $senderId;
        $this->senderName = $senderName;
        $this->type = $type;
        $this->targetId = $targetId;
    }

    public function broadcastOn(): array
    {
        if ($this->type === 'chat') {
            $connection = \App\Models\Connection::findOrFail($this->targetId);
            $receiverId = $connection->user_id === $this->senderId
                ? $connection->friend_id
                : $connection->user_id;

            return [new PrivateChannel('App.Models.User.' . $receiverId)];
        }

        return [new PrivateChannel('room.' . $this->targetId)];
    }

    public function broadcastAs(): string
    {
        return 'CallIncoming';
    }
}
