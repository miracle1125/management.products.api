<?php

namespace App\Services;

use App\Jobs\Modules\Api2cart\SyncProductJob;
use App\Models\Product;

class Api2cartService
{
    /**
     * @param Product $product
     */
    public static function dispatchSyncProductJob(Product $product): void
    {
        SyncProductJob::dispatch($product);

        $product->detachTag('Not Synced');

        logger('SyncProductJob dispatched and tag removed', ['sku' => $product->sku]);
    }
}
