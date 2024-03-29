<?php

namespace Tests\Feature\Http\Controllers\Api\Admin\UserRoleController;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('admin.users.roles.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'guard_name'
                ]
            ]
        ]);
    }
}
