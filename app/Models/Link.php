<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Link extends Model
{
    protected $fillable = [
        'user_id',
        'link_status_id',
        'original_url',
        'short_code',
        'click_count',
    ];

    protected $casts = [
        'click_count' => 'integer',
    ];

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
    public function status(): BelongsTo
    {
        return $this->belongsTo(LinkStatus::class, 'link_status_id');
    }

    /**
     * Get the clicks for this link.
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }

    /**
     * Scope to filter active links.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('status', function (Builder $q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Accessor to check if the link is active.
     */
    public function isActive(): bool
    {
        return $this->status && $this->status->is_active;
    }
}
