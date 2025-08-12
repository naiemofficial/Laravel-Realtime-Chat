<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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

    public function participants(Participant|int|null $Participant = null, $exclude = false) : hasMany {
        $participants = $this->hasMany(Participant::class);
        if($exclude && $Participant !== null){
            $participant_id = ($Participant instanceof Participant) ? $Participant->id : $Participant;
            return $participants->where('id', '!=', $participant_id);
        }
        return $participants;
    }


    public function participant(User|Participant|int|null $Participant = null, $exclude = false): Participant|null {
        if($Participant instanceof User){
            $User = $Participant;
            return $this->participants()->where('user_id', ($exclude ? '!=' : '='), $User->id)->first();
        }

        $participant_id = ($Participant instanceof Participant) ? $Participant->id : $Participant;
        return $this->participants()?->where('id', ($exclude ? '!=' : '='), $participant_id)->first();
    }


    public function hasParticipant(Participant|int|null $Participant = null): bool {
        if ($Participant === null) {
            return $this->participants()->exists();
        }

        $participant_id = $Participant instanceof Participant ? $Participant->id : $Participant;
        return $this->participants()->where('id', $participant_id)->exists();
    }

    public function users(User|int|null $User = null) : hasManyThrough {
        $users = $this->hasManyThrough(User::class, Participant::class, 'conversation_id', 'id', 'id', 'user_id');
        if($User !== null){
            $user_id = ($User instanceof User) ? $User->id : $User;
            return $users->where('users.id', '!=', $user_id);
        }
        return $users;
    }


    public function user(User|int|null $User = null): User|null {
        return $this->users($User)->first();
    }


    public function hasUser(User|int|null $User = null): bool {
        if ($User === null) {
            return $this->users()->exists();
        }

        $user_id = $User instanceof User ? $User->id : $User;
        return $this->users()->where('user_id', $user_id)->exists();
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }
}
