<?php

namespace Tests\External\Rmsapi;

use App\Jobs\Api2cart\ImportOrdersJob;
use App\Jobs\Rmsapi\ImportProductsJob;
use App\Jobs\Rmsapi\ProcessImportedProductsJob;
use App\Models\RmsapiConnection;
use Illuminate\Support\Facades\Bus;
use test\Mockery\HasUnknownClassAsTypeHintOnMethod;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportProductsJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_job_runs()
    {
        RmsapiConnection::query()->delete();

        $connection = factory(RmsapiConnection::class)->create();

        $job = new ImportProductsJob($connection);

        $job->handle();

        // we just check for no exceptions
        $this->assertTrue(true);
    }

}
