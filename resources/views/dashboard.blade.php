<!doctype html>
<html lang="{{ str_replace('_', '-', config('elasticsearch-monitoring.dashboard.locale', 'en')) }}" dir="{{ config('elasticsearch-monitoring.dashboard.locale') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Elasticsearch Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="bg-light">
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h1 class="h3 mb-1">Elasticsearch Monitoring</h1><p class="text-muted mb-0">Cluster overview</p></div>
        <button class="btn btn-outline-secondary btn-sm" onclick="loadMetrics()">Refresh</button>
    </div>
    <div id="alert" class="alert alert-warning d-none"></div>
    <div class="row g-3">
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">Cluster status</small><strong id="status">—</strong></div></div>
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">Nodes</small><strong id="nodes">—</strong></div></div>
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">Documents</small><strong id="docs">—</strong></div></div>
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">Active shards</small><strong id="shards">—</strong></div></div>
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">Search / sec</small><strong id="search-rate">—</strong></div></div>
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">Indexing / sec</small><strong id="index-rate">—</strong></div></div>
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">Unassigned shards</small><strong id="unassigned">—</strong></div></div>
        <div class="col-md-3"><div class="card card-body"><small class="text-muted">Search rejections</small><strong id="rejected">—</strong></div></div>
        <div class="col-md-6"><div class="card card-body"><h2 class="h6">JVM heap</h2><div id="heap"></div></div></div>
        <div class="col-md-6"><div class="card card-body"><h2 class="h6">Filesystem</h2><div id="filesystem"></div></div></div>
    </div>
    <p class="small text-muted mt-3" id="updated"></p>
</main>
<script>
const endpoint = @json(route('elasticsearch-monitoring.data'));
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
            alert.textContent = 'No metrics collected yet. Run php artisan elasticsearch:collect.';
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
        set('search-rate', m.rates?.search_per_second);
        set('index-rate', m.rates?.indexing_per_second);
        set('updated', 'Last collected: ' + payload.latest.collected_at);
        const used = Number(m.jvm?.heap_used_bytes || 0) / 1048576;
        const max = Number(m.jvm?.heap_max_bytes || 0) / 1048576;
        const total = Number(m.filesystem?.total_bytes || 0);
        const available = Number(m.filesystem?.available_bytes || 0);
        renderChart('#heap', {chart:{type:'radialBar',height:230},series:[max ? Math.round(used/max*100) : 0],labels:['Heap used %']});
        renderChart('#filesystem', {chart:{type:'donut',height:230},series:[Math.max(0,total-available),available],labels:['Used','Available'],legend:{position:'bottom'}});
    } catch (error) {
        alert.textContent = 'Unable to load monitoring data: ' + error.message;
        alert.classList.remove('d-none');
    }
}
loadMetrics();
</script>
</body>
</html>