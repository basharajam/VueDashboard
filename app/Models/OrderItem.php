<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Corcel\WooCommerce\Model\Item as Corcel;
class OrderItem extends Corcel
{
    use HasFactory;
}
