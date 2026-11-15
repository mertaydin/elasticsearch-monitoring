<?php
namespace ElasticsearchMonitoring\Tests\Feature;
use ElasticsearchMonitoring\Models\ApiRequestLog;
use ElasticsearchMonitoring\Services\ElasticsearchClient;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
class ApiRequestHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::dropIfExists('elasticsearch_api_request_logs');
        Schema::create('elasticsearch_api_request_logs', function (Blueprint $table): void {
            $table->id(); $table->string('method', 8); $table->string('endpoint', 255);
            $table->unsignedSmallInteger('status_code')->nullable(); $table->decimal('duration_ms', 10, 2);
            $table->boolean('successful')->index(); $table->text('error')->nullable(); $table->timestamps();
            $table->index('created_at'); $table->index(['endpoint', 'created_at']);
        });
    }
    public function test_it_records_successful_requests_without_query_parameters(): void
    {
        Http::fake(['*' => Http::response(['cluster_name' => 'test'], 200)]);
        config(['elasticsearch-monitoring.connection.url' => 'http://localhost:9200']);
        $result = app(ElasticsearchClient::class)->request('/_cluster/health?secret=value');
        $this->assertSame('test', $result['cluster_name']);
        $this->assertDatabaseHas('elasticsearch_api_request_logs', ['method'=>'GET','endpoint'=>'/_cluster/health','status_code'=>200,'successful'=>true]);
        $this->assertDatabaseMissing('elasticsearch_api_request_logs', ['endpoint'=>'/_cluster/health?secret=value']);
    }
    public function test_it_records_failed_http_requests(): void
    {
        Http::fake(['*' => Http::response(['error'=>'unavailable'], 503)]);
        config(['elasticsearch-monitoring.connection.url' => 'http://localhost:9200']);
        try {
            app(ElasticsearchClient::class)->request('/_cluster/health');
            $this->fail('Expected an HTTP exception.');
        } catch (RequestException) {
            $this->assertDatabaseHas('elasticsearch_api_request_logs', ['endpoint'=>'/_cluster/health','status_code'=>503,'successful'=>false]);
        }
    }
    public function test_it_returns_summary_and_endpoint_rankings(): void
    {
        ApiRequestLog::query()->create(['method'=>'GET','endpoint'=>'/_cluster/health','status_code'=>200,'duration_ms'=>12,'successful'=>true]);
        ApiRequestLog::query()->create(['method'=>'GET','endpoint'=>'/_cat/indices','status_code'=>500,'duration_ms'=>120,'successful'=>false]);
        $payload = app(\ElasticsearchMonitoring\Http\Controllers\ApiRequestHistoryController::class)->data()->getData(true);
        $this->assertSame(2, $payload['summary']['total']);
        $this->assertSame(1, $payload['summary']['failed']);
        $this->assertSame('/_cat/indices', $payload['slowest_endpoints'][0]['endpoint']);
    }
}
