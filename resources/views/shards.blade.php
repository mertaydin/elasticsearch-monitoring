@php
    $availableLocales = config('elasticsearch-monitoring.dashboard.available_locales', ['tr', 'en']);
    $requestedLocale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', config('elasticsearch-monitoring.dashboard.locale', 'en')));
    $locale = in_array($requestedLocale, $availableLocales, true) ? $requestedLocale : 'en';
    $text = [
        'tr' => ['title'=>'Shard Dağılımı','description'=>'Shard durumlarını, node dağılımını ve atanmamış shard bilgilerini inceleyin.','back'=>'İzleme paneli','indices'=>'İndeksler','nodes'=>'Node geçmişi','streams'=>'Data Streams','alerts'=>'Alarm geçmişi','refresh'=>'Yenile','search'=>'İndeks, node veya neden ara...','all'=>'Tüm durumlar','started'=>'Başlatıldı','initializing'=>'Başlatılıyor','relocating'=>'Taşınıyor','unassigned'=>'Atanmamış','total'=>'Toplam shard','node_count'=>'Node sayısı','distribution'=>'Node bazında dağılım','table'=>'Shard listesi','index'=>'İndeks','shard'=>'Shard','type'=>'Tür','state'=>'Durum','documents'=>'Belge','storage'=>'Boyut','node'=>'Node','reason'=>'Atanmama nedeni','primary'=>'Primary','replica'=>'Replica','loading'=>'Shard verileri yükleniyor...','empty'=>'Shard bulunamadı.','error'=>'Shard bilgileri alınamadı. Elasticsearch kullanıcı izinlerini kontrol edin.','updated'=>'Son güncelleme','all_nodes'=>'Tüm node’lar','filter_node'=>'Node seçin'],
        'en' => ['title'=>'Shard Allocation','description'=>'Inspect shard states, node distribution, and unassigned shard information.','back'=>'Monitoring','indices'=>'Indices','nodes'=>'Node history','streams'=>'Data Streams','alerts'=>'Alert history','refresh'=>'Refresh','search'=>'Search index, node, or reason...','all'=>'All states','started'=>'Started','initializing'=>'Initializing','relocating'=>'Relocating','unassigned'=>'Unassigned','total'=>'Total shards','node_count'=>'Nodes','distribution'=>'Distribution by node','table'=>'Shard list','index'=>'Index','shard'=>'Shard','type'=>'Type','state'=>'State','documents'=>'Documents','storage'=>'Size','node'=>'Node','reason'=>'Unassigned reason','primary'=>'Primary','replica'=>'Replica','loading'=>'Loading shard data...','empty'=>'No shards found.','error'=>'Unable to load shard information. Check Elasticsearch privileges.','updated'=>'Last updated','all_nodes'=>'All nodes','filter_node'=>'Select node'],
        'es' => ['title'=>'Asignación de shards','description'=>'Consulte estados, distribución por nodo y shards sin asignar.','back'=>'Panel','indices'=>'Índices','nodes'=>'Historial de nodos','streams'=>'Data Streams','alerts'=>'Alertas','refresh'=>'Actualizar','search'=>'Buscar índice, nodo o motivo...','all'=>'Todos los estados','started'=>'Iniciado','initializing'=>'Inicializando','relocating'=>'Reubicando','unassigned'=>'Sin asignar','total'=>'Total de shards','node_count'=>'Nodos','distribution'=>'Distribución por nodo','table'=>'Lista de shards','index'=>'Índice','shard'=>'Shard','type'=>'Tipo','state'=>'Estado','documents'=>'Documentos','storage'=>'Tamaño','node'=>'Nodo','reason'=>'Motivo','primary'=>'Primario','replica'=>'Réplica','loading'=>'Cargando shards...','empty'=>'No se encontraron shards.','error'=>'No se pudo cargar la información.','updated'=>'Actualizado','all_nodes'=>'Todos los nodos','filter_node'=>'Seleccionar nodo'],
        'zh_CN' => ['title'=>'分片分配','description'=>'查看分片状态、节点分布和未分配分片信息。','back'=>'监控','indices'=>'索引','nodes'=>'节点历史','streams'=>'数据流','alerts'=>'告警历史','refresh'=>'刷新','search'=>'搜索索引、节点或原因...','all'=>'所有状态','started'=>'已启动','initializing'=>'初始化中','relocating'=>'迁移中','unassigned'=>'未分配','total'=>'分片总数','node_count'=>'节点数','distribution'=>'节点分布','table'=>'分片列表','index'=>'索引','shard'=>'分片','type'=>'类型','state'=>'状态','documents'=>'文档','storage'=>'大小','node'=>'节点','reason'=>'未分配原因','primary'=>'主分片','replica'=>'副本','loading'=>'正在加载分片...','empty'=>'未找到分片。','error'=>'无法加载分片信息。','updated'=>'最后更新','all_nodes'=>'所有节点','filter_node'=>'选择节点'],
        'hi' => ['title'=>'शार्ड आवंटन','description'=>'शार्ड स्थिति, नोड वितरण और अनअसाइन्ड शार्ड की जानकारी देखें।','back'=>'मॉनिटरिंग','indices'=>'इंडेक्स','nodes'=>'नोड इतिहास','streams'=>'Data Streams','alerts'=>'अलर्ट इतिहास','refresh'=>'रीफ़्रेश','search'=>'इंडेक्स, नोड या कारण खोजें...','all'=>'सभी स्थितियाँ','started'=>'शुरू','initializing'=>'शुरू हो रहा है','relocating'=>'स्थानांतरित','unassigned'=>'अनअसाइन्ड','total'=>'कुल शार्ड','node_count'=>'नोड','distribution'=>'नोड के अनुसार वितरण','table'=>'शार्ड सूची','index'=>'इंडेक्स','shard'=>'शार्ड','type'=>'प्रकार','state'=>'स्थिति','documents'=>'दस्तावेज़','storage'=>'आकार','node'=>'नोड','reason'=>'कारण','primary'=>'प्राइमरी','replica'=>'रेप्लिका','loading'=>'शार्ड लोड हो रहे हैं...','empty'=>'कोई शार्ड नहीं मिला।','error'=>'शार्ड जानकारी लोड नहीं हुई।','updated'=>'अंतिम अपडेट','all_nodes'=>'सभी नोड','filter_node'=>'नोड चुनें'],
        'ar' => ['title'=>'توزيع الأجزاء','description'=>'استعرض حالات الأجزاء وتوزيعها على العقد ومعلومات الأجزاء غير المعيّنة.','back'=>'المراقبة','indices'=>'الفهارس','nodes'=>'سجل العقد','streams'=>'تدفقات البيانات','alerts'=>'سجل التنبيهات','refresh'=>'تحديث','search'=>'ابحث عن فهرس أو عقدة أو سبب...','all'=>'كل الحالات','started'=>'بدأ','initializing'=>'جار التهيئة','relocating'=>'جار النقل','unassigned'=>'غير معيّن','total'=>'إجمالي الأجزاء','node_count'=>'العقد','distribution'=>'التوزيع حسب العقدة','table'=>'قائمة الأجزاء','index'=>'الفهرس','shard'=>'الجزء','type'=>'النوع','state'=>'الحالة','documents'=>'المستندات','storage'=>'الحجم','node'=>'العقدة','reason'=>'سبب عدم التعيين','primary'=>'أساسي','replica'=>'نسخة','loading'=>'جار تحميل الأجزاء...','empty'=>'لم يتم العثور على أجزاء.','error'=>'تعذر تحميل معلومات الأجزاء.','updated'=>'آخر تحديث','all_nodes'=>'كل العقد','filter_node'=>'اختر عقدة'],
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
<style>
body{background:#f4f6fb;color:#182238;font-family:Inter,system-ui,-apple-system,sans-serif}.page{max-width:1600px}
.hero{background:linear-gradient(115deg,#171d38,#303b70 62%,#625bdb);color:#fff;border-radius:20px;padding:24px 28px}.hero p{color:#d5d9f2}
.panel{background:#fff;border:1px solid #e8ecf3;border-radius:16px;box-shadow:0 5px 22px rgba(26,39,71,.045)}
.metric{padding:17px 19px;height:100%}.metric .label{font-size:.8rem;color:#6b7487}.metric .value{font-size:1.7rem;font-weight:750;margin-top:5px}
.table thead th{font-size:.75rem;color:#68748a;font-weight:650;white-space:nowrap}.table td{font-size:.84rem;vertical-align:middle}
.state{display:inline-flex;padding:4px 9px;border-radius:30px;font-size:.72rem;font-weight:700;background:#eef1f6;color:#475467}.state-started{background:#e7f6ef;color:#13734b}.state-unassigned{background:#fff0ed;color:#c43222}.state-relocating,.state-initializing{background:#fff6df;color:#9a6700}
.node-bar{height:8px;background:#edf0f5;border-radius:20px;overflow:hidden;min-width:100px}.node-bar span{display:block;height:100%;background:linear-gradient(90deg,#5865c9,#29a3a0);border-radius:20px}
</style>
</head>
<body>
<main class="container-fluid page py-4 px-3 px-lg-4">
<header class="hero mb-4"><div class="d-flex justify-content-between align-items-start flex-wrap gap-3"><div>
<div class="d-flex flex-wrap gap-2 mb-3">
<a class="btn btn-sm btn-light" href="{{ route('elasticsearch-monitoring.index',['locale'=>$locale]) }}">← {{ $text['back'] }}</a>
<a class="btn btn-sm btn-outline-light" href="{{ route('elasticsearch-monitoring.indices',['locale'=>$locale]) }}">{{ $text['indices'] }}</a>
<a class="btn btn-sm btn-outline-light" href="{{ route('elasticsearch-monitoring.nodes',['locale'=>$locale]) }}">{{ $text['nodes'] }}</a>
<a class="btn btn-sm btn-outline-light" href="{{ route('elasticsearch-monitoring.data-streams',['locale'=>$locale]) }}">{{ $text['streams'] }}</a>
<a class="btn btn-sm btn-outline-light" href="{{ route('elasticsearch-monitoring.alerts',['locale'=>$locale]) }}">{{ $text['alerts'] }}</a>
</div><h1 class="h3 fw-bold mb-1">{{ $text['title'] }}</h1><p class="mb-0">{{ $text['description'] }}</p></div>
<button class="btn btn-light" id="refresh">↻ {{ $text['refresh'] }}</button></div></header>
<div id="error" class="alert alert-danger d-none"></div>
<div class="row g-3 mb-3">
@foreach (['total'=>'total','started'=>'started','initializing'=>'initializing','relocating'=>'relocating','unassigned'=>'unassigned','node_count'=>'node_count'] as $key=>$id)
<div class="col-6 col-md-4 col-xl-2"><div class="panel metric"><div class="label">{{ $text[$key] }}</div><div class="value" id="metric-{{ $id }}">—</div></div></div>
@endforeach
</div>
<div class="row g-3 mb-3">
<div class="col-12 col-lg-4"><section class="panel p-3 h-100"><h2 class="h6 fw-bold mb-3">{{ $text['distribution'] }}</h2><div id="distribution"><div class="text-secondary small">{{ $text['loading'] }}</div></div></section></div>
<div class="col-12 col-lg-8"><section class="panel p-3 h-100">
<div class="row g-2 mb-3"><div class="col-md-7"><input id="search" type="search" class="form-control" placeholder="{{ $text['search'] }}"></div><div class="col-md-3"><select id="state-filter" class="form-select"><option value="">{{ $text['all'] }}</option><option>STARTED</option><option>INITIALIZING</option><option>RELOCATING</option><option>UNASSIGNED</option></select></div><div class="col-md-2"><select id="node-filter" class="form-select"><option value="">{{ $text['all_nodes'] }}</option></select></div></div>
<div class="d-flex justify-content-between align-items-center mb-2"><h2 class="h6 fw-bold mb-0">{{ $text['table'] }}</h2><span class="small text-secondary" id="updated"></span></div>
<div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>{{ $text['index'] }}</th><th>{{ $text['shard'] }}</th><th>{{ $text['type'] }}</th><th>{{ $text['state'] }}</th><th>{{ $text['documents'] }}</th><th>{{ $text['storage'] }}</th><th>{{ $text['node'] }}</th><th>{{ $text['reason'] }}</th></tr></thead><tbody id="shards"><tr><td colspan="8" class="text-center py-5 text-secondary">{{ $text['loading'] }}</td></tr></tbody></table></div>
</section></div></div>
</main>
<script>
const endpoint=@json(route('elasticsearch-monitoring.shards.data',['locale'=>$locale]));
const labels=@json($text);
let allShards=[];
const safe=value=>String(value??'—').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
const number=value=>value===null||value===undefined?'—':Number(value).toLocaleString(document.documentElement.lang);
function renderTable(){
 const query=document.getElementById('search').value.trim().toLowerCase();
 const state=document.getElementById('state-filter').value;
 const node=document.getElementById('node-filter').value;
 const rows=allShards.filter(s=>(!state||s.state===state)&&(!node||(s.node||'')===node)&&[s.index,s.node,s.unassigned_reason,s.state].join(' ').toLowerCase().includes(query));
 document.getElementById('shards').innerHTML=rows.length?rows.map(s=>{
  const stateClass=s.state==='STARTED'?'state-started':s.state==='UNASSIGNED'?'state-unassigned':['RELOCATING','INITIALIZING'].includes(s.state)?'state-relocating':'';
  return '<tr><td class="fw-semibold">'+safe(s.index)+'</td><td>'+number(s.shard)+'</td><td><span class="badge text-bg-light">'+safe(s.type==='p'?labels.primary:s.type==='r'?labels.replica:s.type)+'</span></td><td><span class="state '+stateClass+'">'+safe(s.state)+'</span></td><td>'+number(s.documents)+'</td><td>'+(s.store_bytes===null?'—':formatBytes(s.store_bytes))+'</td><td>'+safe(s.node)+'</td><td class="text-wrap" style="min-width:170px">'+safe(s.unassigned_reason)+'</td></tr>';
 }).join(''):'<tr><td colspan="8" class="text-center text-secondary py-5">'+labels.empty+'</td></tr>';
}
function formatBytes(bytes){if(!bytes)return '0 B';const units=['B','KB','MB','GB','TB'];const i=Math.min(Math.floor(Math.log(bytes)/Math.log(1024)),4);return (bytes/Math.pow(1024,i)).toFixed(1)+' '+units[i];}
function renderDistribution(items){
 const root=document.getElementById('distribution');
 if(!items.length){root.innerHTML='<div class="text-secondary small">—</div>';return;}
 const max=Math.max(...items.map(x=>x.shards),1);
 root.innerHTML=items.map(item=>'<div class="mb-3"><div class="d-flex justify-content-between small mb-1"><span class="fw-semibold">'+safe(item.node)+'</span><span>'+number(item.shards)+'</span></div><div class="node-bar"><span style="width:'+Math.max(2,item.shards/max*100)+'%"></span></div></div>').join('');
}
async function load(){
 const button=document.getElementById('refresh');button.disabled=true;
 try{
  const response=await fetch(endpoint,{headers:{Accept:'application/json'}});
  const payload=await response.json();if(!response.ok)throw new Error(payload.error||payload.message||labels.error);
  allShards=payload.shards||[];
  Object.entries(payload.summary||{}).forEach(([key,value])=>{const el=document.getElementById('metric-'+key);if(el)el.textContent=number(value);});
  renderDistribution(payload.node_distribution||[]);
  const current=document.getElementById('node-filter').value;
  const nodes=[...new Set(allShards.map(s=>s.node).filter(Boolean))].sort();
  document.getElementById('node-filter').innerHTML='<option value="">'+labels.all_nodes+'</option>'+nodes.map(n=>'<option value="'+safe(n)+'">'+safe(n)+'</option>').join('');
  document.getElementById('node-filter').value=nodes.includes(current)?current:'';
  renderTable();document.getElementById('updated').textContent=labels.updated+': '+new Date().toLocaleTimeString();
  document.getElementById('error').classList.add('d-none');
 }catch(error){document.getElementById('error').textContent=error.message||labels.error;document.getElementById('error').classList.remove('d-none');}
 finally{button.disabled=false;}
}
document.getElementById('refresh').addEventListener('click',load);
['search','state-filter','node-filter'].forEach(id=>document.getElementById(id).addEventListener(id==='search'?'input':'change',renderTable));
load();setInterval(load,30000);
</script>
</body></html>
