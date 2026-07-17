<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallOffer implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public int $senderId;
    public string $type; // 'chat' or 'room'
    public int $targetId; // connectionId or roomId
    public array $offer; // SDP data

    public function __construct(int $senderId, string $type, int $targetId, array $offer)
    {
        $this->senderId = $senderId;
        $this->type = $type;
        $this->targetId = $targetId;
        $this->offer = $offer;
    }

    public function broadcastOn(): array
    {
        return [
            $this->type === 'chat' 
                ? new PrivateChannel('chat.' . $this->targetId)
                : new PrivateChannel('room.' . $this->targetId)
        ];
    }

    public function broadcastAs(): string
    {
        return 'CallOffer';
    }
}
