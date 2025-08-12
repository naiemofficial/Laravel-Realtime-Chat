<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $touches = ['conversation'];

    protected $fillable = [
        'conversation_id',
        'participant_id',
        'text',
        'type'
    ];

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }

    public function participant(){
        return $this->belongsTo(Participant::class);
    }
}
