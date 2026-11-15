<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\ApiRequestLog;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ApiRequestHistoryController
{
    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::api-request-history';

        return view($viewName);
    }

    public function data(): JsonResponse
    {
        $logs = ApiRequestLog::query()->latest()->limit(500)->get();
        $total = ApiRequestLog::query()->count();
        $successful = ApiRequestLog::query()->where('successful', true)->count();

        $endpoints = ApiRequestLog::query()
            ->selectRaw('endpoint, COUNT(*) as request_count, AVG(duration_ms) as average_ms, MAX(duration_ms) as max_ms')
            ->groupBy('endpoint')
            ->orderByDesc('request_count')
            ->limit(10)
            ->get();

        $slowest = ApiRequestLog::query()
            ->selectRaw('endpoint, AVG(duration_ms) as average_ms, COUNT(*) as request_count')
            ->groupBy('endpoint')
            ->orderByDesc('average_ms')
            ->limit(10)
            ->get();

        return response()->json([
            'logs' => $logs->map(static fn (ApiRequestLog $log): array => [
                'id' => $log->id,
                'method' => $log->method,
                'endpoint' => $log->endpoint,
                'status_code' => $log->status_code,
                'duration_ms' => $log->duration_ms,
                'successful' => $log->successful,
                'error' => $log->error,
                'created_at' => $log->created_at?->toIso8601String(),
            ]),
            'summary' => [
                'total' => $total,
                'successful' => $successful,
                'failed' => $total - $successful,
                'average_ms' => round((float) ApiRequestLog::query()->avg('duration_ms'), 2),
            ],
            'top_endpoints' => $endpoints,
            'slowest_endpoints' => $slowest,
            'meta' => [
                'returned' => $logs->count(),
                'limit' => 500,
            ],
        ]);
    }
}
