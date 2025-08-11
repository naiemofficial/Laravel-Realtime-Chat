<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{

    public static function existsWith(User $user_1, User $user_2){

        $user_1_conversation_ids = Participant::where('user_id', $user_1->id)->pluck('conversation_id');
        $shared_conversation_id  = Participant::whereIn('conversation_id', $user_1_conversation_ids)
                                    ->where('user_id', $user_2->id)
                                    ->pluck('conversation_id')->first();
        return ($user_1_conversation_ids->contains($shared_conversation_id) ? self::find($shared_conversation_id) : null);
    }

    public function participants(User|int|null $excludeUser = null){
        $user_id = 0;
        if($excludeUser !== null){
            $user_id = $excludeUser instanceof User ? $excludeUser->id : $excludeUser;
        }

        $participants = $this->hasMany(Participant::class);
        if($user_id > 0){
            $participants = $participants->where('user_id', '!=', $user_id);
        }
        return $participants;
    }


    public function participant(User|int|null $excludeUser = null): ?User {
        return $this->participants($excludeUser)->with('user')->first()?->user ?? null;
    }


    public function messages() : Collection {
        return $this->hasMany(Message::class)->get();
    }
}
