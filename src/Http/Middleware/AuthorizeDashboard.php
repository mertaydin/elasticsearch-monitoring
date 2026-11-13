<?php

namespace ElasticsearchMonitoring\Http\Middleware;

use Closure;
use ElasticsearchMonitoring\Services\DashboardAuthorizer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeDashboard
{
    public function __construct(private readonly DashboardAuthorizer $authorizer)
    {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->authorizer->allows($request) === false) {
            abort(403, 'You are not authorized to access the Elasticsearch monitoring dashboard.');
        }

        return $next($request);
    }
}
