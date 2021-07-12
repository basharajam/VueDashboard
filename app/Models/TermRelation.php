<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TermRelation extends Model
{
    protected $table="wpdm_term_relationships";

    public $timestamps = false;

    protected $fillable=[
        'object_id',
        'term_taxonomy_id',
        'term_order'
    ];
    public function taxonomy(){
        return $this->belongsTo('App\Models\TermTaxonomy','term_taxonomy_id');
    }
    public function post(){
        return $this->belongsTo('App\Models\Post','object_id');
    }

}
