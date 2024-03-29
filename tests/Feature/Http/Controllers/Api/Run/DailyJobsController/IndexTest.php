<?php

namespace Tests\Feature\Http\Controllers\Api\Run\DailyJobsController;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('run.daily.jobs.index'));

        $response->assertOk();
    }
}
