<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $touches = ['conversation'];

    protected $fillable = [
        'conversation_id',
        'user_id',
        'text',
        'type'
    ];

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function recipient(){
        return $this->conversation?->participant($this->user, exclude: true)?->user;
    }

    public function call(){
        return $this->hasOne(Call::class)->first();
    }
}
