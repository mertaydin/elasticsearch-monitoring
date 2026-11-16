<?php

namespace ElasticsearchMonitoring\Tests\Feature;

use ElasticsearchMonitoring\Tests\TestCase;

class DashboardChartExportTest extends TestCase
{
    public function test_dashboard_provides_individual_and_combined_chart_png_exports(): void
    {
        $html = view('elasticsearch-monitoring::dashboard')->render();

        $this->assertStringContainsString('downloadAllDashboardCharts()', $html);
        $this->assertStringContainsString("downloadDashboardChart('heap')", $html);
        $this->assertStringContainsString("downloadDashboardChart('storage-chart')", $html);
        $this->assertStringContainsString('createDashboardChartsPng', $html);
        $this->assertStringContainsString('image/png', $html);
    }

    public function test_chart_export_labels_are_available_in_all_supported_locales(): void
    {
        foreach (['tr', 'en', 'es', 'zh_CN', 'hi', 'ar'] as $locale) {
            foreach (['download_chart', 'download_all_charts', 'chart_export_failed'] as $key) {
                $value = trans('elasticsearch-monitoring::messages.'.$key, [], $locale);

                $this->assertIsString($value);
                $this->assertNotSame($key, $value);
            }
        }
    }
}
