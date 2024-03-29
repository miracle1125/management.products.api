<?php

namespace App\Modules\MagentoApi\src\Listeners\ProductTagAttachedEvent;

use App\Events\Product\ProductTagAttachedEvent;
use App\Modules\MagentoApi\src\Jobs\SyncProductStockJob;

class SyncWhenOutOfStockAttachedListener
{
    /**
     * Handle the event.
     *
     * @param ProductTagAttachedEvent $event
     * @return void
     */
    public function handle(ProductTagAttachedEvent $event)
    {
        if (config('modules.magentoApi.enabled') === false) {
            return;
        }

        if ($event->tag() !== 'Out Of Stock') {
            return;
        }

        if ($event->product()->doesNotHaveTags(['Available Online'])) {
            return;
        }

        $event->product()->log('Product out of stock, forcing MagentoApi sync');
        SyncProductStockJob::dispatch($event->product());
    }
}
