<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresetQuestion extends Model
{
    protected $with = ['technology','brand','request'];

    public function request()
    {
        return $this->hasMany('App\Models\ReplyRequest','req_id','id');
    }
    public function technology()
    {
        return $this->belongsTo('App\Models\Technology','tech_id','id');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand','brand_id','id');
    }

    
     


}
