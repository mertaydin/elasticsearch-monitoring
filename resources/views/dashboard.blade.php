@php
    $availableLocales = config('elasticsearch-monitoring.dashboard.available_locales', ['tr', 'en']);
    $defaultLocale = config('elasticsearch-monitoring.dashboard.locale', 'en');
    $requestedLocale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', $defaultLocale));
    $locale = in_array($requestedLocale, $availableLocales, true)
        ? $requestedLocale
        : (in_array($defaultLocale, $availableLocales, true) ? $defaultLocale : 'en');
    $languageNames = [
        'tr' => 'Türkçe',
        'en' => 'English',
        'zh_CN' => '简体中文',
        'hi' => 'हिन्दी',
        'es' => 'Español',
        'ar' => 'العربية',
    ];
    $t = fn (string $key): string => __("elasticsearch-monitoring::messages.{$key}", [], $locale);
    $indexManagementLabel = [
        'tr' => 'İndeks Yönetimi',
        'en' => 'Index Management',
        'es' => 'Gestión de índices',
        'zh_CN' => '索引管理',
        'hi' => 'इंडेक्स प्रबंधन',
        'ar' => 'إدارة الفهارس',
    ][$locale] ?? 'Index Management';
    $uiText = [
        'tr' => ['overview'=>'Küme genel görünümü','performance'=>'Performans','storage'=>'Depolama','cpu'=>'CPU kullanımı','cpu_history'=>'CPU kullanım grafiği','cpu_hint'=>'Node başına ortalama CPU kullanımı','index_history'=>'İndeks depolama grafiği','index_hint'=>'İndeks verilerinin toplam boyutunun zaman içindeki değişimi','disk_history'=>'Disk kullanımı','disk_hint'=>'Kullanılan disk alanının zaman içindeki değişimi','search_history'=>'Arama hızı','indexing_history'=>'İndeksleme hızı','segments_history'=>'Segment sayısı','documents_history'=>'Belge sayısı','filesystem_hint'=>'Kullanılan ve kullanılabilir disk alanı','heap_hint'=>'JVM heap bellek kullanımı','rates_hint'=>'Saniyedeki arama ve indeksleme işlem sayısı','live'=>'Otomatik yenileme · 30 sn','compare_period'=>'Önceki dönemle karşılaştır','previous_period'=>'Önceki dönem'],
        'en' => ['overview'=>'Cluster overview','performance'=>'Performance','storage'=>'Storage','cpu'=>'CPU usage','cpu_history'=>'CPU utilization','cpu_hint'=>'Average CPU usage across nodes over time','index_history'=>'Index storage trend','index_hint'=>'Total Elasticsearch index data size over time','disk_history'=>'Disk usage','disk_hint'=>'Filesystem space in use over time','search_history'=>'Search rate','indexing_history'=>'Indexing rate','segments_history'=>'Segment count','documents_history'=>'Document count','filesystem_hint'=>'Used and available disk capacity','heap_hint'=>'JVM heap memory utilization','rates_hint'=>'Search and indexing operations per second','live'=>'Auto refresh · 30 sec','compare_period'=>'Compare with previous period','previous_period'=>'Previous period'],
        'es' => ['overview'=>'Resumen del clúster','performance'=>'Rendimiento','storage'=>'Almacenamiento','cpu'=>'Uso de CPU','cpu_history'=>'Uso de CPU','cpu_hint'=>'Uso medio de CPU de los nodos a lo largo del tiempo','index_history'=>'Tendencia del almacenamiento de índices','index_hint'=>'Tamaño total de los datos de índices a lo largo del tiempo','disk_history'=>'Uso del disco','disk_hint'=>'Espacio del sistema de archivos utilizado a lo largo del tiempo','search_history'=>'Tasa de búsqueda','indexing_history'=>'Tasa de indexación','segments_history'=>'Número de segmentos','documents_history'=>'Número de documentos','filesystem_hint'=>'Espacio de disco usado y disponible','heap_hint'=>'Uso de memoria heap de JVM','rates_hint'=>'Búsquedas e indexaciones por segundo','live'=>'Actualización automática · 30 s','compare_period'=>'Comparar con el período anterior','previous_period'=>'Período anterior'],
        'zh_CN' => ['overview'=>'集群概览','performance'=>'性能','storage'=>'存储','cpu'=>'CPU 使用率','cpu_history'=>'CPU 使用趋势','cpu_hint'=>'各节点平均 CPU 使用率随时间的变化','index_history'=>'索引存储趋势','index_hint'=>'Elasticsearch 索引数据总量随时间的变化','disk_history'=>'磁盘使用量','disk_hint'=>'文件系统已用空间随时间的变化','search_history'=>'搜索速率','indexing_history'=>'索引速率','segments_history'=>'分段数量','documents_history'=>'文档数量','filesystem_hint'=>'已用和可用磁盘空间','heap_hint'=>'JVM 堆内存使用率','rates_hint'=>'每秒搜索和索引操作数','live'=>'每 30 秒自动刷新','compare_period'=>'与上一周期比较','previous_period'=>'上一周期'],
        'hi' => ['overview'=>'क्लस्टर अवलोकन','performance'=>'प्रदर्शन','storage'=>'स्टोरेज','cpu'=>'CPU उपयोग','cpu_history'=>'CPU उपयोग का रुझान','cpu_hint'=>'समय के साथ नोड्स का औसत CPU उपयोग','index_history'=>'इंडेक्स स्टोरेज का रुझान','index_hint'=>'समय के साथ Elasticsearch इंडेक्स डेटा का कुल आकार','disk_history'=>'डिस्क उपयोग','disk_hint'=>'समय के साथ उपयोग किया गया फ़ाइल सिस्टम स्थान','search_history'=>'खोज दर','indexing_history'=>'इंडेक्सिंग दर','segments_history'=>'सेगमेंट की संख्या','documents_history'=>'दस्तावेज़ों की संख्या','filesystem_hint'=>'उपयोग किया गया और उपलब्ध डिस्क स्थान','heap_hint'=>'JVM हीप मेमोरी उपयोग','rates_hint'=>'प्रति सेकंड खोज और इंडेक्सिंग ऑपरेशन','live'=>'हर 30 सेकंड में अपडेट','compare_period'=>'पिछली अवधि से तुलना करें','previous_period'=>'पिछली अवधि'],
        'ar' => ['overview'=>'نظرة عامة على المجموعة','performance'=>'الأداء','storage'=>'التخزين','cpu'=>'استخدام المعالج','cpu_history'=>'استخدام المعالج','cpu_hint'=>'متوسط استخدام المعالج عبر العقد بمرور الوقت','index_history'=>'اتجاه تخزين الفهارس','index_hint'=>'إجمالي حجم بيانات فهارس Elasticsearch بمرور الوقت','disk_history'=>'استخدام القرص','disk_hint'=>'مساحة نظام الملفات المستخدمة بمرور الوقت','search_history'=>'معدل البحث','indexing_history'=>'معدل الفهرسة','segments_history'=>'عدد المقاطع','documents_history'=>'عدد المستندات','filesystem_hint'=>'مساحة القرص المستخدمة والمتاحة','heap_hint'=>'استخدام ذاكرة JVM','rates_hint'=>'عمليات البحث والفهرسة في الثانية','live'=>'تحديث تلقائي كل 30 ثانية','compare_period'=>'مقارنة بالفترة السابقة','previous_period'=>'الفترة السابقة'],
    ][$locale] ?? [];

    $dashboardWidgetNames = [
        'tr' => ['summary'=>'Özet metrikler','favorites'=>'Favori indeksler','health'=>'Küme sağlığı ve uyarılar','metrics'=>'Detaylı metrikler','nodes'=>'Node karşılaştırması','charts'=>'Grafikler'],
        'en' => ['summary'=>'Summary metrics','favorites'=>'Favorite indices','health'=>'Cluster health and alerts','metrics'=>'Detailed metrics','nodes'=>'Node comparison','charts'=>'Charts'],
        'es' => ['summary'=>'Métricas resumidas','favorites'=>'Índices favoritos','health'=>'Salud del clúster y alertas','metrics'=>'Métricas detalladas','nodes'=>'Comparación de nodos','charts'=>'Gráficos'],
        'zh_CN' => ['summary'=>'摘要指标','favorites'=>'收藏索引','health'=>'集群健康与警报','metrics'=>'详细指标','nodes'=>'节点比较','charts'=>'图表'],
        'hi' => ['summary'=>'सारांश मेट्रिक्स','favorites'=>'पसंदीदा इंडेक्स','health'=>'क्लस्टर स्वास्थ्य और अलर्ट','metrics'=>'विस्तृत मेट्रिक्स','nodes'=>'नोड तुलना','charts'=>'चार्ट'],
        'ar' => ['summary'=>'المقاييس الموجزة','favorites'=>'الفهارس المفضلة','health'=>'صحة المجموعة والتنبيهات','metrics'=>'المقاييس التفصيلية','nodes'=>'مقارنة العقد','charts'=>'الرسوم البيانية'],
    ][$locale] ?? ['summary'=>'Summary metrics','favorites'=>'Favorite indices','health'=>'Cluster health and alerts','metrics'=>'Detailed metrics','nodes'=>'Node comparison','charts'=>'Charts'];

    $favoriteText = [
        'tr' => ['title'=>'Favori indeksler','empty'=>'Henüz favori indeks eklemediniz. İndeks yönetimi ekranında yıldız simgesini kullanarak ekleyebilirsiniz.','open'=>'İndeksi aç'],
        'en' => ['title'=>'Favorite indices','empty'=>'You have not added any favorite indices yet. Use the star in Index Management to add some.','open'=>'Open index'],
        'es' => ['title'=>'Índices favoritos','empty'=>'Aún no has añadido índices favoritos. Usa la estrella en Gestión de índices para añadirlos.','open'=>'Abrir índice'],
        'zh_CN' => ['title'=>'收藏的索引','empty'=>'尚未添加收藏索引。请在索引管理页面点击星标添加。','open'=>'打开索引'],
        'hi' => ['title'=>'पसंदीदा इंडेक्स','empty'=>'अभी कोई पसंदीदा इंडेक्स नहीं जोड़ा गया है। जोड़ने के लिए इंडेक्स प्रबंधन में स्टार चुनें।','open'=>'इंडेक्स खोलें'],
        'ar' => ['title'=>'الفهارس المفضلة','empty'=>'لم تضف أي فهارس مفضلة بعد. استخدم النجمة في إدارة الفهارس لإضافتها.','open'=>'فتح الفهرس'],
    ][$locale] ?? ['title'=>'Favorite indices','empty'=>'No favorite indices yet.','open'=>'Open index'];

