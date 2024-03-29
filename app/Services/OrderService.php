<?php


namespace App\Services;

use App\Events\Order\OrderCreatedEvent;
use App\Modules\Api2cart\src\Jobs\ImportShippingAddressJob;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use Exception;
use Illuminate\Support\Str;
use phpseclib\Math\BigInteger;
use Spatie\QueryBuilder\QueryBuilder;

class OrderService
{

    /**
     * @param Order $order
     * @param string $from_status_code
     * @param bool $condition
     * @param string $to_status_code
     * @return Order
     */
    public static function changeStatusIf(Order $order, string $from_status_code, bool $condition, string $to_status_code): Order
    {
        if ($order->isStatusCode($from_status_code) && $condition) {
            $order->status_code = $to_status_code;
        }

        return $order;
    }

    /**
     * @param Order $order
     * @param $sourceLocationId
     * @return bool
     */
    public static function canNotFulfill(Order $order, $sourceLocationId = null)
    {
        return !self::canFulfill($order, $sourceLocationId);
    }

    /**
     * @param Order $order
     * @param $sourceLocationId
     * @return bool
     */
    public static function canFulfill(Order $order, $sourceLocationId = null): bool
    {
        $orderProducts = $order->orderProducts()->get();

        foreach ($orderProducts as $orderProduct) {
            $query = Inventory::where('product_id', $orderProduct->product_id);

            if ($sourceLocationId) {
                $query->where('location_id', $sourceLocationId);
            }

            $quantity_available = $query->sum(\DB::raw('(quantity)'));

            if (!$quantity_available) {
                return false;
            }

            if ((double) $quantity_available < (double) $orderProduct->quantity_to_ship) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $order_number
     * @param $template_name
     * @return string
     */
    public static function getOrderPdf(string $order_number, $template_name)
    {
        $order = Order::query()
            ->where(['order_number' => $order_number])
            ->with('shippingAddress')
            ->firstOrFail();

        if (!$order->shipping_address_id) {
            ImportShippingAddressJob::dispatchNow($order->id);
            $order = $order->refresh();
        }

        $view = 'pdf/orders/'. $template_name;
        $data = $order->toArray();

        return PdfService::fromView($view, $data);
    }

    /**
     * @param array $orderAttributes
     * @return Order
     * @throws Exception
     */
    public static function updateOrCreate(array $orderAttributes)
    {
        $order = Order::updateOrCreate(
            ['order_number' => $orderAttributes['order_number']],
            $orderAttributes
        );

        self::updateOrCreateShippingAddress($order, $orderAttributes['shipping_address']);

        $order = self::syncOrderProducts($orderAttributes['order_products'], $order);

        OrderCreatedEvent::dispatch($order);

        return $order;
    }

    /**
     * @param array $shippingAddressAttributes
     * @param $order
     * @return Order
     */
    public static function updateOrCreateShippingAddress(Order $order, array $shippingAddressAttributes): Order
    {
        $shipping_address = OrderAddress::query()->findOrNew($order->shipping_address_id ?: 0);
        $shipping_address->fill($shippingAddressAttributes);
        $shipping_address->save();

        $order->shippingAddress()->associate($shipping_address);
        $order->save();

        return $order;
    }

    /**
     * @param array $orderProductAttributes
     * @return BigInteger|null
     */
    private static function getProductId(array $orderProductAttributes)
    {
        $product = ProductService::find($orderProductAttributes['sku_ordered']);
        if ($product) {
            return  $product->id;
        }

        $extractedSku = Str::substr($orderProductAttributes['sku_ordered'], 0, 6);
        $product = ProductService::find($extractedSku);
        if ($product) {
            return  $product->id;
        }

        return null;
    }

    /**
     * @param $order_products
     * @param Order $order
     * @return Order
     * @throws Exception
     */
    private static function syncOrderProducts($order_products, Order $order): Order
    {
        $orderProductIdsToKeep = [];

        foreach ($order_products as $orderProductAttributes) {
            $orderProduct = OrderProduct::where(['order_id' => $order->getKey()])
                ->whereNotIn('id', $orderProductIdsToKeep)
                ->updateOrCreate(
                // attributes
                    collect($orderProductAttributes)
                        ->only([
                            'sku_ordered',
                            'name_ordered',
                            'quantity_ordered',
                            'price',
                        ])
                        ->toArray(),
                    // values
                    [
                        'order_id' => $order->getKey(),
                        'product_id' => self::getProductId($orderProductAttributes),
                    ]
                );

            $orderProductIdsToKeep[] = $orderProduct->getKey();
        }

        OrderProduct::where(['order_id' => $order->id])
            ->whereNotIn('id', $orderProductIdsToKeep)
            ->delete();

        return $order->refresh();
    }
}
