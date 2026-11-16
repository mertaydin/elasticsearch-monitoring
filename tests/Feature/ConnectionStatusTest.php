<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ConnectionStatusTest extends TestCase
{
    public function test_it_reports_a_successful_elasticsearch_connection(): void
    {
        Http::fake([
            '*' => Http::response(['cluster_name' => 'test-cluster', 'version' => ['number' => '8.19.0']], 200),
        ]);

        $this->withoutMiddleware();
        $response = $this->getJson('/monitoring/elasticsearch/connection');

        $response->assertOk()
            ->assertJsonPath('status', 'connected')
            ->assertJsonPath('error', null);

        Http::assertSent(fn ($request): bool => $request->method() === 'GET' && str_starts_with($request->url(), 'http://127.0.0.1:9200'));
    }

    public function test_it_reports_a_disconnected_elasticsearch_connection(): void
    {
        Http::fake(['*' => Http::failedConnection()]);

        $this->withoutMiddleware();
        $response = $this->getJson('/monitoring/elasticsearch/connection');

        $response->assertOk()
            ->assertJsonPath('status', 'disconnected')
            ->assertJsonPath('error', 'ConnectionException');
    }
}
