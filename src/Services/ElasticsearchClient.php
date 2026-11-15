<?php
namespace ElasticsearchMonitoring\Services;
use ElasticsearchMonitoring\Models\ApiRequestLog;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;
class ElasticsearchClient
{
    /** @var array<string, mixed>|null */
    private ?array $info = null;
    /** @return array<string, mixed> */
    public function request(string $path): array
    {
        $startedAt = hrtime(true);
        $endpoint = '/'.ltrim((string) parse_url($path, PHP_URL_PATH), '/');
        try {
            $response = $this->http()->get('/'.ltrim($path, '/'));
            $response->throw();
            $data = $response->json();
            if (! is_array($data)) {
                throw new RuntimeException('Elasticsearch returned an invalid JSON response.');
            }
            $this->recordRequest($endpoint, $response->status(), $startedAt, true);
            return $data;
        } catch (Throwable $exception) {
            $status = $exception instanceof RequestException ? $exception->response->status() : null;
            $this->recordRequest($endpoint, $status, $startedAt, false, $exception->getMessage());
            throw $exception;
        }
    }
    /** @return array<string, mixed> */
    public function clusterInfo(): array { return $this->info ??= $this->request('/'); }
    public function version(): string { return (string) data_get($this->clusterInfo(), 'version.number', 'unknown'); }
    public function majorVersion(): ?int
    {
        return preg_match('/^(\d+)/', $this->version(), $matches) === 1 ? (int) $matches[1] : null;
    }
    public function assertSupportedVersion(): void
    {
        $version = $this->version();
        $major = $this->majorVersion();
        if ($major === null || ($major === 8 && version_compare($version, '8.18.0', '<')) || ! in_array($major, [8, 9], true)) {
            throw new RuntimeException("Elasticsearch {$version} is not supported. Supported versions: 8.18+ and 9.x.");
        }
    }
    private function recordRequest(string $endpoint, ?int $status, int $startedAt, bool $successful, ?string $error = null): void
    {
        try {
            ApiRequestLog::query()->create([
                'method' => 'GET', 'endpoint' => $endpoint, 'status_code' => $status,
                'duration_ms' => round((hrtime(true) - $startedAt) / 1_000_000, 2),
                'successful' => $successful, 'error' => $error === null ? null : mb_substr($error, 0, 1000),
            ]);
        } catch (Throwable) {
            // Request history must never interrupt monitoring if its table is unavailable.
        }
    }
    private function http(): PendingRequest
    {
        $connection = config('elasticsearch-monitoring.connection', []);
        $request = Http::baseUrl(rtrim((string) ($connection['url'] ?? ''), '/'))
            ->timeout((int) ($connection['timeout'] ?? 10))->acceptJson();
        if (! empty($connection['api_key'])) {
            $request = $request->withHeaders(['Authorization' => 'ApiKey '.(string) $connection['api_key']]);
        } elseif (! empty($connection['username'])) {
            $request = $request->withBasicAuth((string) $connection['username'], (string) ($connection['password'] ?? ''));
        }
        if (($connection['verify'] ?? true) === false) { $request = $request->withoutVerifying(); }
        return $request;
    }
}
