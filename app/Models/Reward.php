<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $with = ['partner'];
    public function partner()
    {
        return $this->belongsTo('App\User');
    }
}
