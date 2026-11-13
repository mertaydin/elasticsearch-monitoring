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
<div class="row g-3 mb-4">
<div class="col-12 col-lg-6"><section class="chart-panel"><h2>{{ $text['cpu'] }}</h2><p>{{ $text['percent'] }}</p><div id="cpu-chart"></div></section></div>
<div class="col-12 col-lg-6"><section class="chart-panel"><h2>{{ $text['heap'] }}</h2><p>{{ $text['percent'] }}</p><div id="heap-chart"></div></section></div>
<div class="col-12 col-lg-6"><section class="chart-panel"><h2>{{ $text['disk'] }}</h2><p>{{ $text['percent'] }}</p><div id="disk-chart"></div></section></div>
<div class="col-12 col-lg-6"><section class="chart-panel"><h2>{{ $text['search'] }}</h2><p>{{ $text['per_second'] }}</p><div id="search-chart"></div></section></div>
<div class="col-12 col-lg-6"><section class="chart-panel"><h2>{{ $text['indexing'] }}</h2><p>{{ $text['per_second'] }}</p><div id="indexing-chart"></div></section></div>
<div class="col-12 col-lg-6"><section class="chart-panel"><h2>{{ $text['queue'] }} / {{ $text['rejected'] }}</h2><p>{{ $text['search'] }} · {{ $text['indexing'] }}</p><div id="queue-chart"></div></section></div>
</div>
<section class="panel p-3">
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"><h2 class="h6 fw-bold mb-0">{{ $text['comparison'] }}</h2><span class="small muted" id="updated"></span></div>
<div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>{{ $text['name'] }}</th><th>{{ $text['roles'] }}</th><th>CPU</th><th>JVM</th><th>{{ $text['disk'] }}</th><th>{{ $text['documents'] }}</th><th>{{ $text['shards'] }}</th><th>{{ $text['search_total'] }}</th><th>{{ $text['indexing_total'] }}</th></tr></thead>
<tbody id="node-table"><tr><td colspan="9" class="text-center py-4 muted">{{ $text['loading'] }}</td></tr></tbody>
</table></div>
</section>
</main>
<script>
const endpoint=@json(route('elasticsearch-monitoring.nodes.data',['locale'=>$locale]));
const labels=@json($text);
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
function renderSelected(){
 const node=nodes.find(item=>item.id===selectedId);
 if(!node)return;
 const latest=node.latest||{},points=node.history||[];
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
 if(!nodes.length){body.innerHTML='<tr><td colspan="9" class="text-center py-4 muted">'+labels.empty+'</td></tr>';return;}
 body.innerHTML=nodes.map(node=>{
  const p=node.latest||{};
  return '<tr><td><button class="node-pick" data-node-id="'+safe(node.id)+'">'+safe(node.name)+'</button><div class="small muted">'+safe(node.id)+'</div></td><td>'+safe((node.roles||[]).join(', ')||'—')+'</td><td>'+percent(p.cpu_percent)+'</td><td>'+percent(p.heap_percent)+'</td><td>'+percent(p.filesystem_used_percent)+'</td><td>'+number(p.documents)+'</td><td>'+number(p.shards)+'</td><td>'+number(p.search_total)+'</td><td>'+number(p.indexing_total)+'</td></tr>';
 }).join('');
 body.querySelectorAll('[data-node-id]').forEach(button=>button.addEventListener('click',()=>selectNode(button.dataset.nodeId)));
}
function selectNode(id){selectedId=id;document.getElementById('node-select').value=id;renderSelected();}
async function loadNodes(){
 const notice=document.getElementById('notice');
 try{
  const response=await fetch(endpoint,{headers:{Accept:'application/json'}});
  if(!response.ok)throw new Error('HTTP '+response.status);
  const payload=await response.json();nodes=payload.nodes||[];
  const select=document.getElementById('node-select');
  if(!nodes.length){select.innerHTML='<option>'+labels.node+'</option>';select.disabled=true;notice.textContent=labels.empty;notice.classList.remove('d-none');renderTable();return;}
  notice.classList.add('d-none');
  if(!nodes.some(node=>node.id===selectedId))selectedId=nodes[0].id;
  select.innerHTML=nodes.map(node=>'<option value="'+safe(node.id)+'">'+safe(node.name)+' ('+safe(node.id)+')</option>').join('');
  select.disabled=false;select.value=selectedId;renderSelected();renderTable();
  const latest=payload.meta?.latest_collected_at;
  document.getElementById('updated').textContent=labels.updated+': '+(latest?new Intl.DateTimeFormat(document.documentElement.lang,{dateStyle:'medium',timeStyle:'short'}).format(new Date(latest)):'—')+' · '+(payload.meta?.snapshots||0)+' '+labels.points;
 }catch(error){notice.textContent=labels.error+' '+error.message;notice.classList.remove('d-none');}
}
document.getElementById('node-select').addEventListener('change',event=>selectNode(event.target.value));
document.getElementById('refresh').addEventListener('click',loadNodes);
loadNodes();setInterval(loadNodes,30000);
</script>
</body>
</html>
