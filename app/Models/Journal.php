<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $with = ['sub'];
    public function sub()
    {
        return $this->hasMany('App\Models\ValueJournal','journal_id','id');
    }
}
