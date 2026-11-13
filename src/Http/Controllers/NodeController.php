<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\MetricSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NodeController
{
    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::nodes';

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

            foreach ((array) data_get($snapshot->metrics, 'node_details', []) as $detail) {
                if (! is_array($detail)) {
                    continue;
                }

                $id = (string) ($detail['id'] ?? '');
                if ($id === '') {
                    continue;
                }

                $point = [
                    'collected_at' => $collectedAt->toIso8601String(),
                    'cpu_percent' => isset($detail['cpu_percent']) ? (float) $detail['cpu_percent'] : null,
                    'heap_percent' => isset($detail['heap_percent']) ? (float) $detail['heap_percent'] : null,
                    'filesystem_used_percent' => isset($detail['filesystem_used_percent']) ? (float) $detail['filesystem_used_percent'] : null,
                    'documents' => (int) ($detail['documents'] ?? 0),
                    'shards' => (int) ($detail['shards'] ?? 0),
                    'search_total' => (int) ($detail['search_total'] ?? 0),
                    'indexing_total' => (int) ($detail['indexing_total'] ?? 0),
                    'search_queue' => (int) ($detail['search_queue'] ?? 0),
                    'search_rejected' => (int) ($detail['search_rejected'] ?? 0),
                    'write_queue' => (int) ($detail['write_queue'] ?? 0),
                    'write_rejected' => (int) ($detail['write_rejected'] ?? 0),
                    'search_per_second' => null,
                    'indexing_per_second' => null,
                ];

                if (isset($previous[$id])) {
                    $previousPoint = $previous[$id]['point'];
                    $seconds = max(0.001, (float) $previous[$id]['collected_at']->diffInMilliseconds($collectedAt) / 1000);

                    if ($point['search_total'] >= $previousPoint['search_total']) {
                        $point['search_per_second'] = round(($point['search_total'] - $previousPoint['search_total']) / $seconds, 2);
                    }

                    if ($point['indexing_total'] >= $previousPoint['indexing_total']) {
                        $point['indexing_per_second'] = round(($point['indexing_total'] - $previousPoint['indexing_total']) / $seconds, 2);
                    }
                }

                if (! isset($nodes[$id])) {
                    $nodes[$id] = [
                        'id' => $id,
                        'name' => (string) ($detail['name'] ?? $id),
                        'roles' => (array) ($detail['roles'] ?? []),
                        'latest' => $point,
                        'history' => [],
                    ];
                }

                $nodes[$id]['name'] = (string) ($detail['name'] ?? $nodes[$id]['name']);
                $nodes[$id]['roles'] = (array) ($detail['roles'] ?? $nodes[$id]['roles']);
                $nodes[$id]['latest'] = $point;
                $nodes[$id]['history'][] = $point;

                $previous[$id] = [
                    'collected_at' => $collectedAt,
                    'point' => $point,
                ];
            }
        }

        $result = array_values($nodes);
        usort($result, static fn (array $left, array $right): int => strcasecmp($left['name'], $right['name']));

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
