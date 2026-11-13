<?php

namespace ElasticsearchMonitoring\\Http\\Controllers;

use ElasticsearchMonitoring\\Services\\ElasticsearchClient;
use Illuminate\\Http\\JsonResponse;
use Illuminate\\Http\\Request;
use Illuminate\\View\\View;
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

            $indices = array_map(static function (array $row) use ($dataStreams): array {
                return [
                    'name' => (string) ($row['index'] ?? ''),
                    'health' => (string) ($row['health'] ?? 'unknown'),
                    'status' => (string) ($row['status'] ?? 'unknown'),
                    'primaries' => (int) ($row['pri'] ?? 0),
                    'replicas' => (int) ($row['rep'] ?? 0),
                    'documents_count' => (int) ($row['docs.count'] ?? 0),
                    'storage_bytes' => (int) ($row['store.size'] ?? 0),
                    'data_stream' => $dataStreams[(string) ($row['index'] ?? '')] ?? null,
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

            $lifecycle = null;
            try {
                $lifecycle = $this->client->request('_ilm/explain/'.$encodedIndex);
            } catch (Throwable) {
                // ILM may be disabled or unavailable for the current user.
            }

            return response()->json([
                'index' => $index,
                'mapping' => $mapping[$index]['mappings'] ?? $mapping,
                'settings' => $settings[$index]['settings'] ?? $settings,
                'lifecycle' => $lifecycle[$index] ?? null,
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve index details.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }
}
