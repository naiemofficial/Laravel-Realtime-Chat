<?php

use App\Models\User;
use \App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation-connection.{userID}', function (User $user, int $userID) {
    return $user->id === $userID;
        /*&& Conversation::find($conversationId)
            ?->participants()
            ?->where('user_id', $user->id)
            ->exists();*/
});

