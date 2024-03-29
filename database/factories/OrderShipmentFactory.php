<?php

/** @var Factory $factory */

use App\Models\Order;
use App\Models\OrderShipment;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(OrderShipment::class, function (Faker $faker) {
    $shipping_number = $faker->toUpper( $faker->randomLetter . $faker->randomLetter . '100' . $faker->randomNumber(8));

    $order = Order::query()->inRandomOrder()->first() ?? factory(Order::class)->create();
    $user = User::query()->inRandomOrder()->first() ?? factory(User::class)->create();

    return [
        'order_id' => $order->getKey(),
        'shipping_number' => $shipping_number,
        'user_id' => $user->getKey()
    ];
});
