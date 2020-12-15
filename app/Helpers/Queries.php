<?php

namespace App\Helpers;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductQueriesService
 * @package App\Services
 */
class Queries
{
    /**
     * @param $location_id
     * @return Product|\Illuminate\Database\Eloquent\Builder|Builder
     */
    public static function getProductsWithIncorrectQuantityReservedQuery($location_id)
    {
        // select all products
        return Product::query()
            ->select([
                'products.id as product_id',
                DB::raw($location_id . ' as location_id'),
                DB::raw('ceil(' . DB::getTablePrefix() . 'inventory.quantity_reserved) as quantity_reserved_actual'),
                'product_reserved_totals.total_quantity_to_ship as quantity_reserved_expected',
            ])
            // left join specified $locationId inventory to get actual quantity reserved
            ->leftJoin('inventory', function ($join) use ($location_id) {
                $join->on('inventory.product_id', '=', 'products.id');
                $join->on('inventory.location_id', '=', DB::raw($location_id));
            })
            // left join expected quantities reserved sub query
            ->leftJoinSub(
                Queries::getOrderProductQuantityToShipTotalsByProductIdQuery(),
                'product_reserved_totals',
                'product_reserved_totals.product_id',
                '=',
                'products.id'
            )
            // select only where expected not matching actual
            ->where(
                DB::raw('IFNULL(' . DB::getTablePrefix() . 'inventory.quantity_reserved, 0)'),
                '!=',
                DB::raw('IFNULL(`' . DB::getTablePrefix() . 'product_reserved_totals`.`total_quantity_to_ship`, 0)')
            );
    }

    /**
     * @return Product|\Illuminate\Database\Eloquent\Builder|Builder
     */
    public static function getProductsWithQuantityReservedErrorsQuery()
    {
        // select all products
        // left join inventory to get actual quantity reserved
        // left join expected quantities reserved sub query
        // select only where expected not matching actual
        return Product::query()
            ->select([
                'products.id',
                'products.id as product_id',
                'products.quantity_reserved as actual_product_quantity_reserved',
                'inventory_totals.total_quantity_reserved as correct_inventory_quantity_reserved',
            ])
            ->leftJoinSub(
                Queries::getInventoryTotalsByProductIdQuery(),
                'inventory_totals',
                'inventory_totals.product_id',
                '=',
                'products.id'
            )
            ->where(
                DB::raw('IFNULL(' . DB::getTablePrefix() . 'products.quantity_reserved, 0)'),
                '!=',
                DB::raw('IFNULL(`' . DB::getTablePrefix() . 'inventory_totals`.`total_quantity_reserved`, 0)')
            );
    }

    /**
     * This query will
     * return sum of order_products.quantity_to_ship
     * where orders.status_code is in open status list
     * grouped by product_id
     *
     * @return OrderProduct|\Illuminate\Database\Eloquent\Builder|Builder
     */
    public static function getOrderProductQuantityToShipTotalsByProductIdQuery()
    {
        return OrderProduct::query()
            ->select([
                'product_id',
                DB::raw('sum(quantity_to_ship) as total_quantity_to_ship'),
            ])
            ->whereStatusCodeIn(OrderStatus::getOpenStatuses())
            ->groupBy(['product_id']);
    }


    /**
     * @return OrderProduct|\Illuminate\Database\Eloquent\Builder|Builder
     */
    public static function getInventoryTotalsByProductIdQuery()
    {
        return Inventory::query()
            ->select([
                'product_id',
                DB::raw('sum(quantity_reserved) as total_quantity_reserved')
            ])
            ->groupBy(['product_id']);
    }
}
