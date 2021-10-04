<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VueConfig extends Model
{
    use HasFactory;

    // name	key	value	subValue	type	status	


    protected $fillable=['name','key','value','subValue','type','status'];

}
