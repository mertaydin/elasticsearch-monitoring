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
        // Fail closed: without an explicit host-registered callback no request is authorized.
        if ($this->callback === null) {
            return false;
        }

        return (bool) ($this->callback)($request);
    }
}
