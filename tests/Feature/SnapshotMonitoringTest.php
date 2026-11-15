<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\SnapshotController;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SnapshotMonitoringTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_lists_repositories_recent_snapshots_and_latest_successful_backup(): void
    {
        Http::fake(function ($request) {
            if (str_ends_with($request->url(), '/_snapshot')) {
                return Http::response([
                    'daily-backups' => ['type' => 'fs', 'settings' => ['location' => '/backups']],
                    'cloud-backups' => ['type' => 's3', 'settings' => ['bucket' => 'private-bucket']],
                ]);
            }

            if (str_contains($request->url(), '/_snapshot/daily-backups/*') && str_contains($request->url(), 'size=50')) {
                return Http::response([
                    'total' => 3,
                    'snapshots' => [
                        ['snapshot' => 'backup-running', 'uuid' => 'u3', 'state' => 'IN_PROGRESS', 'start_time' => '2026-11-15T10:00:00Z', 'start_time_in_millis' => 1794736800000, 'indices' => ['logs'], 'shards' => ['total' => 2, 'successful' => 1, 'failed' => 0], 'failures' => []],
                        ['snapshot' => 'backup-partial', 'uuid' => 'u2', 'state' => 'PARTIAL', 'start_time' => '2026-11-14T10:00:00Z', 'start_time_in_millis' => 1794650400000, 'end_time' => '2026-11-14T10:05:00Z', 'duration_in_millis' => 300000, 'indices' => ['logs', 'users'], 'shards' => ['total' => 10, 'successful' => 9, 'failed' => 1], 'failures' => [['reason' => 'node unavailable']]],
                    ],
                ]);
            }

            if (str_contains($request->url(), '/_snapshot/daily-backups/*') && str_contains($request->url(), 'state=SUCCESS')) {
                return Http::response(['total' => 1, 'snapshots' => [
                    ['snapshot' => 'backup-last-good', 'uuid' => 'u1', 'state' => 'SUCCESS', 'start_time' => '2026-11-13T10:00:00Z', 'start_time_in_millis' => 1794564000000, 'end_time' => '2026-11-13T10:04:00Z', 'duration_in_millis' => 240000, 'indices' => ['logs'], 'shards' => ['total' => 5, 'successful' => 5, 'failed' => 0], 'failures' => []],
                ]]);
            }

            if (str_contains($request->url(), '/_snapshot/cloud-backups/*')) {
                return Http::response(['total' => 0, 'snapshots' => []]);
            }

            return Http::response([], 404);
        });

        $payload = app(SnapshotController::class)->data()->getData(true);
        $this->assertTrue($payload['available']);
        $this->assertSame(2, $payload['summary']['repositories']);
        $this->assertSame(2, $payload['summary']['snapshots']);
        $this->assertSame(1, $payload['summary']['partial']);
        $this->assertSame(1, $payload['summary']['in_progress']);
        $this->assertSame('backup-last-good', $payload['meta']['latest_successful']['name']);
        $partial = collect($payload['snapshots'])->firstWhere('state', 'PARTIAL');
        $this->assertSame(1, $partial['shards_failed']);
        $this->assertSame('daily-backups', $partial['repository']);
        $this->assertArrayNotHasKey('settings', $payload['repositories'][0]);
    }

    public function test_it_gracefully_handles_missing_snapshot_monitoring_privileges(): void
    {
        Http::fake(['*/_snapshot' => Http::response(['error' => 'forbidden'], 403)]);
        $response = app(SnapshotController::class)->data();
        $payload = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertFalse($payload['available']);
        $this->assertSame([], $payload['repositories']);
    }

    public function test_it_marks_a_repository_when_snapshot_history_is_unavailable(): void
    {
        Http::fake(function ($request) {
            if (str_ends_with($request->url(), '/_snapshot')) {
                return Http::response(['backup' => ['type' => 'fs']]);
            }

            return Http::response(['error' => 'forbidden'], 403);
        });

        $payload = app(SnapshotController::class)->data()->getData(true);
        $this->assertTrue($payload['available']);
        $this->assertNotNull($payload['repositories'][0]['error']);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}
