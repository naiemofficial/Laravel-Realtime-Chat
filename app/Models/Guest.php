<?php

namespace App\Models;

use App\Models\Cookie;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    public static string $cookie_name = 'guest_session';
    protected $fillable = ['uid', 'name', 'cookie_id'];

    public function cookie(){
        return $this->belongsTo(Cookie::class);
    }


    public static function isValid(Cookie $Cookie = null): bool
    {
        $cookie_value = ($Cookie instanceof Cookie) ? $Cookie->value : Cookie::local(self::$cookie_name);
        $cookie = Cookie::where('name', self::$cookie_name)
            ->where('value', $cookie_value)
            ->first();

        $Guest =  $cookie?->guest();
        return ($cookie != null && $Guest instanceof Guest);
    }



    public static function current(Cookie $Cookie = null)
    {
        $cookie_value = ($Cookie instanceof Cookie) ? $Cookie->value : Cookie::local(self::$cookie_name);
        $cookie = Cookie::where('name', self::$cookie_name)
            ->where('value', $cookie_value)
            ->first();

        $Guest = $cookie?->guest();
        if($Guest instanceof Guest){
            return $cookie->guest();
        }

        return null;
    }

    public function validity(): bool
    {
        return ($this->exists && $this->cookie()->exists());
    }


    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'participants', 'guest_id', 'conversation_id');
    }
}
