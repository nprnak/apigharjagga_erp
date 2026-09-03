<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('site_settings'));
        static::deleted(fn () => Cache::forget('site_settings'));
    }

    /**
     * Fetch a single setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::allSettings()->get($key, $default);
    }

    /**
     * Fetch every setting as a flat [key => value] collection, cached
     * since these are read on nearly every request.
     */
    public static function allSettings(): \Illuminate\Support\Collection
    {
        return Cache::rememberForever('site_settings', function () {
            return static::query()->pluck('value', 'key');
        });
    }

    /**
     * Persist a single key/value pair.
     */
    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Persist many key/value pairs at once.
     *
     * @param  array<string, mixed>  $values
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::set($key, $value);
        }
    }
}
