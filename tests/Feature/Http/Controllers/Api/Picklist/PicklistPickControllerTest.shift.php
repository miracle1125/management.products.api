<?php

namespace Tests\Feature\Http\Controllers\Api\Picklist;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Picklist\PicklistPickController
 */
class PicklistPickControllerTest extends TestCase
{


    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
$this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');



$response = $this->post(route('picks.store'), [
            // TODO: send request data
        ]);

$response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
