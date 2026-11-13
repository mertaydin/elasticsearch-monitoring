# Elasticsearch Monitoring

Laravel 11 ve 12 uygulamaları için Elasticsearch 8.18+ ve 9.x küme izleme paketi.

## Gereksinimler

- PHP 8.2+
- Laravel 11 veya 12
- Elasticsearch 8.18+ ya da 9.x

## Kurulum

```bash
composer require mertaydin/elasticsearch-monitoring
php artisan vendor:publish --tag=elasticsearch-monitoring-config
php artisan migrate
```

Ortam değişkenleri:

```dotenv
ELASTICSEARCH_MONITORING_URL=http://127.0.0.1:9200
ELASTICSEARCH_MONITORING_USERNAME=
ELASTICSEARCH_MONITORING_PASSWORD=
ELASTICSEARCH_MONITORING_API_KEY=
ELASTICSEARCH_MONITORING_TIMEOUT=10
ELASTICSEARCH_MONITORING_VERIFY=true
```

İlk ölçümü almak için:

```bash
php artisan elasticsearch:collect
```

Panel: `/monitoring/elasticsearch`

Varsayılan panel middleware listesi `web,auth` şeklindedir. Ayarları `config/elasticsearch-monitoring.php` üzerinden değiştirebilirsiniz. Otomatik dakikalık toplama için `register_schedule` değerini `true` yapın ve Laravel scheduler'ınızı çalışır durumda tutun.

## Desteklenen ölçümler

Küme sağlığı, node sayısı, JVM heap, CPU, dosya sistemi, belge ve shard sayıları, search/write thread-pool kuyrukları ve reddedilen işler, arama ve indeksleme sayaçlarından hesaplanan saniyelik hızlar.

Sayaçlardan hız hesaplamak için en az iki başarılı toplama gerekir.

## Güvenlik

Paneli herkese açık bırakmayın. Kimlik doğrulama ve uygun yetkilendirme middleware'leri kullanın. Elasticsearch kimlik bilgilerini ortam değişkenlerinde saklayın; TLS doğrulamasını üretimde kapatmayın.

## Lisans

MIT
