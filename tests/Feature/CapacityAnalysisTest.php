<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\CapacityController;
use ElasticsearchMonitoring\Models\MetricSnapshot;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class CapacityAnalysisTest extends TestCase
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

    public function test_it_builds_capacity_history_index_growth_and_node_forecasts(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-15 12:00:00'));
        MetricSnapshot::query()->create([
            'collected_at' => now()->subDays(5), 'elasticsearch_version' => '8.19.14',
            'metrics' => ['indices' => ['documents' => 1000, 'primary_store_size_bytes' => 10000],
                'filesystem' => ['used_bytes' => 500, 'total_bytes' => 1000],
                'node_details' => [['id' => 'node-1', 'name' => 'data-01', 'roles' => ['data'], 'filesystem_used_bytes' => 500, 'filesystem_total_bytes' => 1000, 'filesystem_available_bytes' => 500, 'filesystem_used_percent' => 50]],
                'index_details' => [['name' => 'logs-2026', 'documents' => 1000, 'storage_bytes' => 10000]]],
        ]);
        MetricSnapshot::query()->create([
            'collected_at' => now(), 'elasticsearch_version' => '8.19.14',
            'metrics' => ['indices' => ['documents' => 1500, 'primary_store_size_bytes' => 20000],
                'filesystem' => ['used_bytes' => 700, 'total_bytes' => 1000],
                'node_details' => [['id' => 'node-1', 'name' => 'data-01', 'roles' => ['data'], 'filesystem_used_bytes' => 700, 'filesystem_total_bytes' => 1000, 'filesystem_available_bytes' => 300, 'filesystem_used_percent' => 70]],
                'index_details' => [['name' => 'logs-2026', 'documents' => 1500, 'storage_bytes' => 20000]]],
        ]);
        $payload = app(CapacityController::class)->data(request()->duplicate(['days' => 30]))->getData(true);
        $this->assertSame(30, $payload['period_days']);
        $this->assertSame(1500, $payload['summary']['documents']);
        $this->assertSame(700, $payload['nodes'][0]['latest']['used_bytes']);
        $this->assertSame(40, $payload['nodes'][0]['forecast']['growth_bytes_per_day']);
        $this->assertSame(100, $payload['fastest_indices'][0]['document_growth_per_day']);
        $this->assertSame(2000, $payload['fastest_indices'][0]['storage_growth_per_day']);
        $this->assertCount(2, $payload['history']);
    }

    public function test_unsupported_period_falls_back_to_thirty_days(): void
    {
        $payload = app(CapacityController::class)->data(request()->duplicate(['days' => 14]))->getData(true);
        $this->assertSame(30, $payload['period_days']);
        $this->assertSame([], $payload['nodes']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
