# Changelog

## [0.4.0] - 2026-11-13

- Add a dedicated read-only Data Streams page with backing-index details, generation, template, ILM metadata, document counts, and storage usage.
- Keep Data Streams separate from the main dashboard and index-management workflow.
- Preserve stream discovery when index statistics or ILM privileges are unavailable.


## [0.3.0] - 2026-11-13

- Expand read-only ILM lifecycle and rollover visibility in index details.
- Show lifecycle policy, phase, action, step, age, rollover alias, and failed-step information.
- Correct index lifecycle metadata columns and index details response parsing.


## [0.2.0] - 2026-11-13

- Add configurable cluster health scoring and active alerts for cluster, shard, CPU, JVM, disk, and thread-pool conditions.
- Collect per-node resource, document, shard, and operation counters and show a node comparison table.
- Keep Elasticsearch interactions strictly read-only.


## [0.1.9] - 2026-11-13

- Add a LogViewer-style dashboard authorization callback through the ElasticsearchMonitoring facade.
- Deny dashboard requests with HTTP 403 when the registered callback rejects access.
- Document SuperAdmin authorization setup for Laravel applications.


## [0.1.8] - 2026-11-13

- Fix missing lifecycle table labels in the Turkish locale and align empty/loading table column spans.
- Reorganize dashboard charts into a consistent three-column desktop grid with equal chart heights and centered KPI cards.


## [0.1.7] - 2026-11-13

- Add an index management page with health, status, shard, document, storage, and data stream information.
- Add per-index mapping, settings, and optional ILM lifecycle inspection.
- Support filtering and optional inclusion of hidden and rollup indices.


## [0.1.6] - 2026-11-13

- Fix dashboard metric loading by initializing the byte formatter before KPI sparkline charts.


## [0.1.5] - 2026-11-13

- Add compact KPI cards with live values, change indicators, and sparkline trends for key cluster metrics.
- Include active primary shard counts in collected cluster health metrics.


## [0.1.4] - 2026-11-13

- Show primary and total shard storage and segment counts as separate chart series.


## [0.1.3] - 2026-11-13

- Add disk usage, search rate, indexing rate, segment count, and document count history charts.
- Collect primary and total index storage and segment statistics from Elasticsearch index stats.


All notable changes to this project will be documented in this file.

## [0.1.2] - 2026-11-13

- Refresh the monitoring dashboard visual design.
- Add CPU utilization and index storage history charts.
- Improve chart labels and descriptions for localized dashboards.
- Refresh dashboard metrics automatically every 30 seconds.

## [0.1.1] - 2026-11-13

- Add a dashboard language selector with browser preference persistence.
- Allow switching between the six bundled locales without editing configuration.

## [0.1.0] - 2026-11-13

- Add Elasticsearch 8.18+ and 9.x version detection.
- Collect cluster health, node, JVM, CPU, filesystem, index, shard, and thread-pool metrics.
- Persist metric snapshots and calculate search/indexing rates from counter deltas.
- Add a Bootstrap 5 dashboard with ApexCharts and six locales.
- Add an Artisan collection command and optional scheduler registration.
- Support Laravel 12 and 13.
