<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;

    public function __construct(ChatMessage $message)
    {
        // On charge l'expéditeur pour l'avoir dispo côté front sans requête supplémentaire
        $this->message = $message->load('sender');
    }

    /**
     * Le canal privé propre à cette discussion (connexion) précise.
     */
    public function broadcastOn(): array
    {
        // On calcule qui est le DESTINATAIRE (l'autre personne, pas l'expéditeur)
        // pour pouvoir le notifier même s'il n'a pas cette discussion ouverte.
        $connection = $this->message->connection;
        $receiverId = $connection->user_id === $this->message->sender_id
            ? $connection->friend_id
            : $connection->user_id;

        return [
            // Canal 1 : la discussion elle-même — pour mettre à jour les messages
            // affichés en temps réel QUAND cette discussion précise est ouverte.
            new PrivateChannel('chat.' . $this->message->connection_id),

            // Canal 2 : canal personnel du destinataire — pour rafraîchir la LISTE
            // des discussions (aperçu + remontée en haut) même s'il est ailleurs
            // dans l'app (liste fermée, autre discussion ouverte, etc.).
            new PrivateChannel('App.Models.User.' . $receiverId),
        ];
    }

    /**
     * Nom de l'événement côté front (correspond au listen('echo-private:chat.{id},MessageSent', ...))
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    /**
     * Données envoyées au navigateur.
     */
    public function broadcastWith(): array
    {
    return [
            'id' => $this->message->id,
            'connection_id' => $this->message->connection_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'message' => $this->message->message,
            'has_attachment' => $this->message->hasAttachment(),
            'attachment_name' => $this->message->attachment_name,
            'created_at' => $this->message->created_at->format('H:i'),
        ];
    }
}