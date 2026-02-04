<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\Category;
use App\Models\Address;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;
    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = app(CartService::class);
        $this->orderService = app(OrderService::class);
    }

    public function test_user_can_add_item_to_cart_and_checkout()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $outlet = Outlet::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'is_default' => true,
        ]);

        $this->actingAs($user);

        // 2. Add to Cart
        $this->cartService->addItem($product->id, null, [], 2);
        
        $cart = $this->cartService->getActiveCart();
        $this->assertEquals(1, $cart->items()->count());
        $this->assertEquals(2, $cart->items()->first()->quantity);

        // 3. Checkout
        $order = $this->orderService->checkout($user, [
            'cart_id' => $cart->id,
            'outlet_id' => $outlet->id,
            'address_id' => $address->id,
            'fulfillment' => 'delivery',
            'payment_method' => 'COD',
        ]);

        // 4. Assertions
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'PENDING',
            'user_id' => $user->id,
        ]);

        $this->assertEquals($product->base_price * 2, $order->subtotal);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'PENDING',
        ]);

        // Cart should be cleared
        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }
}
