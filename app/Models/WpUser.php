<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class WpUser extends Authenticatable implements JWTSubject
{

            /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    protected $table = 'wpdm_users';
    
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

        public function getAuthPassword()
        {
            return $this->user_pass;
        }


        public $timestamps = false;
}
