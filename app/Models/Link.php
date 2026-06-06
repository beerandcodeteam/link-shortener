<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Link extends Model
{
    /** @use HasFactory<\Database\Factories\LinkFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'original_url',
        'short_code',
        'click_count',
        'user_id',
        'link_status_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'click_count' => 'integer',
            'link_status_id' => 'integer',
        ];
    }

    /**
     * Get the user that owns the link.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status of the link.
     */
    public function linkStatus(): BelongsTo
    {
        return $this->belongsTo(LinkStatus::class, 'link_status_id');
    }

    /**
     * Get the clicks for this link.
     */
    public function clicks(): HasMany
    {
        // Click model defined in Phase 2.7; reference via string to avoid class_exists checks.
        return $this->hasMany(\App\Models\Click::class)->orderByDesc('clicked_at');
    }

    /**
     * Scope a query to only include active links.
     */
    public function scopeActive($query)
    {
        return $query->whereHas('linkStatus', fn ($q) => $q->where('slug', 'active'));
    }

    /**
     * Get the full short URL for this link.
     */
    protected function shortUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => route('shorten.show', $this->short_code),
        );
    }

    /**
     * Check if the link is currently active.
     */
    public function isActive(): bool
    {
        return $this->linkStatus?->slug === 'active';
    }
}
