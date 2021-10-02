<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegister extends Model
{
    protected $with = ['user','event'];
    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
    public function event()
    {
        return $this->belongsTo('App\Models\Event','event_id','id');
    }
}
