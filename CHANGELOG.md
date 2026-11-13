# Changelog

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
