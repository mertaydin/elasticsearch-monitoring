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

    public function test_it_returns_indexing_statistics_for_the_selected_index(): void
    {
        Http::fake(function ($request) {
            if (str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/_stats/indexing')) {
                return Http::response([
                    'indices' => [
                        'applications' => [
                            'primaries' => ['indexing' => ['index_total' => 120, 'index_time_in_millis' => 2400, 'index_current' => 2, 'delete_total' => 8, 'delete_time_in_millis' => 160, 'delete_current' => 1, 'noop_update_total' => 3, 'is_throttled' => false, 'throttle_time_in_millis' => 0]],
                            'total' => ['indexing' => ['index_total' => 240, 'index_time_in_millis' => 4800, 'index_current' => 4, 'delete_total' => 16, 'delete_time_in_millis' => 320, 'delete_current' => 2, 'noop_update_total' => 6, 'is_throttled' => false, 'throttle_time_in_millis' => 0]],
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
        $this->assertSame(120, $payload['primaries']['index_total']);
        $this->assertSame(240, $payload['total']['index_total']);
        $this->assertSame(16, $payload['total']['delete_total']);
    }
}