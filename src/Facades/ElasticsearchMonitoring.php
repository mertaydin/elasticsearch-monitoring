<?php

namespace ElasticsearchMonitoring\Facades;

use ElasticsearchMonitoring\Services\DashboardAuthorizer;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void auth(callable $callback)
 *
 * @see DashboardAuthorizer
 */
class ElasticsearchMonitoring extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DashboardAuthorizer::class;
    }
}
