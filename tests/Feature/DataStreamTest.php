<?php

namespace ElasticsearchMonitoring\\Tests\\Feature;

use ElasticsearchMonitoring\\Http\\Controllers\\DataStreamController;
use ElasticsearchMonitoring\\Tests\\TestCase;
use Illuminate\\Support\\Facades\\Http;

class DataStreamTest extends TestCase
{
    public function test_it_lists_data_streams_and_aggregates_backing_index_stats(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '_data_stream')) {
                return Http::response(['data_streams' => [[
                    'name' => 'app-logs', 'status' => 'GREEN', 'generation' => 3,
                    'template' => 'app-logs-template', 'ilm_policy' => 'logs-policy', 'prefer_ilm' => true,
                    'indices' => [
                        ['index_name' => '.ds-app-logs-2026.11.12-000002', 'index_uuid' => 'uuid-2'],
                        ['index_name' => '.ds-app-logs-2026.11.13-000003', 'index_uuid' => 'uuid-3'],
                    ],
                ]]]);
            }
            if (str_contains($request->url(), '_stats/docs,store')) {
                return Http::response(['indices' => [
                    '.ds-app-logs-2026.11.12-000002' => ['primaries' => ['docs' => ['count' => 100]], 'total' => ['store' => ['size_in_bytes' => 1024]]],
                    '.ds-app-logs-2026.11.13-000003' => ['primaries' => ['docs' => ['count' => 200]], 'total' => ['store' => ['size_in_bytes' => 2048]]],
                ]]);
            }
            return Http::response([], 404);
        });

        $response = app(DataStreamController::class)->data();
        $this->assertSame(200, $response->getStatusCode());
        $stream = $response->getData(true)['data_streams'][0];
        $this->assertSame('app-logs', $stream['name']);
        $this->assertSame(3, $stream['generation']);
        $this->assertSame(2, $stream['backing_indices_count']);
        $this->assertSame(300, $stream['documents_count']);
        $this->assertSame(3072, $stream['storage_bytes']);
        $this->assertSame('logs-policy', $stream['ilm_policy']);
    }

    public function test_it_returns_backing_index_lifecycle_details(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '_data_stream/app-logs')) {
                return Http::response(['data_streams' => [[
                    'name' => 'app-logs', 'status' => 'GREEN', 'generation' => 2, 'template' => 'app-template',
                    'ilm_policy' => 'logs-policy', 'indices' => [['index_name' => '.ds-app-logs-2026.11.13-000002', 'index_uuid' => 'uuid-2']],
                ]]]);
            }
            if (str_contains($request->url(), '_stats/docs,store')) {
                return Http::response(['indices' => ['.ds-app-logs-2026.11.13-000002' => ['primaries' => ['docs' => ['count' => 42]], 'total' => ['store' => ['size_in_bytes' => 4096]]]]]);
            }
            if (str_contains($request->url(), '_ilm/explain/')) {
                return Http::response(['indices' => ['.ds-app-logs-2026.11.13-000002' => ['managed' => true, 'policy' => 'logs-policy', 'phase' => 'hot', 'action' => 'rollover', 'step' => 'check-rollover-ready']]]);
            }
            return Http::response([], 404);
        });

        $response = app(DataStreamController::class)->details('app-logs');
        $this->assertSame(200, $response->getStatusCode());
        $stream = $response->getData(true)['data_stream'];
        $this->assertSame('app-logs', $stream['name']);
        $this->assertSame(2, $stream['generation']);
        $this->assertSame('.ds-app-logs-2026.11.13-000002', $stream['backing_indices'][0]['name']);
        $this->assertSame(42, $stream['backing_indices'][0]['documents_count']);
        $this->assertTrue($stream['backing_indices'][0]['lifecycle_managed']);
        $this->assertSame('hot', $stream['backing_indices'][0]['lifecycle_phase']);
    }

    public function test_it_returns_an_empty_list_when_cluster_has_no_data_streams(): void
    {
        Http::fake(['*/_data_stream' => Http::response(['data_streams' => []])]);
        $response = app(DataStreamController::class)->data();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $response->getData(true)['data_streams']);
    }
}
