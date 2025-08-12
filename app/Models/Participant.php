<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = [
        'conversation_id',
        'seen_conversation',
        'user_id'
    ];

    public function scopeUsers($query){
        $userIds = $query->pluck('user_id')->toArray();
        return User::whereIn('id', $userIds);
    }

    public function user() : User
    {
        return $this->belongsTo(User::class)->first();
    }
}
