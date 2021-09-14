<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueryRequest extends Model
{
    protected $with = ['user'];
    public function user()
    {
        return $this->hasMany('App\User','from_id','id');
    }
}
