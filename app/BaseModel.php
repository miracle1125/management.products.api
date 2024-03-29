<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App
 *
 * @method static firstOrCreate(array $array, array $array)
 * @method static findOrFail($id)
 */
abstract class BaseModel extends Model
{

    /**
     * @param string $attribute
     * @return bool
     */
    public function isAttributeChanged(string $attribute): bool
    {
        return $this->getOriginal($attribute) !== $this->getAttribute($attribute);
    }

    /**
     * @param string $attribute
     * @return bool
     */
    public function isAttributeNotChanged(string $attribute): bool
    {
        return ! $this->isAttributeChanged($attribute);
    }

    /**
     * @param array $attributes
     * @return bool
     */
    public function isAnyAttributeChanged(array $attributes): bool
    {
        $changedAttributes = collect($attributes)->filter(function (string $attribute) {
            return $this->isAttributeChanged($attribute);
        });

        return $changedAttributes->isNotEmpty();
    }
}
