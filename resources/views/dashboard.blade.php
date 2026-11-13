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
    $uiText = [
        'tr' => ['overview'=>'Küme genel görünümü','performance'=>'Performans','storage'=>'Depolama','cpu'=>'CPU kullanımı','cpu_history'=>'CPU kullanım grafiği','cpu_hint'=>'Node başına ortalama CPU kullanımı','index_history'=>'İndeks depolama grafiği','index_hint'=>'İndeks verilerinin toplam boyutunun zaman içindeki değişimi','disk_history'=>'Disk kullanımı','disk_hint'=>'Kullanılan disk alanının zaman içindeki değişimi','search_history'=>'Arama hızı','indexing_history'=>'İndeksleme hızı','segments_history'=>'Segment sayısı','documents_history'=>'Belge sayısı','filesystem_hint'=>'Kullanılan ve kullanılabilir disk alanı','heap_hint'=>'JVM heap bellek kullanımı','rates_hint'=>'Saniyedeki arama ve indeksleme işlem sayısı','live'=>'Otomatik yenileme · 30 sn'],
        'en' => ['overview'=>'Cluster overview','performance'=>'Performance','storage'=>'Storage','cpu'=>'CPU usage','cpu_history'=>'CPU utilization','cpu_hint'=>'Average CPU usage across nodes over time','index_history'=>'Index storage trend','index_hint'=>'Total Elasticsearch index data size over time','disk_history'=>'Disk usage','disk_hint'=>'Filesystem space in use over time','search_history'=>'Search rate','indexing_history'=>'Indexing rate','segments_history'=>'Segment count','documents_history'=>'Document count','filesystem_hint'=>'Used and available disk capacity','heap_hint'=>'JVM heap memory utilization','rates_hint'=>'Search and indexing operations per second','live'=>'Auto refresh · 30 sec'],
        'es' => ['overview'=>'Resumen del clúster','performance'=>'Rendimiento','storage'=>'Almacenamiento','cpu'=>'Uso de CPU','cpu_history'=>'Uso de CPU','cpu_hint'=>'Uso medio de CPU de los nodos a lo largo del tiempo','index_history'=>'Tendencia del almacenamiento de índices','index_hint'=>'Tamaño total de los datos de índices a lo largo del tiempo','disk_history'=>'Uso del disco','disk_hint'=>'Espacio del sistema de archivos utilizado a lo largo del tiempo','search_history'=>'Tasa de búsqueda','indexing_history'=>'Tasa de indexación','segments_history'=>'Número de segmentos','documents_history'=>'Número de documentos','filesystem_hint'=>'Espacio de disco usado y disponible','heap_hint'=>'Uso de memoria heap de JVM','rates_hint'=>'Búsquedas e indexaciones por segundo','live'=>'Actualización automática · 30 s'],
        'zh_CN' => ['overview'=>'集群概览','performance'=>'性能','storage'=>'存储','cpu'=>'CPU 使用率','cpu_history'=>'CPU 使用趋势','cpu_hint'=>'各节点平均 CPU 使用率随时间的变化','index_history'=>'索引存储趋势','index_hint'=>'Elasticsearch 索引数据总量随时间的变化','disk_history'=>'磁盘使用量','disk_hint'=>'文件系统已用空间随时间的变化','search_history'=>'搜索速率','indexing_history'=>'索引速率','segments_history'=>'分段数量','documents_history'=>'文档数量','filesystem_hint'=>'已用和可用磁盘空间','heap_hint'=>'JVM 堆内存使用率','rates_hint'=>'每秒搜索和索引操作数','live'=>'每 30 秒自动刷新'],
        'hi' => ['overview'=>'क्लस्टर अवलोकन','performance'=>'प्रदर्शन','storage'=>'स्टोरेज','cpu'=>'CPU उपयोग','cpu_history'=>'CPU उपयोग का रुझान','cpu_hint'=>'समय के साथ नोड्स का औसत CPU उपयोग','index_history'=>'इंडेक्स स्टोरेज का रुझान','index_hint'=>'समय के साथ Elasticsearch इंडेक्स डेटा का कुल आकार','disk_history'=>'डिस्क उपयोग','disk_hint'=>'समय के साथ उपयोग किया गया फ़ाइल सिस्टम स्थान','search_history'=>'खोज दर','indexing_history'=>'इंडेक्सिंग दर','segments_history'=>'सेगमेंट की संख्या','documents_history'=>'दस्तावेज़ों की संख्या','filesystem_hint'=>'उपयोग किया गया और उपलब्ध डिस्क स्थान','heap_hint'=>'JVM हीप मेमोरी उपयोग','rates_hint'=>'प्रति सेकंड खोज और इंडेक्सिंग ऑपरेशन','live'=>'हर 30 सेकंड में अपडेट'],
        'ar' => ['overview'=>'نظرة عامة على المجموعة','performance'=>'الأداء','storage'=>'التخزين','cpu'=>'استخدام المعالج','cpu_history'=>'استخدام المعالج','cpu_hint'=>'متوسط استخدام المعالج عبر العقد بمرور الوقت','index_history'=>'اتجاه تخزين الفهارس','index_hint'=>'إجمالي حجم بيانات فهارس Elasticsearch بمرور الوقت','disk_history'=>'استخدام القرص','disk_hint'=>'مساحة نظام الملفات المستخدمة بمرور الوقت','search_history'=>'معدل البحث','indexing_history'=>'معدل الفهرسة','segments_history'=>'عدد المقاطع','documents_history'=>'عدد المستندات','filesystem_hint'=>'مساحة القرص المستخدمة والمتاحة','heap_hint'=>'استخدام ذاكرة JVM','rates_hint'=>'عمليات البحث والفهرسة في الثانية','live'=>'تحديث تلقائي كل 30 ثانية'],
    ][$locale] ?? [];

