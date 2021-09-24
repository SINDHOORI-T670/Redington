<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainService extends Model
{
    protected $with = ['sub'];
    public function sub()
    {
        return $this->hasMany('App\Models\SubService','main_id','id');
    }
}
