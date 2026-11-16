@php
$availableLocales = config('elasticsearch-monitoring.dashboard.available_locales', ['tr', 'en']);
$requestedLocale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', config('elasticsearch-monitoring.dashboard.locale', 'en')));
$locale = in_array($requestedLocale, $availableLocales, true) ? $requestedLocale : 'en';
$texts = [
'tr'=>['title'=>'Sorgu Aktivitesi','description'=>'Node bazında arama ve fetch yoğunluğunu, sorgu sürelerini ve search thread pool durumunu izleyin.','back'=>'İzleme paneli','nodes'=>'Node geçmişi','threads'=>'Thread Pool analizi','node'=>'Tüm node’lar','loading'=>'Ölçümler yükleniyor...','refresh'=>'Yenile','empty'=>'Henüz sorgu aktivitesi ölçümü bulunamadı. Önce php artisan elasticsearch:collect komutunu çalıştırın.','error'=>'Sorgu aktivitesi verileri yüklenemedi.','query_rate'=>'Arama / sn','fetch_rate'=>'Fetch / sn','active_queries'=>'Aktif aramalar','active_fetches'=>'Aktif fetch','queue'=>'Search kuyruğu','rejected_rate'=>'Reddedilme / sn','query_time'=>'Ort. query süresi (ms)','fetch_time'=>'Ort. fetch süresi (ms)','roles'=>'Roller','history'=>'Sorgu aktivitesi geçmişi','updated'=>'Son ölçüm','samples'=>'ölçüm','auto'=>'30 saniyede otomatik yenilenir'],
'en'=>['title'=>'Query Activity','description'=>'Monitor search and fetch workload, query latency, and search thread pool pressure by node.','back'=>'Monitoring','nodes'=>'Node history','threads'=>'Thread Pool analysis','node'=>'All nodes','loading'=>'Loading metrics...','refresh'=>'Refresh','empty'=>'No query activity measurements yet. Run php artisan elasticsearch:collect first.','error'=>'Unable to load query activity data.','query_rate'=>'Search / sec','fetch_rate'=>'Fetch / sec','active_queries'=>'Active queries','active_fetches'=>'Active fetches','queue'=>'Search queue','rejected_rate'=>'Rejections / sec','query_time'=>'Avg. query time (ms)','fetch_time'=>'Avg. fetch time (ms)','roles'=>'Roles','history'=>'Query activity history','updated'=>'Latest sample','samples'=>'samples','auto'=>'Auto refresh every 30 seconds'],
'es'=>['title'=>'Actividad de consultas','description'=>'Supervise la carga de búsqueda y fetch, la latencia y la presión del thread pool por nodo.','back'=>'Monitorización','nodes'=>'Historial de nodos','threads'=>'Thread Pool','node'=>'Todos los nodos','loading'=>'Cargando métricas...','refresh'=>'Actualizar','empty'=>'No hay mediciones. Ejecute php artisan elasticsearch:collect.','error'=>'No se pudieron cargar los datos.','query_rate'=>'Búsquedas / s','fetch_rate'=>'Fetch / s','active_queries'=>'Consultas activas','active_fetches'=>'Fetch activos','queue'=>'Cola de búsqueda','rejected_rate'=>'Rechazos / s','query_time'=>'Tiempo medio query (ms)','fetch_time'=>'Tiempo medio fetch (ms)','roles'=>'Roles','history'=>'Historial de actividad','updated'=>'Última medición','samples'=>'muestras','auto'=>'Actualización automática cada 30 segundos'],
'zh_CN'=>['title'=>'查询活动','description'=>'按节点监控搜索和 fetch 负载、查询延迟及搜索线程池压力。','back'=>'监控','nodes'=>'节点历史','threads'=>'线程池分析','node'=>'所有节点','loading'=>'正在加载指标...','refresh'=>'刷新','empty'=>'暂无查询活动测量。请先运行 php artisan elasticsearch:collect。','error'=>'无法加载查询活动数据。','query_rate'=>'搜索 / 秒','fetch_rate'=>'Fetch / 秒','active_queries'=>'活动查询','active_fetches'=>'活动 Fetch','queue'=>'搜索队列','rejected_rate'=>'拒绝 / 秒','query_time'=>'平均查询时间 (ms)','fetch_time'=>'平均 Fetch 时间 (ms)','roles'=>'角色','history'=>'查询活动历史','updated'=>'最新测量','samples'=>'个样本','auto'=>'每 30 秒自动刷新'],
'hi'=>['title'=>'क्वेरी गतिविधि','description'=>'प्रति नोड खोज और fetch लोड, क्वेरी विलंबता तथा search thread pool दबाव देखें।','back'=>'मॉनिटरिंग','nodes'=>'नोड इतिहास','threads'=>'थ्रेड पूल विश्लेषण','node'=>'सभी नोड','loading'=>'मेट्रिक्स लोड हो रहे हैं...','refresh'=>'रीफ़्रेश','empty'=>'कोई माप उपलब्ध नहीं। पहले php artisan elasticsearch:collect चलाएँ।','error'=>'क्वेरी गतिविधि डेटा लोड नहीं हुआ।','query_rate'=>'खोज / सेकंड','fetch_rate'=>'Fetch / सेकंड','active_queries'=>'सक्रिय क्वेरी','active_fetches'=>'सक्रिय Fetch','queue'=>'Search कतार','rejected_rate'=>'अस्वीकृति / सेकंड','query_time'=>'औसत क्वेरी समय (ms)','fetch_time'=>'औसत Fetch समय (ms)','roles'=>'भूमिकाएँ','history'=>'क्वेरी गतिविधि इतिहास','updated'=>'अंतिम माप','samples'=>'नमूने','auto'=>'हर 30 सेकंड में अपडेट'],
'ar'=>['title'=>'نشاط الاستعلامات','description'=>'راقب حمل البحث وعمليات fetch وزمن الاستجابة وضغط مجموعة البحث حسب العقدة.','back'=>'المراقبة','nodes'=>'سجل العقد','threads'=>'تحليل مجموعات الترابط','node'=>'كل العقد','loading'=>'جار تحميل المقاييس...','refresh'=>'تحديث','empty'=>'لا توجد قياسات. شغّل php artisan elasticsearch:collect أولاً.','error'=>'تعذر تحميل بيانات نشاط الاستعلامات.','query_rate'=>'البحث / ثانية','fetch_rate'=>'Fetch / ثانية','active_queries'=>'الاستعلامات النشطة','active_fetches'=>'عمليات Fetch النشطة','queue'=>'قائمة انتظار البحث','rejected_rate'=>'الرفض / ثانية','query_time'=>'متوسط زمن الاستعلام (ms)','fetch_time'=>'متوسط زمن Fetch (ms)','roles'=>'الأدوار','history'=>'سجل نشاط الاستعلامات','updated'=>'آخر قياس','samples'=>'عينات','auto'=>'تحديث تلقائي كل 30 ثانية'],
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
body{background:#f4f6fb;color:#182238;font-family:Inter,system-ui,-apple-system,sans-serif}.page{max-width:1500px}.hero{background:linear-gradient(115deg,#171d38,#303b70 62%,#625bdb);color:#fff;border-radius:20px;padding:24px 28px}.hero p{color:#d5d9f2}.panel{background:#fff;border:1px solid #e8ecf3;border-radius:16px;box-shadow:0 5px 22px rgba(26,39,71,.045)}.metric{padding:18px;height:100%}.metric small{color:#6b7487}.metric strong{display:block;font-size:1.7rem;margin-top:5px}.chart{padding:18px 20px;min-height:330px}.chart h2{font-size:1rem;font-weight:700}.chart p{font-size:.8rem;color:#7b8496;margin-bottom:8px}.table thead th{font-size:.76rem;color:#68748a;white-space:nowrap}.table tbody td{font-size:.86rem;white-space:nowrap}.form-select{min-width:180px}
</style>
</head>
<body>
<main class="container-fluid page py-4 px-3 px-lg-4">
<div class="hero mb-4"><div class="d-flex justify-content-between align-items-start flex-wrap gap-3"><div>
<div class="d-flex flex-wrap gap-2 mb-3">
<a href="{{ route('elasticsearch-monitoring.index',['locale'=>$locale]) }}" class="btn btn-sm btn-light">← {{ $texts['back'] }}</a>
<a href="{{ route('elasticsearch-monitoring.nodes',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-light">{{ $texts['nodes'] }}</a>
<a href="{{ route('elasticsearch-monitoring.thread-pools',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-light">{{ $texts['threads'] }}</a>
</div><h1 class="h3 fw-bold mb-1">{{ $texts['title'] }}</h1><p class="mb-0">{{ $texts['description'] }}</p></div>
<div class="d-flex flex-wrap gap-2"><select id="node-select" class="form-select" aria-label="{{ $texts['node'] }}" disabled><option>{{ $texts['loading'] }}</option></select><button id="refresh" class="btn btn-light">↻ {{ $texts['refresh'] }}</button></div></div></div>
<div id="notice" class="alert alert-info d-none"></div>
<div class="row g-3 mb-3">
<div class="col-6 col-xl"><div class="panel metric"><small>{{ $texts['query_rate'] }}</small><strong id="query-rate">—</strong></div></div>
<div class="col-6 col-xl"><div class="panel metric"><small>{{ $texts['fetch_rate'] }}</small><strong id="fetch-rate">—</strong></div></div>
<div class="col-6 col-xl"><div class="panel metric"><small>{{ $texts['active_queries'] }}</small><strong id="active-queries">—</strong></div></div>
<div class="col-6 col-xl"><div class="panel metric"><small>{{ $texts['queue'] }}</small><strong id="search-queue">—</strong></div></div>
<div class="col-6 col-xl"><div class="panel metric"><small>{{ $texts['rejected_rate'] }}</small><strong id="rejected-rate">—</strong></div></div>
</div>
<div class="panel chart mb-4"><div class="d-flex justify-content-between align-items-center flex-wrap gap-2"><div><h2>{{ $texts['history'] }}</h2><p>{{ $texts['query_rate'] }}, {{ $texts['fetch_rate'] }}, {{ $texts['rejected_rate'] }}</p></div><div class="d-flex align-items-center gap-2"><button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportMonitoringChartPng(chart, labels.history, 'query-activity-history')">PNG</button><span id="updated" class="small text-secondary"></span></div></div><div id="history-chart"></div><p class="mb-0 mt-2">{{ $texts['auto'] }}</p></div>
<section class="panel p-3"><div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"><h2 class="h6 fw-bold mb-0">{{ $texts['node'] }}</h2><span id="sample-count" class="small text-secondary"></span></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>{{ $texts['node'] }}</th><th>{{ $texts['roles'] }}</th><th>{{ $texts['query_rate'] }}</th><th>{{ $texts['query_time'] }}</th><th>{{ $texts['fetch_rate'] }}</th><th>{{ $texts['fetch_time'] }}</th><th>{{ $texts['active_queries'] }}</th><th>{{ $texts['active_fetches'] }}</th><th>{{ $texts['queue'] }}</th><th>{{ $texts['rejected_rate'] }}</th></tr></thead><tbody id="node-table"><tr><td colspan="10" class="text-center py-4 text-secondary">{{ $texts['loading'] }}</td></tr></tbody></table></div></section>
</main>
<script>
const endpoint=@json(route('elasticsearch-monitoring.query-activity.data',['locale'=>$locale]));
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
let nodes=[],selectedId='all',chart=null,summary={};
function render(){
 const selected=nodes.find(n=>n.id===selectedId);
 const totals=selected?selected.latest:summary;
 const set=(id,value)=>document.getElementById(id).textContent=fmt(value);
 set('query-rate',totals.query_per_second);set('fetch-rate',totals.fetch_per_second);
 set('active-queries',selected?totals.query_current:totals.active_queries);
 set('search-queue',totals.search_queue);set('rejected-rate',totals.rejected_per_second);
 const points=selected?(selected.history||[]):[];
 const aggregate={query:{},fetch:{},rejected:{}};
 if(!selected){nodes.forEach(node=>(node.history||[]).forEach(point=>{
  const time=new Date(point.collected_at).getTime();
  [['query','query_per_second'],['fetch','fetch_per_second'],['rejected','rejected_per_second']].forEach(([key,field])=>{
   if(point[field]!==null&&point[field]!==undefined)aggregate[key][time]=(aggregate[key][time]||0)+Number(point[field]);
  });
 }));}
 const data=key=>selected?points.filter(p=>p[key+'_per_second']!==null).map(p=>[new Date(p.collected_at).getTime(),Number(p[key+'_per_second'])]):Object.entries(aggregate[key]).map(([time,value])=>[Number(time),value]);
 if(chart)chart.destroy();
 chart=new ApexCharts(document.querySelector('#history-chart'),{chart:{type:'line',height:300,toolbar:{show:false},animations:{enabled:false}},series:[
 {name:labels.query_rate,data:data('query')},{name:labels.fetch_rate,data:data('fetch')},{name:labels.rejected_rate,data:data('rejected')}
 ],stroke:{curve:'smooth',width:2},dataLabels:{enabled:false},markers:{size:0},grid:{borderColor:'#edf0f5',strokeDashArray:4},xaxis:{type:'datetime',labels:{datetimeUTC:false}},yaxis:{min:0,labels:{formatter:fmt}},tooltip:{x:{format:'dd MMM HH:mm'}},noData:{text:'—'}});
 chart.render();
}
function renderTable(){
 document.getElementById('node-table').innerHTML=nodes.map(node=>{const p=node.latest||{};return '<tr><td><strong>'+esc(node.name)+'</strong><div class="small text-secondary">'+esc(node.id)+'</div></td><td>'+esc((node.roles||[]).join(', ')||'—')+'</td><td>'+fmt(p.query_per_second)+'</td><td>'+fmt(p.query_avg_time_ms)+'</td><td>'+fmt(p.fetch_per_second)+'</td><td>'+fmt(p.fetch_avg_time_ms)+'</td><td>'+fmt(p.query_current)+'</td><td>'+fmt(p.fetch_current)+'</td><td>'+fmt(p.search_queue)+'</td><td>'+fmt(p.rejected_per_second)+'</td></tr>'}).join('')||'<tr><td colspan="10" class="text-center py-4 text-secondary">'+labels.empty+'</td></tr>';
}
async function load(){
 const notice=document.getElementById('notice');
 try{
  const response=await fetch(endpoint,{headers:{Accept:'application/json'}});if(!response.ok)throw new Error('HTTP '+response.status);
  const payload=await response.json();nodes=payload.nodes||[];summary=payload.summary||{};
  const select=document.getElementById('node-select');
  select.innerHTML='<option value="all">'+labels.node+'</option>'+nodes.map(n=>'<option value="'+esc(n.id)+'">'+esc(n.name)+'</option>').join('');
  select.disabled=!nodes.length;select.value=selectedId;
  if(!nodes.length){notice.textContent=labels.empty;notice.classList.remove('d-none')}else{notice.classList.add('d-none')}
  renderTable();render();
  document.getElementById('updated').textContent=labels.updated+': '+(payload.meta.latest_collected_at||'—');
  document.getElementById('sample-count').textContent=payload.meta.snapshots+' '+labels.samples;
 }catch(error){notice.textContent=labels.error+' '+error.message;notice.classList.remove('d-none')}
}
document.getElementById('node-select').addEventListener('change',event=>{selectedId=event.target.value;render()});
document.getElementById('refresh').addEventListener('click',load);
load();setInterval(load,30000);
</script>
</body>
</html>
