<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $with = ['sub'];
    public function sub()
    {
        return $this->hasMany('App\Models\SubResource','resource_id','id');
    }
}
