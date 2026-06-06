<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends Model
{
    /** @use HasFactory<\Database\Factories\ClickFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'link_id',
        'device_type_id',
        'browser_id',
        'referrer',
        'ip_hash',
        'clicked_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
        ];
    }

    /**
     * Get the link that owns the click.
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }

    /**
     * Get the device type of the click.
     */
    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class);
    }

    /**
     * Get the browser of the click.
     */
    public function browser(): BelongsTo
    {
        return $this->belongsTo(Browser::class);
    }

    /**
     * Hash the raw IP before storing.
     */
    protected function ipHash(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? hash('sha256', $value) : null,
        );
    }

    /**
     * Get a formatted date for display.
     */
    public function clickedAtFormatted(): ?string
    {
        return $this->clicked_at?->diffForHumans();
    }
}
