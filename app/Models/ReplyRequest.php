<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReplyRequest extends Model
{
    protected $with = ['user'];
    public function user()
    {
        return $this->belongsTo('App\User','from_id','id');
    }
}
