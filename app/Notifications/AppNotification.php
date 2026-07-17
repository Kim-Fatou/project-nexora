<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AppNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public string $title;
    public string $message;
    public string $type; // 'message', 'room_message', 'like', 'comment', 'friend_request'
    public ?string $url;
    public array $extraData;

    public function __construct(string $title, string $message, string $type, ?string $url = null, array $extraData = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->url = $url;
        $this->extraData = $extraData;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'url' => $this->url,
            'extra_data' => $this->extraData,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'url' => $this->url,
            'extra_data' => $this->extraData,
        ]);
    }
}
