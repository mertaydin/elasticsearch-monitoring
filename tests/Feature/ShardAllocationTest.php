<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\ShardController;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Support\Facades\Http;

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
        $this->assertSame(2, $payload['summary']['node_count']);
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

    public function test_it_returns_allocation_explanation_for_a_selected_shard_using_read_only_get(): void
    {
        Http::fake([
            '*_cluster/allocation/explain*' => Http::response([
                'index' => 'app-logs',
                'shard' => 0,
                'primary' => false,
                'current_state' => 'unassigned',
                'can_allocate' => 'no',
                'allocate_explanation' => 'No valid shard copy is available.',
                'unassigned_info' => ['reason' => 'NODE_LEFT', 'failed_allocation_attempts' => 2],
                'node_allocation_decisions' => [[
                    'node_name' => 'data-01',
                    'node_decision' => 'no',
                    'deciders' => [['decider' => 'same_shard', 'decision' => 'NO', 'explanation' => 'A copy already exists on this node.']],
                ]],
            ]),
        ]);

        $request = request()->create('/', 'GET', ['index' => 'app-logs', 'shard' => 0, 'primary' => false]);
        $response = app(ShardController::class)->allocationExplain($request);
        $payload = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('unassigned', $payload['current_state']);
        $this->assertSame('NODE_LEFT', $payload['unassigned_info']['reason']);
        $this->assertSame('same_shard', $payload['node_allocation_decisions'][0]['deciders'][0]['decider']);
        Http::assertSent(fn ($request): bool => $request->method() === 'GET'
            && str_contains($request->url(), '_cluster/allocation/explain')
            && str_contains($request->url(), 'include_disk_info=true'));
    }

    public function test_allocation_explanation_validates_required_shard_identity(): void
    {
        $request = request()->create('/', 'GET', ['index' => 'app-logs', 'primary' => true]);
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(ShardController::class)->allocationExplain($request);
    }

}
