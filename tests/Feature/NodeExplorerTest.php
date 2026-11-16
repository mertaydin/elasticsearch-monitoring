<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\NodeExplorerController;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class NodeExplorerTest extends TestCase
{
    public function test_it_returns_basic_node_details_and_assigned_indices(): void
    {
        Http::fake([
            '*_nodes/node-a/stats/jvm,process,fs,indices' => Http::response([
                'nodes' => [
                    'node-a' => [
                        'name' => 'data-01',
                        'ip' => '10.0.0.11',
                        'roles' => ['data', 'ingest'],
                        'process' => ['cpu' => ['percent' => 25]],
                        'jvm' => ['mem' => ['heap_used_in_bytes' => 500, 'heap_max_in_bytes' => 1000]],
                        'fs' => ['total' => ['total_in_bytes' => 10000, 'available_in_bytes' => 4000]],
                        'indices' => ['docs' => ['count' => 120], 'shard_stats' => ['total_count' => 3]],
                    ],
                ],
            ]),
            '*_cat/shards*' => Http::response([
                ['index' => 'applications', 'prirep' => 'p', 'node' => 'data-01'],
                ['index' => 'applications', 'prirep' => 'r', 'node' => 'data-01'],
                ['index' => 'logs', 'prirep' => 'p', 'node' => 'other-node'],
            ]),
        ]);

        $request = request()->create('/monitoring/elasticsearch/nodes/details', 'GET', ['node_id' => 'node-a']);
        $response = app(NodeExplorerController::class)->details($request);
        $payload = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('data-01', $payload['node']['name']);
        $this->assertSame('10.0.0.11', $payload['node']['ip']);
        $this->assertSame(25, $payload['node']['cpu_percent']);
        $this->assertEquals(50.0, $payload['node']['heap_percent']);
        $this->assertEquals(60.0, $payload['node']['disk_percent']);
        $this->assertSame(120, $payload['node']['documents']);
        $this->assertSame(3, $payload['node']['shards']);
        $this->assertSame([['name' => 'applications', 'primary_shards' => 1, 'replica_shards' => 1]], $payload['indices']);
    }
}
