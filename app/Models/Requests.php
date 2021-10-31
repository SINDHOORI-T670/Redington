<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    protected $with = ['from','notifi'];
    public function from()
    {
        return $this->belongsTo('App\User','from_id','id');
    }
    public function subservice(){
        return $this->belongsTo('App\Models\SubService','req_id','id');
    }
    public function business(){
        return $this->belongsTo('App\Models\BusinessSolution','req_id','id');
    }
    public function promotion(){
        return $this->belongsTo('App\Models\Promotion','req_id','id');
    }
    public function notifi()
    {
        return $this->belongsTo('App\Models\Notification','notifid','id');
    }
}
