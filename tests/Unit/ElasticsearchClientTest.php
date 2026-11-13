<?php

namespace ElasticsearchMonitoring\Tests\Unit;

use ElasticsearchMonitoring\Services\ElasticsearchClient;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ElasticsearchClientTest extends TestCase
{
    public function test_it_detects_supported_elasticsearch_versions(): void
    {
        Http::fake([
            '*/' => Http::response([
                'version' => ['number' => '8.19.14'],
            ]),
        ]);

        app(ElasticsearchClient::class)->assertSupportedVersion();

        $this->assertSame('8.19.14', app(ElasticsearchClient::class)->version());
    }

    public function test_it_accepts_the_minimum_supported_elasticsearch_eight_version(): void
    {
        Http::fake([
            '*/' => Http::response([
                'version' => ['number' => '8.18.0'],
            ]),
        ]);

        app(ElasticsearchClient::class)->assertSupportedVersion();

        $this->assertSame('8.18.0', app(ElasticsearchClient::class)->version());
    }

    public function test_it_sends_api_key_authorization_header(): void
    {
        config(['elasticsearch-monitoring.connection.api_key' => 'encoded-api-key']);

        Http::fake([
            '*' => Http::response(['ok' => true]),
        ]);

        app(ElasticsearchClient::class)->request('_cluster/health');

        Http::assertSent(fn ($request): bool => $request->hasHeader(
            'Authorization',
            'ApiKey encoded-api-key'
        ));
    }

    public function test_it_accepts_elasticsearch_nine(): void
    {
        Http::fake([
            '*/' => Http::response([
                'version' => ['number' => '9.5.5'],
            ]),
        ]);

        app(ElasticsearchClient::class)->assertSupportedVersion();

        $this->assertSame(9, app(ElasticsearchClient::class)->majorVersion());
    }

    public function test_it_rejects_unsupported_versions(): void
    {
        Http::fake([
            '*/' => Http::response([
                'version' => ['number' => '8.17.9'],
            ]),
        ]);

        $this->expectException(RuntimeException::class);

        app(ElasticsearchClient::class)->assertSupportedVersion();
    }
}
