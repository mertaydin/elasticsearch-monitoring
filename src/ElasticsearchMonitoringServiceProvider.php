<?php

namespace ElasticsearchMonitoring;

use ElasticsearchMonitoring\Console\CollectMetricsCommand;
use ElasticsearchMonitoring\Services\DashboardAuthorizer;
use ElasticsearchMonitoring\Services\ElasticsearchClient;
use ElasticsearchMonitoring\Services\MetricsCollector;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;

class ElasticsearchMonitoringServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/elasticsearch-monitoring.php', 'elasticsearch-monitoring');

        $this->app->singleton(DashboardAuthorizer::class);
        $this->app->singleton(ElasticsearchClient::class);
        $this->app->singleton(MetricsCollector::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'elasticsearch-monitoring');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'elasticsearch-monitoring');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/elasticsearch-monitoring.php' => config_path('elasticsearch-monitoring.php'),
        ], 'elasticsearch-monitoring-config');

        if ($this->app->runningInConsole()) {
            $this->commands([CollectMetricsCommand::class]);
        }

        if (config('elasticsearch-monitoring.register_schedule')) {
            Schedule::command('elasticsearch:collect')->everyMinute()->withoutOverlapping();
        }
    }
}
