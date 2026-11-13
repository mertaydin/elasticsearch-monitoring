<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\MetricSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController
{
    public function index(): View
    {
        return view('elasticsearch-monitoring::dashboard');
    }

    public function data(): JsonResponse
    {
        $latest = MetricSnapshot::query()->latest('collected_at')->first();
        $history = MetricSnapshot::query()
            ->latest('collected_at')
            ->limit(60)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (MetricSnapshot $snapshot): array => [
                'collected_at' => $snapshot->collected_at?->toIso8601String(),
                'metrics' => $snapshot->metrics,
            ]);

        return response()->json([
            'latest' => $latest,
            'history' => $history,
        ]);
    }
}