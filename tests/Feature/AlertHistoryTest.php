<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Models\AlertEvent;
use ElasticsearchMonitoring\Services\AlertHistoryRecorder;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlertHistoryTest extends TestCase
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
    }

    public function test_it_tracks_alert_occurrences_and_resolution(): void
    {
        $recorder = app(AlertHistoryRecorder::class);
        $alert = [[
            'code' => 'cpu',
            'severity' => 'warning',
            'title' => 'High CPU usage',
            'message' => 'CPU is high.',
            'value' => 82,
            'threshold' => 80,
        ]];

        $recorder->record($alert);
        $event = AlertEvent::query()->firstOrFail();
        $this->assertSame(1, $event->occurrences);
        $this->assertNull($event->resolved_at);

        $recorder->record($alert);
        $event->refresh();
        $this->assertSame(2, $event->occurrences);
        $this->assertSame(82, $event->value);

        $recorder->record([]);
        $event->refresh();
        $this->assertNotNull($event->resolved_at);
    }

    public function test_it_starts_a_new_event_after_a_previous_event_was_resolved(): void
    {
        $recorder = app(AlertHistoryRecorder::class);
        $alert = [[
            'code' => 'disk',
            'severity' => 'critical',
            'title' => 'Disk full',
            'message' => 'Disk usage is critical.',
            'value' => 95,
            'threshold' => 90,
        ]];

        $recorder->record($alert);
        $recorder->record([]);
        $recorder->record($alert);

        $this->assertSame(2, AlertEvent::query()->where('code', 'disk')->count());
        $this->assertSame(1, AlertEvent::query()->where('code', 'disk')->whereNull('resolved_at')->count());
    }
}
