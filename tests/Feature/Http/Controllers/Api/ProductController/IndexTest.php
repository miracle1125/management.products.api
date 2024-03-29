<?php

namespace Tests\Feature\Http\Controllers\Api\ProductController;

use App\Models\Product;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_returns_ok()
    {

        Product::query()->forceDelete();
        factory(Product::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('products.index', [
            'include' => [
                'inventory',
                'aliases',
                'tags'
            ],
            'filter[inventory_source_location_id]' => 1,
        ]));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'sku',
                    'name',
                    'price',
                    'sale_price',
                    'sale_price_start_date',
                    'sale_price_end_date',
                    'quantity',
                    'quantity_reserved',
                    'quantity_available',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'inventory_source_shelf_location',
                    'inventory_source_quantity',
                    'inventory_source_product_id',
                    'inventory_source_location_id',
                    'inventory' => [
                        '*' => []
                    ],
                    'aliases' => [
                        '*' => []
                    ],
                    'tags' => [
                        '*' => []
                    ],
                ]
            ]
        ]);
    }
}
