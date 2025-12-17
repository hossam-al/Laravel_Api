<?php

namespace App\Observers;

use App\Models\products;
use Illuminate\Support\Str;

class productObserver
{
    /**
     * Handle the products "created" event.
     */
    public function created(products $products): void
    {
        // بعد ما يتعمل create بياخد ID ساعتها
        $products->slug = Str::slug($products->name) . '-' . $products->id;
        $products->saveQuietly(); // save بدون firing observer تاني
    }

    /**
     * Handle the products "updating" event.
     */
    public function updating(products $product): void
    {
        if ($product->isDirty('name')) {
            $product->slug = Str::slug($product->name) . '-' . $product->id;
        }
    }

    public function deleted(products $products): void
    {
        //
    }

    /**
     * Handle the products "restored" event.
     */
    public function restored(products $products): void
    {
        //
    }

    /**
     * Handle the products "force deleted" event.
     */
    public function forceDeleted(products $products): void
    {
        //
    }
}
