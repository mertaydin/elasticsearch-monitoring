<?php

namespace ElasticsearchMonitoring\\Http\\Controllers;

use ElasticsearchMonitoring\\Services\\ElasticsearchClient;
use Illuminate\\Http\\JsonResponse;
use Illuminate\\Http\\Request;
use Throwable;

class NodeExplorerController
{
    public function __construct(private readonly ElasticsearchClient $client) {}

    public function details(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'node_id' => ['required', 'string', 'max:255'],
        ]);

        $nodeId = $validated['node_id'];

        try {
            $statsResponse = $this->client->request('_nodes/'.rawurlencode($nodeId).'/stats/jvm,process,os,fs,indices,thread_pool');
            $node = (array) data_get($statsResponse, 'nodes.nodes.'.$nodeId, []);

            if ($node === []) {
                return response()->json(['message' => 'The selected Elasticsearch node was not found.'], 404);
            }

            $shardRows = $this->client->request('_cat/shards?format=json&bytes=b&h=index,shard,prirep,state,docs,store,node');
            $nodeName = (string) data_get($node, 'name', '');
            $indices = [];

            foreach ((array) $shardRows as $shard) {
                if (! is_array($shard) || (string) ($shard['node'] ?? '') !== $nodeName) {
                    continue;
                }

                $index = (string) ($shard['index'] ?? '');
                if ($index === '') {
                    continue;
                }

                $indices[$index] ??= [
                    'name' => $index,
                    'primary_shards' => 0,
                    'replica_shards' => 0,
                    'documents' => 0,
                    'storage_bytes' => 0,
                    'states' => [],
                ];

                if (($shard['prirep'] ?? '') === 'p') {
                    $indices[$index]['primary_shards']++;
                    $indices[$index]['documents'] += is_numeric($shard['docs'] ?? null) ? (int) $shard['docs'] : 0;
                    $indices[$index]['storage_bytes'] += is_numeric($shard['store'] ?? null) ? (int) $shard['store'] : 0;
                } else {
                    $indices[$index]['replica_shards']++;
                }

                $state = strtoupper((string) ($shard['state'] ?? 'unknown'));
                $indices[$index]['states'][$state] = ($indices[$index]['states'][$state] ?? 0) + 1;
            }

            usort($indices, static fn (array $left, array $right): int => $right['storage_bytes'] <=> $left['storage_bytes']);

            $heapUsed = (int) data_get($node, 'jvm.mem.heap_used_in_bytes', 0);
            $heapMax = (int) data_get($node, 'jvm.mem.heap_max_in_bytes', 0);
            $fsTotal = (int) data_get($node, 'fs.total.total_in_bytes', 0);
            $fsAvailable = (int) data_get($node, 'fs.total.available_in_bytes', 0);

            return response()->json([
                'node' => [
                    'id' => $nodeId,
                    'name' => $nodeName,
                    'ip' => (string) data_get($node, 'ip', data_get($node, 'host', '')),
                    'roles' => (array) data_get($node, 'roles', []),
                    'version' => (string) data_get($node, 'version', ''),
                    'uptime_ms' => (int) data_get($node, 'jvm.uptime_in_millis', 0),
                    'cpu_percent' => data_get($node, 'process.cpu.percent'),
                    'os_cpu_percent' => data_get($node, 'os.cpu.percent'),
                    'load_average' => (array) data_get($node, 'os.cpu.load_average', []),
                    'heap_used_bytes' => $heapUsed,
                    'heap_max_bytes' => $heapMax,
                    'heap_percent' => $heapMax > 0 ? round(($heapUsed / $heapMax) * 100, 2) : null,
                    'filesystem_total_bytes' => $fsTotal,
                    'filesystem_available_bytes' => $fsAvailable,
                    'filesystem_used_bytes' => max(0, $fsTotal - $fsAvailable),
                    'filesystem_used_percent' => $fsTotal > 0 ? round((($fsTotal - $fsAvailable) / $fsTotal) * 100, 2) : null,
                    'documents' => (int) data_get($node, 'indices.docs.count', 0),
                    'shards' => (int) data_get($node, 'indices.shard_stats.total_count', 0),
                    'thread_pools' => collect((array) data_get($node, 'thread_pool', []))->map(static fn ($pool, $name): array => [
                        'name' => (string) $name,
                        'threads' => (int) data_get($pool, 'threads', 0),
                        'active' => (int) data_get($pool, 'active', 0),
                        'queue' => (int) data_get($pool, 'queue', 0),
                        'rejected' => (int) data_get($pool, 'rejected', 0),
                        'completed' => (int) data_get($pool, 'completed', 0),
                    ])->values()->all(),
                ],
                'indices' => array_values($indices),
                'shard_summary' => [
                    'total' => array_sum(array_map(static fn (array $index): int => $index['primary_shards'] + $index['replica_shards'], $indices)),
                    'started' => count(array_filter((array) $shardRows, static fn ($shard): bool => is_array($shard) && (string) ($shard['node'] ?? '') === $nodeName && strtoupper((string) ($shard['state'] ?? '')) === 'STARTED')),
                    'initializing' => count(array_filter((array) $shardRows, static fn ($shard): bool => is_array($shard) && (string) ($shard['node'] ?? '') === $nodeName && strtoupper((string) ($shard['state'] ?? '')) === 'INITIALIZING')),
                    'relocating' => count(array_filter((array) $shardRows, static fn ($shard): bool => is_array($shard) && (string) ($shard['node'] ?? '') === $nodeName && strtoupper((string) ($shard['state'] ?? '')) === 'RELOCATING')),
                ],
                'collected_at' => now()->toIso8601String(),
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve node details. Check node monitoring privileges.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }
}
