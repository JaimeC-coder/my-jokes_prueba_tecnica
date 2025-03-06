<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configurations extends Model
{
    //
    protected $fillable = ['key', 'value'];

    /**
     * Get config value by key.
     *
     * @param string $key
     * @return string|null
     */
    public static function getByKey($key)
    {
        $config = self::where('key', $key)->first();
        return $config ? $config->value : null;
    }
}
