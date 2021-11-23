<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class WpUser extends Authenticatable implements JWTSubject
{

    use HasFactory;


    protected $table = 'wpdm_users';
    protected $primaryKey = 'ID';
    
    protected $fillable =  [
        "user_login" ,
        "user_pass" ,
        "user_nicename" ,
        "user_email" ,
        "user_url" ,
        "user_registered" ,
        "user_activation_key" ,
        "user_status" ,
        "display_name" ,
    ];

    protected $hidden = [
        'user_pass'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthPassword()
    {
        return $this->user_pass;
    }


    public $timestamps = false;
}
