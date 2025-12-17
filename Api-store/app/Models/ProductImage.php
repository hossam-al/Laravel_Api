<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = "product_images";

    protected $fillable = ['product_id', 'image', 'is_primary'];

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id')->select('id', 'name', 'is_active');
    }
}
