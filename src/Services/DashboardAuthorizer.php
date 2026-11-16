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
        // The route group already applies the configured middleware (web, auth by default).
        // A host callback is optional and can add application-specific authorization rules.
        if ($this->callback === null) {
            return true;
        }

        return (bool) ($this->callback)($request);
    }
}
