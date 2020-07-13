<?php

namespace App\Jobs\Orders;

use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FixAllNullProductIdsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $entries = OrderProduct::query()->whereNull('product_id')->get();

        foreach ($entries as $orderProduct) {
            $product = Product::findBySKU($orderProduct['sku_ordered']);

            if (is_null($product)) {
                // early exit
                continue;
            }

            $orderProduct->product_id = $product->getKey();
            $orderProduct->save();
        };
    }
}