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
     * Merge checkbox selections with free-typed custom amenities.
     *
     * Splits $customInput on commas/newlines, trims, drops empties/duplicates
     * (case-insensitive, so "Solar Panel" typed twice only appears once) and
     * caps each label at 60 chars to match the amenities.* validation rule.
     *
     * @param  list<string>  $selected
     * @return list<string>
     */
    public static function mergeCustom(array $selected, ?string $customInput): array
    {
        $custom = collect(preg_split('/[,\n\r]+/', (string) $customInput))
            ->map(fn ($item) => Str::of($item)->trim()->limit(60, '')->value())
            ->filter();

        return collect($selected)
            ->merge($custom)
            ->filter()
            ->unique(fn ($item) => Str::lower($item))
            ->values()
            ->all();
    }

    /**
     * Amenities on a record that are not part of the predefined checkbox
     * list — used to pre-fill the "Other amenities" field when editing.
     *
     * @param  list<string>|null  $amenities
     * @return list<string>
     */
    public static function customOnly(?array $amenities): array
    {
        $known = collect(self::options())->map(fn ($item) => Str::lower($item));

        return collect($amenities ?? [])
            ->reject(fn ($item) => $known->contains(Str::lower($item)))
            ->values()
            ->all();
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
