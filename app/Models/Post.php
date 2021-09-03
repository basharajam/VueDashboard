<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\PostMeta;
use App\Models\TermRelation;
use Illuminate\Database\Eloquent\Builder;
use PhpParser\Node\Expr\FuncCall;



use Corcel\Model\Attachment;
use Corcel\WooCommerce\Model\Product as Corcel;
class Post extends Corcel
{

    protected $connection = 'wordpress';

    protected $postType='product';


    protected $appends = ['gallery','on_sale','cbm','cartqty','variation','type'];


    protected function getCbmAttribute(): ?string
    {
        return $this->getMeta('al_cbm');
    }
    protected function getCartqtyAttribute(): ?string
    {
        return $this->getMeta('al_carton_qty');
    }
    protected function getProductVariationsAttribute(): ?string
    {
        return $this->getMeta('al_carton_qty');
    }




    // public function getProductTypeAttribute(){
    //     return    TermTaxonomy::whereIn('term_taxonomy_id',
    //                             TermRelation::where('object_id',$this->ID)
    //                                         ->pluck('term_taxonomy_id'))
    //                             ->where('taxonomy','product_type')
    //                             ->first();
    // }



    // protected $table="wpdm_posts";
    // protected $primaryKey="ID";
    // protected $appends = ['meta','images'];
    // // protected $appends = ['meta','categories','product_type','product_image'];
    // // protected $appends = ['meta','categories','product_type','product_image','tags'];
    // //protected $appends = ['meta','categories','product_type','product_attributes','product_image','tags'];
    // public $timestamps = false;
    // protected static function booted()
    // {
    //     static::addGlobalScope('wpdm_posts', function (Builder $builder) {
    //         $builder->where('post_status','!=','trash')->where('post_status','!=','pending')->where('post_status','!=','draft');
    //     });
    // }
    // protected $fillable =  [
    // "post_author" ,
    // "post_date" ,
    // "post_date_gmt" , //it's like created_at
    // "post_content" ,
    // "post_title" ,
    // "post_excerpt" , //post summary
    // "post_status" ,
    // "comment_status" ,
    // "ping_status" ,
    // "post_password" ,
    // "post_name" ,
    // "to_ping" ,
    // "pinged" ,
    // "post_modified" ,
    // "post_modified_gmt" ,
    // "post_content_filtered" ,
    // "post_parent" ,
    // "guid" , //ex. "https://alyamanlive.com/?post_type=product&p=6763"
    // "menu_order" ,
    // "post_type" ,
    // "post_mime_type" ,
    // "comment_count" ,
    // ];

    // public function scopeProducts($query){
    //     return $query->where('post_type','product')->where('post_status','publish');
    // }
    // public function getMetaAttribute(){
    //     return PostMeta::where('post_id',$this->ID)->pluck('meta_value','meta_key')->toArray();
    // }
    // public function getimagesAttribute(){
        // $image_post_meta =  PostMeta::where('post_id',$this->ID)->where('meta_key','_thumbnail_id')->first();
        // if($image_post_meta){
        //     $image_post =  Post::where('ID',$image_post_meta->meta_value)->orderBy('ID','desc')->first();
        //     if($image_post){
        //         return $image_post;
        //     }
        // }
        // return '';
    //     $image_post_meta =  PostMeta::where('post_id',$this->ID)->where('meta_key','_thumbnail_id')->first();
    //     if($image_post_meta){
    //         $image_post =  Post::where('ID',$image_post_meta->meta_value)->orderBy('ID','desc')->first();
    //         if($image_post){
    //             return $image_post;
    //         }
    //     }
    //     return '';
    // }
    // public function getGalleryAttribute(){
    //     return  Post::where('post_parent',$this->ID)
    //                                 ->where('post_type','attachment')
    //                                 ->get();

    // }
    // public function getCategoriesAttribute(){
    //   return    TermTaxonomy::whereIn('term_taxonomy_id',
    //                         TermRelation::where('object_id',$this->ID)
    //                                     ->pluck('term_taxonomy_id'))
    //                         ->whereIn('taxonomy',['product_cat'])
    //                         ->get();
    // }
    // public function getProductTypeAttribute(){
    //     return    TermTaxonomy::whereIn('term_taxonomy_id',
    //                             TermRelation::where('object_id',$this->ID)
    //                                         ->pluck('term_taxonomy_id'))
    //                             ->where('taxonomy','product_type')
    //                             ->first();
    // }


//     public function getCategoriesAttribute(){
//       return    TermTaxonomy::whereIn('term_taxonomy_id',
//                             TermRelation::where('object_id',$this->ID)
//                                         ->pluck('term_taxonomy_id'))
//                             ->whereIn('taxonomy',['product_cat'])
//                             ->get();
//     }
//     // public function getTagsAttribute(){
//     //     return    TermTaxonomy::whereIn('term_taxonomy_id',
//     //                           TermRelation::where('object_id',$this->ID)
//     //                                       ->pluck('term_taxonomy_id'))
//     //                           ->whereIn('taxonomy',['product_tag'])
//     //                           ->get();
//     //   }
//     public function getProductTypeAttribute(){
//         return    TermTaxonomy::whereIn('term_taxonomy_id',
//                                 TermRelation::where('object_id',$this->ID)
//                                             ->pluck('term_taxonomy_id'))
//                                 ->where('taxonomy','product_type')
//                                 ->first();
//     }

//     ///
//     public function getProductAttributesAttribute(){

//         return  TermTaxonomy::whereIn('term_taxonomy_id',
//                   TermRelation::where('object_id',$this->ID)
//                               ->pluck('term_taxonomy_id'))
//               ->where('taxonomy','like','pa_%')
//               ->get()->groupBy('taxonomy');

// }


//     ///

//     public function getProductVariationsAttribute(){
//         return Post::where('post_parent',$this->ID)->where('post_type','product_variation')->get();
//     }

//     public function getGalleryAttribute(){
//        return  Post::where('post_parent',$this->ID)
//                                     ->where('post_type','attachment')
//                                     ->get();

//     }


}
