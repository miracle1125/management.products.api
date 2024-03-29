<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Product;
use App\Models\RmsapiProductImport;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExtractSkuAndProductIdJob implements ShouldQueue
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
        $productImports = RmsapiProductImport::query()
            ->whereNotNull('when_processed')
            ->whereNull('sku')
            ->limit(200)
            ->get();

        foreach ($productImports as $importedProduct) {
            $product = Product::query()
                ->where('sku', '=', $importedProduct['raw_import']['item_code'])
                ->first();

            $importedProduct['sku'] = $importedProduct['raw_import']['item_code'];
            $importedProduct['product_id'] = $product ? $product->getKey() : null;

            $importedProduct->save();
        }

        // using recurrence to dispatch next batch
        // if we processed some records in this batch, there might be more
        if (count($productImports) > 0) {
            self::dispatch();
        };
    }
}
