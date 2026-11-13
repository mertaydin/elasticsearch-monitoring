<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Services\ElasticsearchClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class IndexController
{
    public function __construct(private readonly ElasticsearchClient $client) {}

    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::indices';

        return view($viewName);
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
                'h' => 'health,status,index,pri,rep,docs.count,store.size',
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
            $response = $this->client->request($encodedIndex.'/_stats/indexing');
            $indexStats = (array) (($response['indices'] ?? [])[$index] ?? []);

            return response()->json([
                'index' => $index,
                'primaries' => (array) data_get($indexStats, 'primaries.indexing', []),
                'total' => (array) data_get($indexStats, 'total.indexing', []),
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve index indexing statistics.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }

}
