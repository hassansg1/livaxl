<?php

namespace FleetCart;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "sku",
        "description",
        "categories",
        "b2b_price",
        "webshop_price",
        "stock",
        "images",
        "slug",
        "product_url",
        "html_description"
    ];

}
