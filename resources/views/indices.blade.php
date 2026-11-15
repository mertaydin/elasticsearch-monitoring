@php
    $locale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', config('elasticsearch-monitoring.dashboard.locale', 'en')));
    $locale = in_array($locale, config('elasticsearch-monitoring.dashboard.available_locales', ['tr', 'en']), true) ? $locale : 'en';
    $text = [
        'tr' => ['title'=>'İndeks Yönetimi','back'=>'İzleme paneline dön','description'=>'Elasticsearch indekslerini görüntüleyin, mapping ve settings ayrıntılarını inceleyin.','search'=>'İndeks ara','hidden'=>'Gizli indeksleri dahil et','rollup'=>'Rollup indekslerini dahil et','reload'=>'İndeksleri yenile','name'=>'Ad','health'=>'Sağlık','status'=>'Durum','primaries'=>'Primary','replicas'=>'Replica','docs'=>'Belge sayısı','storage'=>'Depolama boyutu','stream'=>'Data stream','lifecycle_status'=>'Lifecycle durumu','lifecycle_phase'=>'Lifecycle aşaması','favorite'=>'Favori','favorites_only'=>'Yalnızca favoriler','no_favorites'=>'Henüz favori indeks eklenmedi.','add_favorite'=>'Favorilere ekle','remove_favorite'=>'Favorilerden çıkar','actions'=>'İşlemler','details'=>'Detaylar','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'Lifecycle','close'=>'Kapat','loading'=>'Yükleniyor...','empty'=>'İndeks bulunamadı.','error'=>'İndeksler yüklenemedi.','copy'=>'Kopyala','policy' => "ILM politikası",'managed_status' => "Yönetim",'managed' => "Yönetiliyor",'unmanaged' => "ILM yönetiminde değil",'phase' => "Aşama",'action' => "İşlem",'step' => "Adım",'rollover_alias' => "Rollover alias",'age' => "Yaş",'failed_step' => "Hatalı adım",'export_csv'=>'CSV indir','export_json'=>'JSON indir','export_empty'=>'Dışa aktarılacak indeks bulunamadı.'],
        'en' => ['title'=>'Index Management','back'=>'Back to monitoring','description'=>'Browse Elasticsearch indices and inspect their mappings and settings.','search'=>'Search indices','hidden'=>'Include hidden indices','rollup'=>'Include rollup indices','reload'=>'Reload indices','name'=>'Name','health'=>'Health','status'=>'Status','primaries'=>'Primaries','replicas'=>'Replicas','docs'=>'Documents count','storage'=>'Storage size','stream'=>'Data stream','lifecycle_status'=>'Lifecycle status','lifecycle_phase'=>'Lifecycle phase','favorite'=>'Favorite','favorites_only'=>'Favorites only','no_favorites'=>'No favorite indices yet.','add_favorite'=>'Add to favorites','remove_favorite'=>'Remove from favorites','actions'=>'Actions','details'=>'Details','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'Lifecycle','close'=>'Close','loading'=>'Loading...','empty'=>'No indices found.','error'=>'Unable to load indices.','copy'=>'Copy','policy' => "ILM policy",'managed_status' => "Management",'managed' => "Managed",'unmanaged' => "Not ILM-managed",'phase' => "Phase",'action' => "Action",'step' => "Step",'rollover_alias' => "Rollover alias",'age' => "Age",'failed_step' => "Failed step",'export_csv'=>'Export CSV','export_json'=>'Export JSON','export_empty'=>'There are no indices to export.'],
        'es' => ['title'=>'Gestión de índices','back'=>'Volver al panel','description'=>'Explore los índices de Elasticsearch y consulte sus mappings y settings.','search'=>'Buscar índices','hidden'=>'Incluir índices ocultos','rollup'=>'Incluir índices rollup','reload'=>'Recargar índices','name'=>'Nombre','health'=>'Salud','status'=>'Estado','primaries'=>'Primarios','replicas'=>'Réplicas','docs'=>'Documentos','storage'=>'Almacenamiento','stream'=>'Data stream','lifecycle_status'=>'Estado del ciclo','lifecycle_phase'=>'Fase del ciclo','favorite'=>'Favorito','favorites_only'=>'Solo favoritos','no_favorites'=>'Aún no hay índices favoritos.','add_favorite'=>'Añadir a favoritos','remove_favorite'=>'Quitar de favoritos','actions'=>'Acciones','details'=>'Detalles','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'Lifecycle','close'=>'Cerrar','loading'=>'Cargando...','empty'=>'No se encontraron índices.','error'=>'No se pudieron cargar los índices.','copy'=>'Copiar','policy' => "Política ILM",'managed_status' => "Gestión",'managed' => "Administrado",'unmanaged' => "Sin gestión ILM",'phase' => "Fase",'action' => "Acción",'step' => "Paso",'rollover_alias' => "Alias de rollover",'age' => "Antigüedad",'failed_step' => "Paso fallido",'export_csv'=>'Exportar CSV','export_json'=>'Exportar JSON','export_empty'=>'No hay índices para exportar.'],
        'zh_CN' => ['title'=>'索引管理','back'=>'返回监控面板','description'=>'浏览 Elasticsearch 索引并查看 mapping 和 settings。','search'=>'搜索索引','hidden'=>'包含隐藏索引','rollup'=>'包含 rollup 索引','reload'=>'重新加载索引','name'=>'名称','health'=>'健康状态','status'=>'状态','primaries'=>'主分片','replicas'=>'副本','docs'=>'文档数','storage'=>'存储大小','stream'=>'数据流','lifecycle_status'=>'生命周期状态','lifecycle_phase'=>'生命周期阶段','favorite'=>'收藏','favorites_only'=>'仅显示收藏','no_favorites'=>'尚无收藏索引。','add_favorite'=>'添加到收藏','remove_favorite'=>'从收藏中移除','actions'=>'操作','details'=>'详情','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'生命周期','close'=>'关闭','loading'=>'加载中...','empty'=>'未找到索引。','error'=>'无法加载索引。','copy'=>'复制','policy' => "ILM 策略",'managed_status' => "管理状态",'managed' => "已管理",'unmanaged' => "未由 ILM 管理",'phase' => "阶段",'action' => "操作",'step' => "步骤",'rollover_alias' => "Rollover 别名",'age' => "索引年龄",'failed_step' => "失败步骤",'export_csv'=>'导出 CSV','export_json'=>'导出 JSON','export_empty'=>'没有可导出的索引。'],
        'hi' => ['title'=>'इंडेक्स प्रबंधन','back'=>'मॉनिटरिंग पर वापस','description'=>'Elasticsearch इंडेक्स देखें और mapping तथा settings की जानकारी खोलें।','search'=>'इंडेक्स खोजें','hidden'=>'छिपे इंडेक्स शामिल करें','rollup'=>'Rollup इंडेक्स शामिल करें','reload'=>'इंडेक्स रीफ़्रेश करें','name'=>'नाम','health'=>'स्वास्थ्य','status'=>'स्थिति','primaries'=>'प्राइमरी','replicas'=>'रेप्लिका','docs'=>'दस्तावेज़','storage'=>'स्टोरेज आकार','stream'=>'डेटा स्ट्रीम','lifecycle_status'=>'लाइफसाइकिल स्थिति','lifecycle_phase'=>'लाइफसाइकिल चरण','favorite'=>'पसंदीदा','favorites_only'=>'केवल पसंदीदा','no_favorites'=>'अभी कोई पसंदीदा इंडेक्स नहीं है।','add_favorite'=>'पसंदीदा में जोड़ें','remove_favorite'=>'पसंदीदा से हटाएं','actions'=>'कार्रवाई','details'=>'विवरण','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'Lifecycle','close'=>'बंद करें','loading'=>'लोड हो रहा है...','empty'=>'कोई इंडेक्स नहीं मिला।','error'=>'इंडेक्स लोड नहीं हो सके।','copy'=>'कॉपी','policy' => "ILM नीति",'managed_status' => "प्रबंधन",'managed' => "प्रबंधित",'unmanaged' => "ILM द्वारा प्रबंधित नहीं",'phase' => "चरण",'action' => "कार्रवाई",'step' => "स्टेप",'rollover_alias' => "Rollover alias",'age' => "आयु",'failed_step' => "विफल स्टेप",'export_csv'=>'CSV निर्यात करें','export_json'=>'JSON निर्यात करें','export_empty'=>'निर्यात करने के लिए कोई इंडेक्स नहीं है।'],
        'ar' => ['title'=>'إدارة الفهارس','back'=>'العودة إلى لوحة المراقبة','description'=>'استعرض فهارس Elasticsearch واطلع على إعدادات mapping وsettings.','search'=>'بحث في الفهارس','hidden'=>'تضمين الفهارس المخفية','rollup'=>'تضمين فهارس rollup','reload'=>'إعادة تحميل الفهارس','name'=>'الاسم','health'=>'الصحة','status'=>'الحالة','primaries'=>'الأساسية','replicas'=>'النسخ','docs'=>'عدد المستندات','storage'=>'حجم التخزين','stream'=>'تدفق البيانات','lifecycle_status'=>'حالة دورة الحياة','lifecycle_phase'=>'مرحلة دورة الحياة','favorite'=>'المفضلة','favorites_only'=>'المفضلة فقط','no_favorites'=>'لا توجد فهارس مفضلة بعد.','add_favorite'=>'إضافة إلى المفضلة','remove_favorite'=>'إزالة من المفضلة','actions'=>'الإجراءات','details'=>'التفاصيل','mapping'=>'Mapping','settings'=>'Settings','lifecycle'=>'Lifecycle','close'=>'إغلاق','loading'=>'جار التحميل...','empty'=>'لم يتم العثور على فهارس.','error'=>'تعذر تحميل الفهارس.','copy'=>'نسخ','policy' => "سياسة ILM",'managed_status' => "الإدارة",'managed' => "تحت الإدارة",'unmanaged' => "غير مُدار بواسطة ILM",'phase' => "المرحلة",'action' => "الإجراء",'step' => "الخطوة",'rollover_alias' => "اسم Rollover",'age' => "العمر",'failed_step' => "الخطوة الفاشلة",'export_csv'=>'تصدير CSV','export_json'=>'تصدير JSON','export_empty'=>'لا توجد فهارس للتصدير.'],
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
        .toolbar{padding:18px}.table thead th{font-size:.75rem;color:#667085;font-weight:650;white-space:nowrap}.table td{vertical-align:middle;font-size:.84rem}.sort-button{border:0;background:transparent;color:inherit;font:inherit;font-weight:inherit;padding:0;white-space:nowrap}.sort-button:hover,.sort-button:focus{color:#1759a6}.sort-button span{font-size:.72rem;margin-inline-start:3px;color:#98a2b3}.sort-button[aria-pressed=true] span{color:#1759a6}
        .index-name{font-weight:600;color:#1759a6;text-decoration:none;word-break:break-word}.health-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-inline-end:7px;background:#98a2b3}.health-green{background:#16845b}.health-yellow{background:#d69e2e}.health-red{background:#dc4545}
        .json-view{background:#101828;color:#d7e1f0;border-radius:10px;padding:18px;max-height:65vh;overflow:auto;font-size:.78rem;white-space:pre-wrap;word-break:break-word}
        .nav-tabs .nav-link{font-weight:600;font-size:.88rem}.table-responsive{min-height:180px}.favorite-toggle{font-size:1.15rem;line-height:1;text-decoration:none;color:#d69e2e}.favorite-toggle:hover{color:#b7791f}
    </style>
</head>
<body>
<main class="container-fluid page py-4 px-3 px-lg-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div><div class="d-flex align-items-center gap-2 mb-2"><a href="{{ route('elasticsearch-monitoring.index', ['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">← {{ $text['back'] }}</a><a href="{{ route('elasticsearch-monitoring.data-streams', ['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">Data Streams</a><a href="{{ route('elasticsearch-monitoring.nodes',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">{{ $locale === 'tr' ? 'Node Geçmişi' : 'Node History' }}</a>
<a href="{{ route('elasticsearch-monitoring.alerts', ['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">{{ $locale === 'tr' ? 'Alarm Geçmişi' : 'Alert History' }}</a></div><h1 class="h3 fw-bold mb-1">{{ $text['title'] }}</h1><p class="text-secondary mb-0">{{ $text['description'] }}</p></div>
        <div class="d-flex gap-2 flex-wrap"><button class="btn btn-outline-success" id="export-csv">↓ {{ $text['export_csv'] }}</button><button class="btn btn-outline-primary" id="export-json">↓ {{ $text['export_json'] }}</button><button class="btn btn-success" id="reload">↻ {{ $text['reload'] }}</button></div>
    </div>
    <div class="panel toolbar mb-3">
        <div class="row g-3 align-items-center">
            <div class="col-lg-5"><input type="search" id="search" class="form-control" placeholder="⌕ {{ $text['search'] }}"></div>
            <div class="col-lg-auto"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="hidden"><label class="form-check-label small" for="hidden">{{ $text['hidden'] }}</label></div></div>
            <div class="col-lg-auto"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="rollup"><label class="form-check-label small" for="rollup">{{ $text['rollup'] }}</label></div></div>
            <div class="col-lg-auto"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="favorites-only"><label class="form-check-label small" for="favorites-only">{{ $text['favorites_only'] }}</label></div></div>
            <div class="col text-lg-end"><span class="small text-secondary" id="count">—</span></div>
        </div>
    </div>
    <div class="panel table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th class="ps-3" aria-sort="none"><button type="button" class="sort-button" data-sort="name">{{ $text['name'] }} <span aria-hidden="true">↕</span></button></th><th aria-sort="none"><button type="button" class="sort-button" data-sort="health">{{ $text['health'] }} <span aria-hidden="true">↕</span></button></th><th>{{ $text['status'] }}</th><th>{{ $text['lifecycle_status'] }}</th><th>{{ $text['lifecycle_phase'] }}</th><th>{{ $text['policy'] }}</th><th aria-sort="none"><button type="button" class="sort-button" data-sort="primaries">{{ $text['primaries'] }} <span aria-hidden="true">↕</span></button></th><th aria-sort="none"><button type="button" class="sort-button" data-sort="replicas">{{ $text['replicas'] }} <span aria-hidden="true">↕</span></button></th><th class="text-end" aria-sort="none"><button type="button" class="sort-button" data-sort="documents_count">{{ $text['docs'] }} <span aria-hidden="true">↕</span></button></th><th class="text-end" aria-sort="none"><button type="button" class="sort-button" data-sort="storage_bytes">{{ $text['storage'] }} <span aria-hidden="true">↕</span></button></th><th>{{ $text['stream'] }}</th><th class="text-end pe-3">{{ $text['actions'] }}</th></tr></thead>
            <tbody id="indices"><tr><td colspan="12" class="text-center text-secondary py-5">{{ $text['loading'] }}</td></tr></tbody>
        </table>
    </div>
</main>
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content border-0 rounded-4">
        <div class="modal-header"><div><h5 class="modal-title fw-bold" id="modalTitle">{{ $text['details'] }}</h5><div class="small text-secondary" id="lifecycle"></div></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ $text['close'] }}"></button></div>
        <div class="modal-body">
            <ul class="nav nav-tabs mb-3"><li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#mapping-tab">{{ $text['mapping'] }}</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#settings-tab">{{ $text['settings'] }}</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lifecycle-tab">{{ $text['lifecycle'] }}</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#indexing-tab">{{ $locale === 'tr' ? 'İndeksleme istatistikleri' : 'Indexing statistics' }}</button></li></ul>
            <div class="tab-content"><div class="tab-pane fade show active" id="mapping-tab"><div class="d-flex flex-wrap align-items-center gap-2 mb-2"><button type="button" class="btn btn-sm btn-outline-primary" id="copy-mapping-all"></button></div><div class="d-flex flex-wrap gap-2 mb-3" id="mapping-field-actions"></div><pre class="json-view" id="mapping-json">{{ $text['loading'] }}</pre></div><div class="tab-pane fade" id="settings-tab"><pre class="json-view" id="settings-json">{{ $text['loading'] }}</pre></div><div class="tab-pane fade" id="lifecycle-tab"><div id="lifecycle-summary" class="row g-3 mb-3"></div><pre class="json-view" id="lifecycle-json">{{ $text['loading'] }}</pre></div><div class="tab-pane fade" id="indexing-tab"><div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"><p class="small text-secondary mb-0" id="indexing-description"></p><button class="btn btn-sm btn-outline-primary" id="refresh-indexing"></button></div><div class="row g-3 mb-3" id="indexing-cards"></div><div class="table-responsive"><table class="table table-sm table-bordered align-middle"><thead><tr><th id="indexing-metric-heading"></th><th id="indexing-primary-heading"></th><th id="indexing-total-heading"></th></tr></thead><tbody id="indexing-table"><tr><td colspan="3" class="text-center text-secondary py-4">{{ $text['loading'] }}</td></tr></tbody></table></div></div></div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">{{ $text['close'] }}</button></div>
    </div></div>
</div>
<div id="copy-toast" class="copy-toast" role="status" aria-live="polite"></div>
<style>.copy-toast{position:fixed;bottom:24px;left:50%;transform:translate(-50%,16px);background:#182238;color:#fff;padding:10px 16px;border-radius:9px;opacity:0;pointer-events:none;transition:.2s;z-index:2000}.copy-toast.show{opacity:1;transform:translate(-50%,0)}</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const labels=@json($text);
const copyLabels={"tr":{"copy":"Kopyala","all":"Tüm mapping'i kopyala","field":"Alanı kopyala","done":"Panoya kopyalandı","fail":"Kopyalama başarısız"},"en":{"copy":"Copy","all":"Copy entire mapping","field":"Copy field","done":"Copied to clipboard","fail":"Copy failed"},"es":{"copy":"Copiar","all":"Copiar todo el mapping","field":"Copiar campo","done":"Copiado al portapapeles","fail":"Error al copiar"},"zh_CN":{"copy":"复制","all":"复制完整 mapping","field":"复制字段","done":"已复制到剪贴板","fail":"复制失败"},"hi":{"copy":"कॉपी करें","all":"पूरा mapping कॉपी करें","field":"फ़ील्ड कॉपी करें","done":"क्लिपबोर्ड पर कॉपी किया गया","fail":"कॉपी विफल"},"ar":{"copy":"نسخ","all":"نسخ mapping بالكامل","field":"نسخ الحقل","done":"تم النسخ إلى الحافظة","fail":"فشل النسخ"}}[@json($locale)] || {"copy":"Copy","all":"Copy entire mapping","field":"Copy field","done":"Copied to clipboard","fail":"Copy failed"};
function showCopyToast(message){const el=document.getElementById('copy-toast');if(!el)return;el.textContent=message;el.classList.add('show');clearTimeout(window.__copyToastTimer);window.__copyToastTimer=setTimeout(()=>el.classList.remove('show'),1800);}
async function copyText(value){try{if(navigator.clipboard&&window.isSecureContext)await navigator.clipboard.writeText(String(value));else{const el=document.createElement('textarea');el.value=String(value);el.style.position='fixed';el.style.opacity='0';document.body.append(el);el.select();const ok=document.execCommand('copy');el.remove();if(!ok)throw new Error('Clipboard unavailable');}showCopyToast(copyLabels.done);}catch(e){showCopyToast(copyLabels.fail);}}
document.addEventListener('click',event=>{const button=event.target.closest('[data-copy-value]');if(button){event.preventDefault();copyText(button.dataset.copyValue);}});

const statsLabels={"tr":{"tab":"Performans analizi","description":"Seçilen indeksin arama, yazma, merge, refresh, cache ve translog metrikleri. Sayaçlar indeks ömrü boyunca birikir.","refresh":"İstatistikleri yenile","indexTotal":"Toplam indeksleme","queryTotal":"Toplam arama sorgusu","mergeTotal":"Toplam merge","refreshTotal":"Toplam refresh","indexTime":"İndeksleme süresi","queryTime":"Arama süresi","metric":"Metrik","primaries":"Primary","total":"Toplam","yes":"Evet","no":"Hayır","error":"Performans istatistikleri yüklenemedi."},"en":{"tab":"Performance analysis","description":"Search, indexing, merge, refresh, cache and translog metrics for the selected index. Counters accumulate over the index lifetime.","refresh":"Refresh statistics","indexTotal":"Total indexing","queryTotal":"Total search queries","mergeTotal":"Total merges","refreshTotal":"Total refreshes","indexTime":"Indexing time","queryTime":"Search query time","metric":"Metric","primaries":"Primaries","total":"Total","yes":"Yes","no":"No","error":"Unable to load performance statistics."},"es":{"tab":"Análisis de rendimiento","description":"Métricas de búsqueda, indexación, merge, refresh, caché y translog del índice seleccionado. Los contadores se acumulan durante la vida del índice.","refresh":"Actualizar estadísticas","indexTotal":"Indexaciones totales","queryTotal":"Consultas totales","mergeTotal":"Fusiones totales","refreshTotal":"Actualizaciones totales","indexTime":"Tiempo de indexación","queryTime":"Tiempo de búsqueda","metric":"Métrica","primaries":"Primarios","total":"Total","yes":"Sí","no":"No","error":"No se pudieron cargar las métricas."},"zh_CN":{"tab":"性能分析","description":"所选索引的搜索、索引、合并、刷新、缓存和 translog 指标。计数器在索引生命周期内累计。","refresh":"刷新统计","indexTotal":"索引总数","queryTotal":"搜索查询总数","mergeTotal":"合并总数","refreshTotal":"刷新总数","indexTime":"索引耗时","queryTime":"搜索耗时","metric":"指标","primaries":"主分片","total":"总计","yes":"是","no":"否","error":"无法加载性能统计。"},"hi":{"tab":"प्रदर्शन विश्लेषण","description":"चयनित इंडेक्स के खोज, इंडेक्सिंग, मर्ज, रिफ्रेश, कैश और ट्रांसलॉग मेट्रिक्स। काउंटर इंडेक्स के जीवनकाल में बढ़ते हैं।","refresh":"आँकड़े रीफ़्रेश करें","indexTotal":"कुल इंडेक्सिंग","queryTotal":"कुल खोज क्वेरी","mergeTotal":"कुल मर्ज","refreshTotal":"कुल रिफ्रेश","indexTime":"इंडेक्सिंग समय","queryTime":"खोज समय","metric":"मेट्रिक","primaries":"प्राइमरी","total":"कुल","yes":"हाँ","no":"नहीं","error":"प्रदर्शन आँकड़े लोड नहीं हुए।"},"ar":{"tab":"تحليل الأداء","description":"مقاييس البحث والفهرسة والدمج والتحديث والذاكرة المؤقتة وسجل المعاملات للفهرس المحدد.","refresh":"تحديث الإحصاءات","indexTotal":"إجمالي الفهرسة","queryTotal":"استعلامات البحث","mergeTotal":"إجمالي الدمج","refreshTotal":"إجمالي التحديث","indexTime":"وقت الفهرسة","queryTime":"وقت البحث","metric":"المقياس","primaries":"الأجزاء الأساسية","total":"الإجمالي","yes":"نعم","no":"لا","error":"تعذر تحميل إحصاءات الأداء."}}[@json($locale)] || {"tab":"Performance analysis","description":"Search, indexing, merge, refresh, cache and translog metrics for the selected index. Counters accumulate over the index lifetime.","refresh":"Refresh statistics","indexTotal":"Total indexing","queryTotal":"Total search queries","mergeTotal":"Total merges","refreshTotal":"Total refreshes","indexTime":"Indexing time","queryTime":"Search query time","metric":"Metric","primaries":"Primaries","total":"Total","yes":"Yes","no":"No","error":"Unable to load performance statistics."};
const metricNames={tr:{indexTotal:'Toplam indeksleme',indexTime:'İndeksleme süresi',indexCurrent:'Devam eden indeksleme',deleteTotal:'Toplam silme',deleteTime:'Silme süresi',deleteCurrent:'Devam eden silme',noop:'No-op güncelleme',throttle:'Throttle durumu',throttleTime:'Throttle süresi',queryTotal:'Arama sorguları',queryTime:'Arama sorgusu süresi',queryCurrent:'Devam eden sorgu',fetchTotal:'Fetch işlemleri',fetchTime:'Fetch süresi',fetchCurrent:'Devam eden fetch',mergeTotal:'Toplam merge',mergeCurrent:'Devam eden merge',mergedDocs:'Birleştirilen belgeler',mergedSize:'Birleştirilen boyut',mergeTime:'Merge süresi',mergeThrottle:'Merge throttle süresi',refreshTotal:'Toplam refresh',refreshTime:'Refresh süresi',flushTotal:'Toplam flush',flushTime:'Flush süresi',queryHits:'Query cache isabeti',queryMisses:'Query cache ıskalama',queryEvictions:'Query cache çıkarma',requestHits:'Request cache isabeti',requestMisses:'Request cache ıskalama',requestEvictions:'Request cache çıkarma',segments:'Segment sayısı',segmentMemory:'Segment belleği',translogOps:'Translog işlemleri',translogSize:'Translog boyutu',uncommittedOps:'Commit edilmemiş translog işlemleri',uncommittedSize:'Commit edilmemiş translog boyutu'},en:{indexTotal:'Total indexing',indexTime:'Indexing time',indexCurrent:'Current indexing',deleteTotal:'Total deletes',deleteTime:'Delete time',deleteCurrent:'Current deletes',noop:'No-op updates',throttle:'Throttled',throttleTime:'Throttle time',queryTotal:'Search queries',queryTime:'Search query time',queryCurrent:'Current queries',fetchTotal:'Fetches',fetchTime:'Fetch time',fetchCurrent:'Current fetches',mergeTotal:'Total merges',mergeCurrent:'Current merges',mergedDocs:'Merged documents',mergedSize:'Merged size',mergeTime:'Merge time',mergeThrottle:'Merge throttle time',refreshTotal:'Total refreshes',refreshTime:'Refresh time',flushTotal:'Total flushes',flushTime:'Flush time',queryHits:'Query cache hits',queryMisses:'Query cache misses',queryEvictions:'Query cache evictions',requestHits:'Request cache hits',requestMisses:'Request cache misses',requestEvictions:'Request cache evictions',segments:'Segment count',segmentMemory:'Segment memory',translogOps:'Translog operations',translogSize:'Translog size',uncommittedOps:'Uncommitted translog operations',uncommittedSize:'Uncommitted translog size'},es:{indexTotal:'Indexaciones totales',indexTime:'Tiempo de indexación',indexCurrent:'Indexaciones actuales',deleteTotal:'Eliminaciones totales',deleteTime:'Tiempo de eliminación',deleteCurrent:'Eliminaciones actuales',noop:'Actualizaciones no-op',throttle:'Limitación',throttleTime:'Tiempo de limitación',queryTotal:'Consultas de búsqueda',queryTime:'Tiempo de consulta',queryCurrent:'Consultas actuales',fetchTotal:'Operaciones fetch',fetchTime:'Tiempo fetch',fetchCurrent:'Fetch actuales',mergeTotal:'Fusiones totales',mergeCurrent:'Fusiones actuales',mergedDocs:'Documentos fusionados',mergedSize:'Tamaño fusionado',mergeTime:'Tiempo de fusión',mergeThrottle:'Tiempo de limitación merge',refreshTotal:'Refresh totales',refreshTime:'Tiempo refresh',flushTotal:'Flush totales',flushTime:'Tiempo flush',queryHits:'Aciertos query cache',queryMisses:'Fallos query cache',queryEvictions:'Expulsiones query cache',requestHits:'Aciertos request cache',requestMisses:'Fallos request cache',requestEvictions:'Expulsiones request cache',segments:'Cantidad de segmentos',segmentMemory:'Memoria de segmentos',translogOps:'Operaciones translog',translogSize:'Tamaño translog',uncommittedOps:'Operaciones translog sin confirmar',uncommittedSize:'Tamaño translog sin confirmar'},zh_CN:{indexTotal:'索引操作总数',indexTime:'索引耗时',indexCurrent:'当前索引操作',deleteTotal:'删除总数',deleteTime:'删除耗时',deleteCurrent:'当前删除操作',noop:'无操作更新',throttle:'限流状态',throttleTime:'限流时间',queryTotal:'搜索查询',queryTime:'查询耗时',queryCurrent:'当前查询',fetchTotal:'Fetch 次数',fetchTime:'Fetch 耗时',fetchCurrent:'当前 Fetch',mergeTotal:'合并总数',mergeCurrent:'当前合并',mergedDocs:'已合并文档',mergedSize:'合并数据大小',mergeTime:'合并耗时',mergeThrottle:'合并限流时间',refreshTotal:'刷新总数',refreshTime:'刷新耗时',flushTotal:'Flush 总数',flushTime:'Flush 耗时',queryHits:'查询缓存命中',queryMisses:'查询缓存未命中',queryEvictions:'查询缓存驱逐',requestHits:'请求缓存命中',requestMisses:'请求缓存未命中',requestEvictions:'请求缓存驱逐',segments:'段数量',segmentMemory:'段内存',translogOps:'Translog 操作',translogSize:'Translog 大小',uncommittedOps:'未提交 Translog 操作',uncommittedSize:'未提交 Translog 大小'},hi:{indexTotal:'कुल इंडेक्सिंग',indexTime:'इंडेक्सिंग समय',indexCurrent:'वर्तमान इंडेक्सिंग',deleteTotal:'कुल डिलीट',deleteTime:'डिलीट समय',deleteCurrent:'वर्तमान डिलीट',noop:'No-op अपडेट',throttle:'थ्रॉटल स्थिति',throttleTime:'थ्रॉटल समय',queryTotal:'खोज क्वेरी',queryTime:'खोज समय',queryCurrent:'वर्तमान क्वेरी',fetchTotal:'Fetch ऑपरेशन',fetchTime:'Fetch समय',fetchCurrent:'वर्तमान Fetch',mergeTotal:'कुल मर्ज',mergeCurrent:'वर्तमान मर्ज',mergedDocs:'मर्ज किए गए दस्तावेज़',mergedSize:'मर्ज आकार',mergeTime:'मर्ज समय',mergeThrottle:'मर्ज थ्रॉटल समय',refreshTotal:'कुल रिफ्रेश',refreshTime:'रिफ्रेश समय',flushTotal:'कुल Flush',flushTime:'Flush समय',queryHits:'Query cache हिट',queryMisses:'Query cache मिस',queryEvictions:'Query cache हटाना',requestHits:'Request cache हिट',requestMisses:'Request cache मिस',requestEvictions:'Request cache हटाना',segments:'सेगमेंट संख्या',segmentMemory:'सेगमेंट मेमोरी',translogOps:'Translog ऑपरेशन',translogSize:'Translog आकार',uncommittedOps:'अपूर्ण Translog ऑपरेशन',uncommittedSize:'अपूर्ण Translog आकार'},ar:{indexTotal:'إجمالي الفهرسة',indexTime:'وقت الفهرسة',indexCurrent:'الفهرسة الحالية',deleteTotal:'إجمالي الحذف',deleteTime:'وقت الحذف',deleteCurrent:'الحذف الحالي',noop:'تحديثات بلا تغيير',throttle:'حالة التقييد',throttleTime:'وقت التقييد',queryTotal:'استعلامات البحث',queryTime:'وقت البحث',queryCurrent:'الاستعلامات الحالية',fetchTotal:'عمليات الجلب',fetchTime:'وقت الجلب',fetchCurrent:'عمليات الجلب الحالية',mergeTotal:'إجمالي الدمج',mergeCurrent:'عمليات الدمج الحالية',mergedDocs:'المستندات المدمجة',mergedSize:'حجم البيانات المدمجة',mergeTime:'وقت الدمج',mergeThrottle:'وقت تقييد الدمج',refreshTotal:'إجمالي التحديث',refreshTime:'وقت التحديث',flushTotal:'إجمالي Flush',flushTime:'وقت Flush',queryHits:'إصابات ذاكرة الاستعلام',queryMisses:'إخفاقات ذاكرة الاستعلام',queryEvictions:'إخلاء ذاكرة الاستعلام',requestHits:'إصابات ذاكرة الطلب',requestMisses:'إخفاقات ذاكرة الطلب',requestEvictions:'إخلاء ذاكرة الطلب',segments:'عدد المقاطع',segmentMemory:'ذاكرة المقاطع',translogOps:'عمليات سجل المعاملات',translogSize:'حجم سجل المعاملات',uncommittedOps:'عمليات سجل غير معتمدة',uncommittedSize:'حجم سجل غير معتمد'}};
const metricLabel=key=>(metricNames[@json($locale)]||metricNames.en)[key]||key;
document.querySelector('[data-bs-target="#indexing-tab"]').textContent=statsLabels.tab;
document.getElementById('indexing-description').textContent=statsLabels.description;
document.getElementById('refresh-indexing').textContent='↻ '+statsLabels.refresh;
document.getElementById('indexing-metric-heading').textContent=statsLabels.metric;
document.getElementById('indexing-primary-heading').textContent=statsLabels.primaries;
document.getElementById('indexing-total-heading').textContent=statsLabels.total;
const listUrl=@json(route('elasticsearch-monitoring.indices.data'));
const detailBase=@json(route('elasticsearch-monitoring.indices.details',['index'=>'__INDEX__']));
const indexingStatsBase=@json(route('elasticsearch-monitoring.indices.indexing-stats',['index'=>'__INDEX__']));
const rows=document.getElementById('indices');
const favoritesStorageKey='elasticsearch-monitoring:favorites';
let favorites=new Set(readFavorites());
let allIndices=[];
let selectedIndex=null;
function readFavorites(){
    try{const value=JSON.parse(localStorage.getItem(favoritesStorageKey)||'[]');return Array.isArray(value)?value.filter(name=>typeof name==='string'):[];}
    catch(error){return [];}
}
function saveFavorites(){
    try{localStorage.setItem(favoritesStorageKey,JSON.stringify([...favorites].sort()));}
    catch(error){console.warn('Unable to save favorite indices in this browser.',error);}
}
function toggleFavorite(name){
    if(favorites.has(name))favorites.delete(name);else favorites.add(name);
    saveFavorites();
    render();
}
let indexingRefreshTimer=null;
const modal=new bootstrap.Modal(document.getElementById('detailsModal'));
const number=v=>Number(v||0).toLocaleString();
const bytes=value=>{let n=Number(value||0);if(n>=1024**3)return (n/1024**3).toFixed(2)+' GB';if(n>=1024**2)return (n/1024**2).toFixed(2)+' MB';if(n>=1024)return (n/1024).toFixed(2)+' KB';return n+' B';};
function getFilteredIndices(){
    const q=document.getElementById('search').value.toLowerCase();
    const onlyFavorites=document.getElementById('favorites-only').checked;
    return allIndices.filter(x=>x.name.toLowerCase().includes(q)&&(!onlyFavorites||favorites.has(x.name)));
}
let sortKey=null;
let sortDirection='asc';
const healthOrder={green:0,yellow:1,red:2};
function getSortedIndices(){
    const indices=getFilteredIndices();
    if(!sortKey)return indices;
    return indices.sort((a,b)=>{
        let comparison=0;
        if(sortKey==='health'){
            comparison=(healthOrder[String(a.health||'').toLowerCase()]??3)-(healthOrder[String(b.health||'').toLowerCase()]??3);
        }else if(sortKey==='name'){
            comparison=String(a.name||'').localeCompare(String(b.name||''),undefined,{numeric:true,sensitivity:'base'});
        }else{
            comparison=Number(a[sortKey]||0)-Number(b[sortKey]||0);
        }
        if(comparison===0)comparison=String(a.name||'').localeCompare(String(b.name||''),undefined,{numeric:true,sensitivity:'base'});
        return sortDirection==='asc'?comparison:-comparison;
    });
}
function updateSortIndicators(){
    document.querySelectorAll('[data-sort]').forEach(button=>{
        const active=button.dataset.sort===sortKey;
        button.setAttribute('aria-pressed',active?'true':'false');
        button.querySelector('span').textContent=active?(sortDirection==='asc'?'▲':'▼'):'↕';
        const th=button.closest('th');
        if(th)th.setAttribute('aria-sort',active?(sortDirection==='asc'?'ascending':'descending'):'none');
    });
}
function downloadExport(content,mimeType,extension){
    const blob=new Blob([content],{type:mimeType});
    const url=URL.createObjectURL(blob);
    const link=document.createElement('a');
    link.href=url;
    link.download='elasticsearch-indices-'+new Date().toISOString().slice(0,10)+'.'+extension;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
}
function exportIndices(format){
    const filtered=getSortedIndices();
    if(!filtered.length){alert(labels.export_empty);return;}
    const fields=['name','health','status','documents_count','storage_bytes','primaries','replicas','creation_date','data_stream','lifecycle_status','lifecycle_phase','lifecycle_policy'];
    if(format==='json'){
        const payload={exported_at:new Date().toISOString(),count:filtered.length,indices:filtered.map(index=>Object.fromEntries(fields.map(field=>[field,index[field]??null])))};
        downloadExport(JSON.stringify(payload,null,2),'application/json;charset=utf-8','json');
        return;
    }
    const csvCell=value=>'"'+String(value??'').replace(/"/g,'""')+'"';
    const csv=[fields.join(','),...filtered.map(index=>fields.map(field=>csvCell(index[field])).join(','))].join(String.fromCharCode(13,10));
    downloadExport(String.fromCharCode(0xFEFF)+csv,'text/csv;charset=utf-8','csv');
}
function render(){
    const filtered=getSortedIndices();
    document.getElementById('count').textContent=number(filtered.length)+' / '+number(allIndices.length)+' · '+favorites.size+' '+labels.favorite;
    if(!filtered.length){
        const emptyMessage=onlyFavorites&&favorites.size===0?labels.no_favorites:labels.empty;
        rows.innerHTML='<tr><td colspan="12" class="text-center text-secondary py-5">'+emptyMessage+'</td></tr>';
        return;
    }
    rows.innerHTML=filtered.map(x=>{
        const encoded=encodeURIComponent(x.name),isFavorite=favorites.has(x.name);
        const star=isFavorite?'★':'☆',action=isFavorite?labels.remove_favorite:labels.add_favorite;
        return '<tr><td class="ps-3"><button type="button" class="btn btn-sm btn-link p-0 me-2 favorite-toggle" data-favorite="'+encoded+'" aria-label="'+action+'" aria-pressed="'+(isFavorite?'true':'false')+'" title="'+action+'">'+star+'</button><a href="#" class="index-name" data-index="'+encoded+'">'+escapeHtml(x.name)+'</a> <button type="button" class="btn btn-sm btn-link p-0 text-secondary" data-copy-value="'+escapeHtml(x.name)+'" title="'+copyLabels.copy+'">⧉</button></td><td><span class="health-dot health-'+escapeHtml(x.health)+'"></span>'+escapeHtml(x.health)+'</td><td>'+escapeHtml(x.status)+'</td><td>'+escapeHtml(x.lifecycle_status||'—')+'</td><td>'+escapeHtml(x.lifecycle_phase||'—')+'</td><td>'+escapeHtml(x.lifecycle_policy||'—')+'</td><td>'+number(x.primaries)+'</td><td>'+number(x.replicas)+'</td><td class="text-end">'+number(x.documents_count)+'</td><td class="text-end">'+bytes(x.storage_bytes)+'</td><td>'+escapeHtml(x.data_stream||'—')+'</td><td class="text-end pe-3"><button class="btn btn-sm btn-outline-primary" data-index="'+encoded+'">'+labels.details+'</button></td></tr>';
    }).join('');
}
function escapeHtml(v){return String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
async function load(){
    rows.innerHTML='<tr><td colspan="12" class="text-center text-secondary py-5">'+labels.loading+'</td></tr>';
    const params=new URLSearchParams({include_hidden:document.getElementById('hidden').checked?'1':'0',include_rollup:document.getElementById('rollup').checked?'1':'0'});
    try{const r=await fetch(listUrl+'?'+params,{headers:{Accept:'application/json'}});const data=await r.json();if(!r.ok)throw new Error(data.error||labels.error);allIndices=data.indices||[];render();}
    catch(e){rows.innerHTML='<tr><td colspan="12" class="text-center text-danger py-5">'+escapeHtml(e.message||labels.error)+'</td></tr>';}
}

function renderMappingCopyActions(mapping){const root=document.getElementById('mapping-field-actions');root.replaceChildren();if(!mapping||typeof mapping!=='object'||Array.isArray(mapping))return;Object.entries(mapping).forEach(([key,value])=>{const button=document.createElement('button');button.type='button';button.className='btn btn-sm btn-outline-secondary';button.textContent=copyLabels.field+': '+key;button.dataset.copyValue=JSON.stringify(value,null,2);root.append(button);});}
function formatDuration(value){const ms=Number(value||0);if(ms<1000)return number(ms)+' ms';if(ms<60000)return (ms/1000).toFixed(2)+' s';return (ms/60000).toFixed(2)+' min';}
function statValue(source,path){return path.split('.').reduce((value,key)=>value&&value[key]!==undefined?value[key]:undefined,source);}
function renderIndexingStats(data){
 const primary=data.primaries||{},total=data.total||{};
 const cards=[[statsLabels.indexTotal,total.indexing?.index_total],[statsLabels.queryTotal,total.search?.query_total],[statsLabels.mergeTotal,total.merges?.total],[statsLabels.refreshTotal,total.refresh?.total],[statsLabels.indexTime,total.indexing?.index_time_in_millis,'duration'],[statsLabels.queryTime,total.search?.query_time_in_millis,'duration']];
 const container=document.getElementById('indexing-cards');container.replaceChildren();
 cards.forEach(([label,value,type])=>{const col=document.createElement('div');col.className='col-6 col-xl-4';const card=document.createElement('div');card.className='border rounded-3 p-3 h-100 bg-light';const heading=document.createElement('div');heading.className='small text-secondary mb-1';heading.textContent=label;const content=document.createElement('div');content.className='fs-5 fw-bold';content.textContent=value===undefined?'—':type==='duration'?formatDuration(value):number(value);card.append(heading,content);col.append(card);container.append(col);});
 const metrics=[['indexTotal','indexing.index_total','number'],['indexTime','indexing.index_time_in_millis','duration'],['indexCurrent','indexing.index_current','number'],['deleteTotal','indexing.delete_total','number'],['deleteTime','indexing.delete_time_in_millis','duration'],['deleteCurrent','indexing.delete_current','number'],['noop','indexing.noop_update_total','number'],['throttle','indexing.is_throttled','boolean'],['throttleTime','indexing.throttle_time_in_millis','duration'],['queryTotal','search.query_total','number'],['queryTime','search.query_time_in_millis','duration'],['queryCurrent','search.query_current','number'],['fetchTotal','search.fetch_total','number'],['fetchTime','search.fetch_time_in_millis','duration'],['fetchCurrent','search.fetch_current','number'],['mergeTotal','merges.total','number'],['mergeCurrent','merges.current','number'],['mergedDocs','merges.total_docs','number'],['mergedSize','merges.total_size_in_bytes','bytes'],['mergeTime','merges.total_time_in_millis','duration'],['mergeThrottle','merges.total_throttled_time_in_millis','duration'],['refreshTotal','refresh.total','number'],['refreshTime','refresh.total_time_in_millis','duration'],['flushTotal','flush.total','number'],['flushTime','flush.total_time_in_millis','duration'],['queryHits','query_cache.hit_count','number'],['queryMisses','query_cache.miss_count','number'],['queryEvictions','query_cache.evictions','number'],['requestHits','request_cache.hit_count','number'],['requestMisses','request_cache.miss_count','number'],['requestEvictions','request_cache.evictions','number'],['segments','segments.count','number'],['segmentMemory','segments.memory_in_bytes','bytes'],['translogOps','translog.operations','number'],['translogSize','translog.size_in_bytes','bytes'],['uncommittedOps','translog.uncommitted_operations','number'],['uncommittedSize','translog.uncommitted_size_in_bytes','bytes']];
 const format=(value,type)=>value===undefined||value===null?'—':type==='duration'?formatDuration(value):type==='bytes'?bytes(value):type==='boolean'?(value?statsLabels.yes:statsLabels.no):number(value);
 document.getElementById('indexing-table').innerHTML=metrics.map(([label,path,type])=>'<tr><td>'+escapeHtml(metricLabel(label))+'</td><td>'+escapeHtml(format(statValue(primary,path),type))+'</td><td>'+escapeHtml(format(statValue(total,path),type))+'</td></tr>').join('');
}
async function loadIndexingStats(name){
 selectedIndex=name;const cards=document.getElementById('indexing-cards'),table=document.getElementById('indexing-table');
 cards.innerHTML='<div class="col-12 text-secondary small">'+labels.loading+'</div>';table.innerHTML='<tr><td colspan="3" class="text-center text-secondary py-4">'+labels.loading+'</td></tr>';
 try{const url=indexingStatsBase.replace('__INDEX__',encodeURIComponent(name));const response=await fetch(url,{headers:{Accept:'application/json'}});const data=await response.json();if(!response.ok)throw new Error(data.error||statsLabels.error);renderIndexingStats(data);}
 catch(error){cards.innerHTML='<div class="col-12 text-danger small">'+escapeHtml(error.message)+'</div>';table.innerHTML='<tr><td colspan="3" class="text-center text-danger py-4">'+escapeHtml(error.message)+'</td></tr>';}
}
async function showDetails(name){
    document.getElementById('modalTitle').textContent=name;
    document.getElementById('lifecycle').textContent='';
    document.getElementById('mapping-json').textContent=labels.loading;
    document.getElementById('settings-json').textContent=labels.loading;
    document.getElementById('lifecycle-json').textContent=labels.loading;
    document.getElementById('lifecycle-summary').replaceChildren();
    modal.show();
    loadIndexingStats(name);
    try{
        const url=detailBase.replace('__INDEX__',encodeURIComponent(name));
        const r=await fetch(url,{headers:{Accept:'application/json'}});
        const data=await r.json();if(!r.ok)throw new Error(data.error||labels.error);
        document.getElementById('mapping-json').textContent=JSON.stringify(data.mapping,null,2);
        renderMappingCopyActions(data.mapping);
        document.getElementById('settings-json').textContent=JSON.stringify(data.settings,null,2);
        const lifecycle=data.lifecycle||{};
        document.getElementById('lifecycle').textContent=[lifecycle.managed?labels.managed:labels.unmanaged,lifecycle.policy,lifecycle.phase].filter(Boolean).join(' · ');
        const fields=[[labels.managed_status,lifecycle.managed?labels.managed:labels.unmanaged],[labels.policy,lifecycle.policy],[labels.phase,lifecycle.phase],[labels.action,lifecycle.action],[labels.step,lifecycle.step],[labels.rollover_alias,lifecycle.rollover_alias],[labels.age,lifecycle.age],[labels.failed_step,lifecycle.failed_step]];
        const summary=document.getElementById('lifecycle-summary');
        fields.forEach(([label,value])=>{if(value===null||value===undefined||value==='')return;const col=document.createElement('div');col.className='col-12 col-md-6 col-xl-4';const card=document.createElement('div');card.className='border rounded-3 p-3 h-100 bg-light';const heading=document.createElement('div');heading.className='small text-secondary mb-1';heading.textContent=label;const content=document.createElement('div');content.className='fw-semibold text-break';content.textContent=String(value);card.append(heading,content);col.append(card);summary.append(col);});
        document.getElementById('lifecycle-json').textContent=JSON.stringify(lifecycle.explain||{},null,2);
    }catch(e){document.getElementById('mapping-json').textContent=e.message;document.getElementById('settings-json').textContent=e.message;document.getElementById('lifecycle-json').textContent=e.message;}
}
rows.addEventListener('click',e=>{
    const favoriteButton=e.target.closest('[data-favorite]');
    if(favoriteButton){e.preventDefault();toggleFavorite(decodeURIComponent(favoriteButton.dataset.favorite));return;}
    const button=e.target.closest('[data-index]');
    if(!button)return;
    e.preventDefault();
    showDetails(decodeURIComponent(button.dataset.index));
});
document.getElementById('reload').addEventListener('click',load);
document.getElementById('export-csv').addEventListener('click',()=>exportIndices('csv'));
document.getElementById('export-json').addEventListener('click',()=>exportIndices('json'));
document.querySelector('thead').addEventListener('click',event=>{
    const button=event.target.closest('[data-sort]');
    if(!button)return;
    if(sortKey===button.dataset.sort)sortDirection=sortDirection==='asc'?'desc':'asc';
    else{sortKey=button.dataset.sort;sortDirection='asc';}
    updateSortIndicators();
    render();
});
document.getElementById('search').addEventListener('input',render);
document.getElementById('hidden').addEventListener('change',load);
document.getElementById('rollup').addEventListener('change',load);
document.getElementById('favorites-only').addEventListener('change',render);
const initialSearch=new URLSearchParams(window.location.search).get('search');
if(initialSearch)document.getElementById('search').value=initialSearch;
document.getElementById('copy-mapping-all').textContent=copyLabels.all;
document.getElementById('copy-mapping-all').addEventListener('click',()=>copyText(document.getElementById('mapping-json').textContent));
document.getElementById('refresh-indexing').addEventListener('click',()=>{if(selectedIndex)loadIndexingStats(selectedIndex);});
document.getElementById('detailsModal').addEventListener('shown.bs.modal',()=>{clearInterval(indexingRefreshTimer);indexingRefreshTimer=setInterval(()=>{if(selectedIndex&&document.getElementById('indexing-tab').classList.contains('active'))loadIndexingStats(selectedIndex);},30000);});
document.getElementById('detailsModal').addEventListener('hidden.bs.modal',()=>{selectedIndex=null;clearInterval(indexingRefreshTimer);});
updateSortIndicators();
load();
</script>
</body>
</html>
