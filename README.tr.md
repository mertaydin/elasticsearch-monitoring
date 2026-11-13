# Elasticsearch Monitoring

Laravel 12 ve 13 uygulamaları için Elasticsearch 8.18+ ve 9.x izleme paketi.

## Kurulum

```bash
composer require mertaydin/elasticsearch-monitoring
php artisan vendor:publish --tag=elasticsearch-monitoring-config
php artisan migrate
php artisan elasticsearch:collect
```

Örnek ortam değişkenleri:

```dotenv
ELASTICSEARCH_MONITORING_URL=http://127.0.0.1:9200
ELASTICSEARCH_MONITORING_USERNAME=
ELASTICSEARCH_MONITORING_PASSWORD=
ELASTICSEARCH_MONITORING_API_KEY=
ELASTICSEARCH_MONITORING_TIMEOUT=10
ELASTICSEARCH_MONITORING_VERIFY=true
```

Panel: `/monitoring/elasticsearch`. Varsayılan middleware `web,auth` şeklindedir. Ayarları `config/elasticsearch-monitoring.php` üzerinden değiştirebilirsiniz.

### Panel yetkilendirmesi

LogViewer benzeri bir callback'i `AppServiceProvider::boot()` içinde tanımlayabilirsiniz. Callback mevcut HTTP isteğini alır; `true` dönerse erişim verilir, `false` dönerse HTTP 403 yanıtı döner:

```php
use ElasticsearchMonitoring\Facades\ElasticsearchMonitoring;

public function boot(): void
{
    ElasticsearchMonitoring::auth(function ($request) {
        return $request->user() && $request->user()->hasRole('SuperAdmin');
    });
}
```

Callback tanımlamazsanız mevcut davranış korunur; varsayılan `web,auth` middleware'i oturum açmış kullanıcıları kabul eder. Callback'iniz kimlik doğrulamayı da kendisi yapmıyorsa `auth` middleware'ini kaldırmayın.

Küme sağlığı, node, JVM heap, CPU, dosya sistemi, belge/shard sayıları, thread-pool kuyrukları ve reddedilen işler izlenir. Arama ve indeksleme hızları sayaç farklarından hesaplanır; ilk ölçümden sonra ikinci başarılı ölçüm gerekir.

Dakikalık otomatik toplama için `register_schedule=true` yapın ve Laravel scheduler'ı çalıştırın.

## Lisans

MIT
