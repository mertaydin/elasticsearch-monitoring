<?php

namespace ElasticsearchMonitoring\Services;

use ElasticsearchMonitoring\Models\MetricSnapshot;
use Illuminate\Support\Carbon;

class MetricsCollector
{
    public function __construct(private readonly ElasticsearchClient $client)
    {
    }

    /** @return array<string, mixed> */
    public function collect(): array
    {
        $this->client->assertSupportedVersion();

        $responses = [
            'health' => $this->client->request('_cluster/health'),
            'nodes' => $this->client->request('_nodes/stats/jvm,process,os,fs,thread_pool,indices'),
            'cluster' => $this->client->request('_cluster/stats'),
        ];

        $metrics = $this->normalize($responses);
        $previous = MetricSnapshot::query()->latest('collected_at')->first();

        $metrics['rates'] = $previous !== null
            ? $this->calculateRates((array) $previous->metrics, $metrics, $previous->collected_at, now())
            : ['search_per_second' => null, 'indexing_per_second' => null];

        MetricSnapshot::query()->create([
            'collected_at' => Carbon::now(),
            'elasticsearch_version' => $this->client->version(),
            'metrics' => $metrics,
        ]);

        $retention = (int) config('elasticsearch-monitoring.collection.retention_days', 30);
        if ($retention > 0) {
            MetricSnapshot::query()->where('collected_at', '<', now()->subDays($retention))->delete();
        }

        return $metrics;
    }

    /**
     * @param array<string, mixed> $responses
     * @return array<string, mixed>
     */
    private function normalize(array $responses): array
    {
        $nodes = (array) data_get($responses, 'nodes.nodes', []);
        $nodeCount = count($nodes);
        $heapUsed = $heapMax = $cpu = $search = $index = $docs = $shards = 0;
        $searchQueue = $searchRejected = $bulkQueue = $bulkRejected = 0;
        $filesystemTotal = $filesystemAvailable = $readOps = $writeOps = 0;

        foreach ($nodes as $node) {
            $heapUsed += (int) data_get($node, 'jvm.mem.heap_used_in_bytes', 0);
            $heapMax += (int) data_get($node, 'jvm.mem.heap_max_in_bytes', 0);
            $cpu += (int) data_get($node, 'process.cpu.percent', 0);
            $search += (int) data_get($node, 'indices.search.query_total', 0);
            $index += (int) data_get($node, 'indices.indexing.index_total', 0);
            $docs += (int) data_get($node, 'indices.docs.count', 0);
            $shards += (int) data_get($node, 'indices.shard_stats.total_count', 0);
            $searchQueue += (int) data_get($node, 'thread_pool.search.queue', 0);
            $searchRejected += (int) data_get($node, 'thread_pool.search.rejected', 0);
            $bulkQueue += (int) data_get($node, 'thread_pool.write.queue', data_get($node, 'thread_pool.bulk.queue', 0));
            $bulkRejected += (int) data_get($node, 'thread_pool.write.rejected', data_get($node, 'thread_pool.bulk.rejected', 0));
            $filesystemTotal += (int) data_get($node, 'fs.total.total_in_bytes', 0);
            $filesystemAvailable += (int) data_get($node, 'fs.total.available_in_bytes', 0);
            $readOps += (int) data_get($node, 'fs.io_stats.total.read_operations', 0);
            $writeOps += (int) data_get($node, 'fs.io_stats.total.write_operations', 0);
        }

        return [
            'cluster' => [
                'name' => data_get($responses, 'health.cluster_name'),
                'status' => data_get($responses, 'health.status'),
                'number_of_nodes' => (int) data_get($responses, 'health.number_of_nodes', 0),
                'active_shards' => (int) data_get($responses, 'health.active_shards', 0),
                'unassigned_shards' => (int) data_get($responses, 'health.unassigned_shards', 0),
            ],
            'nodes' => $nodeCount,
            'jvm' => ['heap_used_bytes' => $heapUsed, 'heap_max_bytes' => $heapMax],
            'cpu' => ['percent_average' => $nodeCount > 0 ? round($cpu / $nodeCount, 2) : null],
            'filesystem' => [
                'total_bytes' => $filesystemTotal,
                'available_bytes' => $filesystemAvailable,
                'read_operations' => $readOps,
                'write_operations' => $writeOps,
            ],
            'indices' => ['documents' => $docs, 'shards' => $shards],
            'thread_pool' => [
                'search_queue' => $searchQueue,
                'search_rejected' => $searchRejected,
                'write_queue' => $bulkQueue,
                'write_rejected' => $bulkRejected,
            ],
            'counters' => ['search_total' => $search, 'index_total' => $index],
            'collected_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @param array<string, mixed> $previous
     * @param array<string, mixed> $current
     * @return array<string, float|null>
     */
    private function calculateRates(array $previous, array $current, Carbon $previousAt, Carbon $currentAt): array
    {
        $seconds = max(0.001, (float) $previousAt->diffInMilliseconds($currentAt) / 1000);
        $previousSearch = (int) data_get($previous, 'counters.search_total', 0);
        $previousIndex = (int) data_get($previous, 'counters.index_total', 0);
        $currentSearch = (int) data_get($current, 'counters.search_total', 0);
        $currentIndex = (int) data_get($current, 'counters.index_total', 0);

        return [
            'search_per_second' => $currentSearch >= $previousSearch ? round(($currentSearch - $previousSearch) / $seconds, 2) : null,
            'indexing_per_second' => $currentIndex >= $previousIndex ? round(($currentIndex - $previousIndex) / $seconds, 2) : null,
        ];
    }
}
