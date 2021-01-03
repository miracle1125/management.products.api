<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ProductPrice
 *
 * @property int $id
 * @property int $product_id
 * @property int $location_id
 * @property string $price
 * @property string $sale_price
 * @property string $sale_price_start_date
 * @property string $sale_price_end_date
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ProductPrice newModelQuery()
 * @method static Builder|ProductPrice newQuery()
 * @method static Builder|ProductPrice query()
 * @method static Builder|ProductPrice whereCreatedAt($value)
 * @method static Builder|ProductPrice whereDeletedAt($value)
 * @method static Builder|ProductPrice whereId($value)
 * @method static Builder|ProductPrice whereLocationId($value)
 * @method static Builder|ProductPrice wherePrice($value)
 * @method static Builder|ProductPrice whereProductId($value)
 * @method static Builder|ProductPrice whereSalePrice($value)
 * @method static Builder|ProductPrice whereSalePriceEndDate($value)
 * @method static Builder|ProductPrice whereSalePriceStartDate($value)
 * @method static Builder|ProductPrice whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ProductPrice extends Model
{
    //
}
