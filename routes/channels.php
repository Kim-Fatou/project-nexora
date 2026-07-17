<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Connection;
use App\Models\Room; // pour vérifier qui a le droit d'écouter un salon

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Seuls les deux participants d'une discussion acceptée peuvent écouter son canal
Broadcast::channel('chat.{connectionId}', function ($user, $connectionId) {
    $connection = Connection::find($connectionId);

    if (!$connection || $connection->status !== 'accepted' || $connection->isBlocked()) {
        return false;
    }

    return (int) $user->id === (int) $connection->user_id
        || (int) $user->id === (int) $connection->friend_id;
});


// Seuls les membres d'un salon peuvent écouter ses messages en temps réel
// (sinon n'importe qui pourrait espionner un salon sans l'avoir rejoint)
Broadcast::channel('room.{roomId}', function ($user, $roomId) {
    $room = Room::find($roomId);

    if (!$room) {
        return false; // le salon n'existe pas (ou plus) -> personne ne peut écouter
    }

    return $room->hasMember($user->id); // true seulement si l'utilisateur est membre
});

// Canal de présence global pour savoir qui est en ligne
Broadcast::channel('online', function ($user) {
    return [
        'id' => $user->id,
        'username' => $user->username,
        'name' => $user->name,
    ];
});