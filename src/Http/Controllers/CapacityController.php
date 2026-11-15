<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\MetricSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CapacityController
{
    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::capacity';

        return view($viewName);
    }

    public function data(Request $request): JsonResponse
    {
        $days = (int) $request->query('days', 30);
        if (! in_array($days, [7, 30, 90], true)) {
            $days = 30;
        }

        $bucketFormat = $days === 7 ? 'Y-m-d H:00' : 'Y-m-d';
        $snapshots = MetricSnapshot::query()->where('collected_at', '>=', now()->subDays($days))->orderBy('collected_at')->cursor();
        $clusterHistory = [];
        $nodeBuckets = [];
        $indexBuckets = [];
        $latest = null;
        $snapshotCount = 0;

        foreach ($snapshots as $snapshot) {
            $snapshotCount++;
            $collectedAt = $snapshot->collected_at;
            if ($collectedAt === null) {
                continue;
            }

            $timestamp = $collectedAt->toIso8601String();
            $bucket = $collectedAt->format($bucketFormat);
            $metrics = (array) $snapshot->metrics;
            $latest = ['collected_at' => $timestamp, 'metrics' => $metrics];
            $clusterHistory[$bucket] = [
                'collected_at' => $timestamp,
                'documents' => (int) data_get($metrics, 'indices.documents', 0),
                'storage_bytes' => (int) data_get($metrics, 'indices.primary_store_size_bytes', 0),
                'filesystem_used_bytes' => (int) data_get($metrics, 'filesystem.used_bytes', 0),
                'filesystem_total_bytes' => (int) data_get($metrics, 'filesystem.total_bytes', 0),
            ];

            foreach ((array) data_get($metrics, 'node_details', []) as $node) {
                if (! is_array($node) || empty($node['id'])) {
                    continue;
                }
                $id = (string) $node['id'];
                $nodeBuckets[$id]['name'] = (string) ($node['name'] ?? $id);
                $nodeBuckets[$id]['roles'] = (array) ($node['roles'] ?? []);
                $nodeBuckets[$id]['history'][$bucket] = [
                    'collected_at' => $timestamp,
                    'used_bytes' => (int) ($node['filesystem_used_bytes'] ?? 0),
                    'total_bytes' => (int) ($node['filesystem_total_bytes'] ?? 0),
                    'available_bytes' => (int) ($node['filesystem_available_bytes'] ?? 0),
                    'used_percent' => isset($node['filesystem_used_percent']) ? (float) $node['filesystem_used_percent'] : null,
                ];
            }

            foreach ((array) data_get($metrics, 'index_details', []) as $index) {
                if (! is_array($index) || empty($index['name'])) {
                    continue;
                }
                $name = (string) $index['name'];
                $indexBuckets[$name]['history'][$bucket] = [
                    'collected_at' => $timestamp,
                    'documents' => (int) ($index['documents'] ?? 0),
                    'storage_bytes' => (int) ($index['storage_bytes'] ?? 0),
                ];
            }
        }

        $history = array_values($clusterHistory);
        $nodes = [];
        foreach ($nodeBuckets as $id => $node) {
            $points = array_values($node['history']);
            $nodes[] = [
                'id' => $id, 'name' => $node['name'], 'roles' => $node['roles'],
                'latest' => $points === [] ? null : $points[array_key_last($points)],
                'history' => $points, 'forecast' => $this->forecast($points),
            ];
        }
        usort($nodes, static fn (array $left, array $right): int => strcasecmp($left['name'], $right['name']));

        $fastestIndices = [];
        foreach ($indexBuckets as $name => $index) {
            $points = array_values($index['history']);
            if (count($points) < 2) {
                continue;
            }
            $first = $points[0];
            $last = $points[array_key_last($points)];
            $elapsedDays = max(1 / 24, (strtotime($last['collected_at']) - strtotime($first['collected_at'])) / 86400);
            $fastestIndices[] = [
                'name' => $name,
                'documents' => $last['documents'],
                'storage_bytes' => $last['storage_bytes'],
                'storage_growth_per_day' => (int) round(($last['storage_bytes'] - $first['storage_bytes']) / $elapsedDays),
                'document_growth_per_day' => (int) round(($last['documents'] - $first['documents']) / $elapsedDays),
            ];
        }
        usort($fastestIndices, static fn (array $left, array $right): int => $right['storage_growth_per_day'] <=> $left['storage_growth_per_day']);

        $first = $history[0] ?? null;
        $last = $history === [] ? null : $history[array_key_last($history)];

        return response()->json([
            'period_days' => $days,
            'meta' => [
                'snapshots' => $snapshotCount,
                'buckets' => count($history),
                'latest_collected_at' => $latest['collected_at'] ?? null,
                'retention_days' => (int) config('elasticsearch-monitoring.collection.retention_days', 90),
            ],
            'summary' => [
                'documents' => (int) data_get($latest, 'metrics.indices.documents', 0),
                'storage_bytes' => (int) data_get($latest, 'metrics.indices.primary_store_size_bytes', 0),
                'filesystem_used_bytes' => (int) data_get($latest, 'metrics.filesystem.used_bytes', 0),
                'filesystem_total_bytes' => (int) data_get($latest, 'metrics.filesystem.total_bytes', 0),
                'storage_growth_bytes' => $first !== null && $last !== null ? $last['storage_bytes'] - $first['storage_bytes'] : null,
                'document_growth' => $first !== null && $last !== null ? $last['documents'] - $first['documents'] : null,
            ],
            'history' => $history,
            'nodes' => $nodes,
            'fastest_indices' => array_slice($fastestIndices, 0, 20),
        ]);
    }

    /** @param array<int, array<string, mixed>> $points
     * @return array<string, mixed>|null
     */
    private function forecast(array $points): ?array
    {
        $valid = array_values(array_filter($points, static fn (array $point): bool => $point['total_bytes'] > 0));
        if (count($valid) < 2) {
            return null;
        }
        $firstTime = strtotime($valid[0]['collected_at']);
        $last = $valid[array_key_last($valid)];
        $elapsedDays = (strtotime($last['collected_at']) - $firstTime) / 86400;
        if ($elapsedDays <= 0) {
            return null;
        }
        $growthPerDay = ($last['used_bytes'] - $valid[0]['used_bytes']) / $elapsedDays;
        $daysToFull = $growthPerDay > 0 ? max(0, ($last['total_bytes'] - $last['used_bytes']) / $growthPerDay) : null;

        return [
            'data_points' => count($valid),
            'elapsed_days' => round($elapsedDays, 2),
            'growth_bytes_per_day' => (int) round($growthPerDay),
            'days_to_full' => $daysToFull === null ? null : round($daysToFull, 1),
            'projected_7d_used_bytes' => max(0, (int) round($last['used_bytes'] + $growthPerDay * 7)),
            'projected_30d_used_bytes' => max(0, (int) round($last['used_bytes'] + $growthPerDay * 30)),
            'projected_90d_used_bytes' => max(0, (int) round($last['used_bytes'] + $growthPerDay * 90)),
            'confidence' => count($valid) >= 7 && $elapsedDays >= 3 ? 'moderate' : 'low',
        ];
    }
}