@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $t('title') }}</title>
    @if ($locale === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        body{background:#f4f6fb!important;color:#182238;font-family:Inter,system-ui,-apple-system,sans-serif}
        main.container{max-width:1500px}
        .hero{padding:28px 30px;border-radius:22px;background:linear-gradient(115deg,#171d38,#303b70 60%,#625bdb);color:#fff;box-shadow:0 16px 38px rgba(39,48,104,.18)}
        .hero h1{font-weight:800;letter-spacing:-.04em}.hero p{color:#d5d9f2}
        .hero .form-select{min-width:125px;background-color:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.25)}
        .hero .form-select option{color:#182238}.hero .btn{border-color:rgba(255,255,255,.4);color:#fff;background:rgba(255,255,255,.1)}
         .metric-card,.chart-card,.summary-card{background:#fff;border:1px solid #e9edf4;border-radius:17px;box-shadow:0 5px 18px rgba(29,41,74,.035);height:100%}
        .summary-card{padding:17px 19px 9px;overflow:hidden;position:relative;min-height:164px}
        .summary-label{font-size:.82rem;font-weight:600;color:#8992a6}
        .summary-value{font-size:1.85rem;line-height:1.25;font-weight:750;letter-spacing:-.055em;color:#29334d;margin-top:7px;white-space:nowrap}
        .summary-change{font-size:.72rem;font-weight:700;padding:4px 7px;border-radius:7px;background:#f1f4f9;color:#8b94a7;white-space:nowrap}
        .summary-change.positive{background:#e5f8ee;color:#1aaf68}.summary-change.negative{background:#fff0ed;color:#e05d4b}
        .summary-chart{height:58px;margin:7px -5px -2px}

        .metric-card{padding:18px 19px;transition:transform .2s,box-shadow .2s}.metric-card:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(29,41,74,.08)}
        .metric-card small{font-weight:600;color:#778198}.metric-card strong{display:block;margin-top:12px;font-size:1.5rem;letter-spacing:-.04em}
        .chart-card{padding:20px}.chart-card h2{font-size:.95rem;font-weight:700}.chart-card .text-muted{font-size:.75rem}
        @media(max-width:767px){.hero{padding:21px;border-radius:18px}.metric-card{padding:15px}.metric-card strong{font-size:1.25rem}.chart-card{padding:16px}}
    </style>
</head>
<body class="bg-light">
<main class="container py-4">
    <div class="hero d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div><h1 class="h3 mb-1">{{ $t('title') }}</h1><p class="text-muted mb-0">{{ $t('subtitle') }}</p></div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('elasticsearch-monitoring.index') }}" class="d-flex align-items-center gap-2">
                <label for="dashboard-locale" class="visually-hidden">Dashboard language</label>
                <select
                    id="dashboard-locale"
                    name="locale"
                    class="form-select form-select-sm"
                    aria-label="Dashboard language"
                    onchange="document.cookie = 'elasticsearch_monitoring_locale=' + encodeURIComponent(this.value) + '; path=/; max-age=31536000; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : ''); this.form.submit()"
                >
                    @foreach ($availableLocales as $availableLocale)
                        <option value="{{ $availableLocale }}" @selected($locale === $availableLocale)>
                            {{ $languageNames[$availableLocale] ?? strtoupper($availableLocale) }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('elasticsearch-monitoring.indices', ['locale' => $locale]) }}">Index Management</a>
            <button class="btn btn-outline-secondary btn-sm" onclick="loadMetrics()">{{ $t('refresh') }}</button>
        </div>
    </div>
    <div id="alert" class="alert alert-warning d-none"></div>
    <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h6 fw-bold mb-0">{{ $uiText['overview'] }}</h2><small class="text-muted">● {{ $uiText['live'] }}</small></div>
    <div class="row g-3 mb-4" id="summary-cards">
        @foreach ([
            ['id'=>'docs','label'=>$t('documents'),'unit'=>'','color'=>'#20b6a5'],
            ['id'=>'active-shards','label'=>$t('active_shards'),'unit'=>'','color'=>'#3978b8'],
            ['id'=>'unassigned','label'=>$t('unassigned_shards'),'unit'=>'','color'=>'#e39a36'],
            ['id'=>'search-rate','label'=>$uiText['search_history'],'unit'=>' /s','color'=>'#20b6a5'],
            ['id'=>'index-rate','label'=>$uiText['indexing_history'],'unit'=>' /s','color'=>'#3978b8'],
            ['id'=>'cpu','label'=>$uiText['cpu'],'unit'=>'%','color'=>'#8064c8'],
            ['id'=>'store-size','label'=>$t('store_size'),'unit'=>'','color'=>'#16a085'],
        ] as $card)
            <div class="col-12 col-sm-6 col-xl-3">
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
    <div class="row g-3">
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
        <div class="col-md-6"><div class="chart-card"><h2>{{ $t('heap') }}</h2><p class="text-muted">{{ $uiText['heap_hint'] }}</p><div id="heap"></div></div></div>
        <div class="col-md-6"><div class="chart-card"><h2>{{ $t('filesystem') }}</h2><p class="text-muted">{{ $uiText['filesystem_hint'] }}</p><div id="filesystem"></div></div></div>
        <div class="col-md-6"><div class="chart-card"><h2>{{ $uiText['search_history'] }}</h2><p class="text-muted">{{ $uiText['rates_hint'] }}</p><div id="search-chart"></div></div></div>
        <div class="col-md-6"><div class="chart-card"><h2>{{ $uiText['indexing_history'] }}</h2><p class="text-muted">{{ $uiText['rates_hint'] }}</p><div id="indexing-chart"></div></div></div>
        <div class="col-md-6"><div class="chart-card"><h2>{{ $uiText['disk_history'] }}</h2><p class="text-muted">{{ $uiText['disk_hint'] }}</p><div id="disk-chart"></div></div></div>
        <div class="col-md-6"><div class="chart-card"><h2>{{ $uiText['segments_history'] }}</h2><p class="text-muted">{{ $uiText['segments_history'] }}</p><div id="segments-chart"></div></div></div>
        <div class="col-12"><div class="chart-card"><h2>{{ $uiText['documents_history'] }}</h2><p class="text-muted">{{ $uiText['documents_history'] }}</p><div id="documents-chart"></div></div></div>
        <div class="col-md-6"><div class="chart-card"><h2>{{ $uiText['cpu_history'] }}</h2><p class="text-muted">{{ $uiText['cpu_hint'] }}</p><div id="cpu-chart"></div></div></div>
        <div class="col-md-6"><div class="chart-card"><h2>{{ $uiText['index_history'] }}</h2><p class="text-muted">{{ $uiText['index_hint'] }}</p><div id="storage-chart"></div></div></div>
    </div>
    <p class="small text-muted mt-3" id="updated"></p>
</main>
<script>
const endpoint = @json(route('elasticsearch-monitoring.data'));
const labels = {
    noMetrics: @json($t('no_metrics')),
    lastCollected: @json($t('last_collected')),
    loadError: @json($t('load_error')),
    heapUsed: @json($t('heap_used')),
    used: @json($t('used')),
    available: @json($t('available')), cpu: @json($uiText['cpu']), storage: @json($t('store_size')), disk: @json($uiText['disk_history']), search: @json($uiText['search_history']), indexing: @json($uiText['indexing_history']), segments: @json($uiText['segments_history']), documents: @json($uiText['documents_history'])
};
const charts = {};
const sparkCharts = {};
function renderChart(id, options) {
    if (charts[id]) charts[id].destroy();
    charts[id] = new ApexCharts(document.querySelector(id), options);
    charts[id].render();
}
async function loadMetrics() {
    const alert = document.getElementById('alert');
    try {
        const response = await fetch(endpoint, {headers: {'Accept': 'application/json'}});
        if (!response.ok) throw new Error('HTTP ' + response.status);
        const payload = await response.json();
        if (!payload.latest) {
            alert.textContent = labels.noMetrics;
            alert.classList.remove('d-none');
            return;
        }
        alert.classList.add('d-none');
        const m = payload.latest.metrics || {};
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
        const cpuPoints = history.map(point => [point.time, point.metrics.cpu?.percent_average]).filter(point => point[1] !== null && point[1] !== undefined);
        const storagePoints = history.map(point => [point.time, Number(point.metrics.indices?.store_size_bytes || 0)]).filter(point => point[1] > 0);
        const diskPoints = history.map(point => [point.time, Number(point.metrics.filesystem?.used_bytes ?? ((point.metrics.filesystem?.total_bytes || 0) - (point.metrics.filesystem?.available_bytes || 0)))]).filter(point => point[1] > 0);
        const searchPoints = history.map(point => [point.time, point.metrics.rates?.search_per_second]).filter(point => point[1] !== null && point[1] !== undefined);
        const indexingPoints = history.map(point => [point.time, point.metrics.rates?.indexing_per_second]).filter(point => point[1] !== null && point[1] !== undefined);
        const segmentPoints = history.map(point => [point.time, Number(point.metrics.indices?.segments_total || 0)]).filter(point => point[1] > 0);
        const primarySegmentPoints = history.map(point => [point.time, Number(point.metrics.indices?.segments_primaries || 0)]).filter(point => point[1] > 0);
        const primaryStoragePoints = history.map(point => [point.time, Number(point.metrics.indices?.primary_store_size_bytes || 0)]).filter(point => point[1] > 0);
        const documentPoints = history.map(point => [point.time, Number(point.metrics.indices?.documents || 0)]).filter(point => point[1] >= 0);
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
            xaxis:{type:'datetime',labels:{datetimeUTC:false}},noData:{text:'—'}
        };
        renderChart('#cpu-chart',{...chartBase,colors:['#635bff'],series:[{name:labels.cpu,data:cpuPoints}],
            yaxis:{min:0,max:100,labels:{formatter:v=>v.toFixed(0)+'%'}},
            tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:v=>v.toFixed(1)+'%'}}});
        renderChart('#storage-chart',{...chartBase,colors:['#16a085'],series:[{name:labels.storage,data:storagePoints},{name:'Primary Store',data:primaryStoragePoints}],
            yaxis:{labels:{formatter:formatBytes}},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:formatBytes}}});
        renderChart('#search-chart',{...chartBase,colors:['#20b6a5'],series:[{name:labels.search,data:searchPoints}],yaxis:{min:0},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:v=>v.toFixed(2)+' /s'}}});
        renderChart('#indexing-chart',{...chartBase,colors:['#3978b8'],series:[{name:labels.indexing,data:indexingPoints}],yaxis:{min:0},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:v=>v.toFixed(2)+' /s'}}});
        renderChart('#disk-chart',{...chartBase,colors:['#e39a36'],series:[{name:labels.disk,data:diskPoints}],yaxis:{labels:{formatter:formatBytes}},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:formatBytes}}});
        renderChart('#segments-chart',{...chartBase,colors:['#8064c8'],series:[{name:labels.segments,data:segmentPoints},{name:'Primary Segments',data:primarySegmentPoints}],yaxis:{min:0,labels:{formatter:v=>Math.round(v)}},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:v=>Math.round(v).toLocaleString()}}});
        renderChart('#documents-chart',{...chartBase,colors:['#16a085'],series:[{name:labels.documents,data:documentPoints}],yaxis:{labels:{formatter:v=>v.toLocaleString()}},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:v=>Math.round(v).toLocaleString()}}});
    } catch (error) {
        alert.textContent = labels.loadError + ': ' + error.message;
        alert.classList.remove('d-none');
    }
}
loadMetrics();
setInterval(loadMetrics, 30000);
</script>
</body>
</html>
