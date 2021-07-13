<?php

namespace App\Models\WP;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $table="terms";
    protected $primaryKey="term_id";
    protected $fillable=[
        'term_id',
        'name',
        'slug',
        'description',
        'term_group'
    ];
    // public function taxonomy(){
    //     return $this->hasOne("App\Models\WP\Term");
    // }
    // public $timestamps = false;

}

?>