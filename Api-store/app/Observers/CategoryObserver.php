<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function creating(Category $category): void
    {
        if (empty($category->slug)) {
            $category->slug = Str::slug($category->name);
        }
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updating(Category $category): void
    {
        if ($category->isDirty('name')) {
            $category->slug = Str::slug($category->name);
        }
    }


    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
