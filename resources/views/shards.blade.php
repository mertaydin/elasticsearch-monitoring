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
$explainText = json_decode(<<<'JSON'
{"tr":{"explain":"Dağıtım nedenini incele","explainTitle":"Shard dağıtım açıklaması","explainLoading":"Elasticsearch kararları alınıyor...","close":"Kapat","allocation":"Atama kararı","currentState":"Mevcut durum","unassignedReason":"Atanmama nedeni","allocationExplanation":"Genel açıklama","nodeDecisions":"Node bazında kararlar","noDecisions":"Node karar bilgisi bulunmuyor.","explainError":"Shard dağıtım açıklaması alınamadı. Elasticsearch monitor yetkisini kontrol edin.","disk":"Disk bilgisi","failedAttempts":"Başarısız deneme"},"en":{"explain":"Explain allocation","explainTitle":"Shard allocation explanation","explainLoading":"Fetching Elasticsearch decisions...","close":"Close","allocation":"Allocation decision","currentState":"Current state","unassignedReason":"Unassigned reason","allocationExplanation":"Overall explanation","nodeDecisions":"Node decisions","noDecisions":"No node decision details available.","explainError":"Unable to retrieve allocation explanation. Check the Elasticsearch monitor privilege.","disk":"Disk information","failedAttempts":"Failed attempts"},"es":{"explain":"Explicar asignación","explainTitle":"Explicación de asignación","explainLoading":"Consultando decisiones...","close":"Cerrar","allocation":"Decisión de asignación","currentState":"Estado actual","unassignedReason":"Motivo sin asignar","allocationExplanation":"Explicación general","nodeDecisions":"Decisiones por nodo","noDecisions":"No hay detalles de decisiones.","explainError":"No se pudo obtener la explicación. Compruebe el privilegio monitor.","disk":"Información de disco","failedAttempts":"Intentos fallidos"},"zh_CN":{"explain":"解释分片分配","explainTitle":"分片分配说明","explainLoading":"正在获取 Elasticsearch 决策...","close":"关闭","allocation":"分配决策","currentState":"当前状态","unassignedReason":"未分配原因","allocationExplanation":"总体说明","nodeDecisions":"节点决策","noDecisions":"没有节点决策详情。","explainError":"无法获取分配说明，请检查 monitor 权限。","disk":"磁盘信息","failedAttempts":"失败次数"},"hi":{"explain":"आवंटन समझाएँ","explainTitle":"शार्ड आवंटन विवरण","explainLoading":"Elasticsearch निर्णय प्राप्त हो रहे हैं...","close":"बंद करें","allocation":"आवंटन निर्णय","currentState":"वर्तमान स्थिति","unassignedReason":"अनअसाइन्ड कारण","allocationExplanation":"समग्र विवरण","nodeDecisions":"नोड निर्णय","noDecisions":"नोड निर्णय विवरण उपलब्ध नहीं।","explainError":"आवंटन विवरण प्राप्त नहीं हुआ। monitor अनुमति जाँचें।","disk":"डिस्क जानकारी","failedAttempts":"विफल प्रयास"},"ar":{"explain":"تفسير التخصيص","explainTitle":"شرح تخصيص الجزء","explainLoading":"جار جلب قرارات Elasticsearch...","close":"إغلاق","allocation":"قرار التخصيص","currentState":"الحالة الحالية","unassignedReason":"سبب عدم التعيين","allocationExplanation":"التفسير العام","nodeDecisions":"قرارات العقد","noDecisions":"لا تتوفر تفاصيل قرارات العقد.","explainError":"تعذر جلب شرح التخصيص. تحقق من صلاحية monitor.","disk":"معلومات القرص","failedAttempts":"المحاولات الفاشلة"}}
JSON, true)[$locale];
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
.explain-btn{font-size:.72rem;white-space:nowrap}.explain-overlay{position:fixed;inset:0;background:rgba(12,18,35,.62);z-index:1050;display:none;align-items:center;justify-content:center;padding:18px}.explain-overlay.show{display:flex}.explain-dialog{background:#fff;border-radius:16px;width:min(100%,1000px);max-height:90vh;overflow:auto;padding:22px;box-shadow:0 20px 60px rgba(0,0,0,.25)}.decision-card{border:1px solid #e8ecf3;border-radius:10px;padding:12px;margin-bottom:10px}.decision-no{color:#bd3024}.decision-yes{color:#13734b}.node-bar{height:8px;background:#edf0f5;border-radius:20px;overflow:hidden;min-width:100px}.node-bar span{display:block;height:100%;background:linear-gradient(90deg,#5865c9,#29a3a0);border-radius:20px}
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
<div class="explain-overlay" id="explain-overlay" role="dialog" aria-modal="true" aria-labelledby="explain-heading">
<section class="explain-dialog">
<div class="d-flex justify-content-between align-items-center mb-3">
<h2 class="h5 fw-bold mb-0" id="explain-heading">{{ $explainText['explainTitle'] }}</h2>
<button class="btn btn-sm btn-outline-secondary" id="explain-close">{{ $explainText['close'] }} ✕</button>
</div>
<div id="explain-content" class="small">{{ $explainText['explainLoading'] }}</div>
</section></div>
<script>
const endpoint=@json(route('elasticsearch-monitoring.shards.data',['locale'=>$locale]));
const labels=@json($text);
const explainLabels=@json($explainText);
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
  return '<tr><td class="fw-semibold">'+safe(s.index)+'</td><td>'+number(s.shard)+'</td><td><span class="badge text-bg-light">'+safe(s.type==='p'?labels.primary:s.type==='r'?labels.replica:s.type)+'</span></td><td><span class="state '+stateClass+'">'+safe(s.state)+'</span></td><td>'+number(s.documents)+'</td><td>'+(s.store_bytes===null?'—':formatBytes(s.store_bytes))+'</td><td>'+safe(s.node)+'</td><td class="text-wrap" style="min-width:170px">'+safe(s.unassigned_reason)+'</td><td><button class="btn btn-sm btn-outline-primary explain-btn" data-index="'+safe(s.index)+'" data-shard="'+safe(s.shard)+'" data-primary="'+(s.type==='p'?'true':'false')+'" data-node="'+safe(s.node||'')+'">'+safe(explainLabels.explain)+'</button></td></tr>';
 }).join(''):'<tr><td colspan="9" class="text-center text-secondary py-5">'+labels.empty+'</td></tr>';
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

