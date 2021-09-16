<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueryRequest extends Model
{
    protected $with = ['user','reply'];
    public function user()
    {
        return $this->belongsTo('App\User','from_id','id');
    }
    public function reply()
    {
        return $this->hasMany('App\Models\ReplyRequest','req_id','id');
    }
}
