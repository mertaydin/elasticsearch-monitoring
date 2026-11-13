<?php

namespace ElasticsearchMonitoring\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void auth(callable $callback)
 *
 * @see \ElasticsearchMonitoring\Services\DashboardAuthorizer
 */
class ElasticsearchMonitoring extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ElasticsearchMonitoring\Services\DashboardAuthorizer::class;
    }
}
