<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['title'];
    
    function users() {
        return $this->belongsToMany('App\User', 'user_games')
            ->withPivot('color');
    }
}