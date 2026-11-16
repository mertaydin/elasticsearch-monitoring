<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\NodeController;
use ElasticsearchMonitoring\Models\MetricSnapshot;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class NodeHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('elasticsearch_metric_snapshots');
        Schema::create('elasticsearch_metric_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->timestamp('collected_at')->index();
            $table->string('elasticsearch_version', 32);
            $table->json('metrics');
            $table->timestamps();
        });
    }

    public function test_it_returns_per_node_history_and_calculates_operation_rates(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-13 10:00:00'));

        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => [[
                'id' => 'node-a',
                'name' => 'data-01',
                'ip' => '10.0.0.11',
                'roles' => ['data', 'ingest'],
                'cpu_percent' => 20,
                'heap_percent' => 50,
                'filesystem_used_percent' => 60,
                'documents' => 100,
                'shards' => 4,
                'search_total' => 100,
                'indexing_total' => 20,
                'search_queue' => 1,
                'search_rejected' => 0,
                'write_queue' => 0,
                'write_rejected' => 0,
            ]]],
        ]);

        Carbon::setTestNow(Carbon::parse('2026-11-13 10:02:00'));

        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => [[
                'id' => 'node-a',
                'name' => 'data-01',
                'roles' => ['data', 'ingest'],
                'cpu_percent' => 30,
                'heap_percent' => 55,
                'filesystem_used_percent' => 62,
                'documents' => 120,
                'shards' => 5,
                'search_total' => 160,
                'indexing_total' => 50,
                'search_queue' => 2,
                'search_rejected' => 1,
                'write_queue' => 1,
                'write_rejected' => 0,
            ], [
                'id' => 'node-b',
                'name' => 'master-01',
                'roles' => ['master'],
                'cpu_percent' => 5,
                'heap_percent' => 25,
                'filesystem_used_percent' => 10,
                'documents' => 0,
                'shards' => 0,
                'search_total' => 0,
                'indexing_total' => 0,
            ]]],
        ]);

        $response = app(NodeController::class)->data();
        $payload = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertCount(2, $payload['nodes']);
        $this->assertSame('data-01', $payload['nodes'][0]['name']);
        $this->assertSame('10.0.0.11', collect($payload['nodes'])->firstWhere('id', 'node-a')['ip']);
        $node = collect($payload['nodes'])->firstWhere('id', 'node-a');

        $this->assertNotNull($node);
        $this->assertSame(30, $node['latest']['cpu_percent']);
        $this->assertSame(0.5, $node['latest']['search_per_second']);
        $this->assertSame(0.25, $node['latest']['indexing_per_second']);
        $this->assertCount(2, $node['history']);
        $this->assertSame(2, $payload['meta']['snapshots']);
    }

    public function test_it_tracks_node_offline_and_recovery_periods(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-13 10:00:00'));

        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => [['id' => 'node-a', 'name' => 'data-01']]],
        ]);

        Carbon::setTestNow(Carbon::parse('2026-11-13 10:01:00'));
        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => []],
        ]);

        Carbon::setTestNow(Carbon::parse('2026-11-13 10:02:00'));
        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => [['id' => 'node-a', 'name' => 'data-01']]],
        ]);

        $payload = app(NodeController::class)->data()->getData(true);
        $node = collect($payload['nodes'])->firstWhere('id', 'node-a');

        $this->assertNotNull($node);
        $this->assertSame('online', $node['availability']['status']);
        $this->assertCount(3, $node['availability']['periods']);
        $this->assertSame('offline', $node['availability']['periods'][1]['status']);
        $this->assertSame(60, $node['availability']['periods'][1]['duration_seconds']);
        $this->assertNull($node['availability']['offline_since']);
    }

    public function test_it_reports_the_duration_of_an_ongoing_node_outage(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-13 10:00:00'));
        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => [['id' => 'node-a', 'name' => 'data-01']]],
        ]);

        Carbon::setTestNow(Carbon::parse('2026-11-13 10:01:00'));
        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => []],
        ]);

        Carbon::setTestNow(Carbon::parse('2026-11-13 10:04:00'));
        $payload = app(NodeController::class)->data()->getData(true);
        $node = collect($payload['nodes'])->firstWhere('id', 'node-a');

        $this->assertNotNull($node);
        $this->assertSame('offline', $node['availability']['status']);
        $this->assertSame(180, $node['availability']['offline_duration_seconds']);
        $this->assertSame('2026-11-13T10:01:00+00:00', $node['availability']['offline_since']);
    }

    public function test_it_summarizes_mixed_node_versions_and_unknown_versions(): void
    {
        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => [
                ['id' => 'node-a', 'name' => 'data-01', 'version' => '8.19.14'],
                ['id' => 'node-b', 'name' => 'data-02', 'version' => '8.18.2'],
                ['id' => 'node-c', 'name' => 'master-01'],
            ]],
        ]);

        $payload = app(NodeController::class)->data()->getData(true);

        $this->assertSame('mixed', $payload['version_summary']['status']);
        $this->assertSame(3, $payload['version_summary']['node_count']);
        $this->assertSame(1, $payload['version_summary']['unknown_count']);
        $this->assertSame([
            ['version' => '8.18.2', 'node_count' => 1],
            ['version' => '8.19.14', 'node_count' => 1],
        ], $payload['version_summary']['versions']);
    }

    public function test_it_returns_an_empty_node_list_when_no_snapshots_exist(): void
    {
        $payload = app(NodeController::class)->data()->getData(true);

        $this->assertSame([], $payload['nodes']);
        $this->assertSame(0, $payload['meta']['snapshots']);
        $this->assertSame('unknown', $payload['version_summary']['status']);
        $this->assertSame([], $payload['version_summary']['versions']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
