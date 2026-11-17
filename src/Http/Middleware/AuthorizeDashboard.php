<?php

namespace ElasticsearchMonitoring\Http\Middleware;

use Closure;
use ElasticsearchMonitoring\Services\DashboardAuthorizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeDashboard
{
    public function __construct(private readonly DashboardAuthorizer $authorizer) {}

    public function handle(Request $request, Closure $next): Response
    {
        $ability = (string) config('elasticsearch-monitoring.authorization.gate', 'viewElasticsearchMonitoring');

        if (Gate::has($ability)) {
            Gate::authorize($ability);
        }

        if ($this->authorizer->allows($request) === false) {
            abort(403, 'You are not authorized to access the Elasticsearch monitoring dashboard.');
        }

        return $next($request);
    }
}
