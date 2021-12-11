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

          $checkMeta = UserMeta::where('meta_key',$meta['key'])->where('user_id',$id)->first();

          if($checkMeta){

            UserMeta::where('meta_key',$meta['key'])->where('user_id',$id)->update(
              [
                'meta_value'=>$meta['value'],
              ]
            );
          }
          else{

            $saveMeta=new UserMeta;
            $saveMeta->user_id=$id;
            $saveMeta->meta_key=$meta['key'];
            $saveMeta->meta_value=$meta['value'];
            $saveMeta->save();

          }

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


    static function getMeta($type,$key,$userId)
    {
      if($type === 'user'){

        $meta =  UserMeta::where('meta_key',$key)->where('user_id',$userId)->first();
        if(empty($meta)){
          return null;
        }
        else{
          return $meta['meta_value'];
        }

      }
      


    }

}



?>