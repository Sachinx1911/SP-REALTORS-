<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public const CACHE_KEY = 'spr.settings';

    protected $fillable = ['key', 'value', 'group'];

    protected static function booted(): void
    {
        static::saved(fn () => self::flush());
        static::deleted(fn () => self::flush());
    }

    /**
     * All settings as a key => value map, cached for the request/app lifetime.
     *
     * @return array<string, string|null>
     */
    public static function all_cached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            // Table may not exist yet during a fresh install / first migration.
            try {
                return self::query()->pluck('value', 'key')->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = self::all_cached()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function put(string $key, ?string $value, string $group = 'general'): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Defaults used when a setting has not been configured yet.
     *
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'site_name' => 'SP REALTORS',
            'tagline' => 'Your Property, Our Priority',
            'phone' => '+91 80979 85588',
            'whatsapp' => '918097985588',
            'email' => 'info@sprealtors.in',
            'address' => 'Navi Mumbai, Maharashtra',
            'working_hours' => 'Mon - Sat: 9:00 AM - 7:00 PM',
            'map_url' => '',
            'facebook' => '',
            'instagram' => '',
            'linkedin' => '',
            'youtube' => '',
            'stat_1_number' => '100+',
            'stat_1_label' => 'Happy Clients',
            'stat_2_number' => '50+',
            'stat_2_label' => 'Properties Sold',
            'stat_3_number' => '5+',
            'stat_3_label' => 'Years of Experience',
            'stat_4_number' => 'Navi Mumbai',
            'stat_4_label' => 'Our Focus Region',
            'seo_title' => 'SP REALTORS — Trusted Real Estate Partner in Navi Mumbai',
            'seo_description' => 'Buy, rent or invest in residential and commercial properties across Navi Mumbai with expert guidance from SP REALTORS.',
        ];
    }
}
