<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\PostMeta;
use App\Models\TermRelation;
use Illuminate\Database\Eloquent\Builder;
use PhpParser\Node\Expr\FuncCall;



use Corcel\Model\Attachment;
use Corcel\WooCommerce\Model\Product as Corcel;
class PostV extends Corcel
{

    protected $connection = 'wordpress';
    protected $postType='product_variation';


    protected $appends = ['cbm','cartqty','type'];


    protected function getCbmAttribute(): ?string
    {
        return $this->getMeta('al_cbm');
    }

    protected function getCartqtyAttribute(): ?string
    {
        return $this->getMeta('al_carton_qty');
    }

}












// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class PostV extends Model
// {
//     use HasFactory;


//     protected $table="wpdm_posts";
//     protected $primaryKey="ID";
//     protected $appends = ['meta'];
//     // protected $appends = ['meta','categories','product_type','product_image'];
//     // protected $appends = ['meta','categories','product_type','product_image','tags'];
//     //protected $appends = ['meta','categories','product_type','product_attributes','product_image','tags'];
//     public $timestamps = false;



//     protected $hidden = [
//         "post_author" ,
//         "post_date" ,
//         "post_date_gmt" , //it's like created_at
//         "post_content" ,
//         "post_title" ,
//         "post_excerpt" , //post summary
//         "post_status" ,
//         "comment_status" ,
//         "ping_status" ,
//         "post_password" ,
//         "post_name" ,
//         "to_ping" ,
//         "pinged" ,
//         "post_modified" ,
//         "post_modified_gmt" ,
//         "post_content_filtered" ,
//         "post_parent" ,
//         "guid" , //ex. "https://alyamanlive.com/?post_type=product&p=6763"
//         "menu_order" ,
//         "post_type" ,
//         "post_mime_type" ,
//         "comment_count" ,
//     ];

//     public function getMetaAttribute(){
//         return PostMeta::where('post_id',$this->ID)->pluck('meta_value','meta_key')->toArray();
//     }
// }
