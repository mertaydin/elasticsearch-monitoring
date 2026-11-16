<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class JsonViewerTest extends TestCase
{
    public function test_json_viewer_is_a_local_get_only_tool(): void
    {
        $route = Route::getRoutes()->getByName('elasticsearch-monitoring.tools.json');

        $this->assertNotNull($route);
        $this->assertSame(['GET', 'HEAD'], $route->methods());
        $this->assertSame('monitoring/elasticsearch/tools/json', $route->uri());
    }

    public function test_json_viewer_renders_without_an_elasticsearch_request(): void
    {
        $this->view('elasticsearch-monitoring::tools.json-viewer', ['locale' => 'en'])
            ->assertSee('JSON Viewer')
            ->assertSee('Your data is never uploaded')
            ->assertSee('navigator.clipboard')
            ->assertDontSee('fetch(');
    }
}
