<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\MetricSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class QueryActivityController
{
    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::query-activity';

        return view($viewName);
    }

    public function data(): JsonResponse
    {
        $snapshots = MetricSnapshot::query()->latest('collected_at')->limit(1440)->get()->reverse()->values();
        $nodes = [];
        $previous = [];

        foreach ($snapshots as $snapshot) {
            $at = $snapshot->collected_at;
            if ($at === null) {
                continue;
            }

            foreach ((array) data_get($snapshot->metrics, 'node_details', []) as $node) {
                if (! is_array($node) || empty($node['id'])) {
                    continue;
                }

                $id = (string) $node['id'];
                $nodes[$id] ??= [
                    'id' => $id,
                    'name' => (string) ($node['name'] ?? $id),
                    'roles' => (array) ($node['roles'] ?? []),
                    'history' => [],
                ];

                $activity = (array) ($node['search_activity'] ?? []);
                $current = [
                    'query_total' => $activity['query_total'] ?? null,
                    'query_time_in_millis' => $activity['query_time_in_millis'] ?? null,
                    'query_current' => $activity['query_current'] ?? null,
                    'fetch_total' => $activity['fetch_total'] ?? null,
                    'fetch_time_in_millis' => $activity['fetch_time_in_millis'] ?? null,
                    'fetch_current' => $activity['fetch_current'] ?? null,
                    'scroll_total' => $activity['scroll_total'] ?? null,
                    'suggest_total' => $activity['suggest_total'] ?? null,
                    'search_queue' => $node['search_queue'] ?? null,
                    'search_rejected' => $node['search_rejected'] ?? null,
                ];
                $point = [
                    'collected_at' => $at->toIso8601String(),
                    'query_per_second' => null,
                    'fetch_per_second' => null,
                    'scroll_per_second' => null,
                    'suggest_per_second' => null,
                    'rejected_per_second' => null,
                    'query_avg_time_ms' => null,
                    'fetch_avg_time_ms' => null,
                ];

                if (isset($previous[$id])) {
                    $old = $previous[$id];
                    $seconds = max(0.001, (float) $old['at']->diffInMilliseconds($at) / 1000);
                    foreach (['query', 'fetch', 'scroll', 'suggest'] as $metric) {
                        $counter = $metric.'_total';
                        if (is_numeric($current[$counter]) && is_numeric($old[$counter]) && $current[$counter] >= $old[$counter]) {
                            $delta = $current[$counter] - $old[$counter];
                            $point[$metric.'_per_second'] = round($delta / $seconds, 2);
                            if (in_array($metric, ['query', 'fetch'], true) && $delta > 0) {
                                $timeKey = $metric.'_time_in_millis';
                                if (is_numeric($current[$timeKey]) && is_numeric($old[$timeKey]) && $current[$timeKey] >= $old[$timeKey]) {
                                    $point[$metric.'_avg_time_ms'] = round(($current[$timeKey] - $old[$timeKey]) / $delta, 2);
                                }
                            }
                        }
                    }
                    if (is_numeric($current['search_rejected']) && is_numeric($old['search_rejected']) && $current['search_rejected'] >= $old['search_rejected']) {
                        $point['rejected_per_second'] = round(($current['search_rejected'] - $old['search_rejected']) / $seconds, 3);
                    }
                }

                $nodes[$id]['history'][] = array_merge($current, $point);
                $previous[$id] = array_merge($current, ['at' => $at]);
            }
        }

        foreach ($nodes as &$node) {
            $node['latest'] = $node['history'][array_key_last($node['history'])];
        }
        unset($node);

        $result = array_values($nodes);
        usort($result, static fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));
        $summary = [
            'query_per_second' => 0.0,
            'fetch_per_second' => 0.0,
            'active_queries' => 0,
            'active_fetches' => 0,
            'search_queue' => 0,
            'rejected_per_second' => 0.0,
        ];

        foreach ($result as $node) {
            $latest = $node['latest'];
            foreach (['query_per_second', 'fetch_per_second', 'rejected_per_second'] as $key) {
                if (is_numeric($latest[$key] ?? null)) {
                    $summary[$key] += (float) $latest[$key];
                }
            }
            foreach (['query_current' => 'active_queries', 'fetch_current' => 'active_fetches', 'search_queue' => 'search_queue'] as $source => $target) {
                if (is_numeric($latest[$source] ?? null)) {
                    $summary[$target] += (int) $latest[$source];
                }
            }
        }

        return response()->json([
            'nodes' => $result,
            'summary' => $summary,
            'meta' => [
                'snapshots' => $snapshots->count(),
                'limit' => 1440,
                'latest_collected_at' => $snapshots->last()?->collected_at?->toIso8601String(),
            ],
        ]);
    }
}
