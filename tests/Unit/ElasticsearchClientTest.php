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