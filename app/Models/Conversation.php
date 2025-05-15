<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{

    public static function existsWith(Guest $guest_1, Guest $guest_2) {
        $guest_1_conversation_ids = Participant::where('guest_id', $guest_1->id)->pluck('conversation_id');
        $shared_conversation_id  = Participant::whereIn('conversation_id', $guest_1_conversation_ids)
                                    ->where('guest_id', $guest_2->id)
                                    ->pluck('conversation_id')->first();

        $Conversation = ($shared_conversation_id ? self::find($shared_conversation_id) : null);
        return $Conversation;
    }

    public function recipient(array|int|null $current_guest_id = null) {
        return $this->recipients($current_guest_id)->first();
    }

    public function recipients(array|int|null $current_guest_id = null) {
        $guest_ids = Participant::where('conversation_id', $this->id)->pluck('guest_id');

        if ($current_guest_id !== null) {
            $guest_ids = $guest_ids->filter(function ($id) use ($current_guest_id) {
                return is_array($current_guest_id)
                    ? !in_array($id, $current_guest_id)
                    : $id != $current_guest_id;
            });
        }

        return Guest::whereIn('id', $guest_ids)->get() ?? null;
    }
}
