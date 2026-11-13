<?php

namespace ElasticsearchMonitoring\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ElasticsearchClient
{
    /** @var array<string, mixed>|null */
    private ?array $info = null;

    /** @return array<string, mixed> */
    public function request(string $path): array
    {
        $response = $this->http()->get('/'.ltrim($path, '/'));
        $response->throw();
        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('Elasticsearch returned an invalid JSON response.');
        }

        return $data;
    }

    /** @return array<string, mixed> */
    public function clusterInfo(): array
    {
        return $this->info ??= $this->request('/');
    }

    public function version(): string
    {
        return (string) data_get($this->clusterInfo(), 'version.number', 'unknown');
    }

    public function majorVersion(): ?int
    {
        return preg_match('/^(\d+)/', $this->version(), $matches) === 1
            ? (int) $matches[1]
            : null;
    }

    public function assertSupportedVersion(): void
    {
        $version = $this->version();
        $major = $this->majorVersion();

        if ($major === null || ($major === 8 && version_compare($version, '8.18.0', '<')) || ! in_array($major, [8, 9], true)) {
            throw new RuntimeException("Elasticsearch {$version} is not supported. Supported versions: 8.18+ and 9.x.");
        }
    }

    private function http(): PendingRequest
    {
        $connection = config('elasticsearch-monitoring.connection', []);
        $request = Http::baseUrl(rtrim((string) ($connection['url'] ?? ''), '/'))
            ->timeout((int) ($connection['timeout'] ?? 10))
            ->acceptJson();

        if (! empty($connection['api_key'])) {
            $request = $request->withHeaders([
                'Authorization' => 'ApiKey '.(string) $connection['api_key'],
            ]);
        } elseif (! empty($connection['username'])) {
            $request = $request->withBasicAuth(
                (string) $connection['username'],
                (string) ($connection['password'] ?? '')
            );
        }

        if (($connection['verify'] ?? true) === false) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }
}