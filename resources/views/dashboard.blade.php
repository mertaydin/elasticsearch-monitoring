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
</head>
<body class="bg-light">
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
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
            <button class="btn btn-outline-secondary btn-sm" onclick="loadMetrics()">{{ $t('refresh') }}</button>
        </div>
    </div>
    <div id="alert" class="alert alert-warning d-none"></div>
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
                <div class="card card-body h-100"><small class="text-muted">{{ $id === 'cpu' ? 'CPU' : $t($label) }}</small><strong id="{{ $id }}">—</strong></div>
            </div>
        @endforeach
        <div class="col-md-6"><div class="card card-body"><h2 class="h6">{{ $t('heap') }}</h2><div id="heap"></div></div></div>
        <div class="col-md-6"><div class="card card-body"><h2 class="h6">{{ $t('filesystem') }}</h2><div id="filesystem"></div></div></div>
        <div class="col-12"><div class="card card-body"><h2 class="h6">{{ $t('rates') }}</h2><div id="rates"></div></div></div>
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
    available: @json($t('available'))
};
const charts = {};
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
        set('shards', m.cluster?.active_shards);
        set('unassigned', m.cluster?.unassigned_shards);
        set('rejected', m.thread_pool?.search_rejected);
        set('search-queue', m.thread_pool?.search_queue);
        set('write-queue', m.thread_pool?.write_queue);
        set('write-rejected', m.thread_pool?.write_rejected);
        set('index-count', m.indices?.count);
        const storeBytes = Number(m.indices?.store_size_bytes || 0);
        set('store-size', storeBytes >= 1073741824 ? (storeBytes / 1073741824).toFixed(2) + ' GB' : (storeBytes / 1048576).toFixed(1) + ' MB');
        set('search-rate', m.rates?.search_per_second);
        set('index-rate', m.rates?.indexing_per_second);
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
        renderChart('#rates', {chart:{type:'line',height:280,toolbar:{show:false}},series:[
            {name:'Search / sec',data:ratePoints.map(point => [point.time, point.search])},
            {name:'Indexing / sec',data:ratePoints.map(point => [point.time, point.indexing])}
        ],xaxis:{type:'datetime'},stroke:{curve:'smooth',width:2},noData:{text:'—'}});
    } catch (error) {
        alert.textContent = labels.loadError + ': ' + error.message;
        alert.classList.remove('d-none');
    }
}
loadMetrics();
</script>
</body>
</html>
