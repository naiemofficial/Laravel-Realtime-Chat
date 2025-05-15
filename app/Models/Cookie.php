<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie as CookieFacade;

class Cookie extends Model
{
    //
    protected $fillable = [
        'name',
        'value',
        'expires_at'
    ];

    public function user(){
        return $this->hasMany(Guest::class, 'cookie_id')->first();
    }

    public function guest(){
        return $this->user();
    }

    public static function local($name = ''){
        return CookieFacade::get($name);
    }

    public function current(string $cookie_name, string $cookie_value){
        return $this->where('name', $cookie_name)->update(['value' => $cookie_value])->first();
    }
}
