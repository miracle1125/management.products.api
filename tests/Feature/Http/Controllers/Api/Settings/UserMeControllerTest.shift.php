<?php

namespace Tests\Feature\Http\Controllers\Api\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Settings\UserMeController
 */
class UserMeControllerTest extends TestCase
{


    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');



$response = $this->get(route('me.index'));

$response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');



$response = $this->post(route('me.store'), [
            // TODO: send request data
        ]);

$response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
