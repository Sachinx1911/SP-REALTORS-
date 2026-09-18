<?php

namespace App\Models;

use App\Mail\NewEnquiryMail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Enquiry extends Model
{
    use HasFactory;

    protected $table = 'enquiries';

    protected $fillable = [
        'name', 'phone', 'email', 'message',
        'property_id', 'project_id', 'source', 'status', 'admin_notes', 'ip_address',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn (Builder $q, $v) => $q->where('status', $v));
    }

    public function scopeSource(Builder $query, ?string $source): Builder
    {
        return $query->when($source, fn (Builder $q, $v) => $q->where('source', $v));
    }

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return [
            'new' => 'New',
            'contacted' => 'Contacted',
            'qualified' => 'Qualified',
            'closed' => 'Closed',
        ];
    }

    /** @return array<string, string> */
    public static function sourceOptions(): array
    {
        return [
            'contact' => 'Contact Page',
            'property' => 'Property Page',
            'project' => 'Project Page',
            'home-search' => 'Home Search',
            'brochure' => 'Brochure Download',
            'popup' => 'Website Popup',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function sourceLabel(): string
    {
        return self::sourceOptions()[$this->source] ?? $this->source;
    }

    public function subject(): ?string
    {
        return $this->property?->title ?? $this->project?->name;
    }

    /**
     * Email the broker about this enquiry. The lead is already saved in the
     * database at this point, so a mail failure (e.g. SMTP not configured
     * yet) is logged and swallowed — it must never break the public form.
     */
    public function notifyOwner(): void
    {
        $to = setting('email');
        if (! $to) {
            return;
        }

        try {
            Mail::to($to)->send(new NewEnquiryMail($this));
        } catch (\Throwable $e) {
            Log::warning('Failed to send enquiry notification email.', [
                'enquiry_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
