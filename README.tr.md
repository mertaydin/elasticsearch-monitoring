# Elasticsearch Monitoring

- Dashboard geçmiş grafiklerini son 1, 6, 24 saat, son 7 gün veya özel başlangıç/bitiş tarihine göre filtreleyin. Seçim tarayıcıda saklanır ve büyük tarih aralıklarında yanıt en fazla 2.000 örneklenmiş noktayla sınırlandırılır.

![CI](https://github.com/mertaydin/elasticsearch-monitoring/actions/workflows/tests.yml/badge.svg?branch=main)
[![Latest Version](https://img.shields.io/packagist/v/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Total Downloads](https://img.shields.io/packagist/dt/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Monthly Downloads](https://img.shields.io/packagist/dm/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![Daily Downloads](https://img.shields.io/packagist/dd/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](https://packagist.org/packages/mertaydin/elasticsearch-monitoring)
[![License](https://img.shields.io/packagist/l/mertaydin/elasticsearch-monitoring.svg?style=flat-square)](LICENSE)


Laravel 12 ve 13 uygulamaları için Elasticsearch 8.18+ ve 9.x izleme paketi.

**Tasarım gereği salt okunurdur:** paket yalnızca Elasticsearch metriklerini, indeks metaverilerini, mapping, settings ve lifecycle bilgilerini okur. İndeks oluşturmaz, güncellemez, kapatmaz veya silmez; mapping, settings, shard dağılımı ya da ILM politikalarını değiştirmez.

## Özellikler

- Seçili tarih aralığı, küme adı, temel metrikler, görünür performans grafikleri, varsa önceki dönem karşılaştırması ve o dönemde kaydedilen notları içeren PDF dashboard raporu oluşturun. PDF tarayıcıda hazırlanır; ek Elasticsearch isteği veya sunucu tarafı PDF bağımlılığı gerektirmez.

- Dashboard grafiklerini tek tek başlıklı PNG olarak indirin veya görünür tüm grafikleri tek bir PNG görselinde birleştirin. Seçili tarih aralığı dışa aktarılan görsele eklenir.
- Dashboard geçmiş grafiklerini aynı uzunluktaki bir önceki dönemle karşılaştırın. Önceki dönem çizgileri seçili zaman aralığına hizalanır ve karşılaştırma tercihi tarayıcıda saklanır.
- Daha düzenli gezinme çubuğu, belirgin görsel hiyerarşi, mobil uyumlu kontroller ve tutarlı KPI/sağlık/grafik kartlarıyla yenilenmiş dashboard tasarımı.
- Uzun dashboard grafiklerinde 24 saati aşan aralıklar saatlik, 7 günü aşan aralıklar günlük ortalamalarla gösterilir. Daha kısa aralıklarda ham ölçümler korunur.

- Dashboard otomatik yenileme aralığını (15 saniye, 30 saniye, 1 dakika veya 5 dakika) seçin; yenilemeyi duraklatın/sürdürün ve arka planda gereksiz istekleri durdurun.

- Atanmamış veya atanmış shard'lar için node bazında dağıtım kararlarını açıklayan Shard Allocation Explorer. Bu teşhis endpoint'i Elasticsearch cluster `monitor` yetkisi gerektirir ve cluster durumunu değiştirmez.
- Sık kullanılan indeksleri tarayıcıda favorilere ekleyin, listeyi favorilere göre filtreleyin ve dashboard üzerinden hızlıca açın.
- Arama ve favori filtreleriyle görünen indeks listesini tarayıcıdan UTF-8 CSV veya JSON olarak indirin.
- İndeks, node, shard ve snapshot bilgilerini tek tıkla kopyalayın.
- Mapping içeriğinin tamamını biçimlendirilmiş JSON olarak veya üst seviye bölümlerini ayrı ayrı kopyalayın.
- Kopyalama bildirimlerini altı dilde gösterin ve node geçmişinde IP adreslerini görüntüleyin.
- İndeks Yönetimi ekranına indeks adı, sağlık, primary/replica shard sayısı, belge sayısı ve depolama boyutu için sütun sıralama eklendi.
- İndeks Yönetimi arama metnini ve yalnızca favoriler seçimini tarayıcının yerel depolama alanında hatırlayın.
- Tek tıkla tüm indeks filtrelerini temizleyin ve sıralamayı varsayılana döndürün.
- Elasticsearch API İstek Geçmişi ekranında endpoint, HTTP durum kodu, yanıt süresi, hata kayıtları ve en çok/en yavaş endpoint sıralamalarını inceleyin.
- Node bazında query/fetch hızlarını, ortalama işlem sürelerini, aktif arama işlemlerini, search kuyruğunu ve reddedilme oranlarını geçmiş grafiklerle izleyin.
- Dashboard bölümlerini gizleyin, sürükleyerek sıralayın ve varsayılan düzene dönün. Tercihler mevcut tarayıcı profilinde saklanır.
- Yedi özet KPI kartını bağımsız yönetin: kartları gösterin/gizleyin, sürükleyerek sıralayın ve önemli kartları üste sabitleyin. Tercihler tarayıcıda saklanır; migration veya ek Elasticsearch isteği gerekmez.

- İlgili geçmiş grafiğine ve tam tarih/saat bilgisine bağlı kalıcı dashboard notları ekleyin. Notlar hesap bazında saklanır ve seçili tarih aralığında ilgili grafikte işaretlenir.

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

Panel: `/monitoring/elasticsearch`. İndeks yönetimi ve indeks bazlı performans analizi: `/monitoring/elasticsearch/indices`. Node performans geçmişi: `/monitoring/elasticsearch/nodes`. Thread Pool analizi: `/monitoring/elasticsearch/thread-pools`. Yedekleme izleme: `/monitoring/elasticsearch/snapshots`. Shard dağılımı: `/monitoring/elasticsearch/shards`. Kapasite analizi: `/monitoring/elasticsearch/capacity`. Alarm geçmişi: `/monitoring/elasticsearch/alerts`. Bildirim gönderim geçmişi: `/monitoring/elasticsearch/alerts/notifications`. Sorgu Aktivitesi: `/monitoring/elasticsearch/query-activity`. Varsayılan middleware `web,auth` şeklindedir. Ayarları `config/elasticsearch-monitoring.php` üzerinden değiştirebilirsiniz.

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

İndeks detay penceresindeki performans analizinde indeksleme, arama, merge, refresh, flush, query/request cache, segment ve translog istatistikleri primary ve toplam (primary + replica) değerleriyle karşılaştırılır. Yalnızca seçilen indeks sorgulanır; performans sekmesi açıkken 30 saniyede bir yenilenir. Elasticsearch tarafında ilgili indeks için `monitor` yetkisi gerekir.

Shard dağılımı ekranında primary/replica yerleşimi, shard durumları, atanmama nedenleri ve node başına shard sayıları görüntülenir. Küme sağlık puanı, aktif uyarılar, node bazında CPU/JVM/disk kullanımı, belge/shard sayıları, thread-pool kuyrukları ve reddedilen işler izlenir. Thread Pool Analyzer ekranında node ve thread pool bazında aktif iş, kuyruk, thread, tamamlanan ve reddedilen işlem sayılarını; geçmiş kuyruk ve rejection hızlarını inceleyebilirsiniz. Yedekleme izleme ekranında kayıtlı repository'leri, son snapshot'ları, shard sonuçlarını, hataları ve son başarılı yedeklemeyi görüntüleyebilirsiniz. `/monitoring/elasticsearch/nodes` ekranında node seçerek CPU, JVM heap, disk, arama/indeksleme hızı ve kuyruk geçmişini son 1.440 ölçüme kadar grafiklerle inceleyebilirsiniz. Uyarı eşikleri paket yapılandırmasındaki `alerts.thresholds` altında değiştirilebilir. Arama ve indeksleme hızları sayaç farklarından hesaplanır; ilk ölçümden sonra ikinci başarılı ölçüm gerekir. Alarm geçmişi, alarmın ilk oluşma zamanını, sonraki gözlemleri, son değerini ve çözülme zamanını kaydeder. Aktif ve çözülmüş kayıtları `/monitoring/elasticsearch/alerts` adresinden inceleyebilirsiniz. Güncelleme sonrasında mevcut kurulumlarda `php artisan migrate` çalıştırılmalıdır.

Dakikalık otomatik toplama için `register_schedule=true` yapın ve Laravel scheduler'ı çalıştırın.

Yedekleme izleme ekranı Elasticsearch tarafında `monitor_snapshot` cluster yetkisi gerektirir. Her repository için en son 50 snapshot kaydını okur, sonuçları 60 saniye önbelleğe alır; repository ayarlarını göstermez ve yedekleme işlemi başlatmaz.

## Sorgu Aktivitesi İzleme

Sorgu Aktivitesi ekranı node bazında query ve fetch sayaçlarını, anlık query/fetch işlemlerini, scroll ve suggest hızlarını, ortalama query/fetch sürelerini, search thread pool kuyruğunu ve reddedilme hızını gösterir. Hızlar ve ortalama süreler başarılı ölçümler arasındaki sayaç farklarından hesaplanır; değerlerin oluşması için en az iki ölçüm gerekir. Ekran son 1.440 metrik kaydını kullanır, görünümü 30 saniyede bir yeniler ve Elasticsearch'e ek istek göndermez. Güncellemeden sonra `php artisan elasticsearch:collect` çalıştırılmalıdır; özellik öncesinde alınan geçmiş ölçümlerde sorgu aktivitesi alanları bulunmaz.

## Elasticsearch API İstek Geçmişi

`/monitoring/elasticsearch/api-requests` ekranı, izleme panelinin Elasticsearch'e gönderdiği GET isteklerini kaydeder. Endpoint, HTTP durum kodu, yanıt süresi, başarılı/başarısız sonucu ve hata mesajı görüntülenir; en çok çağrılan ve ortalama yanıt süresi en yüksek endpoint'ler ayrıca sıralanır. Son 500 istek listelenir. URL sorgu parametreleri kaydedilmez; kimlik bilgileri ve istek gövdeleri loglanmaz. Özellik `elasticsearch_api_request_logs` tablosunu oluşturur; güncelleme sonrası `php artisan migrate` çalıştırın.

## Favoriler

Favori indeksler tarayıcının yerel depolama alanında saklanır. Yalnızca mevcut tarayıcı ve tarayıcı profili için geçerlidir; kullanıcılar veya cihazlar arasında eşitlenmez ve veritabanı migration'ı gerektirmez.

## İndeks listesini sıralama

İndeks listesini sıralamak için **Ad**, **Sağlık**, **Primary**, **Replica**, **Belge sayısı** veya **Depolama boyutu** sütun başlığına tıklayın. İlk tıklama artan, aynı başlığa ikinci tıklama azalan sıralama yapar. Sağlık sırası Green, Yellow ve Red şeklindedir. Sıralama tarayıcıda yapılır; arama ve favori filtreleriyle birlikte çalışır ve Elasticsearch'e ek istek göndermez. Seçtiğiniz sütun ve sıralama yönü tarayıcının yerel depolama alanına kaydedilir ve sayfaya tekrar geldiğinizde geri yüklenir.

## Dashboard notları

**Dashboard notları** bölümünden önemli olayları ilgili geçmiş grafiğine ve kesin tarih/saat bilgisine bağlayabilirsiniz. Notlar paket veritabanında oturum açmış kullanıcı bazında saklanır; zaman damgası seçili grafik aralığına denk geldiğinde ilgili grafikte işaret olarak görünür. Not ekleyebilir, düzenleyebilir ve silebilirsiniz; en son 200 not listelenir. `elasticsearch_dashboard_notes` tablosunu oluşturmak için güncelleme sonrasında `php artisan migrate` çalıştırın. Notlar Elasticsearch'e yazılmaz.

## İndeks listesi tercihlerini hatırlama

İndeks Yönetimi ekranı mevcut arama metnini ve yalnızca favoriler seçimini tarayıcının yerel depolama alanına kaydeder; sayfaya tekrar geldiğinizde bu tercihler geri yüklenir. Sayfa URL'sinde `search` parametresi varsa kayıtlı arama metni yerine URL'deki değer kullanılır. Tercihler yalnızca mevcut tarayıcı profiline aittir, kullanıcılar veya cihazlar arasında eşitlenmez ve veritabanı migration'ı gerektirmez. Tarayıcı depolaması kullanılamıyorsa ekran varsayılan görünümle çalışmaya devam eder.

## İndeks listesini sıfırlama

İndeks Yönetimi ekranındaki **Filtreleri temizle** düğmesi arama metnini, yalnızca favoriler seçimini ve gizli/rollup indeks filtrelerini temizler; sıralamayı varsayılana döndürür. İşlem tarayıcıda yapılır ve indeks listesi varsayılan görünürlük seçenekleriyle yeniden yüklenir.

## İndeks dışa aktarma

İndeks Yönetimi ekranındaki **CSV indir** veya **JSON indir** düğmeleriyle mevcut filtrelenmiş listeyi indirebilirsiniz. Dışa aktarma arama metnini ve yalnızca favoriler filtresini dikkate alır. İndeks adı, sağlık, durum, belge sayısı, depolama baytı, primary/replica shard sayıları, Elasticsearch tarafından döndürülüyorsa oluşturulma tarihi, data stream ve lifecycle bilgilerini içerir. Dosya tarayıcıda oluşturulur; ek endpoint, veritabanı migration'ı veya Elasticsearch yazma isteği kullanılmaz.

## Kopyalama

İndeks Yönetimi, Node Geçmişi, Shard Dağılımı ve Snapshot ekranlarında indeks adlarını, node adı/IP adresini, shard indeks/node bilgilerini ve repository/snapshot adlarını tek tıkla kopyalayabilirsiniz. İndeks detaylarında mapping'in tamamını biçimlendirilmiş JSON olarak veya üst seviye mapping bölümlerini ayrı ayrı kopyalamak mümkündür. Kopyalama bildirimi altı dilde gösterilir. Node IP adresleri node istatistikleri yanıtından alınır ve sonraki ölçümlerde saklanır; geçmiş ölçümler geriye dönük güncellenmez.

## Güvenlik ve salt okunur çalışma

Paket Elasticsearch üzerinde hiçbir veri veya yapılandırma değişikliği yapmaz. İndeks, mapping, settings, shard veya ILM politikalarını değiştiren bir özellik içermez. İndeks detaylarında ILM politikası, aşama, işlem, adım, indeks yaşı, rollover alias ve varsa hata adımı salt okunur olarak görüntülenir.

## Lisans

MIT


### Alarm bildirimleri

Bildirimler varsayılan olarak kapalıdır. Yayınladığınız paket yapılandırmasında `alerts.notifications.channels` altında e-posta, Slack ve Microsoft Teams kanallarını ayrı ayrı etkinleştirebilirsiniz. E-posta alıcılarını, webhook adreslerini ve `cooldown_minutes` bekleme süresini belirleyin. Yeni alarm olayları kanal başına bir kez gönderilir; aktif alarmın sonraki ölçümleri tekrar bildirim oluşturmaz. Çözülüp bekleme süresi içinde yeniden oluşan alarmlar bastırılır ve geçmişe kaydedilir. Başarısız kuyruk işleri en fazla üç kez yeniden denenir. Asenkron kuyruk kullanıyorsanız Laravel queue worker çalıştırın. Gönderim, hata ve beklemeye alınan kayıtları `/monitoring/elasticsearch/alerts/notifications` adresinden inceleyebilirsiniz.


## Dashboard düzeni tercihleri

**Paneli düzenle** seçeneğiyle özet, favoriler, küme sağlığı, metrikler, node karşılaştırması ve grafik bölümlerini gizleyebilir veya gösterebilirsiniz. Listedeki bölümleri sürükleyerek sıralayabilir, varsayılan düzene dönebilirsiniz. Tercihler mevcut tarayıcı profilinin localStorage alanında tutulur; cihazlar arasında eşitlenmez ve veritabanı migration'ı gerektirmez.

## Dashboard otomatik yenileme

Dashboard için **15 saniye, 30 saniye, 1 dakika veya 5 dakika** yenileme aralığını seçebilirsiniz. Otomatik yenilemeyi istediğiniz zaman duraklatıp sürdürebilirsiniz. Seçilen aralık mevcut tarayıcı profilinde saklanır ve sonraki ziyaretinizde geri yüklenir. Dashboard sekmesi arka plandayken otomatik istekler durdurulur, sekme yeniden görünür olduğunda yenileme devam eder. Son yenileme denemesinin zamanı da gösterilir. Otomatik yenileme duraklatılsa bile manuel yenileme kullanılabilir.

## Dashboard grafiklerini dışa aktarma

Her grafiğin üzerindeki **PNG indir** seçeneğiyle grafiği başlığı ve seçili tarih aralığıyla birlikte ayrı kaydedebilir, **Tüm grafikleri PNG indir** seçeneğiyle görünür dashboard grafiklerini tek bir görselde birleştirebilirsiniz. Görseller tarayıcıda oluşturulur; ek bir endpoint, veritabanı değişikliği veya Elasticsearch isteği gerekmez.

## Elasticsearch bağlantı durumu

Dashboard başlığında Elasticsearch bağlantısı **Bağlı**, **Yavaş yanıt** veya **Bağlantı kesildi** olarak gösterilir; son başarılı bağlantı zamanı ve kontrol yanıt süresi de görüntülenir. Durum, metrik toplama işleminden bağımsız olarak 30 saniyede bir kontrol edilir. Yavaş yanıt eşiği varsayılan olarak 1.000 ms'dir; `ELASTICSEARCH_MONITORING_SLOW_RESPONSE_MS` ortam değişkeniyle veya yayınlanan yapılandırmadaki `connection.slow_response_ms` değeriyle değiştirilebilir. Kontrol kimlik doğrulamalı, salt okunur bir GET isteği kullanır ve küme durumunu değiştirmez.
