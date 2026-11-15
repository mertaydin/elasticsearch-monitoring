<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\MetricSnapshot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController
{
    private const MAX_HISTORY_POINTS = 2000;

    private const AGGREGATED_METRIC_PATHS = [
        'cpu.percent_average',
        'filesystem.total_bytes',
        'filesystem.available_bytes',
        'filesystem.used_bytes',
        'indices.documents',
        'indices.store_size_bytes',
        'indices.primary_store_size_bytes',
        'indices.segments_total',
        'indices.segments_primaries',
        'cluster.active_shards',
        'cluster.unassigned_shards',
        'rates.search_per_second',
        'rates.indexing_per_second',
    ];

    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::dashboard';

        return view($viewName);
    }

    public function data(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'compare' => ['sometimes', 'boolean'],
        ]);

        $to = isset($validated['to']) ? Carbon::parse($validated['to']) : now();
        $from = isset($validated['from']) ? Carbon::parse($validated['from']) : $to->copy()->subHour();

        if ($from->diffInDays($to) > (int) config('elasticsearch-monitoring.collection.retention_days', 90)) {
            return response()->json(['message' => 'The selected date range exceeds the configured metric retention period.'], 422);
        }

        $latest = MetricSnapshot::query()->latest('collected_at')->first();
        $query = MetricSnapshot::query()->whereBetween('collected_at', [$from, $to])->orderBy('collected_at');
        $total = (clone $query)->count();
        $durationSeconds = max(0, $to->getTimestamp() - $from->getTimestamp());
        $aggregation = $durationSeconds > 7 * 86400 ? 'daily' : ($durationSeconds > 86400 ? 'hourly' : 'raw');

        $history = $this->buildHistory($query, $aggregation, $total);

        $comparison = ['history' => [], 'range' => null];
        if ($request->boolean('compare')) {
            $comparisonFrom = $from->copy()->subSeconds(max(1, $durationSeconds));
            $comparisonTo = $from->copy()->subSecond();
            $comparisonQuery = MetricSnapshot::query()
                ->whereBetween('collected_at', [$comparisonFrom, $comparisonTo])
                ->orderBy('collected_at');
            $comparisonTotal = (clone $comparisonQuery)->count();
            $comparisonHistory = $this->buildHistory($comparisonQuery, $aggregation, $comparisonTotal);

            foreach ($comparisonHistory as &$point) {
                $point['collected_at'] = Carbon::parse($point['collected_at'])
                    ->addSeconds($durationSeconds)
                    ->toIso8601String();
            }
            unset($point);

            $comparison = [
                'history' => $comparisonHistory,
                'range' => [
                    'from' => $comparisonFrom->toIso8601String(),
                    'to' => $comparisonTo->toIso8601String(),
                    'total' => $comparisonTotal,
                    'returned' => count($comparisonHistory),
                ],
            ];
        }

        return response()->json([
            'latest' => $latest,
            'history' => $history,
            'comparison' => $comparison,
            'range' => [
                'from' => $from->toIso8601String(),
                'to' => $to->toIso8601String(),
                'total' => $total,
                'returned' => count($history),
                'aggregation' => $aggregation,
            ],
        ]);
    }

    /**
     * @param  Builder<MetricSnapshot>  $query
     * @return array<int, array{collected_at: string, metrics: array<string, mixed>}>
     */
    private function buildHistory($query, string $aggregation, int $total): array
    {
        if ($aggregation !== 'raw') {
            return $this->aggregateHistory($query, $aggregation);
        }

        $step = max(1, (int) ceil($total / self::MAX_HISTORY_POINTS));
        $history = [];
        $position = 0;

        foreach ($query->cursor() as $snapshot) {
            if ($position % $step === 0 || $position === $total - 1) {
                $history[] = [
                    'collected_at' => $snapshot->collected_at?->toIso8601String(),
                    'metrics' => $snapshot->metrics,
                ];
            }
            $position++;
        }

        return $history;
    }

    /**
     * @param  Builder<MetricSnapshot>  $query
     * @return array<int, array{collected_at: string, metrics: array<string, mixed>}>
     */
    private function aggregateHistory($query, string $aggregation): array
    {
        $buckets = [];

        foreach ($query->cursor() as $snapshot) {
            $collectedAt = $snapshot->collected_at;
            if ($collectedAt === null) {
                continue;
            }

            $bucketTime = $aggregation === 'daily'
                ? $collectedAt->copy()->startOfDay()
                : $collectedAt->copy()->startOfHour();
            $bucketKey = $bucketTime->toDateTimeString();

            if (! isset($buckets[$bucketKey])) {
                $buckets[$bucketKey] = [
                    'collected_at' => $bucketTime->toIso8601String(),
                    'sums' => [],
                    'counts' => [],
                ];
            }

            $metrics = (array) $snapshot->metrics;
            foreach (self::AGGREGATED_METRIC_PATHS as $path) {
                $value = data_get($metrics, $path);
                if (! is_numeric($value)) {
                    continue;
                }

                $buckets[$bucketKey]['sums'][$path] = ($buckets[$bucketKey]['sums'][$path] ?? 0) + (float) $value;
                $buckets[$bucketKey]['counts'][$path] = ($buckets[$bucketKey]['counts'][$path] ?? 0) + 1;
            }
        }

        $history = [];
        foreach ($buckets as $bucket) {
            $metrics = [];
            foreach ($bucket['sums'] as $path => $sum) {
                data_set($metrics, $path, round($sum / $bucket['counts'][$path], 2));
            }

            $history[] = ['collected_at' => $bucket['collected_at'], 'metrics' => $metrics];
        }

        return $history;
    }
}
