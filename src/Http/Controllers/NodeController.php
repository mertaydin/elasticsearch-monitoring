<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\MetricSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
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
            ->where('collected_at', '>=', now()->subDay())
            ->latest('collected_at')
            ->limit(1440)
            ->get()
            ->reverse()
            ->values();

        $firstSeen = [];
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
                if ($id !== '' && ! isset($firstSeen[$id])) {
                    $firstSeen[$id] = $collectedAt;
                }
            }
        }

        $nodes = [];
        $previous = [];
        $availabilitySamples = array_fill_keys(array_keys($firstSeen), []);

        foreach ($snapshots as $snapshot) {
            $collectedAt = $snapshot->collected_at;
            if ($collectedAt === null) {
                continue;
            }

            $present = [];
            foreach ((array) data_get($snapshot->metrics, 'node_details', []) as $detail) {
                if (! is_array($detail)) {
                    continue;
                }

                $id = (string) ($detail['id'] ?? '');
                if ($id === '') {
                    continue;
                }

                $present[$id] = true;
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
                        'ip' => (string) ($detail['ip'] ?? ''),
                        'roles' => (array) ($detail['roles'] ?? []),
                        'latest' => $point,
                        'history' => [],
                    ];
                }

                $nodes[$id]['name'] = (string) ($detail['name'] ?? $nodes[$id]['name']);
                $nodes[$id]['roles'] = (array) ($detail['roles'] ?? $nodes[$id]['roles']);
                if (! empty($detail['ip'])) {
                    $nodes[$id]['ip'] = (string) $detail['ip'];
                }
                $nodes[$id]['latest'] = $point;
                $nodes[$id]['history'][] = $point;

                $previous[$id] = [
                    'collected_at' => $collectedAt,
                    'point' => $point,
                ];
            }

            foreach ($firstSeen as $id => $seenAt) {
                if ($collectedAt->greaterThanOrEqualTo($seenAt)) {
                    $availabilitySamples[$id][] = [
                        'collected_at' => $collectedAt,
                        'status' => isset($present[$id]) ? 'online' : 'offline',
                    ];
                }
            }
        }

        foreach ($nodes as $id => &$node) {
            $node['availability'] = $this->buildAvailabilityHistory($availabilitySamples[$id] ?? []);
        }
        unset($node);

        $result = array_values($nodes);
        usort($result, static fn (array $left, array $right): int => strcasecmp($left['name'], $right['name']));

        return response()->json([
            'nodes' => $result,
            'meta' => [
                'snapshots' => $snapshots->count(),
                'limit' => 1440,
                'window_hours' => 24,
                'latest_collected_at' => $snapshots->last()?->collected_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * @param  array<int, array{collected_at: Carbon, status: string}>  $samples
     * @return array{status: string, offline_since: string|null, offline_duration_seconds: int, periods: array<int, array{status: string, from: string, to: string, duration_seconds: int}>}
     */
    private function buildAvailabilityHistory(array $samples): array
    {
        if ($samples === []) {
            return [
                'status' => 'unknown',
                'offline_since' => null,
                'offline_duration_seconds' => 0,
                'periods' => [],
            ];
        }

        $periods = [];
        $periodStart = $samples[0]['collected_at'];
        $periodStatus = $samples[0]['status'];
        $lastSampleAt = $samples[0]['collected_at'];

        foreach (array_slice($samples, 1) as $sample) {
            if ($sample['status'] !== $periodStatus) {
                $periodEnd = $sample['collected_at'];
                $periods[] = [
                    'status' => $periodStatus,
                    'from' => $periodStart->toIso8601String(),
                    'to' => $periodEnd->toIso8601String(),
                    'duration_seconds' => max(0, (int) $periodStart->diffInSeconds($periodEnd)),
                ];
                $periodStart = $periodEnd;
                $periodStatus = $sample['status'];
            }

            $lastSampleAt = $sample['collected_at'];
        }

        $now = now();
        $periodEnd = $now->greaterThan($lastSampleAt) ? $now : $lastSampleAt;
        $periods[] = [
            'status' => $periodStatus,
            'from' => $periodStart->toIso8601String(),
            'to' => $periodEnd->toIso8601String(),
            'duration_seconds' => max(0, (int) $periodStart->diffInSeconds($periodEnd)),
        ];

        $offlineSince = $periodStatus === 'offline' ? $periodStart->toIso8601String() : null;
        $offlineDuration = $periodStatus === 'offline'
            ? max(0, (int) $periodStart->diffInSeconds($now))
            : 0;

        return [
            'status' => $periodStatus,
            'offline_since' => $offlineSince,
            'offline_duration_seconds' => $offlineDuration,
            'periods' => $periods,
        ];
    }
}
