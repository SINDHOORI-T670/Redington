<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerReward extends Model
{
    protected $with = ['partner','rewarddetail'];
    public function partner()
    {
        return $this->belongsTo('App\User');
    }
    public function rewarddetail(){
        return $this->belongsTo('App\Models\Reward','reward_id','id');
    }
}
