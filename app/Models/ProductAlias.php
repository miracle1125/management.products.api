<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * App\Models\ProductAlias
 *
 * @property int $id
 * @property int $product_id
 * @property string $alias
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Product $product
 * @method static Builder|ProductAlias newModelQuery()
 * @method static Builder|ProductAlias newQuery()
 * @method static Builder|ProductAlias query()
 * @method static Builder|ProductAlias whereAlias($value)
 * @method static Builder|ProductAlias whereCreatedAt($value)
 * @method static Builder|ProductAlias whereId($value)
 * @method static Builder|ProductAlias whereProductId($value)
 * @method static Builder|ProductAlias whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ProductAlias extends Model
{
    protected $table = 'products_aliases';

    protected $fillable = [
        'product_id',
        'alias'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(ProductAlias::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('product_id'),
                AllowedFilter::partial('alias'),
            ])
            ->allowedIncludes([
            ])
            ->allowedSorts([
            ]);
    }
}
