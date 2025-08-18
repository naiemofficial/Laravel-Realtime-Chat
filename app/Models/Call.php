<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    //
    protected $fillable = [
        'message_id',
        'type',
        'status'
    ];
}
