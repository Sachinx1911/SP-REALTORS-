<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'developer', 'location_id', 'starting_price',
        'configurations', 'possession', 'rera_number', 'property_type', 'status',
        'address', 'description', 'highlights', 'amenities', 'nearby_places',
        'configuration_details', 'hero_image', 'brochure', 'map_url',
        'contact_phone', 'contact_whatsapp', 'seo_title', 'seo_description',
        'is_featured', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'amenities' => 'array',
            'nearby_places' => 'array',
            'configuration_details' => 'array',
            'starting_price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            if (blank($project->slug)) {
                $project->slug = Str::slug($project->name);
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
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function galleryImages(): HasMany
    {
        return $this->images()->where('type', 'gallery');
    }

    public function floorPlans(): HasMany
    {
        return $this->images()->where('type', 'floor_plan');
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

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return [
            'pre-launch' => 'Pre Launch',
            'under-construction' => 'Under Construction',
            'ready-to-move' => 'Ready to Move',
            'completed' => 'Completed',
        ];
    }

    /* ---------------------------------------------------------------------
     * Presentation helpers
     * ------------------------------------------------------------------ */

    public function formattedStartingPrice(): string
    {
        if ($this->starting_price === null) {
            return 'Price on request';
        }

        $price = (float) $this->starting_price;

        // Crore keeps 2 decimals (₹ 1.25 Cr*); Lac drops empty decimals (₹ 78 Lac*).
        if ($price >= 10000000) {
            return '₹ '.number_format($price / 10000000, 2).' Cr*';
        }

        if ($price >= 100000) {
            return '₹ '.rtrim(rtrim(number_format($price / 100000, 2), '0'), '.').' Lac*';
        }

        return '₹ '.number_format($price);
    }

    public function statusLabel(): ?string
    {
        return self::statusOptions()[$this->status] ?? null;
    }

    public function locationLabel(): string
    {
        return $this->location?->name
            ? $this->location->name.', Navi Mumbai'
            : (string) $this->address;
    }

    public function heroImageUrl(): ?string
    {
        return $this->hero_image ? asset('storage/'.$this->hero_image) : null;
    }

    public function brochureUrl(): ?string
    {
        return $this->brochure ? asset('storage/'.$this->brochure) : null;
    }

    public function whatsappUrl(): string
    {
        $number = $this->contact_whatsapp ?: setting('whatsapp', '919324473328');
        $message = sprintf(
            "Hello SP REALTORS,\nI am interested in %s.\nPlease share project details.",
            $this->name
        );

        return 'https://wa.me/'.preg_replace('/\D/', '', $number).'?text='.rawurlencode($message);
    }

    public function telUrl(): string
    {
        $number = $this->contact_phone ?: setting('phone', '+91 93244 73328');

        return 'tel:'.preg_replace('/[^+0-9]/', '', $number);
    }
}
