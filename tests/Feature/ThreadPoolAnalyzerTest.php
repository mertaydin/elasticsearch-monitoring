<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\ThreadPoolController;
use ElasticsearchMonitoring\Models\MetricSnapshot;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class ThreadPoolAnalyzerTest extends TestCase
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

    public function test_it_builds_per_node_thread_pool_history_and_rates(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-15 10:00:00'));
        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => [[
                'id' => 'node-1', 'name' => 'data-01', 'roles' => ['data'],
                'thread_pools' => [
                    ['name' => 'search', 'threads' => 8, 'queue' => 0, 'active' => 2, 'rejected' => 10, 'largest' => 4, 'completed' => 100],
                    ['name' => 'write', 'threads' => 4, 'queue' => 2, 'active' => 4, 'rejected' => 5, 'largest' => 4, 'completed' => 80],
                ],
            ]]],
        ]);

        Carbon::setTestNow(Carbon::parse('2026-11-15 10:01:00'));
        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => [[
                'id' => 'node-1', 'name' => 'data-01', 'roles' => ['data'],
                'thread_pools' => [
                    ['name' => 'search', 'threads' => 8, 'queue' => 3, 'active' => 8, 'rejected' => 16, 'largest' => 8, 'completed' => 220],
                    ['name' => 'write', 'threads' => 4, 'queue' => 0, 'active' => 1, 'rejected' => 5, 'largest' => 4, 'completed' => 140],
                ],
            ]]],
        ]);

        $payload = app(ThreadPoolController::class)->data()->getData(true);
        $this->assertSame(2, $payload['meta']['snapshots']);
        $node = $payload['nodes'][0];
        $search = collect($node['pools'])->firstWhere('name', 'search');
        $this->assertSame(3, $search['latest']['queue']);
        $this->assertSame(0.1, $search['latest']['rejected_per_second']);
        $this->assertSame(2, $search['latest']['completed_per_second']);
        $this->assertSame('saturated', $search['status']);
        $write = collect($node['pools'])->firstWhere('name', 'write');
        $this->assertSame('healthy', $write['status']);
    }

    public function test_it_handles_missing_thread_pool_snapshots(): void
    {
        $payload = app(ThreadPoolController::class)->data()->getData(true);
        $this->assertSame([], $payload['nodes']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
