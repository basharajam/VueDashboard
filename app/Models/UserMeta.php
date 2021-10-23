<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    
    protected $table="wpdm_postmeta";
    protected $primaryKey="umeta_id ";

    protected $fillable =  [
        'umeta_id ',
        'user_id',
        'meta_key ',
        'meta_value'
    ];
    public $timestamps = false;

}
