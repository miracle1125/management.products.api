<?php

namespace Tests\External\PrintNode;

use App\Modules\PrintNode\src\Models\Client;
use App\Modules\PrintNode\src\PrintNode;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintNodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_if_api_key_set()
    {
        $actual = config('printnode.test_api_key');
        $this->assertNotEmpty($actual, 'PRINTNODE_TEST_API_KEY env key is not set');
    }

    public function test_get_clients()
    {
        $repository = config('printnode.test_api_key');

        Client::query()->updateOrCreate([], ['api_key' => $repository]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->get('api/settings/modules/printnode/clients');

        $response->assertSuccessful();
    }

    public function test_get_printers()
    {
        $repository = config('printnode.test_api_key');

        Client::query()->updateOrCreate([], ['api_key' => $repository]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->get('api/settings/modules/printnode/printers');

        $response->assertSuccessful();
    }

    public function test_store_printjob()
    {
        $repository = config('printnode.test_api_key');

        Client::query()->updateOrCreate([], ['api_key' => $repository]);

        $printers = collect(PrintNode::getPrinters());

        $this->assertGreaterThan(0, $printers->count());

        $user = factory(User::class)->create();

        $this->actingAs($user, 'api')->postJson('api/settings/modules/printnode/printjobs', [
            'printer_id' => $printers->first()['id'],
            'pdf_url' => 'https://api.printnode.com/static/test/pdf/label_6in_x_4in.pdf'
        ]);
    }

}
