# Elasticsearch Monitoring

![CI](https://github.com/mertaydin/elasticsearch-monitoring/actions/workflows/tests.yml/badge.svg?branch=main)
[![Latest Version](https://img.shields.io/packagist/v/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Total Downloads](https://img.shields.io/packagist/dt/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Monthly Downloads](https://img.shields.io/packagist/dm/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Daily Downloads](https://img.shields.io/packagist/dd/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![License](https://img.shields.io/packagist/l/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](LICENSE)


A Laravel package for monitoring Elasticsearch clusters with metric collection and a lightweight dashboard.

**Read-only by design:** the package only reads Elasticsearch metrics, index metadata, mappings, settings, and lifecycle information. It never creates, updates, closes, or deletes indices, and never changes mappings, settings, shard allocation, or ILM policies.

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

The dashboard uses `web` and `auth` middleware by default. Configure the middleware, dashboard path, and locale in `config/elasticsearch-monitoring.php`. Set `register_schedule` to `true` to register a one-minute collection task and ensure Laravel's scheduler is running.

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

## Metrics

Cluster health score, active alerts, per-node CPU/JVM/disk usage, documents, shards, search/write thread-pool queues and rejections, plus search and indexing rates calculated from counter deltas. Alert thresholds are configurable under `alerts.thresholds` in the package configuration. Rates are available after at least two successful collections.

## Compatibility

The package detects the Elasticsearch version from `GET /` and supports Elasticsearch 8.18+ and 9.x. It uses Laravel's HTTP client; the official Elasticsearch PHP SDK is not required.

## Localization

Turkish (`tr`), English (`en`), Simplified Chinese (`zh_CN`), Hindi (`hi`), Spanish (`es`), and Arabic (`ar`). Arabic uses RTL layout.

## Security

Keep the dashboard behind authentication and authorization. Store credentials in environment variables and keep TLS verification enabled in production. The package is strictly read-only against Elasticsearch; it has no index, mapping, settings, shard, or ILM modification features. Index details display ILM policy, phase, action, step, index age, rollover alias, and any failed-step information in read-only form.

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
