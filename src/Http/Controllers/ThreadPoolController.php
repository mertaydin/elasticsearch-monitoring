<?php

namespace ElasticsearchMonitoring\\Http\\Controllers;

use ElasticsearchMonitoring\\Models\\MetricSnapshot;
use Illuminate\\Http\\JsonResponse;
use Illuminate\\View\\View;

class ThreadPoolController
{
    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::thread-pools';

        return view($viewName);
    }

    public function data(): JsonResponse
    {
        $snapshots = MetricSnapshot::query()
            ->latest('collected_at')
            ->limit(1440)
            ->get()
            ->reverse()
            ->values();

        $nodes = [];
        $previous = [];

        foreach ($snapshots as $snapshot) {
            $collectedAt = $snapshot->collected_at;
            if ($collectedAt === null) {
                continue;
            }

            foreach ((array) data_get($snapshot->metrics, 'node_details', []) as $node) {
                if (! is_array($node)) {
                    continue;
                }

                $nodeId = (string) ($node['id'] ?? '');
                if ($nodeId === '') {
                    continue;
                }

                $nodes[$nodeId] ??= [
                    'id' => $nodeId,
                    'name' => (string) ($node['name'] ?? $nodeId),
                    'roles' => (array) ($node['roles'] ?? []),
                    'pools' => [],
                ];
                $nodes[$nodeId]['name'] = (string) ($node['name'] ?? $nodes[$nodeId]['name']);
                $nodes[$nodeId]['roles'] = (array) ($node['roles'] ?? $nodes[$nodeId]['roles']);

                foreach ((array) ($node['thread_pools'] ?? []) as $pool) {
                    if (! is_array($pool) || empty($pool['name'])) {
                        continue;
                    }

                    $poolName = (string) $pool['name'];
                    $point = [
                        'collected_at' => $collectedAt->toIso8601String(),
                        'threads' => (int) ($pool['threads'] ?? 0),
                        'queue' => (int) ($pool['queue'] ?? 0),
                        'active' => (int) ($pool['active'] ?? 0),
                        'rejected' => (int) ($pool['rejected'] ?? 0),
                        'largest' => (int) ($pool['largest'] ?? 0),
                        'completed' => (int) ($pool['completed'] ?? 0),
                        'rejected_per_second' => null,
                        'completed_per_second' => null,
                    ];

                    $previousKey = $nodeId.'|'.$poolName;
                    if (isset($previous[$previousKey])) {
                        $old = $previous[$previousKey];
                        $seconds = max(0.001, (float) $old['at']->diffInMilliseconds($collectedAt) / 1000);
                        if ($point['rejected'] >= $old['rejected']) {
                            $point['rejected_per_second'] = round(($point['rejected'] - $old['rejected']) / $seconds, 3);
                        }
                        if ($point['completed'] >= $old['completed']) {
                            $point['completed_per_second'] = round(($point['completed'] - $old['completed']) / $seconds, 2);
                        }
                    }

                    $nodes[$nodeId]['pools'][$poolName] ??= ['name' => $poolName, 'history' => []];
                    $nodes[$nodeId]['pools'][$poolName]['history'][] = $point;
                    $previous[$previousKey] = [
                        'at' => $collectedAt,
                        'rejected' => $point['rejected'],
                        'completed' => $point['completed'],
                    ];
                }
            }
        }

        foreach ($nodes as &$node) {
            $node['pools'] = array_values($node['pools']);
            foreach ($node['pools'] as &$pool) {
                $history = $pool['history'];
                $latest = $history === [] ? [] : $history[array_key_last($history)];
                $pool['latest'] = $latest;
                $pool['status'] = ((int) ($latest['queue'] ?? 0) > 0 && (float) ($latest['rejected_per_second'] ?? 0) > 0)
                    ? 'saturated'
                    : (((int) ($latest['queue'] ?? 0) > 0) ? 'busy' : 'healthy');
            }
            unset($pool);
        }
        unset($node);

        $result = array_values($nodes);
        usort($result, static fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));

        return response()->json([
            'nodes' => $result,
            'meta' => [
                'snapshots' => $snapshots->count(),
                'limit' => 1440,
                'latest_collected_at' => $snapshots->last()?->collected_at?->toIso8601String(),
            ],
        ]);
    }
}
