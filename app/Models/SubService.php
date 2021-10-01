<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubService extends Model
{
    public function notification()
    {
        return $this->hasMany('App\Models\Notification','req_from','id')->where('type','Sub_service')->latest();
    }
}
