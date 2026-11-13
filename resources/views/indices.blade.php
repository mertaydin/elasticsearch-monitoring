@php
    $locale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', config('elasticsearch-monitoring.dashboard.locale', 'en')));
    $locale = in_array($locale, config('elasticsearch-monitoring.dashboard.available_locales', ['tr', 'en']), true) ? $locale : 'en';
    $text = [
        'tr' => ['title'=>'İndeks Yönetimi','back'=>'İzleme paneline dön','description'=>'Elasticsearch indekslerini görüntüleyin, mapping ve settings ayrıntılarını inceleyin.','search'=>'İndeks ara','hidden'=>'Gizli indeksleri dahil et','rollup'=>'Rollup indekslerini dahil et','reload'=>'İndeksleri yenile','name'=>'Ad','health'=>'Sağlık','status'=>'Durum','primaries'=>'Primary','replicas'=>'Replica','docs'=>'Belge sayısı','storage'=>'Depolama boyutu','stream'=>'Data stream','lifecycle_status'=>'Lifecycle durumu','lifecycle_phase'=>'Lifecycle aşaması','actions'=>'İşlemler','details'=>'Detaylar','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'Lifecycle','close'=>'Kapat','loading'=>'Yükleniyor...','empty'=>'İndeks bulunamadı.','error'=>'İndeksler yüklenemedi.','copy'=>'Kopyala','policy' => "ILM politikası",'managed_status' => "Yönetim",'managed' => "Yönetiliyor",'unmanaged' => "ILM yönetiminde değil",'phase' => "Aşama",'action' => "İşlem",'step' => "Adım",'rollover_alias' => "Rollover alias",'age' => "Yaş",'failed_step' => "Hatalı adım"],
        'en' => ['title'=>'Index Management','back'=>'Back to monitoring','description'=>'Browse Elasticsearch indices and inspect their mappings and settings.','search'=>'Search indices','hidden'=>'Include hidden indices','rollup'=>'Include rollup indices','reload'=>'Reload indices','name'=>'Name','health'=>'Health','status'=>'Status','primaries'=>'Primaries','replicas'=>'Replicas','docs'=>'Documents count','storage'=>'Storage size','stream'=>'Data stream','lifecycle_status'=>'Lifecycle status','lifecycle_phase'=>'Lifecycle phase','actions'=>'Actions','details'=>'Details','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'Lifecycle','close'=>'Close','loading'=>'Loading...','empty'=>'No indices found.','error'=>'Unable to load indices.','copy'=>'Copy','policy' => "ILM policy",'managed_status' => "Management",'managed' => "Managed",'unmanaged' => "Not ILM-managed",'phase' => "Phase",'action' => "Action",'step' => "Step",'rollover_alias' => "Rollover alias",'age' => "Age",'failed_step' => "Failed step"],
        'es' => ['title'=>'Gestión de índices','back'=>'Volver al panel','description'=>'Explore los índices de Elasticsearch y consulte sus mappings y settings.','search'=>'Buscar índices','hidden'=>'Incluir índices ocultos','rollup'=>'Incluir índices rollup','reload'=>'Recargar índices','name'=>'Nombre','health'=>'Salud','status'=>'Estado','primaries'=>'Primarios','replicas'=>'Réplicas','docs'=>'Documentos','storage'=>'Almacenamiento','stream'=>'Data stream','lifecycle_status'=>'Estado del ciclo','lifecycle_phase'=>'Fase del ciclo','actions'=>'Acciones','details'=>'Detalles','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'Lifecycle','close'=>'Cerrar','loading'=>'Cargando...','empty'=>'No se encontraron índices.','error'=>'No se pudieron cargar los índices.','copy'=>'Copiar','policy' => "Política ILM",'managed_status' => "Gestión",'managed' => "Administrado",'unmanaged' => "Sin gestión ILM",'phase' => "Fase",'action' => "Acción",'step' => "Paso",'rollover_alias' => "Alias de rollover",'age' => "Antigüedad",'failed_step' => "Paso fallido"],
        'zh_CN' => ['title'=>'索引管理','back'=>'返回监控面板','description'=>'浏览 Elasticsearch 索引并查看 mapping 和 settings。','search'=>'搜索索引','hidden'=>'包含隐藏索引','rollup'=>'包含 rollup 索引','reload'=>'重新加载索引','name'=>'名称','health'=>'健康状态','status'=>'状态','primaries'=>'主分片','replicas'=>'副本','docs'=>'文档数','storage'=>'存储大小','stream'=>'数据流','lifecycle_status'=>'生命周期状态','lifecycle_phase'=>'生命周期阶段','actions'=>'操作','details'=>'详情','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'生命周期','close'=>'关闭','loading'=>'加载中...','empty'=>'未找到索引。','error'=>'无法加载索引。','copy'=>'复制','policy' => "ILM 策略",'managed_status' => "管理状态",'managed' => "已管理",'unmanaged' => "未由 ILM 管理",'phase' => "阶段",'action' => "操作",'step' => "步骤",'rollover_alias' => "Rollover 别名",'age' => "索引年龄",'failed_step' => "失败步骤"],
        'hi' => ['title'=>'इंडेक्स प्रबंधन','back'=>'मॉनिटरिंग पर वापस','description'=>'Elasticsearch इंडेक्स देखें और mapping तथा settings की जानकारी खोलें।','search'=>'इंडेक्स खोजें','hidden'=>'छिपे इंडेक्स शामिल करें','rollup'=>'Rollup इंडेक्स शामिल करें','reload'=>'इंडेक्स रीफ़्रेश करें','name'=>'नाम','health'=>'स्वास्थ्य','status'=>'स्थिति','primaries'=>'प्राइमरी','replicas'=>'रेप्लिका','docs'=>'दस्तावेज़','storage'=>'स्टोरेज आकार','stream'=>'डेटा स्ट्रीम','lifecycle_status'=>'लाइफसाइकिल स्थिति','lifecycle_phase'=>'लाइफसाइकिल चरण','actions'=>'कार्रवाई','details'=>'विवरण','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'Lifecycle','close'=>'बंद करें','loading'=>'लोड हो रहा है...','empty'=>'कोई इंडेक्स नहीं मिला।','error'=>'इंडेक्स लोड नहीं हो सके।','copy'=>'कॉपी','policy' => "ILM नीति",'managed_status' => "प्रबंधन",'managed' => "प्रबंधित",'unmanaged' => "ILM द्वारा प्रबंधित नहीं",'phase' => "चरण",'action' => "कार्रवाई",'step' => "स्टेप",'rollover_alias' => "Rollover alias",'age' => "आयु",'failed_step' => "विफल स्टेप"],
        'ar' => ['title'=>'إدارة الفهارس','back'=>'العودة إلى لوحة المراقبة','description'=>'استعرض فهارس Elasticsearch واطلع على إعدادات mapping وsettings.','search'=>'بحث في الفهارس','hidden'=>'تضمين الفهارس المخفية','rollup'=>'تضمين فهارس rollup','reload'=>'إعادة تحميل الفهارس','name'=>'الاسم','health'=>'الصحة','status'=>'الحالة','primaries'=>'الأساسية','replicas'=>'النسخ','docs'=>'عدد المستندات','storage'=>'حجم التخزين','stream'=>'تدفق البيانات','lifecycle_status'=>'حالة دورة الحياة','lifecycle_phase'=>'مرحلة دورة الحياة','actions'=>'الإجراءات','details'=>'التفاصيل','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'Lifecycle','close'=>'إغلاق','loading'=>'جار التحميل...','empty'=>'لم يتم العثور على فهارس.','error'=>'تعذر تحميل الفهارس.','copy'=>'نسخ','policy' => "سياسة ILM",'managed_status' => "الإدارة",'managed' => "تحت الإدارة",'unmanaged' => "غير مُدار بواسطة ILM",'phase' => "المرحلة",'action' => "الإجراء",'step' => "الخطوة",'rollover_alias' => "اسم Rollover",'age' => "العمر",'failed_step' => "الخطوة الفاشلة"],
    ][$locale];
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $text['title'] }}</title>
    @if ($locale === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <style>
        body{background:#f4f6fb;color:#182238;font-family:Inter,system-ui,-apple-system,sans-serif}
        .page{max-width:1500px}.panel{background:#fff;border:1px solid #e7ebf2;border-radius:16px;box-shadow:0 5px 18px rgba(29,41,74,.035)}
        .toolbar{padding:18px}.table thead th{font-size:.75rem;color:#667085;font-weight:650;white-space:nowrap}.table td{vertical-align:middle;font-size:.84rem}
        .index-name{font-weight:600;color:#1759a6;text-decoration:none;word-break:break-word}.health-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-inline-end:7px;background:#98a2b3}.health-green{background:#16845b}.health-yellow{background:#d69e2e}.health-red{background:#dc4545}
        .json-view{background:#101828;color:#d7e1f0;border-radius:10px;padding:18px;max-height:65vh;overflow:auto;font-size:.78rem;white-space:pre-wrap;word-break:break-word}
        .nav-tabs .nav-link{font-weight:600;font-size:.88rem}.table-responsive{min-height:180px}
    </style>
</head>
<body>
<main class="container-fluid page py-4 px-3 px-lg-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div><div class="d-flex align-items-center gap-2 mb-2"><a href="{{ route('elasticsearch-monitoring.index', ['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">← {{ $text['back'] }}</a><a href="{{ route('elasticsearch-monitoring.data-streams', ['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">Data Streams</a><a href="{{ route('elasticsearch-monitoring.alerts', ['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">{{ $locale === 'tr' ? 'Alarm Geçmişi' : 'Alert History' }}</a></div><h1 class="h3 fw-bold mb-1">{{ $text['title'] }}</h1><p class="text-secondary mb-0">{{ $text['description'] }}</p></div>
        <button class="btn btn-success" id="reload">↻ {{ $text['reload'] }}</button>
    </div>
    <div class="panel toolbar mb-3">
        <div class="row g-3 align-items-center">
            <div class="col-lg-5"><input type="search" id="search" class="form-control" placeholder="⌕ {{ $text['search'] }}"></div>
            <div class="col-lg-auto"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="hidden"><label class="form-check-label small" for="hidden">{{ $text['hidden'] }}</label></div></div>
            <div class="col-lg-auto"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="rollup"><label class="form-check-label small" for="rollup">{{ $text['rollup'] }}</label></div></div>
            <div class="col text-lg-end"><span class="small text-secondary" id="count">—</span></div>
        </div>
    </div>
    <div class="panel table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th class="ps-3">{{ $text['name'] }}</th><th>{{ $text['health'] }}</th><th>{{ $text['status'] }}</th><th>{{ $text['lifecycle_status'] }}</th><th>{{ $text['lifecycle_phase'] }}</th><th>{{ $text['policy'] }}</th><th>{{ $text['primaries'] }}</th><th>{{ $text['replicas'] }}</th><th class="text-end">{{ $text['docs'] }}</th><th class="text-end">{{ $text['storage'] }}</th><th>{{ $text['stream'] }}</th><th class="text-end pe-3">{{ $text['actions'] }}</th></tr></thead>
            <tbody id="indices"><tr><td colspan="12" class="text-center text-secondary py-5">{{ $text['loading'] }}</td></tr></tbody>
        </table>
    </div>
</main>
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content border-0 rounded-4">
        <div class="modal-header"><div><h5 class="modal-title fw-bold" id="modalTitle">{{ $text['details'] }}</h5><div class="small text-secondary" id="lifecycle"></div></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ $text['close'] }}"></button></div>
        <div class="modal-body">
            <ul class="nav nav-tabs mb-3"><li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#mapping-tab">{{ $text['mapping'] }}</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#settings-tab">{{ $text['settings'] }}</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lifecycle-tab">{{ $text['lifecycle'] }}</button></li></ul>
            <div class="tab-content"><div class="tab-pane fade show active" id="mapping-tab"><pre class="json-view" id="mapping-json">{{ $text['loading'] }}</pre></div><div class="tab-pane fade" id="settings-tab"><pre class="json-view" id="settings-json">{{ $text['loading'] }}</pre></div><div class="tab-pane fade" id="lifecycle-tab"><div id="lifecycle-summary" class="row g-3 mb-3"></div><pre class="json-view" id="lifecycle-json">{{ $text['loading'] }}</pre></div></div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">{{ $text['close'] }}</button></div>
    </div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const labels=@json($text);
const listUrl=@json(route('elasticsearch-monitoring.indices.data'));
const detailBase=@json(route('elasticsearch-monitoring.indices.details',['index'=>'__INDEX__']));
const rows=document.getElementById('indices');
let allIndices=[];
const modal=new bootstrap.Modal(document.getElementById('detailsModal'));
const number=v=>Number(v||0).toLocaleString();
const bytes=value=>{let n=Number(value||0);if(n>=1024**3)return (n/1024**3).toFixed(2)+' GB';if(n>=1024**2)return (n/1024**2).toFixed(2)+' MB';if(n>=1024)return (n/1024).toFixed(2)+' KB';return n+' B';};
function render(){
    const q=document.getElementById('search').value.toLowerCase();
    const filtered=allIndices.filter(x=>x.name.toLowerCase().includes(q));
    document.getElementById('count').textContent=number(filtered.length)+' / '+number(allIndices.length);
    if(!filtered.length){rows.innerHTML='<tr><td colspan="12" class="text-center text-secondary py-5">'+labels.empty+'</td></tr>';return;}
    rows.innerHTML=filtered.map(x=>'<tr><td class="ps-3"><a href="#" class="index-name" data-index="'+encodeURIComponent(x.name)+'">'+escapeHtml(x.name)+'</a></td><td><span class="health-dot health-'+escapeHtml(x.health)+'"></span>'+escapeHtml(x.health)+'</td><td>'+escapeHtml(x.status)+'</td><td>'+escapeHtml(x.lifecycle_status||'—')+'</td><td>'+escapeHtml(x.lifecycle_phase||'—')+'</td><td>'+escapeHtml(x.lifecycle_policy||'—')+'</td><td>'+number(x.primaries)+'</td><td>'+number(x.replicas)+'</td><td class="text-end">'+number(x.documents_count)+'</td><td class="text-end">'+bytes(x.storage_bytes)+'</td><td>'+escapeHtml(x.data_stream||'—')+'</td><td class="text-end pe-3"><button class="btn btn-sm btn-outline-primary" data-index="'+encodeURIComponent(x.name)+'">'+labels.details+'</button></td></tr>').join('');
}
function escapeHtml(v){return String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
async function load(){
    rows.innerHTML='<tr><td colspan="12" class="text-center text-secondary py-5">'+labels.loading+'</td></tr>';
    const params=new URLSearchParams({include_hidden:document.getElementById('hidden').checked?'1':'0',include_rollup:document.getElementById('rollup').checked?'1':'0'});
    try{const r=await fetch(listUrl+'?'+params,{headers:{Accept:'application/json'}});const data=await r.json();if(!r.ok)throw new Error(data.error||labels.error);allIndices=data.indices||[];render();}
    catch(e){rows.innerHTML='<tr><td colspan="12" class="text-center text-danger py-5">'+escapeHtml(e.message||labels.error)+'</td></tr>';}
}
async function showDetails(name){
    document.getElementById('modalTitle').textContent=name;
    document.getElementById('lifecycle').textContent='';
    document.getElementById('mapping-json').textContent=labels.loading;
    document.getElementById('settings-json').textContent=labels.loading;
    document.getElementById('lifecycle-json').textContent=labels.loading;
    document.getElementById('lifecycle-summary').replaceChildren();
    modal.show();
    try{
        const url=detailBase.replace('__INDEX__',encodeURIComponent(name));
        const r=await fetch(url,{headers:{Accept:'application/json'}});
        const data=await r.json();if(!r.ok)throw new Error(data.error||labels.error);
        document.getElementById('mapping-json').textContent=JSON.stringify(data.mapping,null,2);
        document.getElementById('settings-json').textContent=JSON.stringify(data.settings,null,2);
        const lifecycle=data.lifecycle||{};
        document.getElementById('lifecycle').textContent=[lifecycle.managed?labels.managed:labels.unmanaged,lifecycle.policy,lifecycle.phase].filter(Boolean).join(' · ');
        const fields=[[labels.managed_status,lifecycle.managed?labels.managed:labels.unmanaged],[labels.policy,lifecycle.policy],[labels.phase,lifecycle.phase],[labels.action,lifecycle.action],[labels.step,lifecycle.step],[labels.rollover_alias,lifecycle.rollover_alias],[labels.age,lifecycle.age],[labels.failed_step,lifecycle.failed_step]];
        const summary=document.getElementById('lifecycle-summary');
        fields.forEach(([label,value])=>{if(value===null||value===undefined||value==='')return;const col=document.createElement('div');col.className='col-12 col-md-6 col-xl-4';const card=document.createElement('div');card.className='border rounded-3 p-3 h-100 bg-light';const heading=document.createElement('div');heading.className='small text-secondary mb-1';heading.textContent=label;const content=document.createElement('div');content.className='fw-semibold text-break';content.textContent=String(value);card.append(heading,content);col.append(card);summary.append(col);});
        document.getElementById('lifecycle-json').textContent=JSON.stringify(lifecycle.explain||{},null,2);
    }catch(e){document.getElementById('mapping-json').textContent=e.message;document.getElementById('settings-json').textContent=e.message;document.getElementById('lifecycle-json').textContent=e.message;}
}
rows.addEventListener('click',e=>{const button=e.target.closest('[data-index]');if(!button)return;e.preventDefault();showDetails(decodeURIComponent(button.dataset.index));});
document.getElementById('reload').addEventListener('click',load);
document.getElementById('search').addEventListener('input',render);
document.getElementById('hidden').addEventListener('change',load);
document.getElementById('rollup').addEventListener('change',load);
load();
</script>
</body>
</html>
