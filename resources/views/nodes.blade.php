@php
    $availableLocales = config('elasticsearch-monitoring.dashboard.available_locales', ['tr', 'en']);
    $requestedLocale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', config('elasticsearch-monitoring.dashboard.locale', 'en')));
    $locale = in_array($requestedLocale, $availableLocales, true) ? $requestedLocale : 'en';
    $text = [
        'tr' => ['title'=>'Node Performans Geçmişi','description'=>'Node bazında kaynak kullanımı ve işlem hızlarının zaman içindeki değişimi.','back'=>'İzleme paneli','indices'=>'İndeks yönetimi','streams'=>'Data Streams','alerts'=>'Alarm geçmişi','node'=>'Node seçin','refresh'=>'Yenile','cpu'=>'CPU kullanımı','heap'=>'JVM heap kullanımı','disk'=>'Disk kullanımı','documents'=>'Belge sayısı','shards'=>'Shard sayısı','search'=>'Arama hızı','indexing'=>'İndeksleme hızı','search_total'=>'Toplam arama','indexing_total'=>'Toplam indeksleme','queue'=>'Kuyruk','rejected'=>'Reddedilen','roles'=>'Roller','comparison'=>'Node karşılaştırması','name'=>'Node','loading'=>'Ölçümler yükleniyor...','empty'=>'Henüz node ölçümü bulunamadı. Önce php artisan elasticsearch:collect komutunu çalıştırın.','error'=>'Node performans geçmişi yüklenemedi.','updated'=>'Son güncelleme','points'=>'ölçüm','percent'=>'Kullanım (%)','per_second'=>'İşlem / sn'],
        'en' => ['title'=>'Node Performance History','description'=>'Historical resource utilization and operation rates for each Elasticsearch node.','back'=>'Monitoring','indices'=>'Index management','streams'=>'Data Streams','alerts'=>'Alert history','node'=>'Select node','refresh'=>'Refresh','cpu'=>'CPU utilization','heap'=>'JVM heap utilization','disk'=>'Disk utilization','documents'=>'Documents','shards'=>'Shards','search'=>'Search rate','indexing'=>'Indexing rate','search_total'=>'Search total','indexing_total'=>'Indexing total','queue'=>'Queue','rejected'=>'Rejected','roles'=>'Roles','comparison'=>'Node comparison','name'=>'Node','loading'=>'Loading node metrics...','empty'=>'No node metrics found yet. Run php artisan elasticsearch:collect first.','error'=>'Unable to load node performance history.','updated'=>'Last updated','points'=>'samples','percent'=>'Utilization (%)','per_second'=>'Operations / sec'],
        'es' => ['title'=>'Historial de rendimiento de nodos','description'=>'Historial de uso de recursos y tasas de operaciones por nodo.','back'=>'Panel','indices'=>'Índices','streams'=>'Data Streams','alerts'=>'Historial de alertas','node'=>'Seleccionar nodo','refresh'=>'Actualizar','cpu'=>'Uso de CPU','heap'=>'Uso de heap JVM','disk'=>'Uso de disco','documents'=>'Documentos','shards'=>'Fragmentos','search'=>'Tasa de búsqueda','indexing'=>'Tasa de indexación','search_total'=>'Búsquedas totales','indexing_total'=>'Indexaciones totales','queue'=>'Cola','rejected'=>'Rechazados','roles'=>'Roles','comparison'=>'Comparación de nodos','name'=>'Nodo','loading'=>'Cargando métricas...','empty'=>'No hay métricas. Ejecute php artisan elasticsearch:collect.','error'=>'No se pudo cargar el historial.','updated'=>'Actualizado','points'=>'muestras','percent'=>'Uso (%)','per_second'=>'Operaciones / s'],
        'zh_CN' => ['title'=>'节点性能历史','description'=>'各 Elasticsearch 节点资源使用率和操作速率的历史记录。','back'=>'监控','indices'=>'索引管理','streams'=>'数据流','alerts'=>'告警历史','node'=>'选择节点','refresh'=>'刷新','cpu'=>'CPU 使用率','heap'=>'JVM 堆使用率','disk'=>'磁盘使用率','documents'=>'文档','shards'=>'分片','search'=>'搜索速率','indexing'=>'索引速率','search_total'=>'搜索总数','indexing_total'=>'索引总数','queue'=>'队列','rejected'=>'拒绝数','roles'=>'角色','comparison'=>'节点比较','name'=>'节点','loading'=>'正在加载指标...','empty'=>'暂无节点指标。请先运行 php artisan elasticsearch:collect。','error'=>'无法加载节点性能历史。','updated'=>'最后更新','points'=>'个样本','percent'=>'使用率 (%)','per_second'=>'操作 / 秒'],
        'hi' => ['title'=>'नोड प्रदर्शन इतिहास','description'=>'प्रत्येक Elasticsearch नोड के संसाधन उपयोग और ऑपरेशन दर का इतिहास।','back'=>'मॉनिटरिंग','indices'=>'इंडेक्स प्रबंधन','streams'=>'Data Streams','alerts'=>'अलर्ट इतिहास','node'=>'नोड चुनें','refresh'=>'रीफ़्रेश','cpu'=>'CPU उपयोग','heap'=>'JVM हीप उपयोग','disk'=>'डिस्क उपयोग','documents'=>'दस्तावेज़','shards'=>'शार्ड','search'=>'खोज दर','indexing'=>'इंडेक्सिंग दर','search_total'=>'कुल खोज','indexing_total'=>'कुल इंडेक्सिंग','queue'=>'कतार','rejected'=>'अस्वीकृत','roles'=>'भूमिकाएँ','comparison'=>'नोड तुलना','name'=>'नोड','loading'=>'मेट्रिक्स लोड हो रहे हैं...','empty'=>'कोई नोड मेट्रिक्स नहीं। पहले php artisan elasticsearch:collect चलाएँ।','error'=>'नोड इतिहास लोड नहीं हुआ।','updated'=>'अंतिम अपडेट','points'=>'नमूने','percent'=>'उपयोग (%)','per_second'=>'ऑपरेशन / सेकंड'],
        'ar' => ['title'=>'سجل أداء العقد','description'=>'السجل التاريخي لاستخدام الموارد ومعدلات العمليات لكل عقدة.','back'=>'المراقبة','indices'=>'إدارة الفهارس','streams'=>'تدفقات البيانات','alerts'=>'سجل التنبيهات','node'=>'اختر عقدة','refresh'=>'تحديث','cpu'=>'استخدام المعالج','heap'=>'استخدام JVM heap','disk'=>'استخدام القرص','documents'=>'المستندات','shards'=>'الأجزاء','search'=>'معدل البحث','indexing'=>'معدل الفهرسة','search_total'=>'إجمالي البحث','indexing_total'=>'إجمالي الفهرسة','queue'=>'قائمة الانتظار','rejected'=>'المرفوض','roles'=>'الأدوار','comparison'=>'مقارنة العقد','name'=>'العقدة','loading'=>'جار تحميل المقاييس...','empty'=>'لا توجد مقاييس للعقد. شغّل php artisan elasticsearch:collect أولاً.','error'=>'تعذر تحميل سجل أداء العقد.','updated'=>'آخر تحديث','points'=>'عينات','percent'=>'الاستخدام (%)','per_second'=>'عملية / ثانية'],
    ][$locale];
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $text['title'] }}</title>
@if ($locale === 'ar')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
@else
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endif
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
body{background:#f4f6fb;color:#182238;font-family:Inter,system-ui,-apple-system,sans-serif}
.page{max-width:1600px}.panel,.chart-panel{background:#fff;border:1px solid #e8ecf3;border-radius:16px;box-shadow:0 5px 22px rgba(26,39,71,.045)}
.hero{background:linear-gradient(115deg,#171d38,#303b70 62%,#625bdb);color:#fff;border-radius:20px;padding:24px 28px}.hero p{color:#d5d9f2}
.metric{padding:18px 20px;height:100%}.metric .label{font-size:.82rem;color:#6b7487}.metric .value{font-size:1.65rem;font-weight:750;letter-spacing:-.03em;margin-top:5px}.chart-panel{padding:18px 20px;height:100%;min-height:330px}.chart-panel h2{font-size:1rem;font-weight:700;margin:0}.chart-panel p{font-size:.8rem;color:#7b8496;margin:5px 0 12px}.node-select{min-width:230px}.table thead th{font-size:.76rem;color:#68748a;font-weight:650;white-space:nowrap}.table tbody td{font-size:.86rem;white-space:nowrap}.node-pick{border:0;background:none;color:#334c9b;font-weight:650;padding:0}.muted{color:#7b8496}
.availability-panel{padding:18px 20px}.availability-track{display:flex;height:13px;overflow:hidden;border-radius:99px;background:#edf0f5;gap:2px}.availability-segment{height:100%;min-width:2px}.availability-segment.online{background:#20a36b}.availability-segment.offline{background:#e05252}.availability-legend{font-size:.78rem;color:#68748a}.availability-period{display:flex;justify-content:space-between;gap:12px;font-size:.82rem;padding:7px 0;border-bottom:1px solid #edf0f5}.availability-period:last-child{border-bottom:0}.availability-state{font-size:.82rem;font-weight:700;border-radius:99px;padding:5px 10px}.availability-state.online{background:#e6f6ee;color:#167448}.availability-state.offline{background:#fff0f0;color:#b83232}.availability-state.unknown{background:#edf0f5;color:#68748a}
.version-summary{display:flex;flex-wrap:wrap;gap:10px}.version-chip{border:1px solid #e7ebf2;background:#f8f9fc;border-radius:12px;padding:10px 14px;min-width:150px}.version-chip strong{display:block;font-size:1rem;color:#263451}.version-chip span{font-size:.78rem;color:#738097}.version-status{font-size:.8rem;font-weight:700;border-radius:99px;padding:5px 10px}.version-status.consistent{background:#e6f6ee;color:#167448}.version-status.mixed{background:#fff1df;color:#9b5b08}.version-status.incomplete,.version-status.unknown{background:#edf0f5;color:#68748a}
</style>
</head>
<body>
<main class="container-fluid page py-4 px-3 px-lg-4">
<div class="hero mb-4">
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
<div>
<div class="d-flex flex-wrap gap-2 mb-3">
<a href="{{ route('elasticsearch-monitoring.index',['locale'=>$locale]) }}" class="btn btn-sm btn-light">← {{ $text['back'] }}</a>
<a href="{{ route('elasticsearch-monitoring.indices',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-light">{{ $text['indices'] }}</a>
<a href="{{ route('elasticsearch-monitoring.data-streams',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-light">{{ $text['streams'] }}</a>
<a href="{{ route('elasticsearch-monitoring.alerts',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-light">{{ $text['alerts'] }}</a>
</div>
<h1 class="h3 fw-bold mb-1">{{ $text['title'] }}</h1><p class="mb-0">{{ $text['description'] }}</p>
</div>
<div class="d-flex flex-wrap align-items-center gap-2">
<select class="form-select node-select" id="node-select" aria-label="{{ $text['node'] }}" disabled><option>{{ $text['loading'] }}</option></select>
<button class="btn btn-light" id="refresh">↻ {{ $text['refresh'] }}</button>
</div>
</div>
</div>
<div id="notice" class="alert alert-info d-none"></div>
<div class="row g-3 mb-3">
<div class="col-6 col-xl"><div class="panel metric"><div class="label">{{ $text['cpu'] }}</div><div class="value" id="cpu-value">—</div></div></div>
<div class="col-6 col-xl"><div class="panel metric"><div class="label">{{ $text['heap'] }}</div><div class="value" id="heap-value">—</div></div></div>
<div class="col-6 col-xl"><div class="panel metric"><div class="label">{{ $text['disk'] }}</div><div class="value" id="disk-value">—</div></div></div>
<div class="col-6 col-xl"><div class="panel metric"><div class="label">{{ $text['documents'] }}</div><div class="value" id="documents-value">—</div></div></div>
<div class="col-6 col-xl"><div class="panel metric"><div class="label">{{ $text['shards'] }}</div><div class="value" id="shards-value">—</div></div></div>
</div>
<section class="panel p-3 mb-4" aria-live="polite" id="version-panel">
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"><div><h2 class="h6 fw-bold mb-1">{{ $locale === 'tr' ? 'Elasticsearch sürüm dağılımı' : 'Elasticsearch version distribution' }}</h2><div class="small muted">{{ $locale === 'tr' ? 'Node sürümlerini karşılaştırın; rolling upgrade sırasında farklı kalan node’ları görün.' : 'Compare node versions and spot nodes left behind during a rolling upgrade.' }}</div></div><span id="version-status" class="version-status unknown">—</span></div>
<div id="version-summary" class="version-summary"><div class="small muted">{{ $text['loading'] }}</div></div>
</section>
<section class="panel p-3 mb-4" id="node-details-panel">
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
<h2 class="h6 fw-bold mb-0">{{ $locale === 'tr' ? 'Node detayları' : 'Node details' }}</h2>
<span class="small muted" id="node-details-meta">—</span>
</div>
<div class="row g-3 mb-3">
<div class="col-6 col-lg-3"><div class="label muted small">IP / {{ $locale === 'tr' ? 'Roller' : 'Roles' }}</div><div class="fw-semibold" id="node-detail-address">—</div><div class="small muted" id="node-detail-roles">—</div></div>
<div class="col-6 col-lg-3"><div class="label muted small">{{ $locale === 'tr' ? 'Elasticsearch sürümü' : 'Elasticsearch version' }}</div><div class="fw-semibold" id="node-detail-version">—</div></div>
<div class="col-6 col-lg-3"><div class="label muted small">CPU</div><div class="fw-semibold" id="node-detail-cpu">—</div></div>
<div class="col-6 col-lg-3"><div class="label muted small">JVM Heap</div><div class="fw-semibold" id="node-detail-heap">—</div></div>
<div class="col-6 col-lg-3"><div class="label muted small">Disk</div><div class="fw-semibold" id="node-detail-disk">—</div></div>
</div>
<div class="d-flex flex-wrap gap-3 small mb-2">
<span>{{ $locale === 'tr' ? 'Doküman' : 'Documents' }}: <strong id="node-detail-documents">—</strong></span>
<span>{{ $locale === 'tr' ? 'Shard' : 'Shards' }}: <strong id="node-detail-shards">—</strong></span>
</div>
<div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0">
<thead><tr><th>{{ $locale === 'tr' ? 'İndeks' : 'Index' }}</th><th>{{ $locale === 'tr' ? 'Primary shard' : 'Primary shards' }}</th><th>{{ $locale === 'tr' ? 'Replica shard' : 'Replica shards' }}</th></tr></thead>
<tbody id="node-detail-indices"><tr><td colspan="3" class="muted">{{ $locale === 'tr' ? 'Yükleniyor…' : 'Loading…' }}</td></tr></tbody>
</table></div></section>
<section class="panel availability-panel mb-4" aria-live="polite">
 <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
  <div><h2 class="h6 fw-bold mb-1">{{ $locale === 'tr' ? 'Node sağlık geçmişi' : 'Node health history' }}</h2><div class="small muted">{{ $locale === 'tr' ? 'Son 24 saatteki erişilebilirlik değişimleri' : 'Availability changes over the last 24 hours' }}</div></div>
  <span id="availability-state" class="availability-state unknown">—</span>
 </div>
 <div class="availability-track mb-2" id="availability-track" role="img" aria-label="{{ $locale === 'tr' ? 'Node çevrimiçi ve çevrimdışı zaman çizelgesi' : 'Node online and offline timeline' }}"><span class="availability-segment online" style="width:100%"></span></div>
 <div class="d-flex justify-content-between availability-legend mb-3"><span>{{ $locale === 'tr' ? 'İlk ölçüm' : 'First sample' }}</span><span>{{ $locale === 'tr' ? 'Şimdi' : 'Now' }}</span></div>
 <div class="d-flex flex-wrap gap-3 small mb-2">
  <span><span class="availability-segment online d-inline-block rounded-pill me-1" style="width:10px;height:10px"></span>{{ $locale === 'tr' ? 'Çevrimiçi' : 'Online' }}</span>
  <span><span class="availability-segment offline d-inline-block rounded-pill me-1" style="width:10px;height:10px"></span>{{ $locale === 'tr' ? 'Erişilemiyor' : 'Unreachable' }}</span>
  <span class="muted" id="availability-duration"></span>
 </div>
 <div id="availability-periods" class="mt-2"></div>
</section>
<div class="row g-3 mb-4">
<div class="col-12 col-lg-6"><section class="chart-panel"><div class="chart-title-row d-flex justify-content-between align-items-center gap-2"><h2>{{ $text['cpu'] }}</h2><button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportMonitoringChartPng(charts['cpu-chart'], labels.cpu, 'node-cpu-chart')">PNG</button></div><p>{{ $text['percent'] }}</p><div id="cpu-chart"></div></section></div>
<div class="col-12 col-lg-6"><section class="chart-panel"><div class="chart-title-row d-flex justify-content-between align-items-center gap-2"><h2>{{ $text['heap'] }}</h2><button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportMonitoringChartPng(charts['heap-chart'], labels.heap, 'node-heap-chart')">PNG</button></div><p>{{ $text['percent'] }}</p><div id="heap-chart"></div></section></div>
<div class="col-12 col-lg-6"><section class="chart-panel"><div class="chart-title-row d-flex justify-content-between align-items-center gap-2"><h2>{{ $text['disk'] }}</h2><button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportMonitoringChartPng(charts['disk-chart'], labels.disk, 'node-disk-chart')">PNG</button></div><p>{{ $text['percent'] }}</p><div id="disk-chart"></div></section></div>
<div class="col-12 col-lg-6"><section class="chart-panel"><div class="chart-title-row d-flex justify-content-between align-items-center gap-2"><h2>{{ $text['search'] }}</h2><button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportMonitoringChartPng(charts['search-chart'], labels.search, 'node-search-chart')">PNG</button></div><p>{{ $text['per_second'] }}</p><div id="search-chart"></div></section></div>
<div class="col-12 col-lg-6"><section class="chart-panel"><div class="chart-title-row d-flex justify-content-between align-items-center gap-2"><h2>{{ $text['indexing'] }}</h2><button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportMonitoringChartPng(charts['indexing-chart'], labels.indexing, 'node-indexing-chart')">PNG</button></div><p>{{ $text['per_second'] }}</p><div id="indexing-chart"></div></section></div>
<div class="col-12 col-lg-6"><section class="chart-panel"><div class="chart-title-row d-flex justify-content-between align-items-center gap-2"><h2>{{ $text['queue'] }} / {{ $text['rejected'] }}</h2><button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportMonitoringChartPng(charts['queue-chart'], labels.queue + ' / ' + labels.rejected, 'node-queue-chart')">PNG</button></div><p>{{ $text['search'] }} · {{ $text['indexing'] }}</p><div id="queue-chart"></div></section></div>
</div>
<section class="panel p-3">
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"><h2 class="h6 fw-bold mb-0">{{ $text['comparison'] }}</h2><div class="d-flex align-items-center gap-2"><label for="node-sort" class="small muted mb-0">{{ $locale === 'tr' ? 'Sırala:' : 'Sort by:' }}</label><select id="node-sort" class="form-select form-select-sm" style="width:auto"><option value="cpu">{{ $locale === 'tr' ? 'CPU kullanımı' : 'CPU usage' }}</option><option value="heap">{{ $locale === 'tr' ? 'RAM / JVM kullanımı' : 'RAM / JVM usage' }}</option><option value="disk">{{ $locale === 'tr' ? 'Disk kullanımı' : 'Disk usage' }}</option></select><span class="small muted ms-2" id="updated"></span></div></div>
<div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>{{ $text['name'] }}</th><th>{{ $locale === 'tr' ? 'IP adresi' : 'IP address' }}</th><th>{{ $text['roles'] }}</th><th>{{ $locale === 'tr' ? 'Sürüm' : 'Version' }}</th><th>CPU</th><th>JVM</th><th>{{ $text['disk'] }}</th><th>{{ $text['documents'] }}</th><th>{{ $text['shards'] }}</th><th>{{ $text['search_total'] }}</th><th>{{ $text['indexing_total'] }}</th></tr></thead>
<tbody id="node-table"><tr><td colspan="11" class="text-center py-4 muted">{{ $text['loading'] }}</td></tr></tbody>
</table></div>
</section>
</main>
<div id="copy-toast" class="copy-toast" role="status" aria-live="polite"></div>
<style>.copy-toast{position:fixed;bottom:24px;left:50%;transform:translate(-50%,16px);background:#182238;color:#fff;padding:10px 16px;border-radius:9px;opacity:0;pointer-events:none;transition:.2s;z-index:2000}.copy-toast.show{opacity:1;transform:translate(-50%,0)}</style>
<script>
const endpoint=@json(route('elasticsearch-monitoring.nodes.data',['locale'=>$locale]));
const detailsEndpoint=@json(route('elasticsearch-monitoring.nodes.details',['locale'=>$locale]));
const labels=@json($text);
const chartExportLabels = {error: @json(trans('elasticsearch-monitoring::messages.chart_export_failed', [], $locale))};
async function exportMonitoringChartPng(chart, title, filename) {
    try {
        if (!chart) throw new Error(chartExportLabels.error);
        const result = await chart.dataURI({scale: 2});
        const image = new Image();
        await new Promise((resolve, reject) => { image.onload = resolve; image.onerror = () => reject(new Error(chartExportLabels.error)); image.src = result.imgURI; });
        const canvas = document.createElement('canvas'); canvas.width = 1400; canvas.height = 900;
        const context = canvas.getContext('2d'); context.fillStyle = '#fff'; context.fillRect(0, 0, canvas.width, canvas.height);
        context.fillStyle = '#182238'; context.font = '700 34px Arial, sans-serif'; context.fillText(String(title), 42, 58, 1316);
        context.fillStyle = '#77839a'; context.font = '18px Arial, sans-serif'; context.fillText(new Date().toLocaleString(), 42, 91);
        const scale = Math.min(1316 / image.width, 760 / image.height), width = image.width * scale, height = image.height * scale;
        context.drawImage(image, (1400 - width) / 2, 120 + (760 - height) / 2, width, height);
        const blob = await new Promise((resolve, reject) => canvas.toBlob(value => value ? resolve(value) : reject(new Error(chartExportLabels.error)), 'image/png'));
        const url = URL.createObjectURL(blob), link = document.createElement('a'); link.href = url;
        link.download = filename + '-' + new Date().toISOString().slice(0, 10) + '.png';
        document.body.appendChild(link); link.click(); link.remove(); URL.revokeObjectURL(url);
    } catch (error) { window.alert(chartExportLabels.error); }
}
const copyLabels={"tr":{"copy":"Kopyala","all":"Tüm mapping'i kopyala","field":"Alanı kopyala","done":"Panoya kopyalandı","fail":"Kopyalama başarısız"},"en":{"copy":"Copy","all":"Copy entire mapping","field":"Copy field","done":"Copied to clipboard","fail":"Copy failed"},"es":{"copy":"Copiar","all":"Copiar todo el mapping","field":"Copiar campo","done":"Copiado al portapapeles","fail":"Error al copiar"},"zh_CN":{"copy":"复制","all":"复制完整 mapping","field":"复制字段","done":"已复制到剪贴板","fail":"复制失败"},"hi":{"copy":"कॉपी करें","all":"पूरा mapping कॉपी करें","field":"फ़ील्ड कॉपी करें","done":"क्लिपबोर्ड पर कॉपी किया गया","fail":"कॉपी विफल"},"ar":{"copy":"نسخ","all":"نسخ mapping بالكامل","field":"نسخ الحقل","done":"تم النسخ إلى الحافظة","fail":"فشل النسخ"}}[@json($locale)] || {"copy":"Copy","all":"Copy entire mapping","field":"Copy field","done":"Copied to clipboard","fail":"Copy failed"};
function showCopyToast(message){const el=document.getElementById('copy-toast');if(!el)return;el.textContent=message;el.classList.add('show');clearTimeout(window.__copyToastTimer);window.__copyToastTimer=setTimeout(()=>el.classList.remove('show'),1800);}
async function copyText(value){try{if(navigator.clipboard&&window.isSecureContext)await navigator.clipboard.writeText(String(value));else{const el=document.createElement('textarea');el.value=String(value);el.style.position='fixed';el.style.opacity='0';document.body.append(el);el.select();const ok=document.execCommand('copy');el.remove();if(!ok)throw new Error('Clipboard unavailable');}showCopyToast(copyLabels.done);}catch(e){showCopyToast(copyLabels.fail);}}
document.addEventListener('click',event=>{const button=event.target.closest('[data-copy-value]');if(button){event.preventDefault();copyText(button.dataset.copyValue);}});

const number=value=>value===null||value===undefined?'—':Number(value).toLocaleString(document.documentElement.lang,{maximumFractionDigits:2});
const percent=value=>value===null||value===undefined?'—':Number(value).toFixed(1)+'%';
const safe=value=>String(value??'—').replace(/[&<>"']/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
const charts={};
let nodes=[],selectedId=null;
function drawChart(id,series,suffix='',maximum=null){
 if(charts[id])charts[id].destroy();
 const options={chart:{type:'area',height:235,toolbar:{show:false},zoom:{enabled:false},animations:{enabled:false}},series,dataLabels:{enabled:false},stroke:{curve:'smooth',width:2.5},fill:{type:'gradient',gradient:{opacityFrom:.2,opacityTo:.02,stops:[0,100]}},grid:{borderColor:'#edf0f5',strokeDashArray:4},markers:{size:0,hover:{size:4}},xaxis:{type:'datetime',labels:{datetimeUTC:false}},yaxis:{min:0,...(maximum===null?{}:{max:maximum}),labels:{formatter:value=>number(value)+suffix}},tooltip:{x:{format:'dd MMM HH:mm'},y:{formatter:value=>number(value)+suffix}},noData:{text:'—'}};
 charts[id]=new ApexCharts(document.querySelector('#'+id),options);
 charts[id].render();
}
function formatDuration(seconds){
 const total=Math.max(0,Math.floor(Number(seconds)||0));
 const hours=Math.floor(total/3600),minutes=Math.floor((total%3600)/60),remaining=total%60;
 if(hours>0)return hours+'h '+minutes+'m';
 if(minutes>0)return minutes+'m '+remaining+'s';
 return remaining+'s';
}
function renderVersionSummary(summary){
 const status=summary?.status||'unknown';
 const statusLabels={consistent:@json($locale === 'tr' ? 'Sürümler tutarlı' : 'Versions consistent'),mixed:@json($locale === 'tr' ? 'Sürüm farklılığı var' : 'Mixed versions'),incomplete:@json($locale === 'tr' ? 'Eksik sürüm bilgisi' : 'Incomplete version data'),unknown:@json($locale === 'tr' ? 'Sürüm bilgisi yok' : 'No version data')};
 const badge=document.getElementById('version-status');badge.className='version-status '+status;badge.textContent=statusLabels[status]||statusLabels.unknown;
 const chips=(summary?.versions||[]).map(item=>'<div class="version-chip"><strong>'+safe(item.version)+'</strong><span>'+number(item.node_count)+' '+@json($locale === 'tr' ? 'node' : 'nodes')+'</span></div>');
 const unknown=Number(summary?.unknown_count)||0;if(unknown>0)chips.push('<div class="version-chip"><strong>—</strong><span>'+number(unknown)+' '+@json($locale === 'tr' ? 'sürümü bilinmeyen node' : 'nodes with unknown version')+'</span></div>');
 document.getElementById('version-summary').innerHTML=chips.join('')||'<div class="small muted">'+@json($locale === 'tr' ? 'Henüz node sürüm bilgisi bulunamadı.' : 'No node version information available yet.')+'</div>';
}
function renderAvailability(availability){
 const state=availability?.status||'unknown';
 const stateElement=document.getElementById('availability-state');
 const stateLabels={online:@json($locale === 'tr' ? 'Şu an çevrimiçi' : 'Currently online'),offline:@json($locale === 'tr' ? 'Şu an erişilemiyor' : 'Currently unreachable'),unknown:@json($locale === 'tr' ? 'Durum bilinmiyor' : 'Status unknown')};
 stateElement.className='availability-state '+state;
 stateElement.textContent=stateLabels[state]||stateLabels.unknown;
 const periods=availability?.periods||[];
 const total=periods.reduce((sum,period)=>sum+Math.max(0,Number(period.duration_seconds)||0),0)||1;
 document.getElementById('availability-track').innerHTML=periods.map(period=>{
  const width=Math.max(.5,(Number(period.duration_seconds)||0)/total*100);
  const from=new Date(period.from).toLocaleString(document.documentElement.lang,{dateStyle:'short',timeStyle:'short'});
  const to=new Date(period.to).toLocaleString(document.documentElement.lang,{dateStyle:'short',timeStyle:'short'});
  const statusLabel=period.status==='offline'?@json($locale === 'tr' ? 'Erişilemiyor' : 'Unreachable'):@json($locale === 'tr' ? 'Çevrimiçi' : 'Online');
  return '<span class="availability-segment '+(period.status==='offline'?'offline':'online')+'" style="width:'+width+'%" title="'+safe(statusLabel+' · '+from+' – '+to)+'"></span>';
 }).join('')||'<span class="availability-segment online" style="width:100%"></span>';
 const duration=state==='offline'?(@json($locale === 'tr' ? 'Kesinti süresi: ' : 'Offline for ')+formatDuration(availability.offline_duration_seconds)):'';
 document.getElementById('availability-duration').textContent=duration;
 const statusLabels={online:@json($locale === 'tr' ? 'Çevrimiçi' : 'Online'),offline:@json($locale === 'tr' ? 'Erişilemiyor' : 'Unreachable')};
 document.getElementById('availability-periods').innerHTML=periods.slice().reverse().map(period=>{
  const from=new Date(period.from).toLocaleString(document.documentElement.lang,{dateStyle:'medium',timeStyle:'short'});
  const to=new Date(period.to).toLocaleString(document.documentElement.lang,{dateStyle:'medium',timeStyle:'short'});
  return '<div class="availability-period"><span><strong>'+(statusLabels[period.status]||period.status)+'</strong><span class="muted ms-2">'+from+' – '+to+'</span></span><span class="muted">'+formatDuration(period.duration_seconds)+'</span></div>';
 }).join('')||'<div class="small muted">'+@json($locale === 'tr' ? 'Henüz yeterli ölçüm yok.' : 'No availability samples yet.')+'</div>';
}
function renderSelected(){
 const node=nodes.find(item=>item.id===selectedId);
 if(!node)return;
 const latest=node.latest||{},points=node.history||[];
 renderAvailability(node.availability);
 document.getElementById('node-detail-version').textContent=latest.version||'—';
 document.getElementById('cpu-value').textContent=percent(latest.cpu_percent);
 document.getElementById('heap-value').textContent=percent(latest.heap_percent);
 document.getElementById('disk-value').textContent=percent(latest.filesystem_used_percent);
 document.getElementById('documents-value').textContent=number(latest.documents);
 document.getElementById('shards-value').textContent=number(latest.shards);
 const series=key=>[{name:key,data:points.filter(p=>p[key]!==null&&p[key]!==undefined).map(p=>[new Date(p.collected_at).getTime(),Number(p[key])])}];
 drawChart('cpu-chart',series('cpu_percent'),'%',100);
 drawChart('heap-chart',series('heap_percent'),'%',100);
 drawChart('disk-chart',series('filesystem_used_percent'),'%',100);
 drawChart('search-chart',series('search_per_second'),' /s');
 drawChart('indexing-chart',series('indexing_per_second'),' /s');
 drawChart('queue-chart',[
  {name:labels.search+' '+labels.queue,data:points.map(p=>[new Date(p.collected_at).getTime(),Number(p.search_queue||0)])},
  {name:labels.indexing+' '+labels.queue,data:points.map(p=>[new Date(p.collected_at).getTime(),Number(p.write_queue||0)])},
  {name:labels.search+' '+labels.rejected,data:points.map(p=>[new Date(p.collected_at).getTime(),Number(p.search_rejected||0)])},
  {name:labels.indexing+' '+labels.rejected,data:points.map(p=>[new Date(p.collected_at).getTime(),Number(p.write_rejected||0)])}
 ]);
}
function renderTable(){
 const body=document.getElementById('node-table');
 if(!nodes.length){body.innerHTML='<tr><td colspan="11" class="text-center py-4 muted">'+labels.empty+'</td></tr>';return;}
 const sortKey=document.getElementById('node-sort')?.value||'cpu';
 const metricKey={cpu:'cpu_percent',heap:'heap_percent',disk:'filesystem_used_percent'}[sortKey];
 const sortedNodes=[...nodes].sort((a,b)=>{
  const aValue=Number(a.latest?.[metricKey]),bValue=Number(b.latest?.[metricKey]);
  const aValid=Number.isFinite(aValue),bValid=Number.isFinite(bValue);
  if(!aValid&&!bValid)return String(a.name).localeCompare(String(b.name));
  if(!aValid)return 1;if(!bValid)return -1;
  return bValue-aValue;
 });
 body.innerHTML=sortedNodes.map(node=>{
  const p=node.latest||{};
  return '<tr><td><button class="node-pick" data-node-id="'+safe(node.id)+'">'+safe(node.name)+'</button> <button type="button" class="btn btn-sm btn-link p-0 text-secondary" data-copy-value="'+safe(node.name)+'" title="'+copyLabels.copy+'">⧉</button><div class="small muted">'+safe(node.id)+'</div></td><td>'+(node.ip?'<button type="button" class="btn btn-sm btn-link p-0" data-copy-value="'+safe(node.ip)+'" title="'+copyLabels.copy+'">'+safe(node.ip)+'</button>':'—')+'</td><td>'+safe((node.roles||[]).join(', ')||'—')+'</td><td>'+safe(p.version||'—')+'</td><td>'+percent(p.cpu_percent)+'</td><td>'+percent(p.heap_percent)+'</td><td>'+percent(p.filesystem_used_percent)+'</td><td>'+number(p.documents)+'</td><td>'+number(p.shards)+'</td><td>'+number(p.search_total)+'</td><td>'+number(p.indexing_total)+'</td></tr>';
 }).join('');
 body.querySelectorAll('[data-node-id]').forEach(button=>button.addEventListener('click',()=>selectNode(button.dataset.nodeId)));
}
async function loadNodeDetails(id){
 const body=document.getElementById('node-detail-indices');
 try{
  const response=await fetch(detailsEndpoint+'?node_id='+encodeURIComponent(id),{headers:{Accept:'application/json'}});
  if(!response.ok)throw new Error('HTTP '+response.status);
  const payload=await response.json(), node=payload.node||{};
  document.getElementById('node-detail-address').textContent=node.ip||'—';
  document.getElementById('node-detail-roles').textContent=(node.roles||[]).join(', ')||'—';
  document.getElementById('node-detail-cpu').textContent=percent(node.cpu_percent);
  document.getElementById('node-detail-heap').textContent=percent(node.heap_percent);
  document.getElementById('node-detail-disk').textContent=percent(node.disk_percent);
  document.getElementById('node-detail-documents').textContent=number(node.documents);
  document.getElementById('node-detail-shards').textContent=number(node.shards);
  document.getElementById('node-details-meta').textContent=node.name||'—';
  body.innerHTML=(payload.indices||[]).length?(payload.indices||[]).map(index=>'<tr><td>'+safe(index.name)+'</td><td>'+number(index.primary_shards)+'</td><td>'+number(index.replica_shards)+'</td></tr>').join(''):'<tr><td colspan="3" class="muted">—</td></tr>';
 }catch(error){body.innerHTML='<tr><td colspan="3" class="text-danger">'+safe(error.message)+'</td></tr>';}
}
function selectNode(id){selectedId=id;document.getElementById('node-select').value=id;renderSelected();loadNodeDetails(id);}
async function loadNodes(){
 const notice=document.getElementById('notice');
 try{
  const response=await fetch(endpoint,{headers:{Accept:'application/json'}});
  if(!response.ok)throw new Error('HTTP '+response.status);
  const payload=await response.json();nodes=payload.nodes||[];renderVersionSummary(payload.version_summary);
  const select=document.getElementById('node-select');
  if(!nodes.length){select.innerHTML='<option>'+labels.node+'</option>';select.disabled=true;notice.textContent=labels.empty;notice.classList.remove('d-none');renderTable();return;}
  notice.classList.add('d-none');
  if(!nodes.some(node=>node.id===selectedId))selectedId=nodes[0].id;
  select.innerHTML=nodes.map(node=>'<option value="'+safe(node.id)+'">'+safe(node.name)+' ('+safe(node.id)+')</option>').join('');
  select.disabled=false;select.value=selectedId;renderSelected();renderTable();loadNodeDetails(selectedId);
  const latest=payload.meta?.latest_collected_at;
  document.getElementById('updated').textContent=labels.updated+': '+(latest?new Intl.DateTimeFormat(document.documentElement.lang,{dateStyle:'medium',timeStyle:'short'}).format(new Date(latest)):'—')+' · '+(payload.meta?.snapshots||0)+' '+labels.points;
 }catch(error){notice.textContent=labels.error+' '+error.message;notice.classList.remove('d-none');}
}
document.getElementById('node-select').addEventListener('change',event=>selectNode(event.target.value));
document.getElementById('refresh').addEventListener('click',loadNodes);
document.getElementById('node-sort').addEventListener('change',renderTable);
loadNodes();setInterval(loadNodes,30000);
</script>
</body>
</html>
