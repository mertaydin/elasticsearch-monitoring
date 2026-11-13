# Elasticsearch Monitoring

![CI](https://github.com/mertaydin/elasticsearch-monitoring/actions/workflows/tests.yml/badge.svg?branch=main)
[![Latest Version](https://img.shields.io/packagist/v/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Total Downloads](https://img.shields.io/packagist/dt/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Monthly Downloads](https://img.shields.io/packagist/dm/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Daily Downloads](https://img.shields.io/packagist/dd/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![License](https://img.shields.io/packagist/l/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](LICENSE)


Laravel 12 ve 13 uygulamaları için Elasticsearch 8.18+ ve 9.x izleme paketi.

**Tasarım gereği salt okunurdur:** paket yalnızca Elasticsearch metriklerini, indeks metaverilerini, mapping, settings ve lifecycle bilgilerini okur. İndeks oluşturmaz, güncellemez, kapatmaz veya silmez; mapping, settings, shard dağılımı ya da ILM politikalarını değiştirmez.

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

Panel: `/monitoring/elasticsearch`. İndeks yönetimi: `/monitoring/elasticsearch/indices`. Data Streams ekranı: `/monitoring/elasticsearch/data-streams`. Varsayılan middleware `web,auth` şeklindedir. Ayarları `config/elasticsearch-monitoring.php` üzerinden değiştirebilirsiniz. Data Streams ekranı yalnızca bu yapıyı kullanan cluster'larda veri gösterir; klasik indekslerle çalışan cluster'larda mevcut işleyiş değişmez.

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

Küme sağlık puanı, aktif uyarılar, node bazında CPU/JVM/disk kullanımı, belge/shard sayıları, thread-pool kuyrukları ve reddedilen işler izlenir. Uyarı eşikleri paket yapılandırmasındaki `alerts.thresholds` altında değiştirilebilir. Arama ve indeksleme hızları sayaç farklarından hesaplanır; ilk ölçümden sonra ikinci başarılı ölçüm gerekir.

Dakikalık otomatik toplama için `register_schedule=true` yapın ve Laravel scheduler'ı çalıştırın.

## Güvenlik ve salt okunur çalışma

Paket Elasticsearch üzerinde hiçbir veri veya yapılandırma değişikliği yapmaz. İndeks, mapping, settings, shard veya ILM politikalarını değiştiren bir özellik içermez. İndeks detaylarında ILM politikası, aşama, işlem, adım, indeks yaşı, rollover alias ve varsa hata adımı salt okunur olarak görüntülenir. Ayrı Data Streams ekranında stream metaverileri, backing index'ler, generation, template, ILM politikası, belge sayısı ve depolama boyutu görüntülenir.

## Lisans

MIT
