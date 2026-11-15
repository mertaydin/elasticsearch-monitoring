<?php

namespace ElasticsearchMonitoring\Services;

use ElasticsearchMonitoring\Models\AlertEvent;
use ElasticsearchMonitoring\Models\AlertNotificationDelivery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class AlertNotificationDispatcher
{
    public function dispatch(AlertEvent $event): void
    {
        $settings = (array) config('elasticsearch-monitoring.alerts.notifications', []);
        $channels = (array) ($settings['channels'] ?? []);

        foreach ($channels as $channel => $configuration) {
            $configuration = (array) $configuration;

            if (! ($configuration['enabled'] ?? false) || ! $this->isConfigured($channel, $configuration)) {
                continue;
            }

            $delivery = AlertNotificationDelivery::query()->firstOrCreate(
                ['alert_event_id' => $event->id, 'channel' => $channel],
                [
                    'alert_code' => $event->code,
                    'status' => 'pending',
                    'attempts' => 0,
                ]
            );

            if ($delivery->status === 'sent') {
                continue;
            }

            $cooldown = max(0, (int) ($settings['cooldown_minutes'] ?? 30));
            $recentDelivery = $cooldown > 0
                ? AlertNotificationDelivery::query()
                    ->where('alert_code', $event->code)
                    ->where('channel', $channel)
                    ->where('status', 'sent')
                    ->where('sent_at', '>=', now()->subMinutes($cooldown))
                    ->where('id', '!=', $delivery->id)
                    ->exists()
                : false;

            if ($recentDelivery) {
                $delivery->update(['status' => 'suppressed', 'error' => 'Cooldown is active.']);

                continue;
            }

            try {
                $this->send($channel, $configuration, $event);
                $delivery->update([
                    'status' => 'sent',
                    'attempts' => $delivery->attempts + 1,
                    'sent_at' => now(),
                    'error' => null,
                ]);
            } catch (Throwable $exception) {
                $delivery->update([
                    'status' => 'failed',
                    'attempts' => $delivery->attempts + 1,
                    'error' => mb_substr($exception->getMessage(), 0, 5000),
                ]);

                throw $exception;
            }
        }
    }

    private function isConfigured(string $channel, array $configuration): bool
    {
        return match ($channel) {
            'mail' => ! empty($configuration['recipients']),
            'slack', 'teams' => filter_var($configuration['webhook_url'] ?? null, FILTER_VALIDATE_URL) !== false,
            default => false,
        };
    }

    private function send(string $channel, array $configuration, AlertEvent $event): void
    {
        $message = implode("\n", [
            '['.strtoupper($event->severity).'] '.$event->title,
            $event->message ?? '',
            'Code: '.$event->code,
            'Value: '.json_encode($event->value, JSON_UNESCAPED_UNICODE),
            'Threshold: '.json_encode($event->threshold, JSON_UNESCAPED_UNICODE),
            'First seen: '.$event->first_seen_at?->toIso8601String(),
        ]);

        if ($channel === 'mail') {
            Mail::raw($message, function ($mail) use ($configuration, $event): void {
                $mail->to((array) $configuration['recipients'])
                    ->subject('[Elasticsearch Monitoring] '.$event->title);
            });

            return;
        }

        $payload = $channel === 'slack'
            ? ['text' => $message]
            : [
                'type' => 'message',
                'attachments' => [[
                    'contentType' => 'application/vnd.microsoft.card.adaptive',
                    'content' => [
                        '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
                        'type' => 'AdaptiveCard',
                        'version' => '1.4',
                        'body' => [
                            ['type' => 'TextBlock', 'text' => $event->title, 'weight' => 'Bolder', 'size' => 'Medium', 'wrap' => true],
                            ['type' => 'TextBlock', 'text' => $message, 'wrap' => true],
                        ],
                    ],
                ]],
            ];

        $response = Http::timeout(8)->acceptJson()->post($configuration['webhook_url'], $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Notification webhook returned HTTP '.$response->status().'.');
        }
    }
}
