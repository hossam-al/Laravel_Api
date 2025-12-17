<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class products extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'image_url',
        'sku',
        'description',
        'price',
        'stock',
        'is_active',
        'is_featured',
        'category_id'
    ];

    // مثال علاقة مع التصنيف
    public function category()
    {
        return $this->belongsTo(Category::class)->select('id', 'name', 'is_active');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'price', 'subtotal')
            ->withTimestamps();
    }
}
