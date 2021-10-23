<?php

namespace App;

use App\Models\PostMeta;
use App\Models\UserMeta;


class Facades  
{
    

    static function saveMeta($arr,$type,$id)
    {
        if($type === 'user'){
          //
          foreach ($arr as  $meta) {
            $saveMeta=new UserMeta;
            $saveMeta->user_id=$id;
            $saveMeta->meta_key=$meta['key'];
            $saveMeta->meta_value=$meta['value'];
            $saveMeta->save();
          };
        }
        elseif($type === 'order'){
          //
          foreach ($arr as  $meta) {
            $saveMeta=new PostMeta;
            $saveMeta->post_id=$id;
            $saveMeta->meta_key=$meta['key'];
            $saveMeta->meta_value=$meta['value'];
            $saveMeta->save();
          };

        }


    }





}



?>