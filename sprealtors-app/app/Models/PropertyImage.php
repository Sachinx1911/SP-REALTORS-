<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'path', 'alt', 'sort_order'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }
}
