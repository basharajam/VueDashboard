<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VueLayouts extends Model
{
    use HasFactory;

    //      // 	
        // 	
        // 	
        // 	
        // 	
    public $timestamps = false;
    
    
    protected $fillable=['title','value','type','wherePage','compName','compType','itemNum','link','sort','sortMobile','mobileDisplay','Display'];
}
