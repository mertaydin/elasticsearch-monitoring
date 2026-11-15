<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Http\Controllers\DashboardController;
use ElasticsearchMonitoring\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardHistoryRangeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::dropIfExists('elasticsearch_metric_snapshots');
        Schema::create('elasticsearch_metric_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->timestamp('collected_at')->index();
            $table->string('elasticsearch_version', 32);
            $table->json('metrics');
            $table->timestamps();
        });
    }

    public function test_dashboard_history_is_limited_to_the_requested_date_range(): void
    {
        foreach (['2026-11-15 10:00:00', '2026-11-15 11:00:00', '2026-11-15 12:00:00'] as $date) {
            DB::table('elasticsearch_metric_snapshots')->insert([
                'collected_at' => $date, 'elasticsearch_version' => '8.19.14',
                'metrics' => json_encode(['cpu' => ['percent_average' => 25]]),
                'created_at' => $date, 'updated_at' => $date,
            ]);
        }
        $request = Request::create('/monitoring/elasticsearch/data', 'GET', [
            'from' => '2026-11-15T10:30:00+00:00', 'to' => '2026-11-15T12:00:00+00:00',
        ]);
        $response = app(DashboardController::class)->data($request);
        $payload = $response->getData(true);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertCount(2, $payload['history']);
        $this->assertSame(2, $payload['range']['total']);
        $this->assertSame(3, $payload['latest']['id']);
    }

    public function test_dashboard_history_uses_hourly_averages_for_ranges_longer_than_one_day(): void
    {
        foreach ([['2026-11-15 10:05:00', 20], ['2026-11-15 10:35:00', 40], ['2026-11-15 11:10:00', 60]] as [$date, $cpu]) {
            DB::table('elasticsearch_metric_snapshots')->insert([
                'collected_at' => $date, 'elasticsearch_version' => '8.19.14',
                'metrics' => json_encode([
                    'cpu' => ['percent_average' => $cpu],
                    'indices' => ['documents' => $cpu * 10],
                    'rates' => ['search_per_second' => $cpu / 10],
                ]),
                'created_at' => $date, 'updated_at' => $date,
            ]);
        }

        $response = app(DashboardController::class)->data(Request::create('/monitoring/elasticsearch/data', 'GET', [
            'from' => '2026-11-14T00:00:00+00:00', 'to' => '2026-11-15T12:00:00+00:00',
        ]));
        $payload = $response->getData(true);

        $this->assertSame('hourly', $payload['range']['aggregation']);
        $this->assertCount(2, $payload['history']);
        $this->assertEquals(30.0, $payload['history'][0]['metrics']['cpu']['percent_average']);
        $this->assertEquals(300.0, $payload['history'][0]['metrics']['indices']['documents']);
        $this->assertEquals(3.0, $payload['history'][0]['metrics']['rates']['search_per_second']);
    }

    public function test_dashboard_history_uses_daily_averages_for_ranges_longer_than_seven_days(): void
    {
        foreach ([['2026-11-01 08:00:00', 20], ['2026-11-01 16:00:00', 40], ['2026-11-02 08:00:00', 60]] as [$date, $cpu]) {
            DB::table('elasticsearch_metric_snapshots')->insert([
                'collected_at' => $date, 'elasticsearch_version' => '8.19.14',
                'metrics' => json_encode(['cpu' => ['percent_average' => $cpu]]),
                'created_at' => $date, 'updated_at' => $date,
            ]);
        }

        $response = app(DashboardController::class)->data(Request::create('/monitoring/elasticsearch/data', 'GET', [
            'from' => '2026-10-25T00:00:00+00:00', 'to' => '2026-11-02T23:59:59+00:00',
        ]));
        $payload = $response->getData(true);

        $this->assertSame('daily', $payload['range']['aggregation']);
        $this->assertCount(2, $payload['history']);
        $this->assertEquals(30.0, $payload['history'][0]['metrics']['cpu']['percent_average']);
        $this->assertEquals(60.0, $payload['history'][1]['metrics']['cpu']['percent_average']);
    }

    public function test_dashboard_history_defaults_to_the_last_hour(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-15 12:00:00'));
        DB::table('elasticsearch_metric_snapshots')->insert([
            'collected_at' => '2026-11-15 11:30:00', 'elasticsearch_version' => '8.19.14',
            'metrics' => json_encode(['cpu' => ['percent_average' => 25]]),
            'created_at' => '2026-11-15 11:30:00', 'updated_at' => '2026-11-15 11:30:00',
        ]);
        $response = app(DashboardController::class)->data(Request::create('/monitoring/elasticsearch/data'));
        $this->assertCount(1, $response->getData(true)['history']);
        Carbon::setTestNow();
    }
}
