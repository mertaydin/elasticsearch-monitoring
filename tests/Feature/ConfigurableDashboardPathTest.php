<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Tests\TestCase;

class ConfigurableDashboardPathTest extends TestCase
{
    public function test_dashboard_path_supports_nested_paths_and_slash_normalization(): void
    {
        config([
            'elasticsearch-monitoring.dashboard.path' => '/panel/super/elasticsearch/',
        ]);

        require dirname(__DIR__, 2).'/routes/web.php';

        $route = collect(app('router')->getRoutes())->first(
            fn ($route): bool => $route->getName() === 'elasticsearch-monitoring.index'
                && $route->uri() === 'panel/super/elasticsearch'
        );

        $this->assertNotNull($route);
        $this->assertContains('web', $route->middleware());
        $this->assertContains('auth', $route->middleware());
        $this->assertNotContains('super-admin', $route->middleware());
    }
}
