<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class PackageMigrationTest extends TestCase
{
    public function test_package_migration_creates_the_metric_snapshots_table(): void
    {
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);

        $this->assertTrue(Schema::hasColumns('elasticsearch_metric_snapshots', [
            'id',
            'collected_at',
            'elasticsearch_version',
            'metrics',
            'created_at',
            'updated_at',
        ]));
    }
}
