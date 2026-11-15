# Changelog

## [0.14.0] - 2026-11-15

- Add configurable email, Slack, and Microsoft Teams notifications for newly opened alert events.
- Add per-alert/channel deduplication, configurable cooldown suppression, and retryable queued delivery.
- Add notification delivery history with sent, failed, suppressed, and pending states.
- Keep all notification channels disabled by default; Elasticsearch interactions remain strictly read-only.


## [0.13.0] - 2026-11-15

- Add per-shard allocation explanations with node-level decider decisions, unassigned reasons, and optional disk information.
- Use the diagnostic allocation explain API through GET requests only; no cluster state changes are performed.
- Add localized explanation details to the Shard Allocation Explorer in all six supported languages.


## [0.12.0] - 2026-11-15

- Expand selected-index statistics into a read-only performance analyzer covering indexing, search, merge, refresh, flush, query/request cache, segments, and translog metrics.
- Compare primary-shard and total (primary + replica) statistics in the index details modal.
- Refresh selected-index metrics on demand and every 30 seconds while the performance tab is active.


## [0.11.0] - 2026-11-15

- Add read-only snapshot repository and backup history monitoring.
- Show recent snapshot states, duration, index/data stream counts, shard outcomes, and failure reasons.
- Highlight the latest successful backup and gracefully handle missing snapshot monitoring privileges.
- Cache snapshot API responses for 60 seconds and limit history to recent records.


## [0.10.0] - 2026-11-15

- Add a read-only Thread Pool Analyzer with per-node and per-pool thread, active, queue, completed, and rejected metrics.
- Add historical queue depth and rejection/completion rate trends with saturation status indicators.
- Preserve compatibility with Elasticsearch nodes that expose different thread pool names.


## [0.9.0] - 2026-11-15

- Add Cluster Capacity & Growth analysis with historical filesystem, document, and primary index storage trends.
- Add per-node disk growth projections and fastest-growing index rankings.
- Extend metric snapshots with per-node filesystem byte counters and per-index document/storage statistics.
- Increase default metric snapshot retention to 90 days to support longer trend analysis.


## [0.8.1] - 2026-11-15

- Clean up README line breaks and remove duplicated Shard Allocation documentation.


## [0.8.0] - 2026-11-15

- Add a read-only Shard Allocation Explorer with per-shard state, primary/replica type, node placement, unassigned reasons, and node distribution.
- Add state, node, and text filters with 30-second refresh and localized interface in all six supported languages.


## [0.7.0] - 2026-11-13

- Add read-only per-index indexing statistics for indexing, deletion, no-op update, and throttling counters, with primary and total comparisons.


## [0.6.0] - 2026-11-13

- Add a dedicated node performance history page with per-node CPU, JVM heap, filesystem, search/indexing rate, and thread-pool charts.
- Add a node comparison table and selector using persisted metric snapshots (up to 1,440 latest collections).
- Calculate per-node search and indexing rates from counter deltas without additional Elasticsearch requests or schema changes.
- Add navigation links and localized labels for all six supported locales.


## [0.5.0] - 2026-11-13

- Add persistent alert lifecycle history with first/last seen timestamps, occurrence counts, and resolution timestamps.
- Add a dedicated localized alert history page with active/resolved filters and summary counters.
- Keep alert history persistence in the package database; Elasticsearch access remains read-only.

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
