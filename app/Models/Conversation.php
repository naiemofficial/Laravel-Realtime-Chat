<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Conversation extends Model
{

    public static function existsWith(Guest $guest_1, Guest $guest_2) {
        $guest_1_conversation_ids = Participant::where('guest_id', $guest_1->id)->pluck('conversation_id');
        $shared_conversation_id  = Participant::whereIn('conversation_id', $guest_1_conversation_ids)
                                    ->where('guest_id', $guest_2->id)
                                    ->pluck('conversation_id')->first();
        return ($guest_1_conversation_ids->contains($shared_conversation_id) ? self::find($shared_conversation_id) : null);
    }

    public function recipient(Guest|array|int|null $guest = null) : ?Guest {
        if($guest instanceof Guest){
            $guest = $guest->id;
        }
        return $this->recipients($guest)->first();
    }

    public function recipients(Guest|array|int|null $guest = null) : Collection {
        if($guest instanceof Guest){
            $guest = $guest->id;
        }
        $current_guest_id = $guest ?? Guest::current()->id;
        $guest_ids = Participant::where('conversation_id', $this->id)->pluck('guest_id');

        if ($current_guest_id !== null) {
            $guest_ids = $guest_ids->filter(function ($id) use ($current_guest_id) {
                return is_array($current_guest_id)
                    ? !in_array($id, $current_guest_id)
                    : $id != $current_guest_id;
            });
        }

        return Guest::whereIn('id', $guest_ids)->get();
    }

    public function messages(){
        return $this->hasMany(Message::class)->get();
    }
}
