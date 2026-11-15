## [0.27.0] - 2026-11-16

- Add an optional comparison overlay for the immediately preceding period of equal duration across dashboard history charts.
- Align previous-period timestamps to the selected range so both periods can be compared on the same chart timeline.
- Persist the comparison toggle in browser local storage and keep it disabled by default.
- Add comparison API coverage and localized labels in all six supported languages.
- Refresh the dashboard visual design with a clearer header, compact navigation, responsive controls, and consistent metric/chart cards.
- Correct numeric type assertions in hourly/daily aggregation tests across supported Laravel/PHP matrix jobs.
- Aggregate dashboard chart history into hourly averages for ranges longer than 24 hours and daily averages for ranges longer than 7 days.
- Keep raw observations for ranges up to 24 hours and preserve the latest live KPI values independently of chart aggregation.
- Show the active aggregation interval in the dashboard and localize it in all six supported languages.

## [0.26.0] - 2026-11-16

- Add shared dashboard chart date filters for the last 1 hour, 6 hours, 24 hours, 7 days, and custom start/end dates.
- Query persisted metric snapshots within the selected range and retain the selected range in browser local storage.
- Sample large result sets to a maximum of 2,000 chart points while preserving the final observation.
- Keep current KPI values based on the latest snapshot and localize date-range controls in all six supported languages.

## [0.25.0] - 2026-11-16

- Add configurable dashboard auto-refresh intervals: 15 seconds, 30 seconds, 1 minute, and 5 minutes.
- Allow users to pause and resume automatic refresh while keeping manual refresh available.
- Persist the selected interval in browser local storage and suspend scheduled requests while the dashboard tab is hidden.
- Show the last refresh attempt and localize all refresh controls in the six supported dashboard languages.

## [0.24.0] - 2026-11-16

- Add dashboard layout customization: hide/show sections, drag-and-drop ordering, browser-local persistence, and reset to defaults.
- Remove the dedicated Data Streams page, controller, routes, view, and feature tests; index-level data stream metadata remains available where relevant.
- Update English and Turkish documentation and all six dashboard locales.
- Keep dashboard layout preferences browser-local; no Elasticsearch cluster state or database schema is changed.

## [0.22.0] - 2026-11-15

- Add a dedicated Query Activity Monitor with per-node query/fetch counters, current operations, scroll/suggest rates, and search thread-pool pressure.
- Calculate query/fetch throughput, average operation time, and rejection rates from metric snapshot counter deltas.
- Add historical charts, node-level comparison, automatic refresh, and localized UI in all six supported languages.
- Reuse existing metric snapshots; no database migration or additional Elasticsearch API request is required.

## [0.23.0] - 2026-11-16

- Add API Request History for Elasticsearch GET calls made by the monitoring panel.
- Record endpoint, HTTP status, duration, success/failure, and error details; show top-called and slowest endpoints.
- Strip URL query parameters and never record credentials or request bodies.
- Add a localized history page and feature tests.

# Changelog

## [0.21.0] - 2026-11-15

- Add a one-click Clear filters action to reset search, favorites-only, hidden-index, and rollup-index filters.
- Restore the default index sorting and refresh the list using default visibility options.
- Localize the action in all six supported dashboard languages.

## [0.20.0] - 2026-11-15

- Remember the Index Management search query and favorites-only filter in browser local storage.
- Restore saved list preferences on return while allowing an explicit `search` URL parameter to take precedence.
- Gracefully fall back to the default view when browser storage is unavailable.

## [0.19.0] - 2026-11-15

- Remember the selected Index Management sort column and direction in browser local storage and restore them on return.
- Validate stored sort preferences and gracefully fall back to the default order when unavailable or invalid.

## [0.18.0] - 2026-11-15

- Add browser-side sorting for index name, health, primary/replica shard counts, document count, and storage size.
- Support ascending/descending toggling and preserve sorting alongside search and favorites filters without additional Elasticsearch requests.

## [0.17.0] - 2026-11-15

- Add one-click copying for index names, node names and IP addresses, shard index/node identifiers, and snapshot repository/name values.
- Add full mapping JSON copy and individual top-level mapping section copy actions with localized clipboard feedback.
- Store node IP addresses with newly collected node history without requiring a database migration.


## [0.16.0] - 2026-11-15

- Add browser-side CSV and JSON exports for the currently filtered index list, including search and favorites-only filters.
- Include health, status, document count, storage bytes, shard counts, optional creation date, data stream, and lifecycle metadata without adding an export API or Elasticsearch write operations.

## [0.15.0] - 2026-11-15

- Add browser-local favorite indices with one-click toggling and a favorites-only filter on the Index Management page.
- Add a dashboard quick-access section for favorite indices; favorites are stored in localStorage and require no database migration or Elasticsearch write operations.

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
