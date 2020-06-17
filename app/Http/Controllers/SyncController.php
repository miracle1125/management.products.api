<?php

namespace App\Http\Controllers;

use App\Jobs\Api2cart\ImportOrdersJob;
use App\Jobs\Rmsapi\ImportProductsJob;
use App\Jobs\Rmsapi\SyncRmsapiConnectionCollectionJob;
use Illuminate\Http\Request;

/**
 * Class SyncController
 * @package App\Http\Controllers
 */
class SyncController extends Controller
{
    /**
     *
     */
    public function index() {
        // import API2CART orders
        ImportOrdersJob::dispatch();

        // import RMSAPI products
        SyncRmsapiConnectionCollectionJob::dispatch();

        return $this->respond_OK_200('Sync jobs dispatched');
    }
}
