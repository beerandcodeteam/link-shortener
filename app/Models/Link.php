<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'link_status_id',
    'original_url',
    'short_code',
    'click_count',
])]
class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'click_count' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<LinkStatus, $this>
     */
    public function linkStatus(): BelongsTo
    {
        return $this->belongsTo(LinkStatus::class);
    }

    /**
     * @return HasMany<Click, $this>
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }

    /**
     * Scope a query to only include links whose status is active.
     *
     * @param  Builder<Link>  $query
     * @return Builder<Link>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('linkStatus', fn (Builder $q) => $q->where('slug', 'active'));
    }

    /**
     * Determine if the link is currently active.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->linkStatus?->slug === 'active';
    }
}
