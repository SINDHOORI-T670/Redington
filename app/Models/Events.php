<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $with = ['user'];
    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
