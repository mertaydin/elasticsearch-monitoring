<?php

namespace ElasticsearchMonitoring\\Http\\Controllers;

use ElasticsearchMonitoring\\Models\\ApiRequestLog;
use ElasticsearchMonitoring\\Services\\ElasticsearchClient;
use Illuminate\\Http\\JsonResponse;
use Throwable;

class ConnectionStatusController
{
    public function show(ElasticsearchClient $client): JsonResponse
    {
        $startedAt = hrtime(true);
        $status = 'connected';
        $error = null;

        try {
            $client->request('/');
        } catch (Throwable $exception) {
            $status = 'disconnected';
            $error = class_basename($exception);
        }

        $durationMs = round((hrtime(true) - $startedAt) / 1_000_000, 2);
        if ($status === 'connected' && $durationMs >= (int) config('elasticsearch-monitoring.connection.slow_response_ms', 1000)) {
            $status = 'slow';
        }

        $lastSuccessfulAt = ApiRequestLog::query()
            ->where('endpoint', '/')
            ->where('successful', true)
            ->latest('created_at')
            ->value('created_at');

        return response()->json([
            'status' => $status,
            'duration_ms' => $durationMs,
            'last_successful_at' => $lastSuccessfulAt,
            'error' => $error,
        ]);
    }
}
