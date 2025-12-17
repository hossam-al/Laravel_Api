<?php

namespace App\Observers;

use App\Models\products;
use App\Models\AdminProduct;
use Illuminate\Support\Str;

class productObserver
{
    /**
     * Handle the products "created" event.
     */
    public function created(products $products): void
    {
        // بعد ما يتعمل create بياخد ID ساعتها
        $products->sku = strtoupper(Str::random(10));
        $products->saveQuietly(); // save بدون firing observer تاني

    }

    /**
     * Handle the products "updating" event.
     */
    public function updating(products $products): void
    {
        if ($products->isDirty('name')) {
            $products->sku = strtoupper(Str::random(10));
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
