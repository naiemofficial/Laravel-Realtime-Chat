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

    public function call(int $id = 0){
        $model = ($id > 0) ? self::find($id) : $this;
        // $model->belongsTo(Call::class)
        return false;
    }
}
