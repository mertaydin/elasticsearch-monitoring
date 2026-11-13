<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\MetricSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController
{
    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::dashboard';

        return view($viewName);
    }

    public function data(): JsonResponse
    {
        $latest = MetricSnapshot::query()->latest('collected_at')->first();
        $snapshots = MetricSnapshot::query()
            ->latest('collected_at')
            ->limit(60)
            ->get()
            ->reverse()
            ->values();

        $history = $snapshots->map(static function ($snapshot): array {
            /** @var MetricSnapshot $snapshot */
            return [
                'collected_at' => $snapshot->collected_at?->toIso8601String(),
                'metrics' => $snapshot->metrics,
            ];
        });

        return response()->json([
            'latest' => $latest,
            'history' => $history,
        ]);
    }
}
