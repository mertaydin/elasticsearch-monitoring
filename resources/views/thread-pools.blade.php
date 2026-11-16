@php
    $availableLocales = config('elasticsearch-monitoring.dashboard.available_locales', ['tr', 'en']);
    $requestedLocale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', config('elasticsearch-monitoring.dashboard.locale', 'en')));
    $locale = in_array($requestedLocale, $availableLocales, true) ? $requestedLocale : 'en';
    $texts = [
        'tr' => ['title'=>'Thread Pool Analizi','description'=>'Node ve thread pool bazında kuyruk, aktif iş ve reddedilen işlem eğilimlerini izleyin.','back'=>'İzleme paneli','nodes'=>'Node geçmişi','capacity'=>'Kapasite analizi','node'=>'Node','pool'=>'Thread pool','loading'=>'Ölçümler yükleniyor...','refresh'=>'Yenile','empty'=>'Henüz thread pool ölçümü bulunamadı. Önce php artisan elasticsearch:collect komutunu çalıştırın.','error'=>'Thread pool verileri yüklenemedi.','threads'=>'Thread sayısı','active'=>'Aktif işler','queue'=>'Kuyruk','rejected'=>'Reddedilen','completed'=>'Tamamlanan','rejected_rate'=>'Rejection / sn','completed_rate'=>'Tamamlanan / sn','history'=>'Geçmiş kuyruk ve işlem eğilimleri','status'=>'Durum','healthy'=>'Normal','busy'=>'Kuyruk var','saturated'=>'Darboğaz riski','largest'=>'En yüksek thread','updated'=>'Son ölçüm','roles'=>'Roller','samples'=>'ölçüm'],
        'en' => ['title'=>'Thread Pool Analyzer','description'=>'Monitor queue depth, active work, and rejected operations by node and thread pool.','back'=>'Monitoring','nodes'=>'Node history','capacity'=>'Capacity analysis','node'=>'Node','pool'=>'Thread pool','loading'=>'Loading metrics...','refresh'=>'Refresh','empty'=>'No thread pool measurements found yet. Run php artisan elasticsearch:collect first.','error'=>'Unable to load thread pool data.','threads'=>'Threads','active'=>'Active','queue'=>'Queue','rejected'=>'Rejected','completed'=>'Completed','rejected_rate'=>'Rejections / sec','completed_rate'=>'Completed / sec','history'=>'Historical queue and operation trends','status'=>'Status','healthy'=>'Healthy','busy'=>'Queued','saturated'=>'Saturation risk','largest'=>'Largest threads','updated'=>'Latest sample','roles'=>'Roles','samples'=>'samples'],
        'es' => ['title'=>'Analizador de Thread Pool','description'=>'Supervise colas, tareas activas y operaciones rechazadas por nodo y grupo.','back'=>'Panel','nodes'=>'Historial de nodos','capacity'=>'Capacidad','node'=>'Nodo','pool'=>'Thread pool','loading'=>'Cargando métricas...','refresh'=>'Actualizar','empty'=>'No hay mediciones. Ejecute php artisan elasticsearch:collect.','error'=>'No se pudieron cargar los datos.','threads'=>'Hilos','active'=>'Activos','queue'=>'Cola','rejected'=>'Rechazados','completed'=>'Completados','rejected_rate'=>'Rechazos / s','completed_rate'=>'Completados / s','history'=>'Tendencias históricas','status'=>'Estado','healthy'=>'Correcto','busy'=>'En cola','saturated'=>'Riesgo de saturación','largest'=>'Máximo de hilos','updated'=>'Última medición','roles'=>'Roles','samples'=>'muestras'],
        'zh_CN' => ['title'=>'线程池分析','description'=>'按节点和线程池监控队列、活动任务及拒绝操作。','back'=>'监控','nodes'=>'节点历史','capacity'=>'容量分析','node'=>'节点','pool'=>'线程池','loading'=>'正在加载指标...','refresh'=>'刷新','empty'=>'暂无线程池测量。请先运行 php artisan elasticsearch:collect。','error'=>'无法加载线程池数据。','threads'=>'线程数','active'=>'活动任务','queue'=>'队列','rejected'=>'拒绝数','completed'=>'已完成','rejected_rate'=>'拒绝/秒','completed_rate'=>'完成/秒','history'=>'历史队列和操作趋势','status'=>'状态','healthy'=>'正常','busy'=>'有排队','saturated'=>'饱和风险','largest'=>'最大线程数','updated'=>'最新测量','roles'=>'角色','samples'=>'个样本'],
        'hi' => ['title'=>'थ्रेड पूल विश्लेषक','description'=>'नोड और थ्रेड पूल के अनुसार कतार, सक्रिय कार्य और अस्वीकृत ऑपरेशन देखें।','back'=>'मॉनिटरिंग','nodes'=>'नोड इतिहास','capacity'=>'क्षमता विश्लेषण','node'=>'नोड','pool'=>'थ्रेड पूल','loading'=>'मेट्रिक्स लोड हो रहे हैं...','refresh'=>'रीफ़्रेश','empty'=>'कोई माप उपलब्ध नहीं। पहले php artisan elasticsearch:collect चलाएँ।','error'=>'थ्रेड पूल डेटा लोड नहीं हुआ।','threads'=>'थ्रेड','active'=>'सक्रिय','queue'=>'कतार','rejected'=>'अस्वीकृत','completed'=>'पूर्ण','rejected_rate'=>'अस्वीकृति / सेकंड','completed_rate'=>'पूर्ण / सेकंड','history'=>'ऐतिहासिक रुझान','status'=>'स्थिति','healthy'=>'सामान्य','busy'=>'कतार में','saturated'=>'संतृप्ति जोखिम','largest'=>'अधिकतम थ्रेड','updated'=>'अंतिम माप','roles'=>'भूमिकाएँ','samples'=>'नमूने'],
        'ar' => ['title'=>'محلل مجموعات مؤشرات الترابط','description'=>'راقب قوائم الانتظار والمهام النشطة والعمليات المرفوضة حسب العقدة والمجموعة.','back'=>'المراقبة','nodes'=>'سجل العقد','capacity'=>'تحليل السعة','node'=>'العقدة','pool'=>'مجموعة الترابط','loading'=>'جار تحميل المقاييس...','refresh'=>'تحديث','empty'=>'لا توجد قياسات. شغّل php artisan elasticsearch:collect أولاً.','error'=>'تعذر تحميل بيانات مجموعات الترابط.','threads'=>'الخيوط','active'=>'النشطة','queue'=>'قائمة الانتظار','rejected'=>'المرفوضة','completed'=>'المكتملة','rejected_rate'=>'الرفض / ثانية','completed_rate'=>'المكتملة / ثانية','history'=>'اتجاهات السجل','status'=>'الحالة','healthy'=>'سليم','busy'=>'في الانتظار','saturated'=>'خطر التشبع','largest'=>'أكبر عدد خيوط','updated'=>'آخر قياس','roles'=>'الأدوار','samples'=>'عينات'],
    ][$locale];
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $texts['title'] }}</title>
@if ($locale === 'ar')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
@else
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endif
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
body{background:#f4f6fb;color:#182238;font-family:Inter,system-ui,-apple-system,sans-serif}.page{max-width:1500px}.hero{background:linear-gradient(115deg,#171d38,#303b70 62%,#625bdb);color:#fff;border-radius:20px;padding:24px 28px}.hero p{color:#d5d9f2}.panel{background:#fff;border:1px solid #e8ecf3;border-radius:16px;box-shadow:0 5px 22px rgba(26,39,71,.045)}.metric{padding:18px;height:100%}.metric small{color:#6b7487}.metric strong{display:block;font-size:1.7rem;margin-top:5px}.chart{padding:18px 20px;min-height:330px}.chart h2{font-size:1rem;font-weight:700}.chart p{font-size:.8rem;color:#7b8496;margin-bottom:8px}.form-select{min-width:180px}.table thead th{font-size:.76rem;color:#68748a;white-space:nowrap}.table tbody td{font-size:.86rem;white-space:nowrap}.status{font-size:.75rem;padding:.35rem .6rem;border-radius:20px;font-weight:700}.status-healthy{background:#e8f7ef;color:#18794e}.status-busy{background:#fff4df;color:#986400}.status-saturated{background:#fde8e8;color:#b42318}
</style>
</head>
<body>
<main class="container-fluid page py-4 px-3 px-lg-4">
<div class="hero mb-4"><div class="d-flex justify-content-between align-items-start flex-wrap gap-3"><div>
<div class="d-flex flex-wrap gap-2 mb-3">
<a href="{{ route('elasticsearch-monitoring.index',['locale'=>$locale]) }}" class="btn btn-sm btn-light">← {{ $texts['back'] }}</a>
<a href="{{ route('elasticsearch-monitoring.nodes',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-light">{{ $texts['nodes'] }}</a>
<a href="{{ route('elasticsearch-monitoring.capacity',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-light">{{ $texts['capacity'] }}</a>
</div><h1 class="h3 fw-bold mb-1">{{ $texts['title'] }}</h1><p class="mb-0">{{ $texts['description'] }}</p></div>
<div class="d-flex flex-wrap gap-2"><select id="node-select" class="form-select" aria-label="{{ $texts['node'] }}" disabled><option>{{ $texts['loading'] }}</option></select><select id="pool-select" class="form-select" aria-label="{{ $texts['pool'] }}" disabled><option>—</option></select><button id="refresh" class="btn btn-light">↻ {{ $texts['refresh'] }}</button></div></div></div>
<div id="notice" class="alert alert-info d-none"></div>
<div class="row g-3 mb-3">
<div class="col-6 col-xl"><div class="panel metric"><small>{{ $texts['threads'] }}</small><strong id="threads">—</strong></div></div>
<div class="col-6 col-xl"><div class="panel metric"><small>{{ $texts['active'] }}</small><strong id="active">—</strong></div></div>
<div class="col-6 col-xl"><div class="panel metric"><small>{{ $texts['queue'] }}</small><strong id="queue">—</strong></div></div>
<div class="col-6 col-xl"><div class="panel metric"><small>{{ $texts['rejected'] }}</small><strong id="rejected">—</strong></div></div>
<div class="col-6 col-xl"><div class="panel metric"><small>{{ $texts['completed'] }}</small><strong id="completed">—</strong></div></div>
</div>
<div class="panel chart mb-4"><div class="d-flex justify-content-between align-items-center flex-wrap gap-2"><div><h2>{{ $texts['history'] }}</h2><p>{{ $texts['queue'] }}, {{ $texts['active'] }}, {{ $texts['rejected_rate'] }} · {{ $texts['completed_rate'] }}</p></div><div class="d-flex align-items-center gap-2"><button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportMonitoringChartPng(chart, labels.history, 'thread-pool-history')">PNG</button><span id="status" class="status status-healthy">—</span></div></div><div id="history-chart"></div></div>
<section class="panel p-3"><div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"><h2 class="h6 fw-bold mb-0">{{ $texts['pool'] }}</h2><span id="updated" class="small text-secondary"></span></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>{{ $texts['node'] }}</th><th>{{ $texts['pool'] }}</th><th>{{ $texts['threads'] }}</th><th>{{ $texts['active'] }}</th><th>{{ $texts['queue'] }}</th><th>{{ $texts['rejected'] }}</th><th>{{ $texts['rejected_rate'] }}</th><th>{{ $texts['completed'] }}</th><th>{{ $texts['status'] }}</th></tr></thead><tbody id="pool-table"><tr><td colspan="9" class="text-center py-4 text-secondary">{{ $texts['loading'] }}</td></tr></tbody></table></div></section>
</main>
<script>
const endpoint=@json(route('elasticsearch-monitoring.thread-pools.data',['locale'=>$locale]));
const labels=@json($texts);
const chartExportLabels = {error: @json(trans('elasticsearch-monitoring::messages.chart_export_failed', [], $locale))};
async function exportMonitoringChartPng(chart, title, filename) {
    try {
        if (!chart) throw new Error(chartExportLabels.error);
        const result = await chart.dataURI({scale: 2});
        const image = new Image();
        await new Promise((resolve, reject) => {
            image.onload = resolve;
            image.onerror = () => reject(new Error(chartExportLabels.error));
            image.src = result.imgURI;
        });
        const canvas = document.createElement('canvas');
        canvas.width = 1400;
        canvas.height = 900;
        const context = canvas.getContext('2d');
        context.fillStyle = '#ffffff';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.fillStyle = '#182238';
        context.font = '700 34px Arial, sans-serif';
        context.fillText(String(title), 42, 58, 1316);
        context.fillStyle = '#77839a';
        context.font = '18px Arial, sans-serif';
        context.fillText(new Date().toLocaleString(), 42, 91);
        const scale = Math.min(1316 / image.width, 760 / image.height);
        const width = image.width * scale, height = image.height * scale;
        context.drawImage(image, (1400 - width) / 2, 120 + (760 - height) / 2, width, height);
        const blob = await new Promise((resolve, reject) => canvas.toBlob(value => value ? resolve(value) : reject(new Error(chartExportLabels.error)), 'image/png'));
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename + '-' + new Date().toISOString().slice(0, 10) + '.png';
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
    } catch (error) {
        window.alert(chartExportLabels.error);
    }
}
const fmt=value=>value===null||value===undefined?'—':Number(value).toLocaleString(document.documentElement.lang,{maximumFractionDigits:2});
const esc=value=>String(value??'—').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
let nodes=[],selectedNode=null,selectedPool=null,chart=null;
const statusText={healthy:labels.healthy,busy:labels.busy,saturated:labels.saturated};
function allPools(){return nodes.flatMap(node=>(node.pools||[]).map(pool=>({...pool,node_id:node.id,node_name:node.name})))}
function selected(){const node=nodes.find(n=>n.id===selectedNode);return node?.pools?.find(p=>p.name===selectedPool)||null}
function render(){
 const pool=selected();if(!pool)return;
 const latest=pool.latest||{},history=pool.history||[];
 ['threads','active','queue','rejected','completed'].forEach(key=>document.getElementById(key).textContent=fmt(latest[key]));
 const status=document.getElementById('status');status.textContent=statusText[pool.status]||'—';status.className='status status-'+(pool.status||'healthy');
 if(chart)chart.destroy();
 chart=new ApexCharts(document.querySelector('#history-chart'),{chart:{type:'line',height:300,toolbar:{show:false},animations:{enabled:false}},series:[
 {name:labels.queue,data:history.map(p=>[new Date(p.collected_at).getTime(),p.queue])},
 {name:labels.active,data:history.map(p=>[new Date(p.collected_at).getTime(),p.active])},
 {name:labels.rejected_rate,data:history.filter(p=>p.rejected_per_second!==null).map(p=>[new Date(p.collected_at).getTime(),p.rejected_per_second])},
 {name:labels.completed_rate,data:history.filter(p=>p.completed_per_second!==null).map(p=>[new Date(p.collected_at).getTime(),p.completed_per_second])}
 ],stroke:{curve:'smooth',width:2},dataLabels:{enabled:false},markers:{size:0},grid:{borderColor:'#edf0f5',strokeDashArray:4},xaxis:{type:'datetime',labels:{datetimeUTC:false}},yaxis:{min:0,labels:{formatter:fmt}},tooltip:{x:{format:'dd MMM HH:mm'}},noData:{text:'—'}});
 chart.render();
}
function renderTable(){
 const rows=allPools();document.getElementById('pool-table').innerHTML=rows.map(pool=>{
 const p=pool.latest||{};return '<tr><td>'+esc(pool.node_name)+'</td><td><button class="btn btn-link btn-sm p-0 text-decoration-none" data-node="'+esc(pool.node_id)+'" data-pool="'+esc(pool.name)+'">'+esc(pool.name)+'</button></td><td>'+fmt(p.threads)+'</td><td>'+fmt(p.active)+'</td><td>'+fmt(p.queue)+'</td><td>'+fmt(p.rejected)+'</td><td>'+fmt(p.rejected_per_second)+'</td><td>'+fmt(p.completed)+'</td><td><span class="status status-'+pool.status+'">'+esc(statusText[pool.status])+'</span></td></tr>';
 }).join('')||'<tr><td colspan="9" class="text-center py-4 text-secondary">'+labels.empty+'</td></tr>';
 document.querySelectorAll('[data-pool]').forEach(button=>button.addEventListener('click',()=>{selectedNode=button.dataset.node;selectedPool=button.dataset.pool;syncSelectors();render()}));
}
function syncSelectors(){
 const node=document.getElementById('node-select'),pool=document.getElementById('pool-select');
 node.innerHTML=nodes.map(n=>'<option value="'+esc(n.id)+'">'+esc(n.name)+'</option>').join('');node.disabled=!nodes.length;node.value=selectedNode||'';
 const current=nodes.find(n=>n.id===selectedNode);const pools=current?.pools||[];
 pool.innerHTML=pools.map(p=>'<option value="'+esc(p.name)+'">'+esc(p.name)+'</option>').join('');pool.disabled=!pools.length;pool.value=selectedPool||'';
}
async function load(){
 const notice=document.getElementById('notice');
 try{
  const response=await fetch(endpoint,{headers:{Accept:'application/json'}});if(!response.ok)throw new Error('HTTP '+response.status);
  const payload=await response.json();nodes=payload.nodes||[];
  if(!nodes.length){notice.textContent=labels.empty;notice.classList.remove('d-none');renderTable();return}
  if(!nodes.some(n=>n.id===selectedNode))selectedNode=nodes[0].id;
  const current=nodes.find(n=>n.id===selectedNode);if(!current.pools.some(p=>p.name===selectedPool))selectedPool=current.pools[0]?.name||null;
  syncSelectors();render();renderTable();notice.classList.add('d-none');
  document.getElementById('updated').textContent=labels.updated+': '+(payload.meta.latest_collected_at||'—')+' · '+payload.meta.snapshots+' '+labels.samples;
 }catch(error){notice.textContent=labels.error+' '+error.message;notice.classList.remove('d-none')}
}
document.getElementById('node-select').addEventListener('change',event=>{selectedNode=event.target.value;selectedPool=null;syncSelectors();render()});
document.getElementById('pool-select').addEventListener('change',event=>{selectedPool=event.target.value;render()});
document.getElementById('refresh').addEventListener('click',load);
load();setInterval(load,30000);
</script>
    @include('elasticsearch-monitoring::partials.theme')
</body>
</html>
