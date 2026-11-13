# Elasticsearch Monitoring

A Laravel package for monitoring Elasticsearch clusters with metric collection and a lightweight dashboard.

## Requirements

- PHP 8.2+
- Laravel 11 or 12
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

## Metrics

Cluster health, nodes, JVM heap, CPU, filesystem, documents, shards, search/write thread-pool queues and rejections, plus search and indexing rates calculated from counter deltas. Rates are available after at least two successful collections.

## Compatibility

The package detects the Elasticsearch version from `GET /` and supports Elasticsearch 8.18+ and 9.x. It uses Laravel's HTTP client; the official Elasticsearch PHP SDK is not required.

## Localization

Turkish (`tr`), English (`en`), Simplified Chinese (`zh_CN`), Hindi (`hi`), Spanish (`es`), and Arabic (`ar`). Arabic uses RTL layout.

## Security

Keep the dashboard behind authentication and authorization. Store credentials in environment variables and keep TLS verification enabled in production.

## Development

```bash
composer install
composer test
composer analyse
composer pint-test
```

## License

MIT
