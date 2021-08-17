<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
// use App\Models\WP\TermRelation;
use App\Models\attachment;
class TermTaxonomy extends Model
{
    protected $table="wpdm_term_taxonomy";
    protected $primaryKey="term_taxonomy_id";

    protected $fillable=[
        'term_id',
        'taxonomy',
        'description',
        'parent',
        'supplier_id'
    ];
    // protected $with=['term','posts'];
    protected $with=['posts'];
    public $timestamps = false;

    protected $appends = ['image'];
    // protected $appends = ['image','terms'];

    public function scopeCategories($query){
        return $query->whereIn('taxonomy',['category'])->distinct('product_cat');
    }

    // public function term(){
    //     return $this->belongsTo('App\Models\WP\Term','term_id');
    // }
    public function posts(){
        return $this->hasMany('App\Models\TermRelation','term_taxonomy_id');
    }
    // public function getTermsAttribute(){
    //     if(\Auth::user() && \Auth::user()->hasRole(\UserRoles::SUPPLIERMANAGER)){
    //         return TermTaxonomy::where('taxonomy',$this->taxonomy)->where('term_taxonomy_id','!=',$this->term_taxonomy_id)->get();
    //     }
    //     else{
    //         return TermTaxonomy::where('taxonomy',$this->taxonomy)
    //                         ->where('term_taxonomy_id','!=',$this->term_taxonomy_id)
    //                         ->where('supplier_id',\Auth::user()->userable_id)->get();
    //     }
    // }
    public function getImageAttribute()
    {
        $image_post_meta =  TermMeta::where('term_id',$this->term_id)->where('meta_key','thumbnail_id')->first();
        if($image_post_meta){
            $image_post =  attachment::where('ID',$image_post_meta->meta_value)->orderBy('ID','desc')->first();
            if($image_post){
                return $image_post;
            }
        }
        return '';
    }
    // public function getParentCategoryAttribute(){
    //     if($this->parent){
    //         return TermTaxonomy::where('term_taxonomy_id',$this->parent)->first();
    //     }
    //     else
    //     return null;
    // }
}
