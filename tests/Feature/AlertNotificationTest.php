<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Jobs\DeliverAlertNotifications;
use ElasticsearchMonitoring\Models\AlertEvent;
use ElasticsearchMonitoring\Models\AlertNotificationDelivery;
use ElasticsearchMonitoring\Services\AlertNotificationDispatcher;
use ElasticsearchMonitoring\Services\AlertHistoryRecorder;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;

class AlertNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('elasticsearch_alert_events', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 100)->index();
            $table->string('severity', 20)->index();
            $table->string('title');
            $table->text('message')->nullable();
            $table->json('value')->nullable();
            $table->json('threshold')->nullable();
            $table->timestamp('first_seen_at')->index();
            $table->timestamp('last_seen_at')->index();
            $table->timestamp('resolved_at')->nullable()->index();
            $table->unsignedInteger('occurrences')->default(1);
            $table->timestamps();
            $table->index(['resolved_at', 'severity']);
        });

        Schema::create('elasticsearch_alert_notification_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('alert_event_id')->constrained('elasticsearch_alert_events')->cascadeOnDelete();
            $table->string('alert_code', 100)->index();
            $table->string('channel', 30);
            $table->string('status', 20)->index();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('sent_at')->nullable()->index();
            $table->text('error')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
            $table->unique(['alert_event_id', 'channel']);
            $table->index(['alert_code', 'channel', 'sent_at']);
        });
    }

    public function test_it_sends_a_slack_notification_for_a_new_alert(): void
    {
        Http::fake(['hooks.slack.com/*' => Http::response(['ok' => true], 200)]);
        config([
            'elasticsearch-monitoring.alerts.notifications.cooldown_minutes' => 30,
            'elasticsearch-monitoring.alerts.notifications.channels.slack.enabled' => true,
            'elasticsearch-monitoring.alerts.notifications.channels.slack.webhook_url' => 'https://hooks.slack.com/services/test',
        ]);

        $alert = [[
            'code' => 'cpu',
            'severity' => 'warning',
            'title' => 'High CPU usage',
            'message' => 'CPU is high.',
            'value' => 82,
            'threshold' => 80,
        ]];

        app(AlertHistoryRecorder::class)->record($alert);

        $this->assertDatabaseHas('elasticsearch_alert_notification_deliveries', [
            'alert_code' => 'cpu',
            'channel' => 'slack',
            'status' => 'sent',
            'attempts' => 1,
        ]);
        Http::assertSentCount(1);
    }

    public function test_it_does_not_send_duplicate_notifications_for_an_active_alert(): void
    {
        Http::fake(['hooks.slack.com/*' => Http::response(['ok' => true], 200)]);
        config([
            'elasticsearch-monitoring.alerts.notifications.channels.slack.enabled' => true,
            'elasticsearch-monitoring.alerts.notifications.channels.slack.webhook_url' => 'https://hooks.slack.com/services/test',
        ]);

        $alert = [['code' => 'cpu', 'severity' => 'warning', 'title' => 'High CPU']];
        $recorder = app(AlertHistoryRecorder::class);
        $recorder->record($alert);
        $recorder->record($alert);

        Http::assertSentCount(1);
        $this->assertSame(1, AlertNotificationDelivery::query()->count());
    }

    public function test_it_suppresses_a_reopened_alert_during_the_cooldown(): void
    {
        Http::fake(['hooks.slack.com/*' => Http::response(['ok' => true], 200)]);
        config([
            'elasticsearch-monitoring.alerts.notifications.cooldown_minutes' => 30,
            'elasticsearch-monitoring.alerts.notifications.channels.slack.enabled' => true,
            'elasticsearch-monitoring.alerts.notifications.channels.slack.webhook_url' => 'https://hooks.slack.com/services/test',
        ]);

        $recorder = app(AlertHistoryRecorder::class);
        $alert = [['code' => 'cpu', 'severity' => 'warning', 'title' => 'High CPU']];
        $recorder->record($alert);
        $recorder->record([]);
        $recorder->record($alert);

        Http::assertSentCount(1);
        $this->assertDatabaseHas('elasticsearch_alert_notification_deliveries', [
            'alert_code' => 'cpu',
            'channel' => 'slack',
            'status' => 'suppressed',
        ]);
    }

    public function test_disabled_channels_do_not_create_delivery_records(): void
    {
        $event = AlertEvent::query()->create([
            'code' => 'cpu',
            'severity' => 'warning',
            'title' => 'High CPU',
            'message' => 'CPU high',
            'first_seen_at' => now(),
            'last_seen_at' => now(),
            'occurrences' => 1,
        ]);

        app(AlertNotificationDispatcher::class)->dispatch($event);

        $this->assertSame(0, AlertNotificationDelivery::query()->count());
    }
}
