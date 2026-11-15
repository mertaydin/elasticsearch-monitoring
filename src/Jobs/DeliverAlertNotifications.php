<?php

namespace ElasticsearchMonitoring\Jobs;

use ElasticsearchMonitoring\Models\AlertEvent;
use ElasticsearchMonitoring\Services\AlertNotificationDispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverAlertNotifications implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $alertEventId) {}

    public function backoff(): array
    {
        return [30, 120];
    }

    public function handle(AlertNotificationDispatcher $dispatcher): void
    {
        $event = AlertEvent::query()->find($this->alertEventId);

        if ($event !== null) {
            $dispatcher->dispatch($event);
        }
    }
}
