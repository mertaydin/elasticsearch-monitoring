<?php

namespace ElasticsearchMonitoring\Console;

use ElasticsearchMonitoring\Services\MetricsCollector;
use Illuminate\Console\Command;
use Throwable;

class CollectMetricsCommand extends Command
{
    protected $signature = 'elasticsearch:collect';

    protected $description = 'Collect Elasticsearch cluster metrics';

    public function handle(MetricsCollector $collector): int
    {
        try {
            $metrics = $collector->collect();
            $this->info('Elasticsearch metrics collected.');
            $this->line('Cluster: '.($metrics['cluster']['name'] ?? 'unknown'));
            $this->line('Status: '.($metrics['cluster']['status'] ?? 'unknown'));

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Metric collection failed: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
