<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Corcel\WooCommerce\Model\ProductType as Corcel;

class ProductType extends Corcel
{

    protected $taxonomy = 'product_type';
}
