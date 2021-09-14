<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesConnect extends Model
{
    protected $with = ['region','technology','brand','user','reschedule'];

    public function region()
    {
        return $this->belongsTo('App\Models\Region','region_id','id');
    }

    public function technology()
    {
        return $this->belongsTo('App\Models\Technology','tech_id','id');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand','brand_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','poc_user_id','id');
    }

    public function reschedule(){
        return $this->belongsTo('App\Models\Reschedule','id','salecon_id');
    }
}
