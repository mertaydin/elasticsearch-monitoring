<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Services\ElasticsearchClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Throwable;

class SnapshotController
{
    public function __construct(private readonly ElasticsearchClient $client) {}

    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::snapshots';

        return view($viewName);
    }

    public function data(): JsonResponse
    {
        $connection = (array) config('elasticsearch-monitoring.connection', []);
        $cacheKey = 'elasticsearch-monitoring:snapshots:'.hash('sha256', implode('|', [
            (string) ($connection['url'] ?? ''),
            (string) ($connection['username'] ?? ''),
            (string) ($connection['password'] ?? ''),
            (string) ($connection['api_key'] ?? ''),
        ]));

        $payload = Cache::remember($cacheKey, now()->addSeconds(60), function (): array {
            try {
                $repositories = $this->client->request('_snapshot');
            } catch (Throwable) {
                return [
                    'available' => false,
                    'message' => 'Snapshot monitoring is unavailable. Check the monitor_snapshot cluster privilege.',
                    'repositories' => [],
                    'snapshots' => [],
                    'summary' => ['repositories' => 0, 'snapshots' => 0, 'successful' => 0, 'partial' => 0, 'failed' => 0, 'in_progress' => 0],
                    'meta' => ['cached_for_seconds' => 60, 'latest_successful' => null],
                ];
            }

            $repositoryRows = [];
            $allSnapshots = [];
            $summary = ['repositories' => count($repositories), 'snapshots' => 0, 'successful' => 0, 'partial' => 0, 'failed' => 0, 'in_progress' => 0];
            $latestSuccessful = null;

            foreach ($repositories as $name => $repository) {
                $name = (string) $name;
                if (! is_array($repository)) {
                    continue;
                }

                $repositoryRow = [
                    'name' => $name,
                    'type' => (string) ($repository['type'] ?? 'unknown'),
                    'total_snapshots' => 0,
                    'snapshots' => [],
                    'latest_successful' => null,
                    'error' => null,
                ];

                try {
                    $response = $this->client->request('_snapshot/'.rawurlencode($name).'/*?verbose=true&size=50&sort=start_time&order=desc');
                    $snapshots = array_values(array_filter((array) ($response['snapshots'] ?? []), 'is_array'));
                    $repositoryRow['total_snapshots'] = (int) ($response['total'] ?? count($snapshots));
                    $summary['snapshots'] += $repositoryRow['total_snapshots'];

                    foreach ($snapshots as $snapshot) {
                        $normalized = $this->normalizeSnapshot($name, $snapshot);
                        $repositoryRow['snapshots'][] = $normalized;
                        $allSnapshots[] = $normalized;
                        if ($normalized['state'] === 'SUCCESS') {
                            $summary['successful']++;
                        } elseif ($normalized['state'] === 'PARTIAL') {
                            $summary['partial']++;
                        } elseif (in_array($normalized['state'], ['FAILED', 'INCOMPATIBLE'], true)) {
                            $summary['failed']++;
                        } elseif ($normalized['state'] === 'IN_PROGRESS') {
                            $summary['in_progress']++;
                        }

                        if ($normalized['state'] === 'SUCCESS' && ($repositoryRow['latest_successful'] === null || $normalized['start_time_in_millis'] > $repositoryRow['latest_successful']['start_time_in_millis'])) {
                            $repositoryRow['latest_successful'] = $normalized;
                        }
                    }

                    if ($repositoryRow['latest_successful'] === null) {
                        $successfulResponse = $this->client->request('_snapshot/'.rawurlencode($name).'/*?verbose=true&size=1&sort=start_time&order=desc&state=SUCCESS');
                        $successfulSnapshots = array_values(array_filter((array) ($successfulResponse['snapshots'] ?? []), 'is_array'));
                        if ($successfulSnapshots !== []) {
                            $repositoryRow['latest_successful'] = $this->normalizeSnapshot($name, $successfulSnapshots[0]);
                        }
                    }

                    if ($repositoryRow['latest_successful'] !== null && ($latestSuccessful === null || $repositoryRow['latest_successful']['start_time_in_millis'] > $latestSuccessful['start_time_in_millis'])) {
                        $latestSuccessful = $repositoryRow['latest_successful'];
                    }
                } catch (Throwable) {
                    $repositoryRow['error'] = 'Unable to read snapshot history. Check repository availability and monitor_snapshot privileges.';
                }

                $repositoryRows[] = $repositoryRow;
            }

            usort($allSnapshots, static fn (array $left, array $right): int => $right['start_time_in_millis'] <=> $left['start_time_in_millis']);

            return [
                'available' => true,
                'message' => null,
                'repositories' => $repositoryRows,
                'snapshots' => array_slice($allSnapshots, 0, 100),
                'summary' => $summary,
                'meta' => [
                    'cached_for_seconds' => 60,
                    'latest_successful' => $latestSuccessful,
                ],
            ];
        });

        return response()->json($payload);
    }

    /** @param array<string, mixed> $snapshot
     * @return array<string, mixed>
     */
    private function normalizeSnapshot(string $repository, array $snapshot): array
    {
        $shards = (array) ($snapshot['shards'] ?? []);
        $failures = (array) ($snapshot['failures'] ?? []);

        return [
            'repository' => $repository,
            'name' => (string) ($snapshot['snapshot'] ?? 'unknown'),
            'uuid' => (string) ($snapshot['uuid'] ?? ''),
            'state' => strtoupper((string) ($snapshot['state'] ?? 'UNKNOWN')),
            'start_time' => $snapshot['start_time'] ?? null,
            'start_time_in_millis' => (int) ($snapshot['start_time_in_millis'] ?? 0),
            'end_time' => $snapshot['end_time'] ?? null,
            'end_time_in_millis' => (int) ($snapshot['end_time_in_millis'] ?? 0),
            'duration_in_millis' => (int) ($snapshot['duration_in_millis'] ?? 0),
            'indices_count' => count((array) ($snapshot['indices'] ?? [])),
            'data_streams_count' => count((array) ($snapshot['data_streams'] ?? [])),
            'shards_total' => (int) ($shards['total'] ?? 0),
            'shards_successful' => (int) ($shards['successful'] ?? 0),
            'shards_failed' => (int) ($shards['failed'] ?? 0),
            'failure_count' => count($failures),
            'failure_reasons' => array_slice(array_values(array_filter(array_map(
                static fn ($failure): string => is_array($failure) ? (string) ($failure['reason'] ?? '') : '',
                $failures
            ))), 0, 3),
        ];
    }
}
