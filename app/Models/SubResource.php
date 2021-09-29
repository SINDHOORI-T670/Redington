<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubResource extends Model
{
    protected $with = ['subfiles'];
    public function subfiles()
    {
        return $this->hasMany('App\Models\SubResourceFile','sub_id','id');
    }
}
