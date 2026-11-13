<?php

namespace ElasticsearchMonitoring\Http\Controllers;

use ElasticsearchMonitoring\Services\ElasticsearchClient;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

class DataStreamController
{
    public function __construct(private readonly ElasticsearchClient $client) {}

    public function index(): View
    {
        /** @var view-string $viewName */
        $viewName = 'elasticsearch-monitoring::data-streams';

        return view($viewName);
    }

    public function data(): JsonResponse
    {
        try {
            $response = $this->client->request('_data_stream');
            $streams = array_values(array_filter((array) ($response['data_streams'] ?? []), 'is_array'));
            $stats = $this->statsByIndex($streams);

            $dataStreams = array_map(static function (array $stream) use ($stats): array {
                $indices = array_values(array_filter((array) ($stream['indices'] ?? []), 'is_array'));
                $documents = 0;
                $storage = 0;

                foreach ($indices as $index) {
                    $item = $stats[(string) ($index['index_name'] ?? '')] ?? [];
                    $documents += (int) data_get($item, 'primaries.docs.count', 0);
                    $storage += (int) data_get($item, 'total.store.size_in_bytes', 0);
                }

                return [
                    'name' => (string) ($stream['name'] ?? ''),
                    'status' => (string) ($stream['status'] ?? 'unknown'),
                    'generation' => (int) ($stream['generation'] ?? 0),
                    'template' => $stream['template'] ?? null,
                    'ilm_policy' => $stream['ilm_policy'] ?? null,
                    'prefer_ilm' => (bool) ($stream['prefer_ilm'] ?? false),
                    'next_generation_managed_by' => $stream['next_generation_managed_by'] ?? null,
                    'hidden' => (bool) ($stream['hidden'] ?? false),
                    'system' => (bool) ($stream['system'] ?? false),
                    'backing_indices_count' => count($indices),
                    'documents_count' => $documents,
                    'storage_bytes' => $storage,
                ];
            }, $streams);

            return response()->json(['data_streams' => $dataStreams]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve Elasticsearch data streams.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }

    public function details(string $name): JsonResponse
    {
        try {
            $response = $this->client->request('_data_stream/'.rawurlencode($name));
            $stream = (array) (array_values((array) ($response['data_streams'] ?? []))[0] ?? []);

            if ($stream === []) {
                return response()->json(['message' => 'Data stream not found.'], 404);
            }

            $indices = array_values(array_filter((array) ($stream['indices'] ?? []), 'is_array'));
            $names = array_values(array_filter(array_map(
                static fn (array $index): string => (string) ($index['index_name'] ?? ''),
                $indices
            )));
            $stats = $this->statsByIndex([$stream]);
            $lifecycle = [];

            if ($names !== []) {
                try {
                    $explain = $this->client->request('_ilm/explain/'.implode(',', array_map('rawurlencode', $names)));
                    $lifecycle = (array) ($explain['indices'] ?? []);
                } catch (Throwable) {
                    // ILM may be disabled or unavailable for the current user.
                }
            }

            $backing = array_map(static function (array $index) use ($stats, $lifecycle): array {
                $indexName = (string) ($index['index_name'] ?? '');
                $item = $stats[$indexName] ?? [];
                $ilm = (array) ($lifecycle[$indexName] ?? []);

                return [
                    'name' => $indexName,
                    'uuid' => $index['index_uuid'] ?? null,
                    'documents_count' => (int) data_get($item, 'primaries.docs.count', 0),
                    'storage_bytes' => (int) data_get($item, 'total.store.size_in_bytes', 0),
                    'lifecycle_managed' => (bool) ($ilm['managed'] ?? false),
                    'lifecycle_policy' => $ilm['policy'] ?? null,
                    'lifecycle_phase' => $ilm['phase'] ?? null,
                    'lifecycle_action' => $ilm['action'] ?? null,
                    'lifecycle_step' => $ilm['step'] ?? null,
                    'lifecycle_age' => $ilm['age'] ?? null,
                    'lifecycle_failed_step' => $ilm['failed_step'] ?? null,
                    'lifecycle_explain' => $ilm,
                ];
            }, $indices);

            return response()->json(['data_stream' => [
                'name' => (string) ($stream['name'] ?? $name),
                'status' => (string) ($stream['status'] ?? 'unknown'),
                'generation' => (int) ($stream['generation'] ?? 0),
                'template' => $stream['template'] ?? null,
                'ilm_policy' => $stream['ilm_policy'] ?? null,
                'prefer_ilm' => (bool) ($stream['prefer_ilm'] ?? false),
                'next_generation_managed_by' => $stream['next_generation_managed_by'] ?? null,
                'hidden' => (bool) ($stream['hidden'] ?? false),
                'system' => (bool) ($stream['system'] ?? false),
                'allow_custom_routing' => (bool) ($stream['allow_custom_routing'] ?? false),
                'replicated' => (bool) ($stream['replicated'] ?? false),
                'rollover_on_write' => (bool) ($stream['rollover_on_write'] ?? false),
                'backing_indices' => $backing,
            ]]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to retrieve Elasticsearch data stream details.',
                'error' => $exception->getMessage(),
            ], 502);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $streams
     * @return array<string, array<string, mixed>>
     */
    private function statsByIndex(array $streams): array
    {
        $names = [];
        foreach ($streams as $stream) {
            foreach ((array) ($stream['indices'] ?? []) as $index) {
                if (is_array($index) && ! empty($index['index_name'])) {
                    $names[] = (string) $index['index_name'];
                }
            }
        }

        $names = array_values(array_unique($names));
        if ($names === []) {
            return [];
        }

        try {
            $response = $this->client->request(implode(',', array_map('rawurlencode', $names)).'/_stats/docs,store');

            return (array) ($response['indices'] ?? []);
        } catch (Throwable) {
            // Stream metadata remains available without index statistics privileges.
            return [];
        }
    }
}
