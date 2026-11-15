<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Services\ElasticsearchClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ShardController
{
    public function __construct(private readonly ElasticsearchClient $client) {}

    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::shards';

        return view($viewName);
    }

    public function data(): JsonResponse
    {
        try {
            $response = $this->client->request('_cat/shards?format=json&bytes=b&h=index,shard,prirep,state,docs,store,node,unassigned.reason');
            $rows = array_values(array_filter($response, 'is_array'));
            $shards = array_map(static function (array $row): array {
                $node = $row['node'] ?? null;
                $reason = $row['unassigned.reason'] ?? $row['unassigned_reason'] ?? null;

                return [
                    'index' => (string) ($row['index'] ?? ''),
                    'shard' => (int) ($row['shard'] ?? 0),
                    'type' => (string) ($row['prirep'] ?? 'unknown'),
                    'state' => strtoupper((string) ($row['state'] ?? 'unknown')),
                    'documents' => isset($row['docs']) && is_numeric($row['docs']) ? (int) $row['docs'] : null,
                    'store_bytes' => isset($row['store']) && is_numeric($row['store']) ? (int) $row['store'] : null,
                    'node' => is_string($node) && $node !== '' ? $node : null,
                    'unassigned_reason' => is_string($reason) && $reason !== '' ? $reason : null,
                ];
            }, $rows);

            $nodeCounts = [];
            foreach ($shards as $shard) {
                if ($shard['node'] !== null) {
                    $nodeCounts[$shard['node']] = ($nodeCounts[$shard['node']] ?? 0) + 1;
                }
            }
            arsort($nodeCounts);

            $summary = [
                'total' => count($shards),
                'started' => count(array_filter($shards, static fn (array $shard): bool => $shard['state'] === 'STARTED')),
                'initializing' => count(array_filter($shards, static fn (array $shard): bool => $shard['state'] === 'INITIALIZING')),
                'relocating' => count(array_filter($shards, static fn (array $shard): bool => $shard['state'] === 'RELOCATING')),
                'unassigned' => count(array_filter($shards, static fn (array $shard): bool => $shard['state'] === 'UNASSIGNED')),
                'node_count' => count($nodeCounts),
            ];

            return response()->json([
                'shards' => $shards,
                'summary' => $summary,
                'node_distribution' => array_map(
                    static fn (string $node, int $count): array => ['node' => $node, 'shards' => $count],
                    array_keys($nodeCounts),
                    array_values($nodeCounts)
                ),
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve Elasticsearch shard allocation.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }

    public function allocationExplain(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'index' => ['required', 'string', 'max:255'],
            'shard' => ['required', 'integer', 'min:0'],
            'primary' => ['required', 'boolean'],
            'current_node' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $query = [
                'index' => $validated['index'],
                'shard' => (int) $validated['shard'],
                'primary' => filter_var($validated['primary'], FILTER_VALIDATE_BOOLEAN),
                'include_disk_info' => 'true',
                'include_yes_decisions' => 'false',
            ];

            if (! empty($validated['current_node'])) {
                $query['current_node'] = $validated['current_node'];
            }

            return response()->json($this->client->request('_cluster/allocation/explain?'.http_build_query($query)));
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve shard allocation explanation. Check the Elasticsearch monitor cluster privilege.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }
}
