<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $fillable = [
        'title', 'slug', 'location_id', 'property_type', 'purpose', 'configuration',
        'status', 'furnishing', 'price', 'price_negotiable', 'is_monthly',
        'area', 'area_unit', 'bedrooms', 'bathrooms', 'car_parking',
        'address', 'description', 'highlights', 'amenities',
        'rera_number', 'possession', 'main_image', 'map_url',
        'contact_phone', 'contact_whatsapp', 'seo_title', 'seo_description',
        'is_featured', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'amenities' => 'array',
            'price' => 'decimal:2',
            'price_negotiable' => 'boolean',
            'is_monthly' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Property $property) {
            if (blank($property->slug)) {
                $property->slug = Str::slug($property->title);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ---------------------------------------------------------------------
     * Relationships
     * ------------------------------------------------------------------ */

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    /* ---------------------------------------------------------------------
     * Scopes
     * ------------------------------------------------------------------ */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Apply the public listing filters (purpose, type, configuration, location, budget).
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['purpose'] ?? null, fn (Builder $q, $v) => $q->where('purpose', $v))
            ->when($filters['type'] ?? null, function (Builder $q, $v) {
                $q->whereIn('property_type', (array) $v);
            })
            ->when($filters['configuration'] ?? null, function (Builder $q, $v) {
                $q->whereIn('configuration', (array) $v);
            })
            ->when($filters['location'] ?? null, function (Builder $q, $v) {
                $q->whereHas('location', fn (Builder $l) => $l->whereIn('slug', (array) $v));
            })
            ->when($filters['budget'] ?? null, function (Builder $q, $v) {
                // Group the buckets so OR-ing them cannot leak past the other filters.
                $q->where(function (Builder $group) use ($v) {
                    foreach ((array) $v as $range) {
                        [$min, $max] = self::budgetRange($range);
                        if ($min === null && $max === null) {
                            continue;
                        }
                        $group->orWhere(function (Builder $inner) use ($min, $max) {
                            if ($min !== null) {
                                $inner->where('price', '>=', $min);
                            }
                            if ($max !== null) {
                                $inner->where('price', '<', $max);
                            }
                        });
                    }
                });
            })
            ->when($filters['search'] ?? null, function (Builder $q, $v) {
                $q->where(function (Builder $inner) use ($v) {
                    $inner->where('title', 'like', "%{$v}%")
                        ->orWhere('address', 'like', "%{$v}%");
                });
            });
    }

    public function scopeSorted(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            default => $query->latest(),
        };
    }

    /**
     * Budget bucket boundaries in rupees.
     *
     * @return array{0: int|null, 1: int|null}
     */
    public static function budgetRange(string $key): array
    {
        return match ($key) {
            'under-50l' => [null, 5000000],
            '50l-1cr' => [5000000, 10000000],
            '1cr-2cr' => [10000000, 20000000],
            'above-2cr' => [20000000, null],
            default => [null, null],
        };
    }

    /** @return array<string, string> */
    public static function budgetOptions(): array
    {
        return [
            'under-50l' => 'Under ₹ 50 Lac',
            '50l-1cr' => '₹ 50 Lac – ₹ 1 Cr',
            '1cr-2cr' => '₹ 1 Cr – ₹ 2 Cr',
            'above-2cr' => 'Above ₹ 2 Cr',
        ];
    }

    /** @return array<string, string> */
    public static function configurationOptions(): array
    {
        return [
            '1bhk' => '1 BHK',
            '2bhk' => '2 BHK',
            '3bhk' => '3 BHK',
            '4bhk+' => '4 BHK+',
            'plot' => 'Plot',
            'office' => 'Office',
            'shop' => 'Shop',
        ];
    }

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return [
            'ready-to-move' => 'Ready to Move',
            'under-construction' => 'Under Construction',
            'new-launch' => 'New Launch',
            'sold' => 'Sold',
            'rented' => 'Rented',
        ];
    }

    /** @return array<string, string> */
    public static function furnishingOptions(): array
    {
        return [
            'furnished' => 'Furnished',
            'semi-furnished' => 'Semi-Furnished',
            'unfurnished' => 'Unfurnished',
        ];
    }

    /* ---------------------------------------------------------------------
     * Presentation helpers
     * ------------------------------------------------------------------ */

    /**
     * Indian-format price: "₹ 85 Lac", "₹ 1.20 Cr", "₹ 55,000 / month".
     */
    public function formattedPrice(): string
    {
        if ($this->price === null) {
            return 'Price on request';
        }

        $price = (float) $this->price;

        if ($this->is_monthly) {
            return '₹ '.number_format($price).' / month';
        }

        // Crore keeps 2 decimals (₹ 1.20 Cr); Lac drops empty decimals (₹ 85 Lac).
        if ($price >= 10000000) {
            return '₹ '.number_format($price / 10000000, 2).' Cr';
        }

        if ($price >= 100000) {
            return '₹ '.rtrim(rtrim(number_format($price / 100000, 2), '0'), '.').' Lac';
        }

        return '₹ '.number_format($price);
    }

    public function badgeLabel(): string
    {
        return $this->purpose === 'rent' ? 'For Rent' : 'For Sale';
    }

    public function badgeClass(): string
    {
        return $this->purpose === 'rent' ? 'badge-rent' : 'badge-sale';
    }

    public function typeLabel(): string
    {
        return $this->property_type === 'commercial' ? 'Commercial' : 'Residential';
    }

    public function statusLabel(): ?string
    {
        return self::statusOptions()[$this->status] ?? null;
    }

    public function furnishingLabel(): ?string
    {
        return self::furnishingOptions()[$this->furnishing] ?? null;
    }

    public function configurationLabel(): ?string
    {
        return self::configurationOptions()[$this->configuration] ?? null;
    }

    public function locationLabel(): string
    {
        return $this->location?->name
            ? $this->location->name.', Navi Mumbai'
            : (string) $this->address;
    }

    public function mainImageUrl(): ?string
    {
        return $this->main_image ? asset('storage/'.$this->main_image) : null;
    }

    /**
     * Gallery = main image first, then additional images.
     *
     * @return array<int, array{url: string, alt: string}>
     */
    public function gallery(): array
    {
        $gallery = [];

        if ($this->main_image) {
            $gallery[] = ['url' => asset('storage/'.$this->main_image), 'alt' => $this->title];
        }

        foreach ($this->images as $image) {
            $gallery[] = [
                'url' => asset('storage/'.$image->path),
                'alt' => $image->alt ?: $this->title,
            ];
        }

        return $gallery;
    }

    public function whatsappUrl(): string
    {
        $number = $this->contact_whatsapp ?: setting('whatsapp', '918097985588');
        $message = sprintf(
            "Hello SP REALTORS,\nI am interested in %s (%s).\nPlease share more details.",
            $this->title,
            $this->locationLabel()
        );

        return 'https://wa.me/'.preg_replace('/\D/', '', $number).'?text='.rawurlencode($message);
    }

    public function telUrl(): string
    {
        $number = $this->contact_phone ?: setting('phone', '+91 80979 85588');

        return 'tel:'.preg_replace('/[^+0-9]/', '', $number);
    }
}
