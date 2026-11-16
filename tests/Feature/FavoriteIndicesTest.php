<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\IndexController;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FavoriteIndicesTest extends TestCase
{
    public function test_index_management_page_renders_favorite_controls(): void
    {
        $this->withoutMiddleware()
            ->get('/monitoring/elasticsearch/indices')
            ->assertOk()
            ->assertSee('elasticsearch-monitoring:favorites')
            ->assertSee('favorites-only')
            ->assertSee('Add to favorites', false);
    }

    public function test_dashboard_renders_favorite_quick_access_section(): void
    {
        $this->withoutMiddleware()
            ->get('/monitoring/elasticsearch')
            ->assertOk()
            ->assertSee('favorite-indices-list')
            ->assertSee('elasticsearch-monitoring:favorites')
            ->assertSee('Favorite indices')
            ->assertSee('favorite-index-metrics')
            ->assertSee('favoriteSummaryEndpoint');
    }

    public function test_it_returns_read_only_summaries_for_requested_favorite_indices(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '_cat/indices/applications,events')) {
                return Http::response([
                    ['index' => 'applications', 'health' => 'green', 'status' => 'open', 'docs.count' => '1250', 'store.size' => '2048'],
                    ['index' => 'events', 'health' => 'yellow', 'status' => 'open', 'docs.count' => '500', 'store.size' => '4096'],
                ]);
            }
            if (str_contains($request->url(), 'applications,events/_stats/indexing')) {
                return Http::response(['indices' => [
                    'applications' => ['total' => ['indexing' => ['index_total' => 900, 'index_current' => 2]]],
                    'events' => ['total' => ['indexing' => ['index_total' => 300, 'index_current' => 0]]],
                ]]);
            }
            return Http::response([], 404);
        });

        $request = Request::create('/monitoring/elasticsearch/indices/favorites-summary', 'GET', [
            'indices' => ['applications', 'events'],
        ]);
        $response = app(IndexController::class)->favoriteSummary($request);
        $this->assertSame(200, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertCount(2, $payload['indices']);
        $this->assertSame(1250, $payload['indices'][0]['documents_count']);
        $this->assertSame(2048, $payload['indices'][0]['storage_bytes']);
        $this->assertSame(900, $payload['indices'][0]['indexing']['total']);
        $this->assertSame(2, $payload['indices'][0]['indexing']['current']);
        Http::assertSent(fn ($request) => str_contains($request->url(), '_cat/indices/applications,events'));
        Http::assertSent(fn ($request) => str_contains($request->url(), 'applications,events/_stats/indexing'));
        Http::assertNotSent(fn ($request) => $request->method() !== 'GET');
    }

    public function test_favorite_summary_rejects_unsafe_index_names(): void
    {
        $this->withoutMiddleware()
            ->getJson('/monitoring/elasticsearch/indices/favorites-summary?indices%5B0%5D=%2A')
            ->assertUnprocessable();
    }
}
