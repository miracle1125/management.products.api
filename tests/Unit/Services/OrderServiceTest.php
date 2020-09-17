<?php

namespace Tests\Unit\Services;

use App\Models\Inventory;
use App\Models\Order;
use App\Services\OrderService;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testFailedCanFulfill()
    {
        $locationId = rand(1, 100);

        $order = factory(Order::class)
            ->with('orderProducts', 1)
            ->create();

        $orderProduct = $order->orderProducts()->first();

        Inventory::updateOrCreate([
            'product_id' => $orderProduct->product_id,
            'location_id' => $locationId,
        ], [
            'quantity' => $orderProduct->quantity_ordered - 1
        ]);

        $this->assertFalse(
            OrderService::canFulfill($order, $locationId)
        );
    }

    public function testSuccessfulCanFulfill()
    {
        $locationId = rand(1, 100);

        $order = factory(Order::class)
            ->with('orderProducts', 1)
            ->create();

        $orderProduct = $order->orderProducts()->first();

        Inventory::updateOrCreate([
            'product_id' => $orderProduct->product_id,
            'location_id' => $locationId,
        ], [
            'quantity' => $orderProduct->quantity_ordered
        ]);

        $this->assertTrue(
            OrderService::canFulfill($order, $locationId)
        );
    }
}