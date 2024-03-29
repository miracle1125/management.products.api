<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\ProductPrice;
use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ImportProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var RmsapiProductImport
     */
    private $importedProduct;

    /**
     * Create a new job instance.
     *
     * @param RmsapiProductImport $importedProduct
     */
    public function __construct(RmsapiProductImport $importedProduct)
    {
        $this->importedProduct = $importedProduct;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->import($this->importedProduct);
    }

    /**
     * @param RmsapiProductImport $importedProduct
     */
    private function import(RmsapiProductImport $importedProduct): void
    {
        $attributes = [
            "sku" => $importedProduct->raw_import['item_code'],
            "name" => $importedProduct->raw_import['description']
        ];

        $product = Product::query()
            ->updateOrCreate([
                "sku" => $attributes['sku']
            ], $attributes);

        $this->attachTags($importedProduct, $product);

        $this->importAliases($importedProduct, $product);

        $this->importInventory($importedProduct, $product);

        $this->importPricing($importedProduct, $product);

        $importedProduct->update([
            'when_processed' => now(),
            'product_id' => $product->id,
            'sku' => $attributes['sku']
        ]);

        $product->touch(); // we touching coz there was changes
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product $product
     */
    private function importAliases(RmsapiProductImport $importedProduct, Product $product): void
    {
        if (! Arr::has($importedProduct->raw_import, 'aliases')) {
            return;
        }

        foreach ($importedProduct->raw_import['aliases'] as $alias) {
            ProductAlias::query()->updateOrCreate(
                ['alias'       => $alias['alias']],
                ['product_id'  => $product->id]
            );
        }
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product $product
     */
    private function importInventory(RmsapiProductImport $importedProduct, Product $product): void
    {
        $connection = RmsapiConnection::query()->find($importedProduct->connection_id);

        $inventory = Inventory::query()->updateOrCreate([
            'product_id' => $product->id,
            'location_id' => $connection->location_id
        ], [
            'quantity' => $importedProduct->raw_import['quantity_on_hand'],
            'quantity_reserved' => $importedProduct->raw_import['quantity_committed'],
            'shelve_location' => Arr::get($importedProduct->raw_import, 'rmsmobile_shelve_location'),
        ]);
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product $product
     */
    private function importPricing(RmsapiProductImport $importedProduct, Product $product): void
    {
        $connection = RmsapiConnection::query()->find($importedProduct->connection_id);

        ProductPrice::query()->updateOrCreate([
            'product_id' => $product->id,
            'location_id' => $connection->location_id
        ], [
            'price' => $importedProduct->raw_import['price'],
            'sale_price' => $importedProduct->raw_import['sale_price'],
            'sale_price_start_date' => $importedProduct->raw_import['sale_start_date'] ?? '1899-01-01 00:00:00',
            'sale_price_end_date' => $importedProduct->raw_import['sale_end_date'] ?? '1899-01-01 00:00:00',
        ]);
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product $product
     */
    private function attachTags(RmsapiProductImport $importedProduct, Product $product): void
    {
        if ($importedProduct->raw_import['is_web_item']) {
            $product->attachTag('Available Online');
        }
    }
}
