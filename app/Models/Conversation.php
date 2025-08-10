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

    public function recipient(User|array|int|null $user = null) : ?User {
        if($user instanceof User){
            $user = $user->id;
        }
        return $this->recipients($user)->first();
    }

    public function recipients(User|array|int|null $user = null) : Collection {
        if($user instanceof User){
            $user = $user->id;
        }
        $current_user_id = $user ?? Auth::user()->id;
        $user_ids = Participant::where('conversation_id', $this->id)->pluck('user_id');

        if ($current_user_id !== null) {
            $user_ids = $user_ids->filter(function ($id) use ($current_user_id) {
                return is_array($current_user_id)
                    ? !in_array($id, $current_user_id)
                    : $id != $current_user_id;
            });
        }

        return User::whereIn('id', $user_ids)->get();
    }

    public function messages(){
        return $this->hasMany(Message::class)->get();
    }
}
