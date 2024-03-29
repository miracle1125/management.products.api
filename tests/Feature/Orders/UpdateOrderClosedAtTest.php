<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\RmsapiProductImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateOrderClosedAtTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfDoesNotFillClosedAtWhenCompletedStatus()
    {
        OrderStatus::query()->forceDelete();
        RmsapiProductImport::query()->forceDelete();
        Order::query()->forceDelete();

        factory(OrderStatus::class)->create([
            'code' => 'pending',
            'name' => 'pending',
            'order_active' => 1,
        ]);

        $order = factory(Order::class)->create([
            'status_code' => 'open',
            'order_closed_at' => null,
        ]);

        $order->update(['status_code' => 'pending']);

        $this->assertNull($order->order_closed_at);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfFillsClosedAtWhenCompletedStatus()
    {
        OrderStatus::query()->forceDelete();
        RmsapiProductImport::query()->forceDelete();
        Order::query()->forceDelete();

        factory(OrderStatus::class)->create([
            'code' => 'closed',
            'name' => 'closed',
            'order_active' => 0,
        ]);

        $order = factory(Order::class)->create([
            'status_code' => 'open',
            'order_closed_at' => null,
        ]);

        $order->update(['status_code' => 'closed']);

        $this->assertNotNull($order->order_closed_at);
    }
}
