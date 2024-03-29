<?php

namespace App\Modules\Api2cart\src\Observers;

use App\Modules\Api2cart\src\Jobs\ProcessApi2cartImportedOrderJob;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;

class Api2cartOrderImportsObserver
{
    /**
     * Handle the api2cart order imports "created" event.
     *
     * @param  Api2cartOrderImports  $api2cartOrderImports
     * @return void
     */
    public function created(Api2cartOrderImports $api2cartOrderImports)
    {
        ProcessApi2cartImportedOrderJob::dispatch($api2cartOrderImports);
    }
}
