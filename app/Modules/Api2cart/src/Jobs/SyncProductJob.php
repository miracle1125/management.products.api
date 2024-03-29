<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Api2cart\src\Products;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncProductJob
 * @package App\Modules\Api2cart\src\Jobs
 */
class SyncProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Product
     */
    private Product $product;

    /**
     * @var Api2cartConnection[]
     */
    private $connections;

    /**
     * @var Api2cartProductLink
     */
    private Api2cartProductLink $api2cartProduct;

    /**
     * Create a new job instance.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->connections = Api2cartConnection::all();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $product = $this->product;

        $this->connections->each(function (Api2cartConnection $connection) use ($product) {
            try {
                $this->api2cartProduct = Api2cartProductLink::firstOrCreate([
                    'product_id' => $product->getKey(),
                    'api2cart_connection_id' => $connection->getKey(),
                ], []);

                $product_data = $this->getProductData($product, $connection);

                $requestResponse = $this->api2cartProduct->updateOrCreate($product_data);

                $product->detachTag('Not Synced');

                if ($requestResponse->isSuccess()) {
                    $product->detachTag('SYNC ERROR');
                    $product->log('eCommerce: Product synced');
                    VerifyProductSyncJob::dispatchNow($connection, $product_data);
                } else {
                    $product->attachTag('SYNC ERROR');
                    $product->log('eCommerce: Sync failed (' .
                        $requestResponse->getReturnCode() . ': ' .
                        $requestResponse->getReturnMessage() . ')');

                    Log::warning('Api2cart: Product NOT SYNCED', [
                        'response' => [
                            'code' => $requestResponse->getReturnCode(),
                            'message' => $requestResponse->getReturnMessage(),
                        ],
                        'product_data' => $product_data ?? null,
                    ]);
                }
            } catch (Exception $exception) {
                if (isset($product_data)) {
                    Cache::forget(Products::getSkuCacheKey($connection->bridge_api_key, $product_data['sku']));
                }

                $product->attachTag('SYNC ERROR');
                $product->detachTag('Not Synced');
                $product->log('eCommerce: Sync failed (code ' . $exception->getCode() . '), see logs');

                Log::warning('Api2cart: Product NOT SYNCED', [
                    'response' => [
                        'code' => $exception->getCode(),
                        'message' => $exception->getMessage(),
                    ],
                    'product_data' => $product_data ?? null,
                ]);
            }
        });
    }

    /**
     * @param Product $product
     * @return array
     */
    private function getBasicData(Product $product): array
    {
        return [
            'product_id' => $product->getKey(),
            'sku' => $product->sku,
            'name' => $product->name,
            'description' => $product->name,
        ];
    }

    /**
     * @param Product $product
     * @param int $location_id
     * @return array
     */
    private function getPricingData(Product $product, int $location_id): array
    {
        $attributes = [
            'product_id' => $product->getKey(),
            'location_id' => $location_id
        ];

        $productPrice = ProductPrice::query()->firstOrCreate($attributes);

        if ($productPrice) {
            return [
                'price' => $productPrice->price,
                'special_price' => $productPrice->sale_price,
                'sprice_create' => $this->formatDateForApi2cart($productPrice->sale_price_start_date),
                'sprice_expire' => $this->formatDateForApi2cart($productPrice->sale_price_end_date),
            ];
        }

        Log::warning('Pricing data not found', $attributes);
        return [];
    }

    /**
     * @param Product $product
     * @param int|null $location_id
     * @return array
     */
    private function getInventoryData(Product $product, int $location_id = null): array
    {
        if (is_null($location_id)) {
            // we will refresh to get latest data
            $product = $product->refresh();

            return [
                'quantity' => floor($product->quantity_available) ?? 0,
                'in_stock' => $product->quantity_available > 0 ? "True" : "False",
            ];
        }

        $attributes = [
            'product_id' => $product->getKey(),
            'location_id' => $location_id
        ];

        $productInventory = Inventory::query()->where($attributes)->first();

        if ($productInventory) {
            return [
                'quantity' => $productInventory->quantity_available ?? 0,
                'in_stock' => $productInventory->quantity_available > 0 ? "True" : "False",
            ];
        }

        Log::warning('Inventory data not found', $attributes);
        return [];
    }

    /**
     * @param $date
     * @return string
     */
    public function formatDateForApi2cart($date): string
    {
        $carbon_date = new Carbon($date ?? '2000-01-01 00:00:00');

        if ($carbon_date->year < 2000) {
            return '2000-01-01 00:00:00';
        }

        return $carbon_date->toDateTimeString();
    }

    /**
     * @param $connection
     * @return array
     */
    private function getMagentoStoreId($connection): array
    {
        return [
            'store_id' => $connection->magento_store_id ?? 0
        ];
    }

    /**
     * @param Product $product
     * @param $connection
     * @return array
     */
    private function getProductData(Product $product, $connection): array
    {
        $product_data = collect();

        $product_data = $product_data->merge($this->getBasicData($product));
        $product_data = $product_data->merge($this->getMagentoStoreId($connection));
        $product_data = $product_data->merge($this->getInventoryData($product, $connection->inventory_location_id));

        if (isset($connection->pricing_location_id)) {
            $product_data = $product_data->merge($this->getMagentoStoreId($connection));
        }
        if (isset($connection->pricing_location_id)) {
            $product_data = $product_data->merge($this->getPricingData($product, $connection->pricing_location_id));
        }

        return $product_data->toArray();
    }
}
