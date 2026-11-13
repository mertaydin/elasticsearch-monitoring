<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Models\AlertEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertHistoryController
{
    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::alerts';

        return view($viewName);
    }

    public function data(Request $request): JsonResponse
    {
        $status = (string) $request->query('status', 'all');
        $query = AlertEvent::query()->latest('first_seen_at');

        if ($status === 'active') {
            $query->whereNull('resolved_at');
        } elseif ($status === 'resolved') {
            $query->whereNotNull('resolved_at');
        }

        $events = $query->limit(500)->get()->map(static fn (AlertEvent $event): array => [
            'id' => $event->id,
            'code' => $event->code,
            'severity' => $event->severity,
            'title' => $event->title,
            'message' => $event->message,
            'value' => $event->value,
            'threshold' => $event->threshold,
            'first_seen_at' => $event->first_seen_at?->toIso8601String(),
            'last_seen_at' => $event->last_seen_at?->toIso8601String(),
            'resolved_at' => $event->resolved_at?->toIso8601String(),
            'occurrences' => $event->occurrences,
            'status' => $event->resolved_at === null ? 'active' : 'resolved',
        ]);

        return response()->json([
            'events' => $events,
            'summary' => [
                'active' => AlertEvent::query()->whereNull('resolved_at')->count(),
                'resolved' => AlertEvent::query()->whereNotNull('resolved_at')->count(),
                'total' => AlertEvent::query()->count(),
            ],
        ]);
    }
}
