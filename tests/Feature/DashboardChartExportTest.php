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

    public function test_dashboard_offers_a_pdf_report_with_metrics_charts_and_notes(): void
    {
        $html = view('elasticsearch-monitoring::dashboard')->render();

        $this->assertStringContainsString('downloadDashboardPdf()', $html);
        $this->assertStringContainsString('html2pdf.bundle.min.js', $html);
        $this->assertStringContainsString('latestDashboardMetrics.cluster?.name', $html);
        $this->assertStringContainsString('dashboardNotes.filter', $html);
        $this->assertStringContainsString('pdfCharts', $html);
    }

    public function test_pdf_report_labels_are_available_in_all_supported_locales(): void
    {
        foreach (['tr', 'en', 'es', 'zh_CN', 'hi', 'ar'] as $locale) {
            foreach (['download_pdf_report', 'pdf_title', 'pdf_failed'] as $key) {
                $value = trans('elasticsearch-monitoring::messages.'.$key, [], $locale);
                $this->assertIsString($value);
                $this->assertNotSame($key, $value);
            }
        }
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
