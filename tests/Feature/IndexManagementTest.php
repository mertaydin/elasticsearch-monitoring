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
    }

    public function test_it_returns_mapping_and_settings_for_an_index(): void
    {
        Http::fake(function ($request) {
            if (str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/_mapping')) {
                return Http::response(['applications' => ['mappings' => ['properties' => ['title' => ['type' => 'text']]]]]);
            }

            if (str_contains($request->url(), '_settings')) {
                return Http::response(['applications' => ['settings' => ['index.number_of_shards' => '1']]]);
            }

            if (str_contains($request->url(), '_ilm/explain/')) {
                return Http::response(['applications' => ['managed' => false]]);
            }

            return Http::response([], 404);
        });

        $response = app(IndexController::class)->details('applications');

        $this->assertSame(200, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertSame('text', $payload['mapping']['properties']['title']['type']);
        $this->assertSame('1', $payload['settings']['index.number_of_shards']);
    }
}
