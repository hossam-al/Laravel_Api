<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\products;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_browse_products_categories_and_manage_cart_orders_reviews_and_wishlist(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::create([
            'name' => 'Cat A',
            'description' => 'D',
            'slug' => 'cat-a',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Brand A',
            'description' => 'BD',
            'logo' => null,
            'is_active' => true,
        ]);

        $product = products::create([
            'user_id' => $user->id,
            'name' => 'Prod 1',
            'description' => 'Desc',
            'price' => 100,
            'sku' => 'SKU1',
            'stock' => 10,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        $this->getJson('/api/products')->assertStatus(200);
        $this->getJson('/api/Category')->assertStatus(200);
        $this->getJson('/api/brands')->assertStatus(200);

        $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertStatus(201);

        $cartIndex = $this->getJson('/api/cart')->assertStatus(200)->json();
        $cartId = $cartIndex['id'];
        $this->getJson("/api/cart/{$cartId}/items")->assertStatus(200);

        $order = $this->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ])->assertStatus(201)->json();

        $this->postJson("/api/orders/{$order['id']}", ['status' => 'paid'])->assertStatus(200);
        $this->getJson('/api/orders')->assertStatus(200);

        $review = $this->postJson('/api/reviews', [
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Great',
        ])->assertStatus(201)->json();
        $this->postJson("/api/reviews/{$review['id']}", ['rating' => 4])->assertStatus(200);

        $wl = $this->postJson('/api/wishlist', [
            'product_id' => $product->id,
        ])->assertStatus(201)->json();
        $this->getJson('/api/wishlist')->assertStatus(200);
        $this->deleteJson("/api/wishlist/{$wl['id']}")->assertStatus(200);
    }
}


