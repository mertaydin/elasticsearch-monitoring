<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Services\HealthAnalyzer;
use ElasticsearchMonitoring\Tests\TestCase;

class HealthAnalyzerTest extends TestCase
{
    public function test_it_reports_cluster_and_resource_alerts_with_a_health_score(): void
    {
        $result = app(HealthAnalyzer::class)->analyze([
            'cluster' => ['status' => 'yellow', 'unassigned_shards' => 2],
            'nodes' => 2,
            'cpu' => ['percent_average' => 92],
            'jvm' => ['heap_used_bytes' => 90, 'heap_max_bytes' => 100],
            'filesystem' => ['used_bytes' => 950, 'total_bytes' => 1000],
            'thread_pool' => ['search_rejected' => 1, 'write_rejected' => 0],
        ]);

        $this->assertSame('critical', $result['status']);
        $this->assertLessThan(65, $result['score']);
        $this->assertContains('cluster_yellow', array_column($result['alerts'], 'code'));
        $this->assertContains('cpu', array_column($result['alerts'], 'code'));
        $this->assertContains('heap', array_column($result['alerts'], 'code'));
        $this->assertContains('disk', array_column($result['alerts'], 'code'));
        $this->assertContains('thread_pool_rejections', array_column($result['alerts'], 'code'));
    }

    public function test_it_returns_a_full_score_for_a_healthy_cluster(): void
    {
        $result = app(HealthAnalyzer::class)->analyze([
            'cluster' => ['status' => 'green', 'unassigned_shards' => 0],
            'nodes' => 3,
            'cpu' => ['percent_average' => 20],
            'jvm' => ['heap_used_bytes' => 40, 'heap_max_bytes' => 100],
            'filesystem' => ['used_bytes' => 400, 'total_bytes' => 1000],
            'thread_pool' => ['search_rejected' => 0, 'write_rejected' => 0],
        ]);

        $this->assertSame(100, $result['score']);
        $this->assertSame('healthy', $result['status']);
        $this->assertSame([], $result['alerts']);
    }
}
