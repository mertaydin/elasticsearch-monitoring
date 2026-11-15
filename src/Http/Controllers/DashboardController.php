<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\MetricSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController
{
    private const MAX_HISTORY_POINTS = 2000;

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
        ]);

        $to = isset($validated['to']) ? Carbon::parse($validated['to']) : now();
        $from = isset($validated['from']) ? Carbon::parse($validated['from']) : $to->copy()->subHour();

        if ($from->diffInDays($to) > (int) config('elasticsearch-monitoring.collection.retention_days', 90)) {
            return response()->json(['message' => 'The selected date range exceeds the configured metric retention period.'], 422);
        }

        $latest = MetricSnapshot::query()->latest('collected_at')->first();
        $query = MetricSnapshot::query()->whereBetween('collected_at', [$from, $to])->orderBy('collected_at');
        $total = (clone $query)->count();
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

        return response()->json([
            'latest' => $latest,
            'history' => $history,
            'range' => ['from' => $from->toIso8601String(), 'to' => $to->toIso8601String(), 'total' => $total, 'returned' => count($history)],
        ]);
    }
}
