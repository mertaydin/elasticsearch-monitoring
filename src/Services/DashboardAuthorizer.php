<?php

namespace ElasticsearchMonitoring\Services;

use Closure;
use Illuminate\Http\Request;

class DashboardAuthorizer
{
    private ?Closure $callback = null;

    public function auth(callable $callback): void
    {
        $this->callback = Closure::fromCallable($callback);
    }

    public function isConfigured(): bool
    {
        return $this->callback !== null;
    }

    public function allows(Request $request): bool
    {
        // Cluster metadata and operational history are sensitive. Require the
        // host application to explicitly authorize dashboard access.
        return $this->callback !== null && (bool) ($this->callback)($request);
    }
}
