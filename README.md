# Elasticsearch Monitoring

A Laravel package for monitoring Elasticsearch clusters.

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Elasticsearch 8.18+ and 9.x

## Installation

```bash
composer require mertaydin/elasticsearch-monitoring
php artisan vendor:publish --tag=elasticsearch-monitoring-config
php artisan migrate
php artisan elasticsearch:collect
```

## License

MIT
