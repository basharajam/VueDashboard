<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TermRelation;
use App\Models\TermTaxonomy;
use App\Models\ProductType;
use Corcel\WooCommerce\Model\Product as Corcel;

class PostAll extends Corcel
{

    protected $connection = 'wordpress';

    //protected $appends = ['gallery','on_sale','cbm','cartqty','variation','type','avg_rate','min_qty'];
    protected $appends = ['gallery','on_sale','cbm','cartqty','variation','type','avg_rate','min_qty'];
   
    protected function getCbmAttribute(): ?string
    {
        return $this->getMeta('al_cbm');
    }
    protected function getCartqtyAttribute(): ?string
    {
        return $this->getMeta('al_carton_qty');
    }

    protected function getAvgRateAttribute()
    {
        return $this->getMeta('_wc_average_rating');
    }

    protected function getMinQtyAttribute()
    {
        return $this->getMeta('_wc_min_qty_product');
    }


    

}
