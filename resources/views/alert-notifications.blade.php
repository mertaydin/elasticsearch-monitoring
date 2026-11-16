@php
    $availableLocales = config('elasticsearch-monitoring.dashboard.available_locales', ['tr', 'en']);
    $requestedLocale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', config('elasticsearch-monitoring.dashboard.locale', 'en')));
    $locale = in_array($requestedLocale, $availableLocales, true) ? $requestedLocale : 'en';
    $text = [
        'tr' => ['title'=>'Bildirim Geçmişi','description'=>'E-posta, Slack ve Microsoft Teams bildirimlerinin gönderim kayıtları.','back'=>'Alarm geçmişi','refresh'=>'Yenile','sent'=>'Gönderildi','failed'=>'Başarısız','suppressed'=>'Bekletildi','pending'=>'Bekliyor','time'=>'Zaman','alert'=>'Alarm','channel'=>'Kanal','status'=>'Durum','attempts'=>'Deneme','error'=>'Hata','empty'=>'Henüz bildirim kaydı yok.','loading'=>'Yükleniyor...','load_error'=>'Bildirim geçmişi yüklenemedi.'],
        'en' => ['title'=>'Notification History','description'=>'Delivery records for email, Slack, and Microsoft Teams notifications.','back'=>'Alert history','refresh'=>'Refresh','sent'=>'Sent','failed'=>'Failed','suppressed'=>'Suppressed','pending'=>'Pending','time'=>'Time','alert'=>'Alert','channel'=>'Channel','status'=>'Status','attempts'=>'Attempts','error'=>'Error','empty'=>'No notification records yet.','loading'=>'Loading...','load_error'=>'Unable to load notification history.'],
        'es' => ['title'=>'Historial de notificaciones','description'=>'Registros de entrega de notificaciones por correo, Slack y Microsoft Teams.','back'=>'Alertas','refresh'=>'Actualizar','sent'=>'Enviada','failed'=>'Fallida','suppressed'=>'Omitida','pending'=>'Pendiente','time'=>'Fecha','alert'=>'Alerta','channel'=>'Canal','status'=>'Estado','attempts'=>'Intentos','error'=>'Error','empty'=>'Todavía no hay notificaciones.','loading'=>'Cargando...','load_error'=>'No se pudo cargar el historial.'],
        'zh_CN' => ['title'=>'通知历史','description'=>'电子邮件、Slack 和 Microsoft Teams 通知发送记录。','back'=>'告警历史','refresh'=>'刷新','sent'=>'已发送','failed'=>'失败','suppressed'=>'已抑制','pending'=>'待处理','time'=>'时间','alert'=>'告警','channel'=>'渠道','status'=>'状态','attempts'=>'尝试次数','error'=>'错误','empty'=>'暂无通知记录。','loading'=>'加载中...','load_error'=>'无法加载通知历史。'],
        'hi' => ['title'=>'सूचना इतिहास','description'=>'ईमेल, Slack और Microsoft Teams सूचनाओं के वितरण रिकॉर्ड।','back'=>'अलर्ट इतिहास','refresh'=>'रीफ़्रेश','sent'=>'भेजा गया','failed'=>'विफल','suppressed'=>'रोका गया','pending'=>'लंबित','time'=>'समय','alert'=>'अलर्ट','channel'=>'चैनल','status'=>'स्थिति','attempts'=>'प्रयास','error'=>'त्रुटि','empty'=>'अभी तक कोई सूचना रिकॉर्ड नहीं।','loading'=>'लोड हो रहा है...','load_error'=>'सूचना इतिहास लोड नहीं हुआ।'],
        'ar' => ['title'=>'سجل الإشعارات','description'=>'سجلات تسليم إشعارات البريد الإلكتروني وSlack وMicrosoft Teams.','back'=>'سجل التنبيهات','refresh'=>'تحديث','sent'=>'تم الإرسال','failed'=>'فشل','suppressed'=>'تم التجاهل','pending'=>'قيد الانتظار','time'=>'الوقت','alert'=>'التنبيه','channel'=>'القناة','status'=>'الحالة','attempts'=>'المحاولات','error'=>'الخطأ','empty'=>'لا توجد سجلات إشعارات بعد.','loading'=>'جار التحميل...','load_error'=>'تعذر تحميل سجل الإشعارات.'],
    ][$locale];
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $text['title'] }}</title>
@if ($locale === 'ar')<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">@else<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">@endif
<style>body{background:#f4f6fb;color:#182238;font-family:Inter,system-ui,sans-serif}.page{max-width:1500px}.panel{background:#fff;border:1px solid #e9edf4;border-radius:16px;box-shadow:0 5px 18px rgba(29,41,74,.035)}.summary{font-size:1.65rem;font-weight:750}.muted{color:#8490a4}.table thead th{font-size:.72rem;text-transform:uppercase;letter-spacing:.035em;color:#8791a5;white-space:nowrap}.table tbody td{font-size:.82rem;vertical-align:middle}</style>
</head>
<body>
<main class="container-fluid page py-4 px-3 px-lg-4">
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
<div><div class="d-flex gap-2 mb-2"><a href="{{ route('elasticsearch-monitoring.alerts',['locale'=>$locale]) }}" class="btn btn-sm btn-outline-secondary">← {{ $text['back'] }}</a></div><h1 class="h3 fw-bold mb-1">{{ $text['title'] }}</h1><p class="text-secondary mb-0">{{ $text['description'] }}</p></div>
<button class="btn btn-success" id="refresh">↻ {{ $text['refresh'] }}</button>
</div>
<div class="row g-3 mb-3">
@foreach (['sent','failed','suppressed','pending'] as $key)
<div class="col-sm-6 col-xl-3"><div class="panel p-3"><div class="small muted">{{ $text[$key] }}</div><div class="summary" id="{{ $key }}-count">—</div></div></div>
@endforeach
</div>
<div class="panel p-3"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>{{ $text['time'] }}</th><th>{{ $text['alert'] }}</th><th>{{ $text['channel'] }}</th><th>{{ $text['status'] }}</th><th>{{ $text['attempts'] }}</th><th>{{ $text['error'] }}</th></tr></thead>
<tbody id="deliveries"><tr><td colspan="6" class="text-center py-5 text-secondary">{{ $text['loading'] }}</td></tr></tbody>
</table></div></div>
</main>
<script>
const endpoint=@json(route('elasticsearch-monitoring.alerts.notifications.data',['locale'=>$locale]));
const labels=@json($text);
const safe=value=>String(value??'—').replace(/[&<>"']/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
const fmt=value=>value?new Intl.DateTimeFormat(document.documentElement.lang,{dateStyle:'medium',timeStyle:'short'}).format(new Date(value)):'—';
async function load(){
 const body=document.getElementById('deliveries');body.innerHTML='<tr><td colspan="6" class="text-center py-5 text-secondary">'+labels.loading+'</td></tr>';
 try{
  const response=await fetch(endpoint,{headers:{Accept:'application/json'}});if(!response.ok)throw new Error('request failed');
  const payload=await response.json();
  Object.entries(payload.summary).forEach(([key,value])=>{const el=document.getElementById(key+'-count');if(el)el.textContent=value;});
  if(!payload.deliveries.length){body.innerHTML='<tr><td colspan="6" class="text-center py-5 text-secondary">'+labels.empty+'</td></tr>';return;}
  body.innerHTML=payload.deliveries.map(item=>'<tr><td class="text-nowrap">'+fmt(item.sent_at||item.created_at)+'</td><td><strong>'+safe(item.title)+'</strong><div class="small muted">'+safe(item.alert_code)+'</div></td><td>'+safe(item.channel)+'</td><td>'+safe(labels[item.status]||item.status)+'</td><td>'+item.attempts+'</td><td class="text-danger">'+safe(item.error)+'</td></tr>').join('');
 }catch(error){body.innerHTML='<tr><td colspan="6" class="text-center text-danger py-5">'+labels.load_error+'</td></tr>';}
}
document.getElementById('refresh').addEventListener('click',load);load();
</script>
    @include('elasticsearch-monitoring::partials.theme')
</body>
</html>
