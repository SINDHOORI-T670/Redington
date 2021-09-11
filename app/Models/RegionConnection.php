<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionConnection extends Model
{
    protected $with = ['region'];

    public function region()
    {
        return $this->belongsTo('App\Models\Region','region_id','id');
    }
}
