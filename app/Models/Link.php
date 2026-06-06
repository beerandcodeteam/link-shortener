<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'link_status_id', 'original_url', 'short_code', 'click_count'])]
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
     * Scope a query to only active links.
     *
     * @param  Builder<Link>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereHas('linkStatus', function (Builder $query) {
            $query->where('slug', 'active');
        });
    }

    /**
     * Determine whether the link is active.
     *
     * @return Attribute<bool, never>
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->linkStatus?->slug === 'active',
        );
    }
}
