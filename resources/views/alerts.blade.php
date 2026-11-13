@php
    $locale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', config('elasticsearch-monitoring.dashboard.locale', 'en')));
    $locale = in_array($locale, config('elasticsearch-monitoring.dashboard.available_locales', ['tr', 'en']), true) ? $locale : 'en';
    $text = [
        'tr' => ['title'=>'Alarm Geçmişi','description'=>'Küme alarmlarının başlangıç, tekrar ve çözülme kayıtları.','back'=>'İzleme paneli','indices'=>'İndeks yönetimi','streams'=>'Data Streams','all'=>'Tümü','active'=>'Aktif','resolved'=>'Çözüldü','severity'=>'Önem','event'=>'Alarm','first'=>'İlk görülme','last'=>'Son görülme','resolved_at'=>'Çözülme','occurrences'=>'Tekrar','value'=>'Değer','threshold'=>'Eşik','status'=>'Durum','loading'=>'Yükleniyor...','empty'=>'Bu filtrede alarm kaydı bulunamadı.','error'=>'Alarm geçmişi yüklenemedi.','warning'=>'Uyarı','critical'=>'Kritik','active_label'=>'Aktif','resolved_label'=>'Çözüldü','refresh'=>'Yenile'],
        'en' => ['title'=>'Alert History','description'=>'Cluster alert lifecycle, recurrence, and resolution records.','back'=>'Monitoring','indices'=>'Index management','streams'=>'Data Streams','all'=>'All','active'=>'Active','resolved'=>'Resolved','severity'=>'Severity','event'=>'Alert','first'=>'First seen','last'=>'Last seen','resolved_at'=>'Resolved at','occurrences'=>'Occurrences','value'=>'Value','threshold'=>'Threshold','status'=>'Status','loading'=>'Loading...','empty'=>'No alert records found for this filter.','error'=>'Unable to load alert history.','warning'=>'Warning','critical'=>'Critical','active_label'=>'Active','resolved_label'=>'Resolved','refresh'=>'Refresh'],
        'es' => ['title'=>'Historial de alertas','description'=>'Inicio, recurrencia y resolución de alertas del clúster.','back'=>'Panel','indices'=>'Índices','streams'=>'Data Streams','all'=>'Todas','active'=>'Activas','resolved'=>'Resueltas','severity'=>'Gravedad','event'=>'Alerta','first'=>'Primera vez','last'=>'Última vez','resolved_at'=>'Resuelta','occurrences'=>'Repeticiones','value'=>'Valor','threshold'=>'Umbral','status'=>'Estado','loading'=>'Cargando...','empty'=>'No hay alertas para este filtro.','error'=>'No se pudo cargar el historial.','warning'=>'Aviso','critical'=>'Crítica','active_label'=>'Activa','resolved_label'=>'Resuelta','refresh'=>'Actualizar'],
        'zh_CN' => ['title'=>'告警历史','description'=>'集群告警的开始、重复和解决记录。','back'=>'监控','indices'=>'索引管理','streams'=>'数据流','all'=>'全部','active'=>'活动','resolved'=>'已解决','severity'=>'严重性','event'=>'告警','first'=>'首次出现','last'=>'最近出现','resolved_at'=>'解决时间','occurrences'=>'次数','value'=>'数值','threshold'=>'阈值','status'=>'状态','loading'=>'加载中...','empty'=>'没有符合筛选条件的告警。','error'=>'无法加载告警历史。','warning'=>'警告','critical'=>'严重','active_label'=>'活动','resolved_label'=>'已解决','refresh'=>'刷新'],
        'hi' => ['title'=>'अलर्ट इतिहास','description'=>'क्लस्टर अलर्ट की शुरुआत, पुनरावृत्ति और समाधान रिकॉर्ड।','back'=>'मॉनिटरिंग','indices'=>'इंडेक्स प्रबंधन','streams'=>'Data Streams','all'=>'सभी','active'=>'सक्रिय','resolved'=>'हल किए गए','severity'=>'गंभीरता','event'=>'अलर्ट','first'=>'पहली बार','last'=>'आखिरी बार','resolved_at'=>'हल हुआ','occurrences'=>'बार','value'=>'मान','threshold'=>'सीमा','status'=>'स्थिति','loading'=>'लोड हो रहा है...','empty'=>'इस फ़िल्टर में कोई अलर्ट नहीं।','error'=>'अलर्ट इतिहास लोड नहीं हुआ।','warning'=>'चेतावनी','critical'=>'गंभीर','active_label'=>'सक्रिय','resolved_label'=>'हल किया गया','refresh'=>'रीफ़्रेश'],
        'ar' => ['title'=>'سجل التنبيهات','description'=>'سجل بداية تنبيهات المجموعة وتكرارها وحلها.','back'=>'المراقبة','indices'=>'إدارة الفهارس','streams'=>'تدفقات البيانات','all'=>'الكل','active'=>'نشطة','resolved'=>'تم حلها','severity'=>'الخطورة','event'=>'التنبيه','first'=>'أول ظهور','last'=>'آخر ظهور','resolved_at'=>'وقت الحل','occurrences'=>'التكرارات','value'=>'القيمة','threshold'=>'الحد','status'=>'الحالة','loading'=>'جار التحميل...','empty'=>'لا توجد تنبيهات لهذا المرشح.','error'=>'تعذر تحميل سجل التنبيهات.','warning'=>'تحذير','critical'=>'حرج','active_label'=>'نشط','resolved_label'=>'تم الحل','refresh'=>'تحديث'],
    ][$locale];
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $text['title'] }}</title>
@if ($locale === 'ar')<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">@else<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">@endif
<style>
body{background:#f4f6fb;color:#182238;font-family:Inter,system-ui,sans-serif}.page{max-width:1600px}.panel{background:#fff;border:1px solid #e9edf4;border-radius:16px;box-shadow:0 5px 18px rgba(29,41,74,.035)}.table thead th{font-size:.72rem;text-transform:uppercase;letter-spacing:.035em;color:#8791a5;white-space:nowrap}.table tbody td{font-size:.82rem;vertical-align:middle}.badge-severity{font-size:.7rem;padding:.38rem .55rem;border-radius:7px}.alert-title{font-weight:650}.muted{color:#8490a4}.summary{font-size:1.65rem;font-weight:750}
</style>
</head>
<body>
<main class="container-fluid page py-4 px-3 px-lg-4">
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
<div><div class="d-flex flex-wrap gap-2 mb-2">
<a href="{{ route('elasticsearch-monitoring.index',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">← {{ $text['back'] }}</a>
<a href="{{ route('elasticsearch-monitoring.indices',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">{{ $text['indices'] }}</a>
<a href="{{ route('elasticsearch-monitoring.data-streams',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">{{ $text['streams'] }}</a>
</div><h1 class="h3 fw-bold mb-1">{{ $text['title'] }}</h1><p class="text-secondary mb-0">{{ $text['description'] }}</p></div>
<button class="btn btn-success" id="refresh">↻ {{ $text['refresh'] }}</button>
</div>
<div class="row g-3 mb-3">
<div class="col-sm-4"><div class="panel p-3"><div class="small muted">{{ $text['active'] }}</div><div class="summary text-danger" id="active-count">—</div></div></div>
<div class="col-sm-4"><div class="panel p-3"><div class="small muted">{{ $text['resolved'] }}</div><div class="summary text-success" id="resolved-count">—</div></div></div>
<div class="col-sm-4"><div class="panel p-3"><div class="small muted">{{ $text['all'] }}</div><div class="summary" id="total-count">—</div></div></div>
</div>
<div class="panel p-3">
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
<div class="btn-group btn-group-sm" role="group">
<button class="btn btn-primary filter active" data-status="all">{{ $text['all'] }}</button>
<button class="btn btn-outline-primary filter" data-status="active">{{ $text['active'] }}</button>
<button class="btn btn-outline-primary filter" data-status="resolved">{{ $text['resolved'] }}</button>
</div><span class="small muted" id="updated"></span>
</div>
<div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>{{ $text['severity'] }}</th><th>{{ $text['event'] }}</th><th>{{ $text['status'] }}</th><th>{{ $text['first'] }}</th><th>{{ $text['last'] }}</th><th>{{ $text['resolved_at'] }}</th><th>{{ $text['occurrences'] }}</th><th>{{ $text['value'] }}</th><th>{{ $text['threshold'] }}</th></tr></thead>
<tbody id="events"><tr><td colspan="9" class="text-center py-5 text-secondary">{{ $text['loading'] }}</td></tr></tbody>
</table></div>
</div>
</main>
<script>
const endpoint=@json(route('elasticsearch-monitoring.alerts.data',['locale'=>$locale]));
const labels=@json($text);
let currentStatus='all';
const fmt=value=>value?new Intl.DateTimeFormat(document.documentElement.lang,{dateStyle:'medium',timeStyle:'short'}).format(new Date(value)):'—';
const safe=value=>String(value??'—').replace(/[&<>"']/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
const renderValue=value=>value===null||value===undefined?'—':(typeof value==='object'?safe(JSON.stringify(value)):safe(value));
async function loadEvents(){
 const body=document.getElementById('events');body.innerHTML='<tr><td colspan="9" class="text-center py-5 text-secondary">'+labels.loading+'</td></tr>';
 try{
  const response=await fetch(endpoint+'&status='+encodeURIComponent(currentStatus),{headers:{Accept:'application/json'}});
  if(!response.ok)throw new Error('request failed');
  const payload=await response.json();
  document.getElementById('active-count').textContent=payload.summary.active;
  document.getElementById('resolved-count').textContent=payload.summary.resolved;
  document.getElementById('total-count').textContent=payload.summary.total;
  document.getElementById('updated').textContent=fmt(new Date().toISOString());
  if(!payload.events.length){body.innerHTML='<tr><td colspan="9" class="text-center py-5 text-secondary">'+labels.empty+'</td></tr>';return;}
  body.innerHTML=payload.events.map(event=>'<tr>'+
   '<td><span class="badge-severity '+(event.severity==='critical'?'bg-danger':'bg-warning text-dark')+'">'+(event.severity==='critical'?labels.critical:labels.warning)+'</span></td>'+
   '<td><div class="alert-title">'+safe(event.title)+'</div><div class="small muted">'+safe(event.message)+'</div><code class="small">'+safe(event.code)+'</code></td>'+
   '<td><span class="badge '+(event.status==='active'?'text-bg-danger':'text-bg-success')+'">'+(event.status==='active'?labels.active_label:labels.resolved_label)+'</span></td>'+
   '<td class="text-nowrap">'+fmt(event.first_seen_at)+'</td><td class="text-nowrap">'+fmt(event.last_seen_at)+'</td><td class="text-nowrap">'+fmt(event.resolved_at)+'</td>'+
   '<td class="text-center">'+event.occurrences+'</td><td>'+renderValue(event.value)+'</td><td>'+renderValue(event.threshold)+'</td></tr>').join('');
 }catch(error){body.innerHTML='<tr><td colspan="9" class="text-center text-danger py-5">'+labels.error+'</td></tr>';}
}
document.querySelectorAll('.filter').forEach(button=>button.addEventListener('click',()=>{
 currentStatus=button.dataset.status;
 document.querySelectorAll('.filter').forEach(item=>{item.classList.toggle('active',item===button);item.classList.toggle('btn-primary',item===button);item.classList.toggle('btn-outline-primary',item!==button);});
 loadEvents();
}));
document.getElementById('refresh').addEventListener('click',loadEvents);
loadEvents();
</script>
</body>
</html>
