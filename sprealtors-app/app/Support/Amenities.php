<?php

namespace App\Support;

use Illuminate\Support\Str;

class Amenities
{
    /**
     * The amenity list offered in the admin panel.
     *
     * @return list<string>
     */
    public static function options(): array
    {
        return [
            'Lift', 'Security', 'Power Backup', 'Car Parking', 'Gym',
            'Swimming Pool', "Children's Play Area", 'Garden', 'Club House',
            'CCTV', 'Intercom', 'Gas Pipeline', '24x7 Security', 'Landscaped Garden',
            'Kids Play Area',
        ];
    }

    /**
     * Map an amenity label to an icon name in the shared icon component.
     */
    public static function icon(string $amenity): string
    {
        $key = Str::of($amenity)->lower()->squish()->value();

        return match (true) {
            str_contains($key, 'lift') => 'building',
            str_contains($key, 'security') || str_contains($key, 'cctv') => 'shield',
            str_contains($key, 'power') => 'zap',
            str_contains($key, 'parking') => 'car',
            str_contains($key, 'gym') => 'dumbbell',
            str_contains($key, 'pool') => 'waves',
            str_contains($key, 'play') || str_contains($key, 'kids') || str_contains($key, 'child') => 'baby',
            str_contains($key, 'garden') || str_contains($key, 'landscap') => 'tree',
            str_contains($key, 'club') => 'home',
            str_contains($key, 'intercom') => 'phone',
            str_contains($key, 'gas') => 'zap',
            default => 'check-circle',
        };
    }
}
