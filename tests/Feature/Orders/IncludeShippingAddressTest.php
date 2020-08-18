<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\OrderAddress;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeShippingAddressTest extends TestCase
{
    /**
     * @return void
     */
    public function test_if_can_include_shipping_address()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $address = factory(OrderAddress::class)->create();

        $order = factory(Order::class)->create([
            'shipping_address_id' => $address->id
        ]);

//        dd($order);

        $response = $this->get('/api/orders?'.
            "filter[order_number]=".$order->order_number.
            "&include=shipping_address"
            ,[]);

        $this->assertEquals(1, $response->json('total'));

//        dd($response->json());
        $response->assertJsonStructure([
            "data" => [
                '*' => [
                    'shipping_address'
                ]
            ]
        ]);

    }

}