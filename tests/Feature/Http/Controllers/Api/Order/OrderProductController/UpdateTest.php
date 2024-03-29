<?php

namespace Tests\Feature\Http\Controllers\Api\Order\OrderProductController;

use App\Models\OrderProduct;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_update_call_returns_ok()
    {
        $user = factory(User::class)->create();
        $orderProduct = factory(OrderProduct::class)->create();

        $response = $this->actingAs($user, 'api')->putJson(route('order.products.update', [$orderProduct]), [
            'quantity_shipped' => $orderProduct->quantity_to_ship
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'order_id',
                'product_id',
                'sku_ordered',
                'name_ordered',
                'quantity_ordered',
                'quantity_picked',
                'quantity_shipped',
            ]
        ]);
    }
}
