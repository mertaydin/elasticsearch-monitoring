<?php

namespace ElasticsearchMonitoring\Tests;

use ElasticsearchMonitoring\ElasticsearchMonitoringServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [ElasticsearchMonitoringServiceProvider::class];
    }
}
