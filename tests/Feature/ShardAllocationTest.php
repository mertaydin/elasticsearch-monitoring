<?php

namespace ElasticsearchMonitoring\\Tests\\Feature;

use ElasticsearchMonitoring\\Http\\Controllers\\ShardController;
use ElasticsearchMonitoring\\Tests\\TestCase;
use Illuminate\\Support\\Facades\\Http;

class ShardAllocationTest extends TestCase
{
    public function test_it_returns_shard_states_and_node_distribution(): void
    {
        Http::fake([
            '*_cat/shards*' => Http::response([
                ['index' => 'app-logs', 'shard' => '0', 'prirep' => 'p', 'state' => 'STARTED', 'docs' => '120', 'store' => '2048', 'node' => 'data-01'],
                ['index' => 'app-logs', 'shard' => '0', 'prirep' => 'r', 'state' => 'UNASSIGNED', 'docs' => null, 'store' => null, 'node' => null, 'unassigned.reason' => 'NODE_LEFT'],
                ['index' => 'users', 'shard' => '1', 'prirep' => 'p', 'state' => 'STARTED', 'docs' => '40', 'store' => '1024', 'node' => 'data-02'],
            ]),
        ]);

        $response = app(ShardController::class)->data();
        $payload = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(3, $payload['summary']['total']);
        $this->assertSame(2, $payload['summary']['started']);
        $this->assertSame(1, $payload['summary']['unassigned']);
        $this->assertSame(2, $payload['summary']['nodes']);
        $this->assertSame('NODE_LEFT', $payload['shards'][1]['unassigned_reason']);
        $this->assertSame('data-01', $payload['node_distribution'][0]['node']);
        Http::assertSent(fn ($request): bool => $request->method() === 'GET' && str_contains($request->url(), '_cat/shards'));
    }

    public function test_it_returns_empty_summary_when_cluster_has_no_shards(): void
    {
        Http::fake(['*_cat/shards*' => Http::response([])]);

        $payload = app(ShardController::class)->data()->getData(true);

        $this->assertSame([], $payload['shards']);
        $this->assertSame(0, $payload['summary']['total']);
        $this->assertSame([], $payload['node_distribution']);
    }
}
