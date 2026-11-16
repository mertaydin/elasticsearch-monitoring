<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Services\ElasticsearchClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class IndexController
{
    private readonly ElasticsearchClient $client;

    public function __construct(ElasticsearchClient $client)
    {
        $this->client = $client;
    }

    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::indices';

        return view($viewName);
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-z0-9._-]+$/i'],
        ]);

        try {
            $term = strtolower($validated['q']);
            $query = http_build_query([
                'format' => 'json',
                'bytes' => 'b',
                'h' => 'health,status,index,docs.count,store.size',
                'expand_wildcards' => 'open,closed,hidden',
            ]);
            $rows = $this->client->request('_cat/indices/*'.rawurlencode($term).'*?'.$query);
            $indices = [];

            foreach (array_filter($rows, 'is_array') as $row) {
                $name = (string) ($row['index'] ?? '');
                if ($name === '' || stripos($name, $term) === false) {
                    continue;
                }

                $indices[] = [
                    'name' => $name,
                    'health' => strtolower((string) ($row['health'] ?? 'unknown')),
                    'status' => (string) ($row['status'] ?? 'unknown'),
                    'documents_count' => (int) ($row['docs.count'] ?? 0),
                    'storage_bytes' => (int) ($row['store.size'] ?? 0),
                ];
            }

            usort($indices, static function (array $a, array $b) use ($term): int {
                $aPrefix = str_starts_with(strtolower($a['name']), $term) ? 0 : 1;
                $bPrefix = str_starts_with(strtolower($b['name']), $term) ? 0 : 1;

                return ($aPrefix <=> $bPrefix) ?: strcmp($a['name'], $b['name']);
            });

            return response()->json(['indices' => array_slice($indices, 0, 12)]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to search Elasticsearch indices.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }

    public function data(Request $request): JsonResponse
    {
        try {
            $expand = ['open', 'closed'];
            if ($request->boolean('include_hidden')) {
                $expand[] = 'hidden';
            }
            if ($request->boolean('include_rollup')) {
                $expand[] = 'all';
            }

            $query = http_build_query([
                'format' => 'json',
                'bytes' => 'b',
                'h' => 'health,status,index,pri,rep,docs.count,store.size,creation.date.string',
                'expand_wildcards' => implode(',', array_unique($expand)),
            ]);
            $rows = $this->client->request('_cat/indices?'.$query);
            $dataStreams = [];
            $lifecycleIndices = [];

            try {
                $lifecycleResponse = $this->client->request('_ilm/explain');
                $lifecycleIndices = (array) data_get($lifecycleResponse, 'indices', []);
            } catch (Throwable) {
                // ILM may be disabled or unavailable for users with restricted cluster privileges.
            }

            try {
                $streamResponse = $this->client->request('_data_stream');
                foreach ((array) data_get($streamResponse, 'data_streams', []) as $stream) {
                    foreach ((array) data_get($stream, 'indices', []) as $backingIndex) {
                        $dataStreams[(string) data_get($backingIndex, 'index_name')] = (string) data_get($stream, 'name');
                    }
                }
            } catch (Throwable) {
                // Data stream APIs may be unavailable to users with restricted cluster privileges.
            }

            $indices = array_map(static function (array $row) use ($dataStreams, $lifecycleIndices): array {
                $name = (string) ($row['index'] ?? '');
                $lifecycle = $lifecycleIndices[$name] ?? null;

                return [
                    'name' => $name,
                    'health' => (string) ($row['health'] ?? 'unknown'),
                    'status' => (string) ($row['status'] ?? 'unknown'),
                    'primaries' => (int) ($row['pri'] ?? 0),
                    'replicas' => (int) ($row['rep'] ?? 0),
                    'documents_count' => (int) ($row['docs.count'] ?? 0),
                    'storage_bytes' => (int) ($row['store.size'] ?? 0),
                    'creation_date' => isset($row['creation.date.string']) ? (string) $row['creation.date.string'] : null,
                    'data_stream' => $dataStreams[$name] ?? null,
                    'lifecycle_status' => is_array($lifecycle) ? ((bool) ($lifecycle['managed'] ?? false) ? 'managed' : 'unmanaged') : '—',
                    'lifecycle_phase' => is_array($lifecycle) ? (string) ($lifecycle['phase'] ?? '—') : '—',
                    'lifecycle_policy' => is_array($lifecycle) ? (string) ($lifecycle['policy'] ?? '—') : '—',
                ];
            }, array_filter($rows, 'is_array'));

            return response()->json(['indices' => $indices]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve Elasticsearch indices.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }

    public function problematic(): JsonResponse
    {
        try {
            $query = http_build_query([
                'format' => 'json',
                'bytes' => 'b',
                'h' => 'health,status,index,pri,rep,docs.count,store.size',
                'expand_wildcards' => 'open,closed,hidden',
            ]);
            $rows = $this->client->request('_cat/indices?'.$query);
            $indices = array_values(array_map(static function (array $row): array {
                return [
                    'name' => (string) ($row['index'] ?? ''),
                    'health' => strtolower((string) ($row['health'] ?? 'unknown')),
                    'status' => (string) ($row['status'] ?? 'unknown'),
                    'primaries' => (int) ($row['pri'] ?? 0),
                    'replicas' => (int) ($row['rep'] ?? 0),
                    'documents_count' => (int) ($row['docs.count'] ?? 0),
                    'storage_bytes' => (int) ($row['store.size'] ?? 0),
                ];
            }, array_filter($rows, static fn ($row): bool => is_array($row)
                && in_array(strtolower((string) ($row['health'] ?? '')), ['red', 'yellow'], true)
                && (string) ($row['index'] ?? '') !== '')));

            usort($indices, static function (array $a, array $b): int {
                $healthOrder = ['red' => 0, 'yellow' => 1];

                return ($healthOrder[$a['health']] <=> $healthOrder[$b['health']])
                    ?: strcmp($a['name'], $b['name']);
            });

            return response()->json([
                'indices' => $indices,
                'counts' => [
                    'red' => count(array_filter($indices, static fn (array $index): bool => $index['health'] === 'red')),
                    'yellow' => count(array_filter($indices, static fn (array $index): bool => $index['health'] === 'yellow')),
                    'total' => count($indices),
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve problematic Elasticsearch indices.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }

    public function favoriteSummary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'indices' => ['required', 'array', 'min:1', 'max:30'],
            'indices.*' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9._-]+$/'],
        ]);

        try {
            $requestedNames = array_values(array_unique($validated['indices']));
            $indexPath = implode(',', array_map('rawurlencode', $requestedNames));
            $query = http_build_query(['format' => 'json', 'bytes' => 'b', 'h' => 'health,status,index,docs.count,store.size']);
            $rows = $this->client->request('_cat/indices/'.$indexPath.'?'.$query);
            $availableRows = array_values(array_filter($rows, 'is_array'));

            if ($availableRows === []) {
                return response()->json(['indices' => []]);
            }

            $availableNames = array_values(array_filter(array_map(
                static fn (array $row): string => (string) ($row['index'] ?? ''),
                $availableRows
            )));
            $availablePath = implode(',', array_map('rawurlencode', $availableNames));
            $stats = $this->client->request($availablePath.'/_stats/indexing');
            $indices = [];

            foreach ($availableRows as $row) {
                $name = (string) ($row['index'] ?? '');
                if ($name === '') {
                    continue;
                }

                $indexStats = (array) (($stats['indices'] ?? [])[$name] ?? []);
                $totalIndexing = (array) data_get($indexStats, 'total.indexing', []);
                $indices[] = [
                    'name' => $name,
                    'health' => (string) ($row['health'] ?? 'unknown'),
                    'status' => (string) ($row['status'] ?? 'unknown'),
                    'documents_count' => (int) ($row['docs.count'] ?? 0),
                    'storage_bytes' => (int) ($row['store.size'] ?? 0),
                    'indexing' => [
                        'total' => (int) ($totalIndexing['index_total'] ?? 0),
                        'current' => (int) ($totalIndexing['index_current'] ?? 0),
                    ],
                ];
            }

            return response()->json(['indices' => $indices]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve favorite index summaries.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }

    public function details(string $index): JsonResponse
    {
        try {
            $encodedIndex = rawurlencode($index);
            $mapping = $this->client->request($encodedIndex.'/_mapping');
            $settings = $this->client->request($encodedIndex.'/_settings?include_defaults=true&flat_settings=true');

            $indexSettings = (array) ($settings[$index]['settings'] ?? []);
            $lifecycleResponse = null;
            try {
                $lifecycleResponse = $this->client->request('_ilm/explain/'.$encodedIndex);
            } catch (Throwable) {
                // ILM may be disabled or unavailable for the current user.
            }

            // Elasticsearch wraps explain responses in an "indices" object.
            $lifecycle = is_array($lifecycleResponse)
                ? (array) ($lifecycleResponse['indices'][$index] ?? $lifecycleResponse[$index] ?? [])
                : [];
            $policy = $indexSettings['index.lifecycle.name'] ?? null;
            $rolloverAlias = $indexSettings['index.lifecycle.rollover_alias'] ?? null;

            return response()->json([
                'index' => $index,
                'mapping' => $mapping[$index]['mappings'] ?? $mapping,
                'settings' => $indexSettings,
                'lifecycle' => [
                    'managed' => (bool) data_get($lifecycle, 'managed', false),
                    'policy' => data_get($lifecycle, 'policy', $policy),
                    'phase' => data_get($lifecycle, 'phase'),
                    'action' => data_get($lifecycle, 'action'),
                    'step' => data_get($lifecycle, 'step'),
                    'age' => data_get($lifecycle, 'age'),
                    'lifecycle_date' => data_get($lifecycle, 'lifecycle_date'),
                    'rollover_alias' => $rolloverAlias,
                    'failed_step' => data_get($lifecycle, 'failed_step'),
                    'step_info' => data_get($lifecycle, 'step_info'),
                    'explain' => $lifecycle,
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve index details.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }

    public function indexingStats(string $index): JsonResponse
    {
        try {
            $encodedIndex = rawurlencode($index);
            $metrics = 'indexing,search,merge,refresh,flush,query_cache,request_cache,segments,translog';
            $response = $this->client->request($encodedIndex.'/_stats/'.$metrics);
            $indexStats = (array) (($response['indices'] ?? [])[$index] ?? []);

            return response()->json([
                'index' => $index,
                'primaries' => (array) ($indexStats['primaries'] ?? []),
                'total' => (array) ($indexStats['total'] ?? []),
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve index performance statistics.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }
}
