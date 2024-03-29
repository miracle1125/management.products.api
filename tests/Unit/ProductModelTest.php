<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\ProductService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    public function testIfQuantityAvailableBelow0NotAllowed()
    {
        Event::fake();

        $product_before = Product::firstOrCreate(['sku' => '0123456']);

        // reserve 1 more than actually in stock
        // so quantity_available < 0
        ProductService::reserve(
            '0123456',
            $product_before->quantity + 1,
            'ProductModeTest reservation'
        );

        $product_after = $product_before->fresh();

        $this->assertEquals(0, $product_after->quantity_available);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfReservesCorrectly()
    {
        $product_before = Product::firstOrCreate(['sku' => '0123456']);

        ProductService::reserve(
            '0123456',
            5,
            'ProductModeTest reservation'
        );

        $product_after = $product_before->fresh();

        $this->assertEquals($product_after->quantity_reserved, $product_before->quantity_reserved + 5);
    }
}
