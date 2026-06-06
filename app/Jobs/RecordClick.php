<?php

namespace App\Jobs;

use App\Models\Click;
use App\Models\Link;
use App\Support\UserAgentParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordClick implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $linkId,
        public string $userAgent,
        public string $referrer,
        public string $ipHash,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $link = Link::find($this->linkId);

        if (! $link) {
            return;
        }

        // Increment atomically (idempotent safe — the first write wins on PK collision).
        $click = Click::create([
            'link_id' => $link->id,
            'device_type_id' => UserAgentParser::resolveDeviceTypeId($this->userAgent),
            'browser_id' => UserAgentParser::resolveBrowserId($this->userAgent),
            'referrer' => $this->referrer,
            'ip_hash' => $this->ipHash,
            'clicked_at' => now(),
        ]);

        // Increment only after a successful insert so the counter is never orphaned.
        if ($click) {
            $link->increment('click_count');
        }
    }
}
