<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $table="wpdm_terms";
    protected $primaryKey="term_id";
    // protected $with=['taxonomy'];
    protected $appends=['taxonomy'];
    protected $fillable=[
        'term_id',
        'name',
        'slug',
        'description',
        'term_group'
    ];

    public function getTaxonomyAttribute()
    {
        //
         return TermTaxonomy::where('term_id',$this->term_id)->first();
    //                         ->where('term_taxonomy_id','!=',$this->term_taxonomy_id)
    //                         ->where('supplier_id',\Auth::user()->userable_id)->get();
    }
    // public function taxonomy(){
    //     return $this->hasOne("App\Models\Term");
    // }
    public $timestamps = false;

}