<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\QueryActivityController;
use ElasticsearchMonitoring\Models\MetricSnapshot;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class QueryActivityTest extends TestCase
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

    public function test_it_calculates_query_and_fetch_rates_and_average_times_per_node(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-15 10:00:00'));
        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => [[
                'id' => 'node-1', 'name' => 'data-01', 'roles' => ['data'],
                'search_activity' => ['query_total'=>100,'query_time_in_millis'=>500,'query_current'=>1,'fetch_total'=>50,'fetch_time_in_millis'=>200,'fetch_current'=>0,'scroll_total'=>5,'suggest_total'=>2],
                'search_queue' => 1, 'search_rejected' => 10,
            ]]],
        ]);

        Carbon::setTestNow(Carbon::parse('2026-11-15 10:01:00'));
        MetricSnapshot::query()->create([
            'collected_at' => now(),
            'elasticsearch_version' => '8.19.14',
            'metrics' => ['node_details' => [[
                'id' => 'node-1', 'name' => 'data-01', 'roles' => ['data'],
                'search_activity' => ['query_total'=>220,'query_time_in_millis'=>1100,'query_current'=>3,'fetch_total'=>110,'fetch_time_in_millis'=>500,'fetch_current'=>1,'scroll_total'=>11,'suggest_total'=>5],
                'search_queue' => 4, 'search_rejected' => 16,
            ]]],
        ]);

        $response = app(QueryActivityController::class)->data();
        $payload = $response->getData(true);
        $this->assertSame(200, $response->getStatusCode());
        $latest = $payload['nodes'][0]['latest'];
        $this->assertEquals(2.0, $latest['query_per_second']);
        $this->assertEquals(1.0, $latest['fetch_per_second']);
        $this->assertEquals(5.0, $latest['query_avg_time_ms']);
        $this->assertEquals(5.0, $latest['fetch_avg_time_ms']);
        $this->assertSame(4, $payload['summary']['search_queue']);
        $this->assertEquals(0.1, $payload['summary']['rejected_per_second']);
        $this->assertSame(3, $payload['summary']['active_queries']);
    }

    public function test_it_handles_empty_history(): void
    {
        $payload = app(QueryActivityController::class)->data()->getData(true);
        $this->assertSame([], $payload['nodes']);
        $this->assertEquals(0.0, $payload['summary']['query_per_second']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
