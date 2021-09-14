<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresetQuestion extends Model
{
    protected $with = ['request'];

    public function request()
    {
        return $this->hasMany('App\Models\QueryRequest','query_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','from_id','id');
    }

}
