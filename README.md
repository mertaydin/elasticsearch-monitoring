# Elasticsearch Monitoring

![CI](https://github.com/mertaydin/elasticsearch-monitoring/actions/workflows/tests.yml/badge.svg?branch=main)
[![Latest Version](https://img.shields.io/packagist/v/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Total Downloads](https://img.shields.io/packagist/dt/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Monthly Downloads](https://img.shields.io/packagist/dm/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Daily Downloads](https://img.shields.io/packagist/dd/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![License](https://img.shields.io/packagist/l/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](LICENSE)


A Laravel package for monitoring Elasticsearch clusters with metric collection and a lightweight dashboard.

**Read-only by design:** the package only reads Elasticsearch metrics, index metadata, mappings, settings, and lifecycle information. It never creates, updates, closes, or deletes indices, and never changes mappings, settings, shard allocation, or ILM policies.

## Features

- Shard Allocation Explorer with per-node allocation explanations for unassigned or assigned shards. The allocation explain API is used only for diagnostics and never changes cluster state.

## Features

- Shard Allocation Explorer with per-node allocation explanations for unassigned or assigned shards. This diagnostic endpoint requires the Elasticsearch cluster `monitor` privilege and does not change cluster state.
- Save frequently used indices as browser-local favorites, filter the index list to favorites, and open them quickly from the dashboard.
- Export the currently visible index list (including search and favorites filters) as UTF-8 CSV or JSON directly in the browser.
- Copy index names, node names and IP addresses, shard index/node identifiers, and snapshot repository/name values with one click.
- Copy the complete formatted mapping JSON or individual top-level mapping sections from Index Management.
- Show localized clipboard feedback in all six supported languages.
- Add sortable columns to Index Management for index name, health, primary/replica shard counts, document count, and storage size.

## Requirements

- PHP 8.2+ for Laravel 12; PHP 8.3+ for Laravel 13
- Laravel 12 or 13
- Elasticsearch 8.18+ or 9.x

## Installation

```bash
composer require mertaydin/elasticsearch-monitoring
php artisan vendor:publish --tag=elasticsearch-monitoring-config
php artisan migrate
```

Configure your connection in `.env`:

```dotenv
ELASTICSEARCH_MONITORING_URL=http://127.0.0.1:9200
ELASTICSEARCH_MONITORING_USERNAME=
ELASTICSEARCH_MONITORING_PASSWORD=
ELASTICSEARCH_MONITORING_API_KEY=
ELASTICSEARCH_MONITORING_TIMEOUT=10
ELASTICSEARCH_MONITORING_VERIFY=true
```

Collect a snapshot with `php artisan elasticsearch:collect`.

Dashboard: `/monitoring/elasticsearch`  
Index management and per-index performance analysis: `/monitoring/elasticsearch/indices`  
Data Streams: `/monitoring/elasticsearch/data-streams`  
Node performance history: `/monitoring/elasticsearch/nodes`  
Thread pool analysis: `/monitoring/elasticsearch/thread-pools`  
Snapshot & backup monitoring: `/monitoring/elasticsearch/snapshots`  
Shard allocation: `/monitoring/elasticsearch/shards`  
Capacity analysis: `/monitoring/elasticsearch/capacity`  
Alert history: `/monitoring/elasticsearch/alerts`  
Notification delivery history: `/monitoring/elasticsearch/alerts/notifications`

The index details modal includes a read-only performance analyzer for indexing, search, merge, refresh, flush, query/request cache, segments, and translog statistics. It compares primary-shard values with totals across primary and replica shards and refreshes only the selected index. The Elasticsearch `monitor` index privilege is required. The dashboard uses `web` and `auth` middleware by default. Configure the middleware, dashboard path, and locale in `config/elasticsearch-monitoring.php`. Set `register_schedule` to `true` to register a one-minute collection task and ensure Laravel's scheduler is running.

### Dashboard authorization

You can register an authorization callback in your application's `AppServiceProvider::boot()` method, just like Laravel LogViewer. The callback receives the current request and must return `true` to allow access:

```php
use ElasticsearchMonitoring\Facades\ElasticsearchMonitoring;

public function boot(): void
{
    ElasticsearchMonitoring::auth(function ($request) {
        return $request->user() && $request->user()->hasRole('SuperAdmin');
    });
}
```

If the callback returns `false`, the package responds with HTTP 403. If no callback is registered, the existing middleware behavior is preserved (`web` and `auth` by default). Keep the `auth` middleware enabled unless your callback intentionally handles authentication itself.

## Favorites

Favorite indices are stored in the browser's local storage. They are specific to the current browser and browser profile, are not synchronized between users or devices, and do not require a database migration.

## Index list sorting

Click the **Name**, **Health**, **Primaries**, **Replicas**, **Documents count**, or **Storage size** column heading to sort the index list. The first click sorts ascending; clicking the same heading again reverses the order. Health is ordered Green, Yellow, then Red. Sorting runs in the browser, works together with search and favorites filters, and does not send additional Elasticsearch requests.

## Index exports

Use **Export CSV** or **Export JSON** on the Index Management page to download the currently filtered list. Exports respect the search query and favorites-only filter and include index name, health, status, document count, storage bytes, primary/replica shard counts, creation date when returned by Elasticsearch, data stream, and lifecycle metadata. Files are generated locally in the browser; no export endpoint, database migration, or Elasticsearch write request is used.

## Metrics

Cluster health score, active alerts, per-node CPU/JVM/disk usage, documents, shards, search/write thread-pool queues and rejections, plus search and indexing rates calculated from counter deltas. The Thread Pool Analyzer provides per-node, per-pool queue, active, thread, completed, and rejected metrics with historical queue and rejection-rate charts. Snapshot Monitoring lists registered repositories, recent snapshots, shard outcomes, failures, and the latest successful backup. The dedicated Node Performance History page charts per-node CPU, JVM heap, filesystem utilization, search/indexing rates, and thread-pool queues from stored snapshots (up to the latest 1,440 collections). The Shard Allocation page displays primary/replica placement, shard states, unassigned reasons, and per-node shard distribution using the CAT Shards read API. Alert thresholds are configurable under `alerts.thresholds` in the package configuration. Email, Slack, and Microsoft Teams notifications can be enabled independently under `alerts.notifications.channels`; configure mail recipients or webhook URLs and set a cooldown period. Notification delivery attempts, failures, and cooldown suppressions are recorded at `/monitoring/elasticsearch/alerts/notifications`. Queue workers are recommended for non-blocking delivery; the default synchronous queue driver also works. Rates are available after at least two successful collections. Alert history records when an alert first appears, each subsequent observation, its latest value, and when it is resolved. Open `/monitoring/elasticsearch/alerts` to review active and resolved events. Existing installations should run `php artisan migrate` after updating.

Snapshot Monitoring requires the Elasticsearch `monitor_snapshot` cluster privilege. It reads repository metadata and the latest 50 snapshots per repository, caches results for 60 seconds, and does not expose repository settings or perform backup operations.

## Copying identifiers and mappings

Copy buttons are available for index names, node names and IP addresses, shard index/node identifiers, and snapshot repository/snapshot names. The Index Management details modal supports copying the complete formatted mapping JSON or an individual top-level mapping section. Clipboard feedback is localized across all six supported languages. Node IP addresses are taken from the node statistics response and stored with subsequent metric snapshots; historical snapshots are not backfilled.

## Compatibility

The package detects the Elasticsearch version from `GET /` and supports Elasticsearch 8.18+ and 9.x. It uses Laravel's HTTP client; the official Elasticsearch PHP SDK is not required.

## Localization

Turkish (`tr`), English (`en`), Simplified Chinese (`zh_CN`), Hindi (`hi`), Spanish (`es`), and Arabic (`ar`). Arabic uses RTL layout.

## Security

Keep the dashboard behind authentication and authorization. Store credentials in environment variables and keep TLS verification enabled in production. The package is strictly read-only against Elasticsearch; it has no index, mapping, settings, shard, or ILM modification features. Index details display ILM policy, phase, action, step, index age, rollover alias, and any failed-step information in read-only form. A separate Data Streams page displays stream metadata, backing indices, generation, template, ILM policy, document counts, and storage usage. Clusters using only classic indices can continue using the package without any Data Streams configuration.

## Development

```bash
composer install
composer test
composer analyse
composer pint-test
```

## Documentation

- [Türkçe README](README.tr.md)
- [Changelog](CHANGELOG.md)
- [Contributing](CONTRIBUTING.md)
- [Security](SECURITY.md)

## License

MIT


### Alert notifications

Notifications are disabled by default. Enable only the channels you use in the published package configuration:

```php
'alerts' => [
    'notifications' => [
        'cooldown_minutes' => 30,
        'channels' => [
            'mail' => ['enabled' => true, 'recipients' => ['ops@example.com']],
            'slack' => ['enabled' => true, 'webhook_url' => env('ELASTICSEARCH_MONITORING_SLACK_WEBHOOK')],
            'teams' => ['enabled' => true, 'webhook_url' => env('ELASTICSEARCH_MONITORING_TEAMS_WEBHOOK')],
        ],
    ],
],
```

Set the Slack and Teams webhook environment variables in your application environment. New alert events are delivered once per channel; recurring observations of an active event do not resend notifications. If an alert resolves and reappears during the configured cooldown, delivery is suppressed and recorded. Failed queued deliveries are retried up to three times with backoff. Run a Laravel queue worker when using an asynchronous queue connection.
