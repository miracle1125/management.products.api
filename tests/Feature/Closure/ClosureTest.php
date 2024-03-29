<?php

namespace Tests\Feature\Closure;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClosureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_closure_call_returns_ok()
    {
        $this->markTestIncomplete('This test was generated by "php artisan app:generate-api-routes-tests" call');
    }
}
