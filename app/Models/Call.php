<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    //
    protected $fillable = [
        'message_id',
        'type',
        'status',
        'accepted_at',
        'ended_at'
    ];

    public function message(){
        return $this->belongsTo(Message::class);
    }

    public function caller(){
        return $this->message->user;
    }

    public function receiver(){
        return $this->message->recipient();
    }
}
