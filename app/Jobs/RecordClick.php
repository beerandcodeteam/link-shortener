<?php

namespace App\Jobs;

use App\Models\Browser;
use App\Models\Click;
use App\Models\DeviceType;
use App\Models\Link;
use App\Services\UserAgentParser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

/**
 * Records a single click against a link without delaying the redirect.
 *
 * Dispatched via {@see static::dispatchAfterResponse()} from the redirect
 * controller so the visitor is sent to their destination immediately.
 */
class RecordClick implements ShouldQueue
{
    use Queueable;

    /**
     * @param  string|null  $ipHash  Pre-hashed visitor IP; the raw IP is never stored.
     */
    public function __construct(
        public int $linkId,
        public ?string $userAgent = null,
        public ?string $referrer = null,
        public ?string $ipHash = null,
        public ?Carbon $clickedAt = null,
    ) {}

    /**
     * Execute the job: increment the counter and persist the click row.
     */
    public function handle(UserAgentParser $parser): void
    {
        $incremented = Link::whereKey($this->linkId)->increment('click_count');

        if ($incremented === 0) {
            return;
        }

        Click::create([
            'link_id' => $this->linkId,
            'device_type_id' => $this->lookupId(DeviceType::class, $parser->deviceSlug($this->userAgent)),
            'browser_id' => $this->lookupId(Browser::class, $parser->browserSlug($this->userAgent)),
            'referrer' => $this->referrer,
            'ip_hash' => $this->ipHash,
            'clicked_at' => $this->clickedAt ?? now(),
        ]);
    }

    /**
     * Resolve a lookup row id by slug, or null when the slug is unseeded.
     *
     * @param  class-string<Model>  $model
     */
    protected function lookupId(string $model, string $slug): ?int
    {
        return $model::where('slug', $slug)->value('id');
    }
}
