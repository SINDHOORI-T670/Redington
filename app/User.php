<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['poc','regionConnect','userSpec'];

    public function isAdmin() {
        return $this->type === 1;
    }

    public function isCustomer() {
        return $this->type === 2;
    }

    public function isPartner() {
        return $this->type === 3;
    }

    public function isEmployee() {
        return $this->type === 4;
    }




    public function poc()
    {
        return $this->belongsTo('App\Models\Poc','poc_id','id');
    }
    public function regionConnect()
    {
        return $this->belongsTo('App\Models\RegionConnection','id','user_id');
    }
    public function userSpec(){
        return $this->hasOne('App\Models\UserSpec','id','user_id');
    }
}
