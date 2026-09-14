<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'path', 'alt', 'type', 'label', 'sort_order'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }
}
