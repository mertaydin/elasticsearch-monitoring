<?php

namespace ElasticsearchMonitoring\Services;

class HealthAnalyzer
{
    /** @param array<string, mixed> $metrics
     *  @return array{score:int,status:string,alerts:array<int,array<string,mixed>>}
     */
    public function analyze(array $metrics): array
    {
        $thresholds = (array) config('elasticsearch-monitoring.alerts.thresholds', []);
        $alerts = [];
        $score = 100;
        $status = (string) data_get($metrics, 'cluster.status', 'unknown');
        $unassigned = (int) data_get($metrics, 'cluster.unassigned_shards', 0);
        $cpu = data_get($metrics, 'cpu.percent_average');
        $heapMax = (float) data_get($metrics, 'jvm.heap_max_bytes', 0);
        $heapPercent = $heapMax > 0 ? ((float) data_get($metrics, 'jvm.heap_used_bytes', 0) / $heapMax) * 100 : null;
        $diskTotal = (float) data_get($metrics, 'filesystem.total_bytes', 0);
        $diskPercent = $diskTotal > 0 ? ((float) data_get($metrics, 'filesystem.used_bytes', 0) / $diskTotal) * 100 : null;

        if ($status === 'red') {
            $score -= 40;
            $alerts[] = $this->alert('cluster_red', 'critical', 'Cluster is RED', 'Primary shards are unavailable.', $status, 'green');
        } elseif ($status === 'yellow') {
            $score -= 15;
            $alerts[] = $this->alert('cluster_yellow', 'warning', 'Cluster is YELLOW', 'One or more replica shards are unassigned.', $status, 'green');
        }

        $this->check($alerts, $score, 'unassigned_shards', 'Unassigned shards', $unassigned, (float) ($thresholds['unassigned_shards_warning'] ?? 1), (float) ($thresholds['unassigned_shards_critical'] ?? 5), 'Unassigned shards detected.');
        $this->check($alerts, $score, 'cpu', 'High CPU usage', $cpu, (float) ($thresholds['cpu_warning'] ?? 80), (float) ($thresholds['cpu_critical'] ?? 90), 'Average node CPU utilization is high.');
        $this->check($alerts, $score, 'heap', 'High JVM heap usage', $heapPercent, (float) ($thresholds['heap_warning'] ?? 75), (float) ($thresholds['heap_critical'] ?? 85), 'JVM heap utilization is high.');
        $this->check($alerts, $score, 'disk', 'High disk usage', $diskPercent, (float) ($thresholds['disk_warning'] ?? 80), (float) ($thresholds['disk_critical'] ?? 90), 'Filesystem utilization is high.');

        $rejected = (int) data_get($metrics, 'thread_pool.search_rejected', 0) + (int) data_get($metrics, 'thread_pool.write_rejected', 0);
        if ($rejected > 0) {
            $score -= 10;
            $alerts[] = $this->alert('thread_pool_rejections', 'warning', 'Thread-pool rejections', 'Elasticsearch rejected search or write operations.', $rejected, 0);
        }
        if ((int) data_get($metrics, 'nodes', 0) === 0) {
            $score = 0;
            $alerts[] = $this->alert('no_nodes', 'critical', 'No nodes available', 'No Elasticsearch nodes were reported.', 0, 1);
        }

        $score = max(0, min(100, $score));
        return ['score' => $score, 'status' => $score >= 85 ? 'healthy' : ($score >= 65 ? 'warning' : 'critical'), 'alerts' => $alerts];
    }

    /** @param array<int,array<string,mixed>> $alerts */
    private function check(array &$alerts, int &$score, string $code, string $title, mixed $value, float $warning, float $critical, string $message): void
    {
        if ($value === null || ! is_numeric($value)) return;
        $number = (float) $value;
        if ($number >= $critical) {
            $score -= 20;
            $alerts[] = $this->alert($code, 'critical', $title, $message, round($number, 2), $critical);
        } elseif ($number >= $warning) {
            $score -= 10;
            $alerts[] = $this->alert($code, 'warning', $title, $message, round($number, 2), $warning);
        }
    }

    /** @return array<string,mixed> */
    private function alert(string $code, string $severity, string $title, string $message, int|float|string $value, int|float|string|null $threshold): array
    {
        return compact('code', 'severity', 'title', 'message', 'value', 'threshold');
    }
}
