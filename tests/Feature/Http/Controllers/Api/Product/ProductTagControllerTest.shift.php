<?php

namespace Tests\Feature\Http\Controllers\Api\Product;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Product\ProductTagController
 */
class ProductTagControllerTest extends TestCase
{


    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');



$response = $this->get(route('tags.index'));

$response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
