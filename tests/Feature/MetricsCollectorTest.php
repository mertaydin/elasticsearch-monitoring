<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Models\MetricSnapshot;
use ElasticsearchMonitoring\Services\MetricsCollector;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class MetricsCollectorTest extends TestCase
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

    public function test_it_stores_metrics_and_calculates_rates_between_snapshots(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-13 10:00:00'));
        $sample = 0;

        Http::fake(function ($request) use (&$sample) {
            $path = parse_url($request->url(), PHP_URL_PATH);

            if ($path === '/') {
                return Http::response(['version' => ['number' => '8.19.14']]);
            }

            if ($path === '/_cluster/health') {
                return Http::response([
                    'cluster_name' => 'test-cluster',
                    'status' => 'green',
                    'number_of_nodes' => 1,
                    'active_shards' => 4,
                    'unassigned_shards' => 0,
                ]);
            }

            if (str_starts_with((string) $path, '/_nodes/stats/')) {
                $sample++;

                return Http::response([
                    'nodes' => [
                        'node-1' => [
                            'jvm' => ['mem' => ['heap_used_in_bytes' => 50, 'heap_max_in_bytes' => 100]],
                            'process' => ['cpu' => ['percent' => 20]],
                            'indices' => [
                                'search' => ['query_total' => $sample === 1 ? 100 : 160],
                                'indexing' => ['index_total' => $sample === 1 ? 20 : 50],
                                'docs' => ['count' => 25],
                                'shard_stats' => ['total_count' => 4],
                            ],
                            'thread_pool' => [
                                'search' => ['queue' => 1, 'rejected' => 0],
                                'write' => ['queue' => 0, 'rejected' => 0],
                            ],
                            'fs' => [
                                'total' => ['total_in_bytes' => 1000, 'available_in_bytes' => 400],
                                'io_stats' => ['total' => ['read_operations' => 10, 'write_operations' => 5]],
                            ],
                        ],
                    ],
                ]);
            }

            if ($path === '/_cluster/stats') {
                return Http::response(['status' => 'ok']);
            }

            return Http::response(['error' => 'Unexpected endpoint'], 404);
        });

        $collector = app(MetricsCollector::class);
        $first = $collector->collect();

        $this->assertNull($first['rates']['search_per_second']);
        $this->assertSame('green', $first['cluster']['status']);
        $this->assertSame(20.0, $first['cpu']['percent_average']);
        $this->assertSame(1, MetricSnapshot::query()->count());

        Carbon::setTestNow(Carbon::parse('2026-11-13 10:02:00'));
        $second = $collector->collect();

        $this->assertSame(0.5, $second['rates']['search_per_second']);
        $this->assertSame(0.25, $second['rates']['indexing_per_second']);
        $this->assertSame(2, MetricSnapshot::query()->count());
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
