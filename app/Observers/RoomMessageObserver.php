<?php

namespace App\Observers;

use App\Models\RoomMessage;

class RoomMessageObserver
{
    public function created(RoomMessage $message): void
    {
        $user = $message->sender;
        if (!$user) {
            return;
        }

        // Limit daily farming: max 50 XP per day from sending messages (+2 XP per message = 25 messages)
        $todayMessagesCount = \App\Models\ChatMessage::where('sender_id', $user->id)
            ->whereDate('created_at', today())
            ->count() + \App\Models\RoomMessage::where('sender_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayMessagesCount <= 25) {
            $user->addXp(2);
        }
    }
}
