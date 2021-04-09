<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\OrderController
 */
class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function destroy_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

$order = factory(\App\Models\Order::class)->create();

$response = $this->delete(route('orders.destroy', [$order]));

$response->assertOk();
$this->assertDeleted($orders);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');



$response = $this->get(route('orders.index'));

$response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

$order = factory(\App\Models\Order::class)->create();

$response = $this->get(route('orders.show', [$order]));

$response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');



$response = $this->post(route('orders.store'), [
            // TODO: send request data
        ]);

$response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

$order = factory(\App\Models\Order::class)->create();

$response = $this->put(route('orders.update', [$order]), [
            // TODO: send request data
        ]);

$response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
