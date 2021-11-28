<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Corcel\Model\User;
use Corcel\Concerns\Aliases;
use Corcel\Concerns\MetaFields;
use Corcel\Concerns\AdvancedCustomFields;

class WpUser extends User implements JWTSubject
{

    use HasFactory;
    use AdvancedCustomFields;
    use Aliases;
    use MetaFields;


    protected $table = 'users';
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


    //get user meta
    protected $with = ['meta'];

    protected static $aliases = [
        'login' => 'user_login',
        'email' => 'user_email',
        'slug' => 'user_nicename',
        'url' => 'user_url',
        'nickname' => ['meta' => 'nickname'],
        'first_name' => ['meta' => 'first_name'],
        'last_name' => ['meta' => 'last_name'],
        'billing_first_name'=> ['meta' => 'billing_first_name'],
        'billing_last_name'=>['meta' => 'billing_last_name'],
        'description' => ['meta' => 'description'],
        'created_at' => 'user_registered',
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
