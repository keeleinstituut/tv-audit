<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// Casts attributes to json in the same way as Laravel's default "json" and "array" casts
// with the addition that will remove \u0000 unicode character from the value as it
// is not supported by Postgres json or jsonb fields
class AsUnicodeSafeArray implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return json_decode($value, true);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $jsonString = json_encode($value);
        return Str::replace('\u0000', '', $jsonString);
    }
}