document.getElementById('shards').addEventListener('click',event=>{
 const button=event.target.closest('[data-index]');
 if(button)explainShard(button.dataset);
});
document.getElementById('explain-close').addEventListener('click',()=>document.getElementById('explain-overlay').classList.remove('show'));
document.getElementById('explain-overlay').addEventListener('click',event=>{if(event.target.id==='explain-overlay')event.currentTarget.classList.remove('show');});
async function explainShard(shard){
 const overlay=document.getElementById('explain-overlay'),content=document.getElementById('explain-content');
 overlay.classList.add('show');content.textContent=explainLabels.explainLoading;
 try{
  const params=new URLSearchParams({index:shard.index,shard:shard.shard,primary:shard.primary==='true'?'1':'0'});
  if(shard.node)params.set('current_node',shard.node);
  const response=await fetch(explainEndpoint+'&'+params.toString(),{headers:{Accept:'application/json'}});
  const data=await response.json();
  if(!response.ok)throw new Error(data.error||data.message||explainLabels.explainError);
  content.innerHTML=renderExplanation(data);
 }catch(error){content.innerHTML='<div class="alert alert-danger">'+safe(error.message||explainLabels.explainError)+'</div>';}
}
function renderExplanation(data){
 const summary=[['index',data.index],['shard',data.shard],['primary',data.primary?labels.primary:labels.replica],['currentState',data.current_state],['allocation',data.can_allocate||data.can_remain_on_current_node||data.can_rebalance_to_other_node],['unassignedReason',data.unassigned_info?.reason],['failedAttempts',data.unassigned_info?.failed_allocation_attempts]];
 const heading=summary.filter(([,value])=>value!==undefined&&value!==null).map(([key,value])=>'<div class="col-6 col-lg-4"><div class="decision-card"><div class="text-secondary">'+safe(explainLabels[key]||labels[key]||key)+'</div><div class="fw-semibold">'+safe(value)+'</div></div></div>').join('');
 const explanation=data.allocate_explanation||data.rebalance_explanation||data.move_explanation||'';
 const nodes=(data.node_allocation_decisions||[]).map(node=>'<div class="decision-card"><div class="d-flex flex-wrap justify-content-between gap-2"><strong>'+safe(node.node_name)+'</strong><span class="fw-bold '+(node.node_decision==='yes'?'decision-yes':node.node_decision==='no'?'decision-no':'')+'">'+safe(node.node_decision||'—')+'</span></div>'+(node.deciders||[]).map(decider=>'<div class="border-top mt-2 pt-2"><div><strong>'+safe(decider.decider)+'</strong> · '+safe(decider.decision)+'</div><div class="text-secondary">'+safe(decider.explanation)+'</div></div>').join('')+(node.store?'<div class="text-secondary mt-2">'+safe(explainLabels.disk)+': '+safe(node.store.matching_size||node.store.matching_size_in_bytes||'—')+'</div>':'')+'</div>').join('');
 return '<div class="row g-2 mb-3">'+heading+'</div>'+(explanation?'<div class="alert alert-info"><strong>'+safe(explainLabels.allocationExplanation)+'</strong><div class="mt-1">'+safe(explanation)+'</div></div>':'')+'<h3 class="h6 fw-bold mt-3">'+safe(explainLabels.nodeDecisions)+'</h3>'+(nodes||'<div class="text-secondary">'+safe(explainLabels.noDecisions)+'</div>');
}

['search','state-filter','node-filter'].forEach(id=>document.getElementById(id).addEventListener(id==='search'?'input':'change',renderTable));
load();setInterval(load,30000);
</script>
</body></html>
