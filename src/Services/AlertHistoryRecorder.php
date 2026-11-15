<?php

namespace ElasticsearchMonitoring\Services;

use ElasticsearchMonitoring\Jobs\DeliverAlertNotifications;
use ElasticsearchMonitoring\Models\AlertEvent;
use Illuminate\Support\Facades\DB;
use Throwable;

class AlertHistoryRecorder
{
    /** @param array<int, array<string, mixed>> $alerts */
    public function record(array $alerts): void
    {
        $newEventIds = DB::transaction(function () use ($alerts): array {
            $now = now();
            $active = AlertEvent::query()->whereNull('resolved_at')->lockForUpdate()->get()->keyBy('code');
            $seen = [];
            $newIds = [];

            foreach ($alerts as $alert) {
                $code = (string) ($alert['code'] ?? '');
                if ($code === '') {
                    continue;
                }

                $seen[] = $code;
                $values = [
                    'severity' => (string) ($alert['severity'] ?? 'warning'),
                    'title' => (string) ($alert['title'] ?? $code),
                    'message' => (string) ($alert['message'] ?? ''),
                    'value' => $alert['value'] ?? null,
                    'threshold' => $alert['threshold'] ?? null,
                    'last_seen_at' => $now,
                ];
                $event = $active->get($code);

                if ($event !== null) {
                    $event->fill($values);
                    $event->occurrences++;
                    $event->save();
                } else {
                    $event = AlertEvent::query()->create($values + [
                        'code' => $code,
                        'first_seen_at' => $now,
                        'occurrences' => 1,
                    ]);
                    $newIds[] = $event->id;
                }
            }

            foreach ($active as $code => $event) {
                if (! in_array($code, $seen, true)) {
                    $event->resolved_at = $now->toImmutable();
                    $event->save();
                }
            }

            return $newIds;
        });

        foreach ($newEventIds as $eventId) {
            try {
                DeliverAlertNotifications::dispatch($eventId);
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }
}
