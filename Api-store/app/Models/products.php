<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    protected $table = "products";
    protected $fillable = ['user_id', 'name', 'description', 'price', 'sku', 'stock', 'category_id', 'brand_id', 'is_active'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name');
    }
    public function Category()
    {
        return  $this->belongsTo(Category::class)->select('id', 'name');
    }
    public function brand()
    {
        return  $this->belongsTo(Brand::class)->select('id', 'name');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')
            ->select('product_id', 'image'); 
    }
}