@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $t('title') }}</title>
    @if ($locale === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root{--em-ink:#182238;--em-muted:#77839a;--em-line:#e7ebf3;--em-surface:#fff;--em-bg:#f3f5fa;--em-primary:#4f5bd5}
        body{background:var(--em-bg)!important;color:var(--em-ink);font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;letter-spacing:-.01em}
        main.container{max-width:1580px;padding-top:28px!important}
        .dashboard-header{border:1px solid rgba(220,226,239,.85);border-radius:22px;background:#fff;overflow:hidden;box-shadow:0 12px 35px rgba(31,45,82,.055)}
        .dashboard-hero{position:relative;isolation:isolate;overflow:hidden;padding:28px 30px;background:linear-gradient(112deg,#141d38 0%,#253765 58%,#5158bd 100%);color:#fff}
        .dashboard-hero:after{content:"";position:absolute;z-index:-1;width:340px;height:340px;right:9%;top:-245px;border:1px solid rgba(255,255,255,.14);border-radius:50%;box-shadow:0 0 0 35px rgba(255,255,255,.035),0 0 0 75px rgba(255,255,255,.025)}
        .dashboard-eyebrow{font-size:.66rem;font-weight:800;letter-spacing:.17em;color:#b7c5ff;margin-bottom:9px}
        .dashboard-hero h1{font-size:1.55rem;font-weight:800;letter-spacing:-.045em}
        .dashboard-hero p{font-size:.86rem;color:#d1daf1}
        .dashboard-header-actions{display:flex;align-items:center;gap:9px;flex-wrap:wrap}
        .dashboard-language-select{min-width:126px;background-color:rgba(255,255,255,.12)!important;color:#fff!important;border:1px solid rgba(255,255,255,.24)!important}
        .dashboard-language-select option{color:#182238}
        .dashboard-primary-action{background:#fff!important;color:#26386d!important;border:0!important;font-weight:700!important;padding:.55rem .9rem!important;box-shadow:0 5px 14px rgba(0,0,0,.12)}
        .dashboard-nav{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:11px 16px;background:#fff}
        .dashboard-nav-links{display:flex;align-items:center;gap:5px;min-width:0;overflow-x:auto;scrollbar-width:thin}
        .dashboard-nav-link{display:inline-flex;align-items:center;white-space:nowrap;text-decoration:none;color:#66728a;font-size:.77rem;font-weight:650;padding:9px 11px;border-radius:9px;transition:background .18s,color .18s}
        .dashboard-nav-link:hover,.dashboard-nav-link.active{background:#eef1ff;color:#414fbd}
        .dashboard-nav-tools{display:flex;gap:7px;flex:none}
        .dashboard-nav-tools .btn{font-size:.75rem;font-weight:650;border-radius:9px;padding:.48rem .7rem}
        #dashboard-refresh-controls,#dashboard-history-controls{border:1px solid var(--em-line);border-radius:15px;background:#fff;padding:13px 16px;box-shadow:0 4px 15px rgba(31,45,82,.025)}
        #dashboard-refresh-controls{min-height:58px}
        #dashboard-refresh-controls .form-select,#dashboard-history-controls .form-select,#dashboard-history-controls input{border-color:#e2e7f0;border-radius:9px;color:#33415e}
        #dashboard-refresh-controls .btn,#dashboard-history-controls .btn{border-radius:9px;font-weight:650}
        #dashboard-history-controls{padding:16px 18px}
        #dashboard-history-controls .form-label{font-weight:700;color:#66728a!important}
        #dashboard-history-summary{font-weight:600}
        .chart-card-heading{display:flex;align-items:center;justify-content:space-between;gap:10px;min-height:1.8rem;margin-bottom:.45rem}
        .chart-card-heading h2{min-height:0!important;margin:0!important}
        .chart-export-button{flex:none;border:1px solid #e4e8f1;border-radius:8px;background:#fff;color:#65718a;font-size:.68rem;font-weight:700;padding:5px 8px;line-height:1.2}
        .chart-export-button:hover{background:#f2f4ff;border-color:#cfd5fb;color:#414fbd}
        .dashboard-note-item{border:1px solid #e9edf4;border-radius:12px;padding:12px 14px;background:#fcfdff}.dashboard-note-item p{white-space:pre-wrap;overflow-wrap:anywhere}.dashboard-note-meta{font-size:.72rem;color:#7c879b}
        .insight-card,.metric-card,.chart-card,.summary-card{background:var(--em-surface);border:1px solid var(--em-line);border-radius:16px;box-shadow:0 5px 20px rgba(31,45,82,.035);height:100%}
        .insight-card{padding:21px}
        .section-title{font-size:.96rem;font-weight:800;color:#26334e;letter-spacing:-.025em}
        #summary-cards{row-gap:13px}
        .summary-card{position:relative;overflow:hidden;padding:17px 18px 10px;min-height:158px;transition:transform .2s,box-shadow .2s}
        .summary-card:before{content:"";position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--spark-color,#5364d8)}
        .summary-card:hover,.metric-card:hover,.chart-card:hover{transform:translateY(-2px);box-shadow:0 12px 27px rgba(31,45,82,.075)}
        .summary-label{font-size:.76rem;font-weight:700;color:#78849a}
        .summary-value{font-size:1.85rem;line-height:1.2;font-weight:800;letter-spacing:-.06em;color:#202d49;margin-top:8px;white-space:nowrap}
        .summary-change{font-size:.68rem;font-weight:750;padding:4px 7px;border-radius:7px;background:#f1f3f8;color:#8490a5;white-space:nowrap}
        .summary-change.positive{background:#e6f7ef;color:#15945d}.summary-change.negative{background:#fff0ed;color:#d95748}
        .summary-chart{height:54px;margin:7px -5px -2px}
        .metric-card{padding:18px;transition:transform .2s,box-shadow .2s}
        .metric-card small{font-size:.75rem;font-weight:700;color:#7a879c}
        .metric-card strong{display:block;margin-top:11px;font-size:1.42rem;font-weight:800;letter-spacing:-.045em;color:#26334e}
        .chart-card{width:100%;min-height:340px;padding:19px;transition:transform .2s,box-shadow .2s}
        .chart-card h2{font-size:.91rem;font-weight:800;letter-spacing:-.02em;color:#293650}
        .chart-card .text-muted{font-size:.73rem;color:#8792a6!important;line-height:1.5}
        .dashboard-chart-grid>.col-xl-4{display:flex}
        .dashboard-chart-grid .chart-card>div[id]{min-height:250px}
        .dashboard-chart-grid .chart-card h2{min-height:1.4rem;margin-bottom:.45rem}
        .dashboard-chart-grid .chart-card p{min-height:2.2rem;margin-bottom:.45rem}
        .health-score{font-size:3rem;line-height:1;font-weight:850;letter-spacing:-.065em;color:#293650}
        .health-score.healthy{color:#159b68}.health-score.warning{color:#d28a25}.health-score.critical{color:#d95149}
        .alert-list{display:grid;gap:9px}.alert-item{display:flex;gap:12px;align-items:flex-start;border:1px solid #edf0f5;border-radius:11px;padding:11px 13px;background:#fcfdff}
        .alert-dot{width:9px;height:9px;border-radius:50%;margin-top:5px;flex:none}.alert-dot.warning{background:#e6a23c}.alert-dot.critical{background:#dc5147}
        .alert-item strong{font-size:.83rem}.alert-item p{font-size:.76rem;color:#7d879a;margin:3px 0 0}
        .table{--bs-table-bg:transparent}
        .table thead th{font-size:.68rem;text-transform:uppercase;letter-spacing:.055em;color:#8791a5;white-space:nowrap;border-bottom-color:#e8ecf3}
        .table tbody td{font-size:.8rem;white-space:nowrap;border-bottom-color:#f0f2f7}
        .table-hover>tbody>tr:hover>*{--bs-table-bg:#f7f8ff}
        .badge.text-bg-light{background:#f0f3fa!important;color:#5d6a84!important;border:1px solid #e6eaf2}
        .progress{background:#edf0f6;border-radius:20px}.progress-bar{border-radius:20px}
        #dashboard-customizer{border-color:#dfe4f1}.dashboard-kpi-pinned .summary-card{outline:2px solid rgba(79,91,213,.22);outline-offset:-2px}
        #updated{color:#8a95a8!important}
        @media(max-width:1199px){#summary-cards>.col-xl-3{max-width:50%}}
        @media(max-width:991px){.dashboard-nav{align-items:stretch;flex-direction:column}.dashboard-nav-tools{justify-content:flex-end}.dashboard-nav-links{padding-bottom:2px}}
        @media(max-width:767px){main.container{padding-top:14px!important}.dashboard-header{border-radius:16px}.dashboard-hero{padding:21px}.dashboard-hero h1{font-size:1.3rem}.dashboard-header-actions{width:100%;justify-content:space-between}.dashboard-nav{padding:9px}.dashboard-nav-link{padding:8px 9px;font-size:.73rem}.dashboard-nav-tools{justify-content:stretch}.dashboard-nav-tools .btn{flex:1}.insight-card{padding:16px}#summary-cards>.col-xl-3{max-width:100%}.summary-card{min-height:148px}.metric-card{padding:15px}.metric-card strong{font-size:1.22rem}.chart-card{padding:16px;min-height:320px}.dashboard-chart-grid .chart-card>div[id]{min-height:230px}#dashboard-refresh-controls{gap:8px!important}#dashboard-last-refresh{width:100%;margin-left:0!important}}
    </style>
</head>
<body class="bg-light">
<main class="container py-4">
    <header class="dashboard-header mb-4">
        <div class="dashboard-hero d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <div class="dashboard-eyebrow">ELASTICSEARCH · OBSERVABILITY</div>
                <h1 class="h3 mb-1">{{ $t('title') }}</h1>
                <p class="mb-0">{{ $t('subtitle') }}</p>
            </div>
            <div class="dashboard-header-actions">
                <form method="GET" action="{{ route('elasticsearch-monitoring.index') }}" class="d-flex align-items-center gap-2">
                    <label for="dashboard-locale" class="visually-hidden">Dashboard language</label>
                    <select id="dashboard-locale" name="locale" class="form-select form-select-sm dashboard-language-select" aria-label="Dashboard language"
                        onchange="document.cookie = 'elasticsearch_monitoring_locale=' + encodeURIComponent(this.value) + '; path=/; max-age=31536000; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : ''); this.form.submit()">
                        @foreach ($availableLocales as $availableLocale)
                            <option value="{{ $availableLocale }}" @selected($locale === $availableLocale)>{{ $languageNames[$availableLocale] ?? strtoupper($availableLocale) }}</option>
                        @endforeach
                    </select>
                </form>
                <button class="btn dashboard-primary-action btn-sm" type="button" onclick="refreshDashboardNow()">{{ $t('refresh') }}</button>
            </div>
        </div>
        <nav class="dashboard-nav" aria-label="{{ $locale === 'tr' ? 'Ana gezinme' : 'Main navigation' }}">
            <div class="dashboard-nav-links">
                <a class="dashboard-nav-link active" href="{{ route('elasticsearch-monitoring.index', ['locale' => $locale]) }}">{{ $locale === 'tr' ? 'Genel Bakış' : 'Overview' }}</a>
                <a class="dashboard-nav-link" href="{{ route('elasticsearch-monitoring.indices', ['locale' => $locale]) }}">{{ $indexManagementLabel }}</a>
                <a class="dashboard-nav-link" href="{{ route('elasticsearch-monitoring.shards', ['locale' => $locale]) }}">{{ $locale === 'tr' ? 'Shard Dağılımı' : 'Shard Allocation' }}</a>
                <a class="dashboard-nav-link" href="{{ route('elasticsearch-monitoring.capacity', ['locale' => $locale]) }}">{{ $locale === 'tr' ? 'Kapasite' : 'Capacity' }}</a>
                <a class="dashboard-nav-link" href="{{ route('elasticsearch-monitoring.nodes', ['locale' => $locale]) }}">{{ $locale === 'tr' ? 'Node Geçmişi' : 'Node History' }}</a>
                <a class="dashboard-nav-link" href="{{ route('elasticsearch-monitoring.thread-pools', ['locale' => $locale]) }}">{{ $locale === 'tr' ? 'Thread Pool' : 'Thread Pools' }}</a>
                <a class="dashboard-nav-link" href="{{ route('elasticsearch-monitoring.snapshots', ['locale' => $locale]) }}">{{ $locale === 'tr' ? 'Yedekleme' : 'Snapshots' }}</a>
                <a class="dashboard-nav-link" href="{{ route('elasticsearch-monitoring.alerts', ['locale' => $locale]) }}">{{ $locale === 'tr' ? 'Alarmlar' : 'Alerts' }}</a>
                <a class="dashboard-nav-link" href="{{ route('elasticsearch-monitoring.api-requests', ['locale' => $locale]) }}">API {{ $locale === 'tr' ? 'İstekleri' : 'Requests' }}</a>
            </div>
            <div class="dashboard-nav-tools">
                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleDashboardCustomizer()">{{ $locale === 'tr' ? 'Paneli düzenle' : 'Customize' }}</button>
            </div>
        </nav>
    </header>
    <div id="dashboard-refresh-controls" class="d-flex align-items-center flex-wrap gap-2 mb-3">
        <label for="dashboard-refresh-interval" class="small text-muted mb-0">{{ $t('refresh_interval') }}</label>
        <select id="dashboard-refresh-interval" class="form-select form-select-sm" style="width:auto" onchange="changeDashboardRefreshInterval(this.value)">
            <option value="15000">{{ $t('refresh_15_seconds') }}</option>
            <option value="30000">{{ $t('refresh_30_seconds') }}</option>
            <option value="60000">{{ $t('refresh_1_minute') }}</option>
            <option value="300000">{{ $t('refresh_5_minutes') }}</option>
        </select>
        <button id="dashboard-refresh-toggle" class="btn btn-sm btn-outline-secondary" type="button" onclick="toggleDashboardAutoRefresh()">{{ $t('pause_refresh') }}</button>
        <span id="dashboard-refresh-status" class="small text-muted" aria-live="polite">{{ $t('auto_refresh') }}</span>
        <span id="dashboard-last-refresh" class="small text-muted ms-auto">{{ $t('last_refresh') }}: —</span>
    </div>
    <div id="dashboard-history-controls" class="insight-card mb-3">
        <div class="d-flex align-items-end flex-wrap gap-2">
            <div><label for="dashboard-history-range" class="form-label small text-muted mb-1">{{ $t('history_range') }}</label>
                <select id="dashboard-history-range" class="form-select form-select-sm" style="min-width:150px" onchange="changeDashboardHistoryRange(this.value)">
                    <option value="1h">{{ $t('range_1_hour') }}</option><option value="6h">{{ $t('range_6_hours') }}</option><option value="24h">{{ $t('range_24_hours') }}</option><option value="7d">{{ $t('range_7_days') }}</option><option value="custom">{{ $t('range_custom') }}</option>
                </select>
            </div>
            <div id="dashboard-custom-range" class="d-flex align-items-end flex-wrap gap-2 d-none">
                <div><label for="dashboard-history-from" class="form-label small text-muted mb-1">{{ $t('start_date') }}</label><input type="date" id="dashboard-history-from" class="form-control form-control-sm"></div>
                <div><label for="dashboard-history-to" class="form-label small text-muted mb-1">{{ $t('end_date') }}</label><input type="date" id="dashboard-history-to" class="form-control form-control-sm"></div>
                <button type="button" class="btn btn-sm btn-primary" onclick="applyCustomDashboardHistoryRange()">{{ $t('apply_range') }}</button>
            </div>
            <div class="form-check form-switch ms-2 mb-1">
                <input class="form-check-input" type="checkbox" role="switch" id="dashboard-compare-period" onchange="toggleDashboardPeriodComparison(this.checked)">
                <label class="form-check-label small fw-semibold" for="dashboard-compare-period">{{ $uiText['compare_period'] }}</label>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="downloadAllDashboardCharts()">{{ $t('download_all_charts') }}</button>
            <button type="button" id="dashboard-pdf-button" class="btn btn-sm btn-primary" onclick="downloadDashboardPdf()">{{ $t('download_pdf_report') }}</button>
            <span id="dashboard-history-summary" class="small text-muted ms-auto"></span>
        </div>
    </div>
    <section id="dashboard-notes" class="insight-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"><div><h2 class="section-title mb-1">{{ $t('notes_title') }}</h2><p class="small text-muted mb-0">{{ $t('notes_hint') }}</p></div><span class="badge text-bg-light" id="dashboard-notes-count">0</span></div>
        <form id="dashboard-note-form" class="border rounded-3 p-3 mb-3" novalidate><input type="hidden" id="dashboard-note-id">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4"><label for="dashboard-note-chart" class="form-label small fw-semibold">{{ $t('notes_chart') }}</label><select id="dashboard-note-chart" class="form-select form-select-sm" required>
                    <option value="cpu-chart">{{ $uiText['cpu_history'] }}</option><option value="storage-chart">{{ $uiText['index_history'] }}</option><option value="disk-chart">{{ $uiText['disk_history'] }}</option><option value="search-chart">{{ $uiText['search_history'] }}</option><option value="indexing-chart">{{ $uiText['indexing_history'] }}</option><option value="segments-chart">{{ $uiText['segments_history'] }}</option><option value="documents-chart">{{ $uiText['documents_history'] }}</option>
                </select></div>
                <div class="col-12 col-md-3"><label for="dashboard-note-time" class="form-label small fw-semibold">{{ $t('notes_time') }}</label><input type="datetime-local" id="dashboard-note-time" class="form-control form-control-sm" required></div>
                <div class="col-12 col-md-5"><label for="dashboard-note-text" class="form-label small fw-semibold">{{ $t('notes_text') }}</label><textarea id="dashboard-note-text" class="form-control form-control-sm" rows="2" maxlength="2000" required placeholder="{{ $t('notes_placeholder') }}"></textarea></div>
                <div class="col-12 d-flex gap-2 justify-content-end"><button type="button" id="dashboard-note-cancel" class="btn btn-sm btn-outline-secondary d-none">{{ $t('notes_cancel') }}</button><button type="submit" id="dashboard-note-submit" class="btn btn-sm btn-primary">{{ $t('notes_save') }}</button></div>
            </div><div id="dashboard-note-feedback" class="small mt-2 d-none" role="status" aria-live="polite"></div>
        </form><div id="dashboard-notes-list" class="d-grid gap-2"><p class="small text-muted mb-0">{{ $t('notes_loading') }}</p></div>
    </section>
    <div id="alert" class="alert alert-warning d-none"></div>
    <section id="dashboard-customizer" class="insight-card mb-4 d-none">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div><h2 class="section-title mb-1">{{ $locale === 'tr' ? 'Dashboard düzeni' : 'Dashboard layout' }}</h2><p class="small text-muted mb-0">{{ $locale === 'tr' ? 'Bölümleri sürükleyerek sıralayın, görmek istemediklerinizi gizleyin.' : 'Drag sections to reorder them and hide the ones you do not need.' }}</p></div>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetDashboardLayout()">{{ $locale === 'tr' ? 'Varsayılana dön' : 'Reset to default' }}</button>
        </div>
        <ol id="dashboard-widget-list" class="list-group list-group-flush mb-4"></ol>
        <div class="border-top pt-3"><h3 class="section-title mb-1">{{ $t('dashboard_kpi_title') }}</h3><p class="small text-muted mb-2">{{ $t('dashboard_kpi_hint') }}</p><ol id="dashboard-kpi-list" class="list-group list-group-flush"></ol></div>
    </section>
    <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h6 fw-bold mb-0">{{ $uiText['overview'] }}</h2></div>
    <div class="row g-3 mb-4 justify-content-center" id="summary-cards" data-dashboard-widget="summary">
        @foreach ([
            ['id'=>'docs','label'=>$t('documents'),'unit'=>'','color'=>'#20b6a5'],
            ['id'=>'active-shards','label'=>$t('active_shards'),'unit'=>'','color'=>'#3978b8'],
            ['id'=>'unassigned','label'=>$t('unassigned_shards'),'unit'=>'','color'=>'#e39a36'],
            ['id'=>'search-rate','label'=>$uiText['search_history'],'unit'=>' /s','color'=>'#20b6a5'],
            ['id'=>'index-rate','label'=>$uiText['indexing_history'],'unit'=>' /s','color'=>'#3978b8'],
            ['id'=>'cpu','label'=>$uiText['cpu'],'unit'=>'%','color'=>'#8064c8'],
            ['id'=>'store-size','label'=>$t('store_size'),'unit'=>'','color'=>'#16a085'],
        ] as $card)
            <div class="col-12 col-sm-6 col-xl-3" data-dashboard-card="{{ $card['id'] }}">
                <div class="summary-card" style="--spark-color: {{ $card['color'] }}">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <span class="summary-label">{{ $card['label'] }}</span>
                        <span class="summary-change" id="{{ $card['id'] }}-change">—</span>
                    </div>
                    <div class="summary-value" id="{{ $card['id'] }}">—</div>
                    <div class="summary-chart" id="{{ $card['id'] }}-spark"></div>
                </div>
            </div>
        @endforeach
    </div>

    <section class="insight-card mb-4" id="favorite-indices-card" data-dashboard-widget="favorites">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h2 class="section-title mb-0">{{ $favoriteText['title'] }}</h2>
            <span class="small text-muted" id="favorite-indices-count">0</span>
        </div>
        <div id="favorite-indices-list" class="d-flex flex-wrap gap-2"></div>
        <p class="small text-muted mb-0 mt-2" id="favorite-indices-empty">{{ $favoriteText['empty'] }}</p>
    </section>
    <div class="row g-3 mb-4" data-dashboard-widget="health">
        <div class="col-12 col-xl-4"><section class="insight-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2"><h2 class="section-title mb-0">{{ $t('health_score') }}</h2><span class="badge text-bg-light" id="health-status">—</span></div>
            <div class="d-flex align-items-end gap-2"><strong class="health-score" id="health-score">—</strong><span class="text-muted mb-2">/ 100</span></div>
            <div class="progress mt-2" style="height:9px"><div id="health-progress" class="progress-bar" role="progressbar" style="width:0%"></div></div>
            <p class="small text-muted mt-3 mb-0" id="health-summary">—</p>
        </section></div>
        <div class="col-12 col-xl-8"><section class="insight-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="section-title mb-0">{{ $t('active_alerts') }}</h2><span class="badge rounded-pill text-bg-light" id="alert-count">0</span></div>
            <div id="health-alerts" class="alert-list"><p class="text-muted small mb-0">{{ $t('no_alerts') }}</p></div>
        </section></div>
    </div>
    <div class="row g-3 mb-4" data-dashboard-widget="metrics">
        @foreach ([
            'status' => 'status',
            'nodes' => 'nodes',
            'docs' => 'documents',
            'shards' => 'active_shards',
            'search-rate' => 'search_rate',
            'index-rate' => 'index_rate',
            'unassigned' => 'unassigned_shards',
            'rejected' => 'search_rejections',
            'search-queue' => 'search_queue',
            'write-queue' => 'write_queue',
            'write-rejected' => 'write_rejections',
            'index-count' => 'index_count',
            'store-size' => 'store_size',
            'cpu' => 'cpu',
        ] as $id => $label)
            <div class="col-md-4 col-lg-3">
                <div class="metric-card"><small class="text-muted">{{ $id === 'cpu' ? $uiText['cpu'] : $t($label) }}</small><strong id="{{ $id }}">—</strong></div>
            </div>
        @endforeach
    </div>

    <section class="insight-card mb-4" data-dashboard-widget="nodes">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div><h2 class="section-title mb-1">{{ $t('node_comparison') }}</h2><p class="small text-muted mb-0">{{ $t('node_comparison_hint') }}</p></div>
            <span class="small text-muted" id="node-count">—</span>
        </div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th>{{ $t('node') }}</th><th>CPU</th><th>{{ $t('heap_usage') }}</th><th>{{ $t('disk_usage') }}</th><th>{{ $t('documents') }}</th><th>{{ $t('shards') }}</th><th>{{ $t('search_total') }}</th><th>{{ $t('indexing_total') }}</th></tr></thead>
            <tbody id="node-comparison"><tr><td colspan="8" class="text-muted text-center py-4">—</td></tr></tbody>
        </table></div>
    </section>
    <div class="row g-3 dashboard-chart-grid" data-dashboard-widget="charts">
        <div class="col-12 col-md-6 col-xl-4"><div class="chart-card"><div class="chart-card-heading"><h2>{{ $t('heap') }}</h2><button type="button" class="chart-export-button" onclick="downloadDashboardChart('heap')" title="{{ $t('download_chart') }}">PNG</button></div><p class="text-muted">{{ $uiText['heap_hint'] }}</p><div id="heap"></div></div></div>
        <div class="col-12 col-md-6 col-xl-4"><div class="chart-card"><div class="chart-card-heading"><h2>{{ $t('filesystem') }}</h2><button type="button" class="chart-export-button" onclick="downloadDashboardChart('filesystem')" title="{{ $t('download_chart') }}">PNG</button></div><p class="text-muted">{{ $uiText['filesystem_hint'] }}</p><div id="filesystem"></div></div></div>
        <div class="col-12 col-md-6 col-xl-4"><div class="chart-card"><div class="chart-card-heading"><h2>{{ $uiText['search_history'] }}</h2><button type="button" class="chart-export-button" onclick="downloadDashboardChart('search-chart')" title="{{ $t('download_chart') }}">PNG</button></div><p class="text-muted">{{ $uiText['rates_hint'] }}</p><div id="search-chart"></div></div></div>
        <div class="col-12 col-md-6 col-xl-4"><div class="chart-card"><div class="chart-card-heading"><h2>{{ $uiText['indexing_history'] }}</h2><button type="button" class="chart-export-button" onclick="downloadDashboardChart('indexing-chart')" title="{{ $t('download_chart') }}">PNG</button></div><p class="text-muted">{{ $uiText['rates_hint'] }}</p><div id="indexing-chart"></div></div></div>
        <div class="col-12 col-md-6 col-xl-4"><div class="chart-card"><div class="chart-card-heading"><h2>{{ $uiText['disk_history'] }}</h2><button type="button" class="chart-export-button" onclick="downloadDashboardChart('disk-chart')" title="{{ $t('download_chart') }}">PNG</button></div><p class="text-muted">{{ $uiText['disk_hint'] }}</p><div id="disk-chart"></div></div></div>
        <div class="col-12 col-md-6 col-xl-4"><div class="chart-card"><div class="chart-card-heading"><h2>{{ $uiText['segments_history'] }}</h2><button type="button" class="chart-export-button" onclick="downloadDashboardChart('segments-chart')" title="{{ $t('download_chart') }}">PNG</button></div><p class="text-muted">{{ $uiText['segments_history'] }}</p><div id="segments-chart"></div></div></div>
        <div class="col-12 col-md-6 col-xl-4"><div class="chart-card"><div class="chart-card-heading"><h2>{{ $uiText['documents_history'] }}</h2><button type="button" class="chart-export-button" onclick="downloadDashboardChart('documents-chart')" title="{{ $t('download_chart') }}">PNG</button></div><p class="text-muted">{{ $uiText['documents_history'] }}</p><div id="documents-chart"></div></div></div>
        <div class="col-12 col-md-6 col-xl-4"><div class="chart-card"><div class="chart-card-heading"><h2>{{ $uiText['cpu_history'] }}</h2><button type="button" class="chart-export-button" onclick="downloadDashboardChart('cpu-chart')" title="{{ $t('download_chart') }}">PNG</button></div><p class="text-muted">{{ $uiText['cpu_hint'] }}</p><div id="cpu-chart"></div></div></div>
        <div class="col-12 col-md-6 col-xl-4"><div class="chart-card"><div class="chart-card-heading"><h2>{{ $uiText['index_history'] }}</h2><button type="button" class="chart-export-button" onclick="downloadDashboardChart('storage-chart')" title="{{ $t('download_chart') }}">PNG</button></div><p class="text-muted">{{ $uiText['index_hint'] }}</p><div id="storage-chart"></div></div></div>
    </div>
    <p class="small text-muted mt-3" id="updated"></p>
</main>
<script>
const endpoint = @json(route('elasticsearch-monitoring.data'));
const notesEndpoint = @json(route('elasticsearch-monitoring.notes.index'));
const notesStoreEndpoint = @json(route('elasticsearch-monitoring.notes.store'));
const notesUpdateEndpoint = @json(route('elasticsearch-monitoring.notes.update', ['note' => '__NOTE__']));
const notesDeleteEndpoint = @json(route('elasticsearch-monitoring.notes.destroy', ['note' => '__NOTE__']));
const noteChartLabels = {'cpu-chart':@json($uiText['cpu_history']),'storage-chart':@json($uiText['index_history']),'disk-chart':@json($uiText['disk_history']),'search-chart':@json($uiText['search_history']),'indexing-chart':@json($uiText['indexing_history']),'segments-chart':@json($uiText['segments_history']),'documents-chart':@json($uiText['documents_history'])};
let dashboardNotes = [];
const favoritesStorageKey = 'elasticsearch-monitoring:favorites';
const favoriteLabels = @json($favoriteText);
const indicesUrl = @json(route('elasticsearch-monitoring.indices', ['locale' => $locale]));
const dashboardLayoutKey = 'elasticsearch-monitoring:dashboard-layout';
const dashboardKpiKey = 'elasticsearch-monitoring:dashboard-kpis';
const defaultDashboardKpiOrder = ['docs', 'active-shards', 'unassigned', 'search-rate', 'index-rate', 'cpu', 'store-size'];
const defaultDashboardWidgetOrder = ['summary', 'favorites', 'health', 'metrics', 'nodes', 'charts'];
const dashboardWidgetLabels = @json($dashboardWidgetNames);
function readDashboardLayout() {
    try {
        const stored = JSON.parse(localStorage.getItem(dashboardLayoutKey) || '{}');
        const order = Array.isArray(stored.order) ? stored.order.filter(id => defaultDashboardWidgetOrder.includes(id)) : [];
        return {order: [...new Set([...order, ...defaultDashboardWidgetOrder])], hidden: Array.isArray(stored.hidden) ? stored.hidden.filter(id => defaultDashboardWidgetOrder.includes(id)) : []};
    } catch (error) { return {order: [...defaultDashboardWidgetOrder], hidden: []}; }
}
function saveDashboardLayout(layout) { try { localStorage.setItem(dashboardLayoutKey, JSON.stringify(layout)); } catch (error) {} }
function applyDashboardLayout(layout) {
    const main = document.querySelector('main.container');
    const widgets = new Map([...document.querySelectorAll('[data-dashboard-widget]')].map(widget => [widget.dataset.dashboardWidget, widget]));
    const anchor = document.getElementById('updated');
    layout.order.forEach(id => { const widget = widgets.get(id); if (widget) { main.insertBefore(widget, anchor); widget.hidden = layout.hidden.includes(id); } });
}
function renderDashboardWidgetList(layout) {
    const list = document.getElementById('dashboard-widget-list'); list.replaceChildren();
    layout.order.forEach(id => {
        const item = document.createElement('li'); item.className = 'list-group-item d-flex align-items-center gap-3'; item.draggable = true;
        const handle = document.createElement('span'); handle.className = 'text-muted'; handle.textContent = '☰';
        const checkbox = document.createElement('input'); checkbox.type = 'checkbox'; checkbox.className = 'form-check-input mt-0'; checkbox.checked = !layout.hidden.includes(id); checkbox.setAttribute('aria-label', dashboardWidgetLabels[id]);
        checkbox.addEventListener('change', () => { const next = readDashboardLayout(); next.hidden = checkbox.checked ? next.hidden.filter(value => value !== id) : [...new Set([...next.hidden, id])]; saveDashboardLayout(next); applyDashboardLayout(next); });
        const label = document.createElement('span'); label.className = 'flex-grow-1'; label.textContent = dashboardWidgetLabels[id]; item.append(handle, checkbox, label);
        item.addEventListener('dragstart', event => { event.dataTransfer.effectAllowed = 'move'; event.dataTransfer.setData('text/plain', id); item.classList.add('opacity-50'); });
        item.addEventListener('dragend', () => item.classList.remove('opacity-50'));
        item.addEventListener('dragover', event => event.preventDefault());
        item.addEventListener('drop', event => { event.preventDefault(); const moved = event.dataTransfer.getData('text/plain'); if (!moved || moved === id) return; const next = readDashboardLayout(); const from = next.order.indexOf(moved), to = next.order.indexOf(id); if (from < 0 || to < 0) return; next.order.splice(from, 1); next.order.splice(to, 0, moved); saveDashboardLayout(next); applyDashboardLayout(next); renderDashboardWidgetList(next); });
        list.appendChild(item);
    });
}
function readDashboardKpis() {
    try {
        const stored = JSON.parse(localStorage.getItem(dashboardKpiKey) || '{}');
        const order = Array.isArray(stored.order) ? stored.order.filter(id => defaultDashboardKpiOrder.includes(id)) : [];
        return {order: [...new Set([...order, ...defaultDashboardKpiOrder])], hidden: Array.isArray(stored.hidden) ? [...new Set(stored.hidden.filter(id => defaultDashboardKpiOrder.includes(id)))] : [], pinned: Array.isArray(stored.pinned) ? [...new Set(stored.pinned.filter(id => defaultDashboardKpiOrder.includes(id)))] : []};
    } catch (error) { return {order: [...defaultDashboardKpiOrder], hidden: [], pinned: []}; }
}
function saveDashboardKpis(state) { try { localStorage.setItem(dashboardKpiKey, JSON.stringify(state)); } catch (error) {} }
function applyDashboardKpis(state) {
    const container = document.getElementById('summary-cards');
    const cards = new Map([...container.querySelectorAll('[data-dashboard-card]')].map(card => [card.dataset.dashboardCard, card]));
    const ordered = [...state.order.filter(id => state.pinned.includes(id)), ...state.order.filter(id => !state.pinned.includes(id))];
    ordered.forEach(id => { const card = cards.get(id); if (card) { container.appendChild(card); card.hidden = state.hidden.includes(id); card.classList.toggle('dashboard-kpi-pinned', state.pinned.includes(id)); } });
}
function renderDashboardKpiList(state) {
    const list = document.getElementById('dashboard-kpi-list'); list.replaceChildren();
    state.order.forEach(id => {
        const card = document.querySelector('[data-dashboard-card="' + id + '"]'); if (!card) return;
        const item = document.createElement('li'); item.className = 'list-group-item d-flex align-items-center gap-3'; item.draggable = true;
        const handle = document.createElement('span'); handle.className = 'text-muted'; handle.textContent = '☰';
        const checkbox = document.createElement('input'); checkbox.type = 'checkbox'; checkbox.className = 'form-check-input mt-0'; checkbox.checked = !state.hidden.includes(id);
        checkbox.setAttribute('aria-label', card.querySelector('.summary-label')?.textContent || id);
        checkbox.addEventListener('change', () => { const next = readDashboardKpis(); next.hidden = checkbox.checked ? next.hidden.filter(value => value !== id) : [...new Set([...next.hidden, id])]; saveDashboardKpis(next); applyDashboardKpis(next); });
        const label = document.createElement('span'); label.className = 'flex-grow-1'; label.textContent = card.querySelector('.summary-label')?.textContent || id;
        const pin = document.createElement('button'); pin.type = 'button'; pin.className = 'btn btn-sm ' + (state.pinned.includes(id) ? 'btn-primary' : 'btn-outline-secondary'); pin.textContent = state.pinned.includes(id) ? '★' : '☆';
        pin.title = state.pinned.includes(id) ? @json($t('dashboard_kpi_unpin')) : @json($t('dashboard_kpi_pin')); pin.setAttribute('aria-label', pin.title);
        pin.addEventListener('click', () => { const next = readDashboardKpis(); next.pinned = next.pinned.includes(id) ? next.pinned.filter(value => value !== id) : [...next.pinned, id]; saveDashboardKpis(next); applyDashboardKpis(next); renderDashboardKpiList(next); });
        item.append(handle, checkbox, label, pin);
        item.addEventListener('dragstart', event => { event.dataTransfer.effectAllowed = 'move'; event.dataTransfer.setData('text/plain', id); item.classList.add('opacity-50'); });
        item.addEventListener('dragend', () => item.classList.remove('opacity-50'));
        item.addEventListener('dragover', event => event.preventDefault());
        item.addEventListener('drop', event => { event.preventDefault(); const moved = event.dataTransfer.getData('text/plain'); if (!moved || moved === id) return; const next = readDashboardKpis(); const from = next.order.indexOf(moved), to = next.order.indexOf(id); if (from < 0 || to < 0) return; next.order.splice(from, 1); next.order.splice(to, 0, moved); saveDashboardKpis(next); applyDashboardKpis(next); renderDashboardKpiList(next); });
        list.appendChild(item);
    });
}
function toggleDashboardCustomizer() { document.getElementById('dashboard-customizer').classList.toggle('d-none'); }
function resetDashboardLayout() {
    try { localStorage.removeItem(dashboardLayoutKey); localStorage.removeItem(dashboardKpiKey); } catch (error) {}
    const layout = {order: [...defaultDashboardWidgetOrder], hidden: []}, kpis = {order: [...defaultDashboardKpiOrder], hidden: [], pinned: []};
    applyDashboardLayout(layout); renderDashboardWidgetList(layout); applyDashboardKpis(kpis); renderDashboardKpiList(kpis);
}
const initialDashboardLayout = readDashboardLayout(); applyDashboardLayout(initialDashboardLayout); renderDashboardWidgetList(initialDashboardLayout);
const initialDashboardKpis = readDashboardKpis(); applyDashboardKpis(initialDashboardKpis); renderDashboardKpiList(initialDashboardKpis);
function renderFavoriteIndices() {
    const container = document.getElementById('favorite-indices-list');
    const empty = document.getElementById('favorite-indices-empty');
    const count = document.getElementById('favorite-indices-count');
    let favorites = [];
    try {
        const stored = JSON.parse(localStorage.getItem(favoritesStorageKey) || '[]');
        favorites = Array.isArray(stored) ? [...new Set(stored.filter(name => typeof name === 'string'))].sort() : [];
    } catch (error) {
        favorites = [];
    }
    count.textContent = String(favorites.length);
    container.replaceChildren();
    empty.classList.toggle('d-none', favorites.length > 0);
    favorites.forEach(name => {
        const link = document.createElement('a');
        const target = new URL(indicesUrl, window.location.href);
        target.searchParams.set('search', name);
        link.href = target.toString();
        link.className = 'btn btn-sm btn-outline-primary';
        link.textContent = '★ ' + name;
        link.title = favoriteLabels.open;
        container.appendChild(link);
    });
}
const labels = {
    noMetrics: @json($t('no_metrics')),
    rangeSummary: @json($t('range_summary')), rangeNoData: @json($t('range_no_data')), invalidRange: @json($t('invalid_range')), previousPeriod: @json($uiText['previous_period']), hourlyAverage: @json($t('aggregation_hourly')), dailyAverage: @json($t('aggregation_daily')),
    lastCollected: @json($t('last_collected')),
    loadError: @json($t('load_error')),
    chartExportFailed: @json($t('chart_export_failed')),
    pdfTitle: @json($t('pdf_title')), pdfCluster: @json($t('pdf_cluster')), pdfGenerated: @json($t('pdf_generated')), pdfPeriod: @json($t('pdf_period')), pdfSummary: @json($t('pdf_summary')), pdfCharts: @json($t('pdf_charts')), pdfComparisonIncluded: @json($t('pdf_comparison_included')), pdfNoNotes: @json($t('pdf_no_notes')), pdfUnknownCluster: @json($t('pdf_unknown_cluster')), pdfUnavailable: @json($t('pdf_unavailable')), pdfPreparing: @json($t('pdf_preparing')), pdfFailed: @json($t('pdf_failed')), notesTitle: @json($t('notes_title')), health: @json($t('health_score')),
    notesEmpty:@json($t('notes_empty')),notesLoadFailed:@json($t('notes_load_failed')),notesSaveFailed:@json($t('notes_save_failed')),notesDeleteConfirm:@json($t('notes_delete_confirm')),notesDelete:@json($t('notes_delete')),notesEdit:@json($t('notes_edit')),notesSaved:@json($t('notes_saved')),notesUpdated:@json($t('notes_updated')),notesUpdateButton:@json($t('notes_update_button')),notesDeleted:@json($t('notes_deleted')),notesError:@json($t('notes_error')),
    heapUsed: @json($t('heap_used')),
    used: @json($t('used')),
    available: @json($t('available')), cpu: @json($uiText['cpu']), storage: @json($t('store_size')), disk: @json($uiText['disk_history']), search: @json($uiText['search_history']), indexing: @json($uiText['indexing_history']), segments: @json($uiText['segments_history']), documents: @json($uiText['documents_history'])
};
const charts = {};
const sparkCharts = {};
let latestDashboardMetrics = {};
function renderChart(id, options) {
    if (charts[id]) charts[id].destroy();
    charts[id] = new ApexCharts(document.querySelector(id), options);
    charts[id].render();
}
const dashboardChartIds = ['heap', 'filesystem', 'search-chart', 'indexing-chart', 'disk-chart', 'segments-chart', 'documents-chart', 'cpu-chart', 'storage-chart'];
async function loadDashboardChartImage(id) {
    const chart = charts['#' + id];
    const element = document.getElementById(id);
    if (!chart || !element || !element.closest('.chart-card')) throw new Error(labels.chartExportFailed);
    const result = await chart.dataURI({scale: 2});
    const image = new Image();
    await new Promise((resolve, reject) => {
        image.onload = resolve;
        image.onerror = () => reject(new Error(labels.chartExportFailed));
        image.src = result.imgURI;
    });
    return {image, title: element.closest('.chart-card').querySelector('h2')?.textContent?.trim() || id};
}
async function createDashboardChartsPng(ids) {
    const chartImages = await Promise.all(ids.map(loadDashboardChartImage));
    const columns = ids.length === 1 ? 1 : 2;
    const tileWidth = 1200, tileHeight = 720, headerHeight = 116;
    const canvas = document.createElement('canvas');
    canvas.width = tileWidth * columns;
    canvas.height = headerHeight + Math.ceil(chartImages.length / columns) * tileHeight;
    const context = canvas.getContext('2d');
    context.fillStyle = '#ffffff';
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.fillStyle = '#182238';
    context.font = '700 32px Arial, sans-serif';
    context.fillText('Elasticsearch Monitoring', 36, 42);
    context.fillStyle = '#77839a';
    context.font = '17px Arial, sans-serif';
    context.fillText(new Date().toLocaleString(), 36, 68);
    const selectedRange = getDashboardHistoryRange();
    context.fillText(new Date(selectedRange.from).toLocaleString() + ' – ' + new Date(selectedRange.to).toLocaleString(), 36, 96);
    chartImages.forEach(({image, title}, index) => {
        const column = index % columns, row = Math.floor(index / columns);
        const x = column * tileWidth + 24, y = headerHeight + row * tileHeight;
        context.strokeStyle = '#e7ebf3';
        context.lineWidth = 2;
        context.strokeRect(x, y + 8, tileWidth - 48, tileHeight - 24);
        context.fillStyle = '#26334e';
        context.font = '700 23px Arial, sans-serif';
        context.fillText(title, x + 22, y + 48, tileWidth - 92);
        const maxWidth = tileWidth - 88, maxHeight = tileHeight - 104;
        const scale = Math.min(maxWidth / image.width, maxHeight / image.height);
        const width = image.width * scale, height = image.height * scale;
        context.drawImage(image, x + (tileWidth - 48 - width) / 2, y + 64 + (maxHeight - height) / 2, width, height);
    });
    return new Promise((resolve, reject) => canvas.toBlob(blob => blob ? resolve(blob) : reject(new Error(labels.chartExportFailed)), 'image/png'));
}
async function downloadDashboardCharts(ids, filename) {
    try {
        const visibleIds = ids.filter(id => {
            const element = document.getElementById(id);
            return element && !element.closest('[data-dashboard-widget="charts"]')?.hidden;
        });
        if (!visibleIds.length) throw new Error(labels.chartExportFailed);
        const blob = await createDashboardChartsPng(visibleIds);
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
    } catch (error) {
        window.alert(labels.chartExportFailed);
    }
}

async function downloadDashboardPdf() {
    const button = document.getElementById('dashboard-pdf-button');
    const originalLabel = button.textContent;
    let report = null;
    try {
        if (typeof html2pdf !== 'function' || !Object.keys(latestDashboardMetrics).length) throw new Error(labels.pdfUnavailable);
        button.disabled = true;
        button.textContent = labels.pdfPreparing;
        const selectedRange = getDashboardHistoryRange();
        const rangeText = new Date(selectedRange.from).toLocaleString() + ' – ' + new Date(selectedRange.to).toLocaleString();
        const clusterName = latestDashboardMetrics.cluster?.name || labels.pdfUnknownCluster;
        const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
        const summaryCards = [...document.querySelectorAll('#summary-cards .summary-card')].map(card => ({
            label: card.querySelector('.summary-label')?.textContent?.trim() || '',
            value: card.querySelector('.summary-value')?.textContent?.trim() || '—'
        })).filter(item => item.label);
        const healthScore = document.getElementById('health-score')?.textContent?.trim() || '—';
        const healthStatus = document.getElementById('health-status')?.textContent?.trim() || '—';
        const visibleChartIds = dashboardChartIds.filter(id => {
            const element = document.getElementById(id);
            return element && !element.closest('[data-dashboard-widget="charts"]')?.hidden;
        });
        const chartImages = await Promise.all(visibleChartIds.map(async id => {
            const chart = charts['#' + id], element = document.getElementById(id);
            if (!chart || !element) return null;
            const result = await chart.dataURI({scale: 2});
            return {title: element.closest('.chart-card')?.querySelector('h2')?.textContent?.trim() || id, src: result.imgURI};
        }));
        const notes = dashboardNotes.filter(note => {
            const time = new Date(note.noted_at).getTime();
            return time >= new Date(selectedRange.from).getTime() && time <= new Date(selectedRange.to).getTime();
        }).sort((a,b) => new Date(a.noted_at)-new Date(b.noted_at));
        const noteRows = notes.length ? notes.map(note => '<article class="note"><strong>' + escapeHtml(noteChartLabels[note.chart_key] || note.chart_key) + '</strong><time>' + escapeHtml(new Date(note.noted_at).toLocaleString()) + '</time><p>' + escapeHtml(note.note).replace(/\n/g,'<br>') + '</p></article>').join('') : '<p class="muted">' + escapeHtml(labels.pdfNoNotes) + '</p>';
        const chartSections = chartImages.filter(Boolean).map(item => '<section class="chart"><h3>' + escapeHtml(item.title) + '</h3><img src="' + item.src + '" alt="' + escapeHtml(item.title) + '"></section>').join('');
        const metricSections = summaryCards.map(item => '<div class="metric"><span>' + escapeHtml(item.label) + '</span><strong>' + escapeHtml(item.value) + '</strong></div>').join('');
        const comparisonText = document.getElementById('dashboard-compare-period')?.checked ? '<p class="muted">' + escapeHtml(labels.pdfComparisonIncluded) + '</p>' : '';
        report = document.createElement('div');
        report.className = 'pdf-report';
        report.setAttribute('dir', document.documentElement.dir || 'ltr');
        report.innerHTML = '<style>' +
            '.pdf-report{width:760px;padding:28px;background:#fff;color:#182238;font-family:Arial,sans-serif;font-size:12px;line-height:1.45}' +
            '.pdf-report h1{font-size:25px;margin:0 0 5px;color:#182238}.pdf-report h2{font-size:17px;margin:22px 0 10px;border-bottom:1px solid #e7ebf3;padding-bottom:7px}.pdf-report h3{font-size:14px;margin:0 0 8px;color:#26334e}.pdf-report .muted{color:#77839a;font-size:10px;margin:3px 0}' +
            '.pdf-report .cover{padding:20px 22px;background:#f3f5fa;border-radius:12px;border-left:5px solid #4f5bd5;margin-bottom:18px}.pdf-report .metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:9px;margin-top:12px}.pdf-report .metric{border:1px solid #e7ebf3;border-radius:9px;padding:10px}.pdf-report .metric span{display:block;color:#77839a;font-size:10px;margin-bottom:5px}.pdf-report .metric strong{font-size:17px;color:#26334e}' +
            '.pdf-report .health{margin-top:10px;padding:10px;border:1px solid #e7ebf3;border-radius:9px}.pdf-report .chart{border:1px solid #e7ebf3;border-radius:10px;padding:12px;margin:0 0 12px;break-inside:avoid;page-break-inside:avoid}.pdf-report .chart img{display:block;width:100%;height:215px;object-fit:contain}.pdf-report .note{border-bottom:1px solid #e7ebf3;padding:9px 0;break-inside:avoid}.pdf-report .note time{float:right;color:#77839a;font-size:10px}.pdf-report .note p{margin:6px 0 0;overflow-wrap:anywhere}' +
            '</style><header class="cover"><h1>' + escapeHtml(labels.pdfTitle) + '</h1><p class="muted">' + escapeHtml(labels.pdfCluster) + ': <strong>' + escapeHtml(clusterName) + '</strong></p><p class="muted">' + escapeHtml(labels.pdfGenerated) + ': ' + escapeHtml(new Date().toLocaleString()) + '</p><p class="muted">' + escapeHtml(labels.pdfPeriod) + ': ' + escapeHtml(rangeText) + '</p>' + comparisonText + '</header>' +
            '<h2>' + escapeHtml(labels.pdfSummary) + '</h2><div class="metrics">' + metricSections + '</div><div class="health"><strong>' + escapeHtml(labels.health) + ': </strong>' + escapeHtml(healthStatus) + ' · ' + escapeHtml(healthScore) + '</div>' +
            '<h2>' + escapeHtml(labels.pdfCharts) + '</h2>' + chartSections +
            '<h2>' + escapeHtml(labels.notesTitle) + ' (' + notes.length + ')</h2>' + noteRows;
        report.style.position = 'fixed';
        report.style.left = '-10000px';
        report.style.top = '0';
        report.style.zIndex = '-1';
        document.body.appendChild(report);
        const safeCluster = String(clusterName).replace(/[^a-z0-9-_]+/gi,'-').replace(/^-|-$/g,'');
        await html2pdf().set({
            margin: [10, 10, 12, 10],
            filename: 'elasticsearch-monitoring-' + (safeCluster || 'cluster') + '-' + new Date().toISOString().slice(0,10) + '.pdf',
            image: {type:'jpeg', quality:0.94},
            html2canvas: {scale:1.5, useCORS:true, backgroundColor:'#ffffff', logging:false},
            jsPDF: {unit:'mm', format:'a4', orientation:'portrait', compress:true},
            pagebreak: {mode:['css','legacy']}
        }).from(report).save();
    } catch (error) {
        window.alert(labels.pdfFailed);
    } finally {
        if (report) report.remove();
        button.disabled = false;
        button.textContent = originalLabel;
    }
}
function dashboardNoteDateValue(value){const date=value?new Date(value):new Date(),pad=n=>String(n).padStart(2,'0');return date.getFullYear()+'-'+pad(date.getMonth()+1)+'-'+pad(date.getDate())+'T'+pad(date.getHours())+':'+pad(date.getMinutes());}
function dashboardNoteRequestHeaders(){return {'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content};}
function showDashboardNoteFeedback(message,isError=false){const el=document.getElementById('dashboard-note-feedback');el.textContent=message;el.className='small mt-2 '+(isError?'text-danger':'text-success');}
function renderDashboardNotes(){const container=document.getElementById('dashboard-notes-list');document.getElementById('dashboard-notes-count').textContent=dashboardNotes.length.toLocaleString();container.replaceChildren();if(!dashboardNotes.length){const empty=document.createElement('p');empty.className='small text-muted mb-0';empty.textContent=labels.notesEmpty;container.appendChild(empty);return;}dashboardNotes.forEach(note=>{const item=document.createElement('article');item.className='dashboard-note-item';const header=document.createElement('div');header.className='d-flex justify-content-between align-items-start gap-2 flex-wrap';const title=document.createElement('strong');title.className='small';title.textContent=noteChartLabels[note.chart_key]||note.chart_key;const time=document.createElement('span');time.className='dashboard-note-meta';time.textContent=new Date(note.noted_at).toLocaleString();header.append(title,time);const body=document.createElement('p');body.className='small mb-2 mt-2';body.textContent=note.note;const actions=document.createElement('div');actions.className='d-flex justify-content-end gap-2';const edit=document.createElement('button');edit.type='button';edit.className='btn btn-sm btn-outline-secondary';edit.textContent=labels.notesEdit;edit.addEventListener('click',()=>editDashboardNote(note));const remove=document.createElement('button');remove.type='button';remove.className='btn btn-sm btn-outline-danger';remove.textContent=labels.notesDelete;remove.addEventListener('click',()=>deleteDashboardNote(note.id));actions.append(edit,remove);item.append(header,body,actions);container.appendChild(item);});}
async function loadDashboardNotes(){try{const response=await fetch(notesEndpoint,{headers:{'Accept':'application/json'}});if(!response.ok)throw new Error('HTTP '+response.status);dashboardNotes=(await response.json()).notes||[];renderDashboardNotes();applyDashboardNoteAnnotations();}catch(error){document.getElementById('dashboard-notes-list').textContent=labels.notesLoadFailed;}}
function resetDashboardNoteForm(){document.getElementById('dashboard-note-form').reset();document.getElementById('dashboard-note-id').value='';document.getElementById('dashboard-note-time').value=dashboardNoteDateValue();document.getElementById('dashboard-note-submit').textContent=labels.notesSave;document.getElementById('dashboard-note-cancel').classList.add('d-none');}
function editDashboardNote(note){document.getElementById('dashboard-note-id').value=note.id;document.getElementById('dashboard-note-chart').value=note.chart_key;document.getElementById('dashboard-note-time').value=dashboardNoteDateValue(note.noted_at);document.getElementById('dashboard-note-text').value=note.note;document.getElementById('dashboard-note-submit').textContent=labels.notesUpdateButton;document.getElementById('dashboard-note-cancel').classList.remove('d-none');document.getElementById('dashboard-note-form').scrollIntoView({behavior:'smooth',block:'center'});}
async function deleteDashboardNote(id){if(!window.confirm(labels.notesDeleteConfirm))return;try{const response=await fetch(notesDeleteEndpoint.replace('__NOTE__',String(id)),{method:'DELETE',headers:dashboardNoteRequestHeaders()});if(!response.ok)throw new Error('HTTP '+response.status);dashboardNotes=dashboardNotes.filter(note=>Number(note.id)!==Number(id));renderDashboardNotes();applyDashboardNoteAnnotations();showDashboardNoteFeedback(labels.notesDeleted);}catch(error){showDashboardNoteFeedback(labels.notesSaveFailed,true);}}
document.getElementById('dashboard-note-time').value=dashboardNoteDateValue();
document.getElementById('dashboard-note-cancel').addEventListener('click',resetDashboardNoteForm);
document.getElementById('dashboard-note-form').addEventListener('submit',async event=>{event.preventDefault();const id=document.getElementById('dashboard-note-id').value,dateValue=document.getElementById('dashboard-note-time').value,noteText=document.getElementById('dashboard-note-text').value.trim(),button=document.getElementById('dashboard-note-submit');if(!noteText||!dateValue){showDashboardNoteFeedback(labels.notesError,true);return;}const payload={chart_key:document.getElementById('dashboard-note-chart').value,noted_at:new Date(dateValue).toISOString(),note:noteText};button.disabled=true;try{const response=await fetch(id?notesUpdateEndpoint.replace('__NOTE__',id):notesStoreEndpoint,{method:id?'PATCH':'POST',headers:dashboardNoteRequestHeaders(),body:JSON.stringify(payload)});if(!response.ok)throw new Error('HTTP '+response.status);resetDashboardNoteForm();await loadDashboardNotes();showDashboardNoteFeedback(id?labels.notesUpdated:labels.notesSaved);}catch(error){showDashboardNoteFeedback(labels.notesSaveFailed,true);}finally{button.disabled=false;}});
function applyDashboardNoteAnnotations(){const range=getDashboardHistoryRange(),from=new Date(range.from).getTime(),to=new Date(range.to).getTime();['cpu-chart','storage-chart','disk-chart','search-chart','indexing-chart','segments-chart','documents-chart'].forEach(key=>{const chart=charts['#'+key];if(!chart||typeof chart.addXaxisAnnotation!=='function')return;dashboardNotes.filter(note=>note.chart_key===key).forEach(note=>{const timestamp=new Date(note.noted_at).getTime();if(!Number.isFinite(timestamp)||timestamp<from||timestamp>to)return;chart.addXaxisAnnotation({x:timestamp,borderColor:'#d28a25',strokeDashArray:4,label:{text:note.note.length>32?note.note.slice(0,32)+'…':note.note,orientation:'horizontal',style:{background:'#fff3d6',color:'#7a5312',fontSize:'10px'}}});});});}

function downloadDashboardChart(id) {
    downloadDashboardCharts([id], 'elasticsearch-' + id + '-' + new Date().toISOString().slice(0, 10) + '.png');
}
function downloadAllDashboardCharts() {
    downloadDashboardCharts(dashboardChartIds, 'elasticsearch-dashboard-charts-' + new Date().toISOString().slice(0, 10) + '.png');
}
const refreshPreferenceKey = 'elasticsearch-monitoring:dashboard-refresh-interval';
const allowedRefreshIntervals = [15000, 30000, 60000, 300000];
let refreshIntervalMs = 30000;
let refreshPaused = false;
let refreshTimer = null;
let refreshInProgress = false;
let lastRefreshAt = null;
try {
    const savedInterval = Number(localStorage.getItem(refreshPreferenceKey));
    if (allowedRefreshIntervals.includes(savedInterval)) refreshIntervalMs = savedInterval;
} catch (error) {}
document.getElementById('dashboard-refresh-interval').value = String(refreshIntervalMs);
const comparisonPreferenceKey = 'elasticsearch-monitoring:dashboard-compare-period';
let comparePreviousPeriod = false;
try { comparePreviousPeriod = localStorage.getItem(comparisonPreferenceKey) === '1'; } catch (error) {}
document.getElementById('dashboard-compare-period').checked = comparePreviousPeriod;
function toggleDashboardPeriodComparison(enabled) {
    comparePreviousPeriod = Boolean(enabled);
    try { localStorage.setItem(comparisonPreferenceKey, comparePreviousPeriod ? '1' : '0'); } catch (error) {}
    refreshDashboardNow();
}
const historyPreferenceKey = 'elasticsearch-monitoring:dashboard-history-range';
let dashboardHistoryRange = '1h', customHistoryFrom = '', customHistoryTo = '';
try {
    const saved = JSON.parse(localStorage.getItem(historyPreferenceKey) || 'null');
    if (saved && ['1h','6h','24h','7d','custom'].includes(saved.range)) { dashboardHistoryRange = saved.range; customHistoryFrom = saved.from || ''; customHistoryTo = saved.to || ''; }
} catch (error) {}
document.getElementById('dashboard-history-range').value = dashboardHistoryRange;
document.getElementById('dashboard-custom-range').classList.toggle('d-none', dashboardHistoryRange !== 'custom');
function localDateInput(date) { return date.getFullYear()+'-'+String(date.getMonth()+1).padStart(2,'0')+'-'+String(date.getDate()).padStart(2,'0'); }
const today = new Date();
document.getElementById('dashboard-history-from').value = customHistoryFrom || localDateInput(new Date(today.getTime()-86400000));
document.getElementById('dashboard-history-to').value = customHistoryTo || localDateInput(today);
function getDashboardHistoryRange() {
    const now = new Date();
    if (dashboardHistoryRange === 'custom') {
        return {from:new Date(document.getElementById('dashboard-history-from').value+'T00:00:00').toISOString(),to:new Date(document.getElementById('dashboard-history-to').value+'T23:59:59.999').toISOString()};
    }
    const durations = {'1h':3600000,'6h':21600000,'24h':86400000,'7d':604800000};
    return {from:new Date(now.getTime()-durations[dashboardHistoryRange]).toISOString(),to:now.toISOString()};
}
function changeDashboardHistoryRange(value) {
    if (!['1h','6h','24h','7d','custom'].includes(value)) return;
    dashboardHistoryRange=value;
    document.getElementById('dashboard-custom-range').classList.toggle('d-none',value!=='custom');
    try { localStorage.setItem(historyPreferenceKey,JSON.stringify({range:dashboardHistoryRange,from:customHistoryFrom,to:customHistoryTo})); } catch(error) {}
    if(value!=='custom') refreshDashboardNow();
}
function applyCustomDashboardHistoryRange() {
    const from=document.getElementById('dashboard-history-from').value,to=document.getElementById('dashboard-history-to').value;
    if(!from||!to||from>to){const alert=document.getElementById('alert');alert.textContent=labels.invalidRange;alert.classList.remove('d-none');return;}
    customHistoryFrom=from;customHistoryTo=to;dashboardHistoryRange='custom';
    try { localStorage.setItem(historyPreferenceKey,JSON.stringify({range:dashboardHistoryRange,from,to})); } catch(error) {}
    refreshDashboardNow();
}
function updateDashboardRefreshControls() {
    document.getElementById('dashboard-refresh-toggle').textContent = refreshPaused ? @json($t('resume_refresh')) : @json($t('pause_refresh'));
    document.getElementById('dashboard-refresh-status').textContent = refreshPaused ? @json($t('refresh_paused')) : @json($t('auto_refresh'));
    document.getElementById('dashboard-last-refresh').textContent = @json($t('last_refresh')) + ': ' + (lastRefreshAt ? lastRefreshAt.toLocaleString() : '—');
}
function scheduleDashboardRefresh(delay = refreshIntervalMs) {
    if (refreshTimer !== null) window.clearTimeout(refreshTimer);
    refreshTimer = null;
    if (refreshPaused || document.hidden) return;
    refreshTimer = window.setTimeout(async () => {
        refreshTimer = null;
        await loadMetrics();
        scheduleDashboardRefresh();
    }, Math.max(0, delay));
}
async function refreshDashboardNow() {
    if (refreshTimer !== null) window.clearTimeout(refreshTimer);
    refreshTimer = null;
    await loadMetrics();
    scheduleDashboardRefresh();
}
function toggleDashboardAutoRefresh() {
    refreshPaused = !refreshPaused;
    if (refreshPaused && refreshTimer !== null) window.clearTimeout(refreshTimer);
    refreshTimer = null;
    updateDashboardRefreshControls();
    if (!refreshPaused) scheduleDashboardRefresh(0);
}
function changeDashboardRefreshInterval(value) {
    const interval = Number(value);
    if (!allowedRefreshIntervals.includes(interval)) return;
    refreshIntervalMs = interval;
    try { localStorage.setItem(refreshPreferenceKey, String(interval)); } catch (error) {}
    scheduleDashboardRefresh();
}
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        if (refreshTimer !== null) window.clearTimeout(refreshTimer);
        refreshTimer = null;
    } else if (!refreshPaused) {
        scheduleDashboardRefresh(0);
    }
});
async function startDashboardRefresh() {
    await loadMetrics();
    scheduleDashboardRefresh();
}
updateDashboardRefreshControls();
async function loadMetrics() {
    if (refreshInProgress) return;
    refreshInProgress = true;
    const alert = document.getElementById('alert');
    try {
        const requestUrl = new URL(endpoint, window.location.href);
        const selectedRange = getDashboardHistoryRange();
        requestUrl.searchParams.set('from', selectedRange.from);
        requestUrl.searchParams.set('to', selectedRange.to);
        if (comparePreviousPeriod) requestUrl.searchParams.set('compare', '1');
        const response = await fetch(requestUrl.toString(), {headers: {'Accept': 'application/json'}});
        if (!response.ok) throw new Error('HTTP ' + response.status);
        const payload = await response.json();
        if (!payload.latest) {
            alert.textContent = labels.noMetrics;
            alert.classList.remove('d-none');
            return;
        }
        alert.classList.add('d-none');
        const aggregationLabel = payload.range?.aggregation === 'hourly' ? labels.hourlyAverage : (payload.range?.aggregation === 'daily' ? labels.dailyAverage : '');
        document.getElementById('dashboard-history-summary').textContent = payload.range?.total
            ? labels.rangeSummary.replace(':count', Number(payload.range.total).toLocaleString()).replace(':returned', Number(payload.range.returned).toLocaleString()) + (aggregationLabel ? ' · ' + aggregationLabel : '')
            : labels.rangeNoData;
        const m = payload.latest.metrics || {};
        latestDashboardMetrics = m;
        const health = m.health || {};
        const healthScore = Number(health.score ?? 0);
        const healthStatus = health.status || 'unknown';
        const scoreElement = document.getElementById('health-score');
        scoreElement.textContent = health.score ?? '—';
        scoreElement.className = 'health-score ' + healthStatus;
        document.getElementById('health-status').textContent = healthStatus.toUpperCase();
        document.getElementById('health-progress').style.width = healthScore + '%';
        document.getElementById('health-progress').className = 'progress-bar ' + (healthStatus === 'healthy' ? 'bg-success' : healthStatus === 'warning' ? 'bg-warning' : 'bg-danger');
        const activeAlerts = health.alerts || [];
        document.getElementById('health-summary').textContent = activeAlerts.length + ' ' + @json($t('active_alerts'));
        document.getElementById('alert-count').textContent = activeAlerts.length;
        const alertContainer = document.getElementById('health-alerts');
        alertContainer.replaceChildren();
        if (!activeAlerts.length) {
            const empty = document.createElement('p'); empty.className = 'text-muted small mb-0'; empty.textContent = @json($t('no_alerts')); alertContainer.appendChild(empty);
        } else {
            activeAlerts.forEach(item => {
                const row = document.createElement('div'); row.className = 'alert-item';
                const dot = document.createElement('span'); dot.className = 'alert-dot ' + item.severity;
                const body = document.createElement('div');
                const title = document.createElement('strong'); title.textContent = item.title;
                const message = document.createElement('p'); message.textContent = item.message + ' (' + item.value + ')';
                body.append(title, message); row.append(dot, body); alertContainer.appendChild(row);
            });
        }
        const nodes = m.node_details || [];
        document.getElementById('node-count').textContent = nodes.length + ' nodes';
        const nodeBody = document.getElementById('node-comparison');
        const escapeHtml = value => String(value ?? '—').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
        nodeBody.innerHTML = nodes.length ? nodes.map(node =>
            '<tr><td><strong>' + escapeHtml(node.name) + '</strong><div class="text-muted small">' + escapeHtml((node.roles || []).join(', ')) + '</div></td>' +
            '<td>' + (node.cpu_percent == null ? '—' : Number(node.cpu_percent).toFixed(1)+'%') + '</td>' +
            '<td>' + (node.heap_percent == null ? '—' : Number(node.heap_percent).toFixed(1)+'%') + '</td>' +
            '<td>' + (node.filesystem_used_percent == null ? '—' : Number(node.filesystem_used_percent).toFixed(1)+'%') + '</td>' +
            '<td>' + Number(node.documents || 0).toLocaleString() + '</td><td>' + Number(node.shards || 0).toLocaleString() + '</td>' +
            '<td>' + Number(node.search_total || 0).toLocaleString() + '</td><td>' + Number(node.indexing_total || 0).toLocaleString() + '</td></tr>'
        ).join('') : '<tr><td colspan="8" class="text-muted text-center py-4">—</td></tr>';
        const set = (id, value) => document.getElementById(id).textContent = value ?? '—';
        set('status', (m.cluster?.status || 'unknown').toUpperCase());
        set('nodes', m.nodes);
        set('docs', Number(m.indices?.documents || 0).toLocaleString());
        set('active-shards', Number(m.cluster?.active_shards || 0).toLocaleString());
        set('unassigned', Number(m.cluster?.unassigned_shards || 0).toLocaleString());
        set('rejected', m.thread_pool?.search_rejected);
        set('search-queue', m.thread_pool?.search_queue);
        set('write-queue', m.thread_pool?.write_queue);
        set('write-rejected', m.thread_pool?.write_rejected);
        set('index-count', m.indices?.count);
        const storeBytes = Number(m.indices?.store_size_bytes || 0);
        set('store-size', storeBytes >= 1073741824 ? (storeBytes / 1073741824).toFixed(2) + ' GB' : (storeBytes / 1048576).toFixed(1) + ' MB');
        set('search-rate', m.rates?.search_per_second == null ? '—' : Number(m.rates.search_per_second).toFixed(2) + ' /s');
        set('index-rate', m.rates?.indexing_per_second == null ? '—' : Number(m.rates.indexing_per_second).toFixed(2) + ' /s');
        set('cpu', m.cpu?.percent_average == null ? '—' : m.cpu.percent_average.toFixed(1) + '%');
        set('updated', labels.lastCollected + ': ' + payload.latest.collected_at);
        const used = Number(m.jvm?.heap_used_bytes || 0) / 1048576;
        const max = Number(m.jvm?.heap_max_bytes || 0) / 1048576;
        const total = Number(m.filesystem?.total_bytes || 0);
        const available = Number(m.filesystem?.available_bytes || 0);
        renderChart('#heap', {chart:{type:'radialBar',height:230},series:[max ? Math.round(used/max*100) : 0],labels:[labels.heapUsed]});
        renderChart('#filesystem', {chart:{type:'donut',height:230},series:[Math.max(0,total-available),available],labels:[labels.used,labels.available],legend:{position:'bottom'}});
        const ratePoints = (payload.history || []).filter(point => point.metrics?.rates).map(point => ({
            time: new Date(point.collected_at).getTime(),
            search: point.metrics.rates.search_per_second,
            indexing: point.metrics.rates.indexing_per_second
        }));
        const history = (payload.history || []).filter(point => point.collected_at && point.metrics).map(point => ({
            time: new Date(point.collected_at).getTime(), metrics: point.metrics
        }));
        const comparisonHistory = (payload.comparison?.history || []).filter(point => point.collected_at && point.metrics).map(point => ({
            time: new Date(point.collected_at).getTime(), metrics: point.metrics
        }));
        const comparisonPoints = path => comparisonHistory.map(point => [point.time, path(point.metrics)])
            .filter(point => point[1] !== null && point[1] !== undefined && Number.isFinite(Number(point[1])));
        const chartSeries = (name, current, previous) => {
            const series = [{name, data: current}];
            if (comparePreviousPeriod && previous.length) series.push({name: labels.previousPeriod, data: previous});
            return series;
        };
        const cpuPoints = history.map(point => [point.time, point.metrics.cpu?.percent_average]).filter(point => point[1] !== null && point[1] !== undefined);
        const comparisonCpuPoints = comparisonPoints(metrics => metrics.cpu?.percent_average);
        const storagePoints = history.map(point => [point.time, Number(point.metrics.indices?.store_size_bytes || 0)]).filter(point => point[1] > 0);
        const comparisonStoragePoints = comparisonPoints(metrics => Number(metrics.indices?.store_size_bytes || 0)).filter(point => point[1] > 0);
        const diskPoints = history.map(point => [point.time, Number(point.metrics.filesystem?.used_bytes ?? ((point.metrics.filesystem?.total_bytes || 0) - (point.metrics.filesystem?.available_bytes || 0)))]).filter(point => point[1] > 0);
        const comparisonDiskPoints = comparisonPoints(metrics => Number(metrics.filesystem?.used_bytes ?? ((metrics.filesystem?.total_bytes || 0) - (metrics.filesystem?.available_bytes || 0)))).filter(point => point[1] > 0);
        const searchPoints = history.map(point => [point.time, point.metrics.rates?.search_per_second]).filter(point => point[1] !== null && point[1] !== undefined);
        const comparisonSearchPoints = comparisonPoints(metrics => metrics.rates?.search_per_second);
        const indexingPoints = history.map(point => [point.time, point.metrics.rates?.indexing_per_second]).filter(point => point[1] !== null && point[1] !== undefined);
        const comparisonIndexingPoints = comparisonPoints(metrics => metrics.rates?.indexing_per_second);
        const segmentPoints = history.map(point => [point.time, Number(point.metrics.indices?.segments_total || 0)]).filter(point => point[1] > 0);
        const comparisonSegmentPoints = comparisonPoints(metrics => Number(metrics.indices?.segments_total || 0)).filter(point => point[1] > 0);
        const primarySegmentPoints = history.map(point => [point.time, Number(point.metrics.indices?.segments_primaries || 0)]).filter(point => point[1] > 0);
        const primaryStoragePoints = history.map(point => [point.time, Number(point.metrics.indices?.primary_store_size_bytes || 0)]).filter(point => point[1] > 0);
        const documentPoints = history.map(point => [point.time, Number(point.metrics.indices?.documents || 0)]).filter(point => point[1] >= 0);
        const comparisonDocumentPoints = comparisonPoints(metrics => Number(metrics.indices?.documents || 0)).filter(point => point[1] >= 0);
        const formatBytes = value => value >= 1073741824 ? (value/1073741824).toFixed(2)+' GB' : (value/1048576).toFixed(1)+' MB';
        const sparkConfigs = [
            {id:'docs',path:p=>Number(p.metrics.indices?.documents || 0),format:v=>Math.round(v).toLocaleString(),inverse:false},
            {id:'active-shards',path:p=>Number(p.metrics.cluster?.active_shards || 0),format:v=>Math.round(v).toLocaleString(),inverse:false},
            {id:'unassigned',path:p=>Number(p.metrics.cluster?.unassigned_shards || 0),format:v=>Math.round(v).toLocaleString(),inverse:true},
            {id:'search-rate',path:p=>p.metrics.rates?.search_per_second,format:v=>Number(v).toFixed(2)+' /s',inverse:false},
            {id:'index-rate',path:p=>p.metrics.rates?.indexing_per_second,format:v=>Number(v).toFixed(2)+' /s',inverse:false},
            {id:'cpu',path:p=>p.metrics.cpu?.percent_average,format:v=>Number(v).toFixed(1)+'%',inverse:true},
            {id:'store-size',path:p=>Number(p.metrics.indices?.store_size_bytes || 0),format:formatBytes,inverse:false}
        ];
        sparkConfigs.forEach(config=>{
            const points=history.map(p=>[p.time,config.path(p)]).filter(p=>p[1]!==null&&p[1]!==undefined&&Number.isFinite(Number(p[1])));
            const current=points.at(-1)?.[1], previous=points.at(-2)?.[1];
            const change=document.getElementById(config.id+'-change');
            if(current!==undefined&&previous!==undefined&&Number(previous)!==0){
                const delta=((Number(current)-Number(previous))/Math.abs(Number(previous)))*100;
                const good=config.inverse?delta<0:delta>0;
                change.textContent=(delta>0?'↑ ':delta<0?'↓ ':'→ ')+Math.abs(delta).toFixed(1)+'%';
                change.className='summary-change '+(delta===0?'':good?'positive':'negative');
            }else{change.textContent='—';change.className='summary-change';}
            if(sparkCharts[config.id])sparkCharts[config.id].destroy();
            sparkCharts[config.id]=new ApexCharts(document.querySelector('#'+config.id+'-spark'),{
                chart:{type:'area',height:58,sparkline:{enabled:true},toolbar:{show:false},animations:{enabled:false}},
                series:[{data:points}],colors:[getComputedStyle(document.querySelector('#'+config.id+'-spark')).getPropertyValue('--spark-color').trim()||'#3978b8'],
                stroke:{curve:'smooth',width:2},fill:{type:'gradient',gradient:{opacityFrom:.22,opacityTo:.015,stops:[0,100]}},
                tooltip:{x:{show:false},y:{formatter:v=>config.format(Number(v))}},markers:{size:0},noData:{text:''}
            });
            sparkCharts[config.id].render();
        });
        const chartBase = {
            chart:{type:'area',height:270,toolbar:{show:false},zoom:{enabled:false}},
            dataLabels:{enabled:false},stroke:{curve:'smooth',width:2.5},
            fill:{type:'gradient',gradient:{opacityFrom:.24,opacityTo:.02,stops:[0,90,100]}},
            grid:{borderColor:'#edf0f5',strokeDashArray:4},markers:{size:0,hover:{size:5}},
            legend:{show:comparePreviousPeriod,position:'top',horizontalAlign:'right'},
            stroke:{curve:'smooth',width:2.5,dashArray:comparePreviousPeriod?[0,6]:0},
            xaxis:{type:'datetime',labels:{datetimeUTC:false}},noData:{text:'—'}
        };
        renderChart('#cpu-chart',{...chartBase,colors:['#635bff','#a4abc8'],series:chartSeries(labels.cpu,cpuPoints,comparisonCpuPoints),
            yaxis:{min:0,max:100,labels:{formatter:v=>v.toFixed(0)+'%'}},
            tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:v=>v.toFixed(1)+'%'}}});
        renderChart('#storage-chart',{...chartBase,colors:['#16a085','#a4abc8','#bda76a'],series:[...chartSeries(labels.storage,storagePoints,comparisonStoragePoints),{name:'Primary Store',data:primaryStoragePoints}],
            yaxis:{labels:{formatter:formatBytes}},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:formatBytes}}});
        renderChart('#search-chart',{...chartBase,colors:['#20b6a5','#a4abc8'],series:chartSeries(labels.search,searchPoints,comparisonSearchPoints),yaxis:{min:0},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:v=>v.toFixed(2)+' /s'}}});
        renderChart('#indexing-chart',{...chartBase,colors:['#3978b8','#a4abc8'],series:chartSeries(labels.indexing,indexingPoints,comparisonIndexingPoints),yaxis:{min:0},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:v=>v.toFixed(2)+' /s'}}});
        renderChart('#disk-chart',{...chartBase,colors:['#e39a36','#a4abc8'],series:chartSeries(labels.disk,diskPoints,comparisonDiskPoints),yaxis:{labels:{formatter:formatBytes}},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:formatBytes}}});
        renderChart('#segments-chart',{...chartBase,colors:['#8064c8','#a4abc8','#bda76a'],series:[...chartSeries(labels.segments,segmentPoints,comparisonSegmentPoints),{name:'Primary Segments',data:primarySegmentPoints}],yaxis:{min:0,labels:{formatter:v=>Math.round(v)}},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:v=>Math.round(v).toLocaleString()}}});
        renderChart('#documents-chart',{...chartBase,colors:['#16a085'],series:chartSeries(labels.documents,documentPoints,comparisonDocumentPoints),yaxis:{labels:{formatter:v=>v.toLocaleString()}},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:v=>Math.round(v).toLocaleString()}}});
        applyDashboardNoteAnnotations();
    } catch (error) {
        alert.textContent = labels.loadError + ': ' + error.message;
        alert.classList.remove('d-none');
    } finally {
        refreshInProgress = false;
        lastRefreshAt = new Date();
        updateDashboardRefreshControls();
    }
}
renderFavoriteIndices();
loadDashboardNotes();
window.addEventListener('storage', event => { if (event.key === favoritesStorageKey) renderFavoriteIndices(); });
startDashboardRefresh();
</script>
</body>
</html>
