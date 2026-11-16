<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Services\ElasticsearchClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class NodeExplorerController
{
    public function __construct(private readonly ElasticsearchClient $client) {}

    public function details(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'node_id' => ['required', 'string', 'max:255'],
        ]);

        try {
            $response = $this->client->request('_nodes/'.rawurlencode($validated['node_id']).'/stats/jvm,process,fs,indices');
            $node = (array) data_get($response, 'nodes.'.$validated['node_id'], []);

            if ($node === []) {
                return response()->json(['message' => 'Node not found.'], 404);
            }

            $shardRows = (array) $this->client->request('_cat/shards?format=json&bytes=b&h=index,prirep,node');
            $nodeName = (string) data_get($node, 'name', '');
            $indices = [];

            foreach ($shardRows as $shard) {
                if (! is_array($shard) || (string) ($shard['node'] ?? '') !== $nodeName) {
                    continue;
                }

                $name = (string) ($shard['index'] ?? '');
                if ($name === '') {
                    continue;
                }

                $indices[$name] ??= ['name' => $name, 'primary_shards' => 0, 'replica_shards' => 0];
                if (($shard['prirep'] ?? '') === 'p') {
                    $indices[$name]['primary_shards']++;
                } else {
                    $indices[$name]['replica_shards']++;
                }
            }

            $heapUsed = (int) data_get($node, 'jvm.mem.heap_used_in_bytes', 0);
            $heapMax = (int) data_get($node, 'jvm.mem.heap_max_in_bytes', 0);
            $diskTotal = (int) data_get($node, 'fs.total.total_in_bytes', 0);
            $diskFree = (int) data_get($node, 'fs.total.available_in_bytes', 0);

            return response()->json([
                'node' => [
                    'name' => $nodeName,
                    'ip' => (string) data_get($node, 'ip', data_get($node, 'host', '')),
                    'roles' => (array) data_get($node, 'roles', []),
                    'cpu_percent' => data_get($node, 'process.cpu.percent'),
                    'heap_percent' => $heapMax > 0 ? round($heapUsed / $heapMax * 100, 1) : null,
                    'disk_percent' => $diskTotal > 0 ? round(($diskTotal - $diskFree) / $diskTotal * 100, 1) : null,
                    'documents' => (int) data_get($node, 'indices.docs.count', 0),
                    'shards' => (int) data_get($node, 'indices.shard_stats.total_count', count($shardRows)),
                ],
                'indices' => array_values($indices),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to retrieve node details.',
            ], 502);
        }
    }
}
