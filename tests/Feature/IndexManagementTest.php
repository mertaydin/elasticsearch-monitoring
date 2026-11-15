<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\IndexController;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IndexManagementTest extends TestCase
{
    public function test_it_lists_indices_with_data_stream_information(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '_cat/indices')) {
                return Http::response([[
                    'index' => 'applications',
                    'health' => 'green',
                    'status' => 'open',
                    'pri' => '1',
                    'rep' => '0',
                    'docs.count' => '1972651',
                    'store.size' => '12310000000',
                ]]);
            }

            if (str_contains($request->url(), '_ilm/explain')) {
                return Http::response(['indices' => ['applications' => ['managed' => true, 'policy' => 'applications-policy', 'phase' => 'hot']]]);
            }

            if (str_contains($request->url(), '_data_stream')) {
                return Http::response(['data_streams' => [[
                    'name' => 'application-events',
                    'indices' => [['index_name' => 'applications']],
                ]]]);
            }

            return Http::response([], 404);
        });

        $response = app(IndexController::class)->data(Request::create('/monitoring/elasticsearch/indices/data'));

        $this->assertSame(200, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertSame('applications', $payload['indices'][0]['name']);
        $this->assertSame(1972651, $payload['indices'][0]['documents_count']);
        $this->assertSame('application-events', $payload['indices'][0]['data_stream']);
        $this->assertSame('managed', $payload['indices'][0]['lifecycle_status']);
        $this->assertSame('hot', $payload['indices'][0]['lifecycle_phase']);
        $this->assertSame('applications-policy', $payload['indices'][0]['lifecycle_policy']);
    }

    public function test_it_returns_mapping_and_settings_for_an_index(): void
    {
        Http::fake(function ($request) {
            if (str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/_mapping')) {
                return Http::response(['applications' => ['mappings' => ['properties' => ['title' => ['type' => 'text']]]]]);
            }

            if (str_contains($request->url(), '_settings')) {
                return Http::response(['applications' => ['settings' => ['index.number_of_shards' => '1', 'index.lifecycle.name' => 'applications-policy', 'index.lifecycle.rollover_alias' => 'applications']]]);
            }

            if (str_contains($request->url(), '_ilm/explain/')) {
                return Http::response(['indices' => ['applications' => ['managed' => true, 'policy' => 'applications-policy', 'phase' => 'hot', 'action' => 'rollover', 'step' => 'check-rollover-ready']]]);
            }

            return Http::response([], 404);
        });

        $response = app(IndexController::class)->details('applications');

        $this->assertSame(200, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertSame('text', $payload['mapping']['properties']['title']['type']);
        $this->assertSame('1', $payload['settings']['index.number_of_shards']);
        $this->assertTrue($payload['lifecycle']['managed']);
        $this->assertSame('applications-policy', $payload['lifecycle']['policy']);
        $this->assertSame('applications', $payload['lifecycle']['rollover_alias']);
        $this->assertSame('rollover', $payload['lifecycle']['action']);
    }

    public function test_it_returns_performance_statistics_for_the_selected_index(): void
    {
        Http::fake(function ($request) {
            if (str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/_stats/indexing,search,merge,refresh,flush,query_cache,request_cache,segments,translog')) {
                return Http::response([
                    'indices' => [
                        'applications' => [
                            'primaries' => [
                                'indexing' => ['index_total' => 120, 'index_time_in_millis' => 2400],
                                'search' => ['query_total' => 900, 'query_time_in_millis' => 1800],
                                'merges' => ['total' => 12, 'total_size_in_bytes' => 4096],
                                'refresh' => ['total' => 30],
                                'query_cache' => ['hit_count' => 80, 'miss_count' => 20],
                                'segments' => ['count' => 14, 'memory_in_bytes' => 2048],
                            ],
                            'total' => [
                                'indexing' => ['index_total' => 240, 'delete_total' => 16],
                                'search' => ['query_total' => 1800, 'query_time_in_millis' => 3600],
                                'merges' => ['total' => 24, 'total_size_in_bytes' => 8192],
                                'refresh' => ['total' => 60],
                                'query_cache' => ['hit_count' => 160, 'miss_count' => 40],
                                'request_cache' => ['hit_count' => 10, 'miss_count' => 2],
                                'segments' => ['count' => 28, 'memory_in_bytes' => 4096],
                                'translog' => ['operations' => 150, 'size_in_bytes' => 1024],
                            ],
                        ],
                    ],
                ]);
            }

            return Http::response([], 404);
        });

        $response = app(IndexController::class)->indexingStats('applications');

        $this->assertSame(200, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertSame('applications', $payload['index']);
        $this->assertSame(120, $payload['primaries']['indexing']['index_total']);
        $this->assertSame(1800, $payload['total']['search']['query_total']);
        $this->assertSame(24, $payload['total']['merges']['total']);
        $this->assertSame(160, $payload['total']['query_cache']['hit_count']);
        $this->assertSame(28, $payload['total']['segments']['count']);
        $this->assertSame(150, $payload['total']['translog']['operations']);
    }
}
