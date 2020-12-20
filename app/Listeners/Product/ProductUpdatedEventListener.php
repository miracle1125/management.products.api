<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductUpdatedEvent;
use App\Jobs\Modules\Sns\PublishSnsNotificationJob;
use App\Services\Api2cartService;
use Exception;

class ProductUpdatedEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ProductUpdatedEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(ProductUpdatedEvent $event)
    {
        $this->publishSnsNotification($event);
        $this->attachAutoTags($event);
        $this->ifOosSyncApi2cart($event);
    }

    /**
     * Handle the event.
     *
     * @param ProductUpdatedEvent $event
     * @return void
     * @throws Exception
     */
    public function ifOosSyncApi2cart(ProductUpdatedEvent $event)
    {
        $product = $event->getProduct();

        if ($product->quantity_available > 0) {
            return;
        }

        Api2cartService::syncProduct($product);
    }

    /**
     * Handle the event.
     *
     * @param ProductUpdatedEvent $event
     * @return void
     */
    public function publishSnsNotification(ProductUpdatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'products_events',
            $event->getProduct()->toJson()
        );
    }

    /**
     * Handle the event.
     *
     * @param  ProductUpdatedEvent  $event
     * @return void
     */
    public function attachAutoTags(ProductUpdatedEvent $event)
    {
        $product = $event->getProduct();

        if ($product->withAllTags(['Available Online'])->exists()) {
            $product->attachTag('Not Synced');
        }

        if ($product->quantity_available <= 0) {
            $product->attachTag('Out Of Stock');
        }

        if ($product->quantity_available > 0) {
            $product->detachTag('Out Of Stock');
        }
    }
}