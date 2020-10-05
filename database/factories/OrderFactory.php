<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatus;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {

    $status_code = $faker->randomElement(['complete','processing','cancelled','picking','on_hold']);

    $order_placed_at = $faker->dateTimeBetween('-5 months');

    if (OrderStatus::isActive($status_code)) {
        $order_closed_at = $faker->dateTimeBetween($order_placed_at, now());
    }

    return [
        'order_number' => (string) (10000000 + $faker->unique()->randomNumber(5)),
        'shipping_address_id' => factory(OrderAddress::class)->create()->id,
        'order_placed_at' => $order_placed_at,
        'order_closed_at' => $order_closed_at ?? null,
        'status_code' => $status_code,
    ];
});
