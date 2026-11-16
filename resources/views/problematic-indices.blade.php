@php
    $locale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', config('elasticsearch-monitoring.dashboard.locale', 'en')));
    $locale = in_array($locale, config('elasticsearch-monitoring.dashboard.available_locales', ['tr', 'en']), true) ? $locale : 'en';
    $labels = [
        'tr' => ['title'=>'Sorunlu indeksler','hint'=>'Küme sağlığı sarı veya kırmızı olan indeksler. Kırmızı indeksler önce gösterilir.','red'=>'Kırmızı','yellow'=>'Sarı','index'=>'İndeks','health'=>'Sağlık','documents'=>'Belge sayısı','storage'=>'Depolama','shards'=>'Primary / Replica','copy'=>'Teşhis özetini kopyala','details'=>'Detayları aç','empty'=>'Sorunlu indeks bulunamadı.','loading'=>'Sorunlu indeksler yükleniyor...','error'=>'Sorunlu indeksler yüklenemedi.','copied'=>'Teşhis özeti panoya kopyalandı.','copyFailed'=>'Kopyalama başarısız oldu.'],
        'en' => ['title'=>'Problematic indices','hint'=>'Indices with yellow or red cluster health. Red indices are shown first.','red'=>'Red','yellow'=>'Yellow','index'=>'Index','health'=>'Health','documents'=>'Documents','storage'=>'Storage','shards'=>'Primary / Replica','copy'=>'Copy diagnostic summary','details'=>'Open details','empty'=>'No problematic indices found.','loading'=>'Loading problematic indices...','error'=>'Unable to load problematic indices.','copied'=>'Diagnostic summary copied.','copyFailed'=>'Copy failed.'],
        'es' => ['title'=>'Índices problemáticos','hint'=>'Índices con estado amarillo o rojo. Los índices rojos aparecen primero.','red'=>'Rojo','yellow'=>'Amarillo','index'=>'Índice','health'=>'Salud','documents'=>'Documentos','storage'=>'Almacenamiento','shards'=>'Primarios / Réplicas','copy'=>'Copiar diagnóstico','details'=>'Ver detalles','empty'=>'No se encontraron índices problemáticos.','loading'=>'Cargando índices problemáticos...','error'=>'No se pudieron cargar los índices problemáticos.','copied'=>'Diagnóstico copiado.','copyFailed'=>'Error al copiar.'],
        'zh_CN' => ['title'=>'异常索引','hint'=>'集群健康状态为黄色或红色的索引，红色索引优先显示。','red'=>'红色','yellow'=>'黄色','index'=>'索引','health'=>'健康状态','documents'=>'文档数','storage'=>'存储','shards'=>'主分片 / 副本','copy'=>'复制诊断摘要','details'=>'查看详情','empty'=>'未发现异常索引。','loading'=>'正在加载异常索引...','error'=>'无法加载异常索引。','copied'=>'诊断摘要已复制。','copyFailed'=>'复制失败。'],
        'hi' => ['title'=>'समस्या वाले इंडेक्स','hint'=>'पीले या लाल स्वास्थ्य स्थिति वाले इंडेक्स। लाल इंडेक्स पहले दिखाए जाते हैं।','red'=>'लाल','yellow'=>'पीला','index'=>'इंडेक्स','health'=>'स्वास्थ्य','documents'=>'दस्तावेज़','storage'=>'स्टोरेज','shards'=>'प्राइमरी / रेप्लिका','copy'=>'डायग्नोस्टिक कॉपी करें','details'=>'विवरण खोलें','empty'=>'कोई समस्या वाला इंडेक्स नहीं मिला।','loading'=>'समस्या वाले इंडेक्स लोड हो रहे हैं...','error'=>'समस्या वाले इंडेक्स लोड नहीं हो सके।','copied'=>'डायग्नोस्टिक कॉपी हो गया।','copyFailed'=>'कॉपी विफल।'],
        'ar' => ['title'=>'الفهارس التي بها مشكلات','hint'=>'الفهارس ذات الحالة الصفراء أو الحمراء. تظهر الفهارس الحمراء أولاً.','red'=>'أحمر','yellow'=>'أصفر','index'=>'الفهرس','health'=>'الصحة','documents'=>'المستندات','storage'=>'التخزين','shards'=>'أساسي / نسخة','copy'=>'نسخ ملخص التشخيص','details'=>'فتح التفاصيل','empty'=>'لم يتم العثور على فهارس بها مشكلات.','loading'=>'جار تحميل الفهارس...','error'=>'تعذر تحميل الفهارس.','copied'=>'تم نسخ ملخص التشخيص.','copyFailed'=>'فشل النسخ.'],
    ][$locale] ?? ['title'=>'Problematic indices','hint'=>'Indices with yellow or red health.','red'=>'Red','yellow'=>'Yellow','index'=>'Index','health'=>'Health','documents'=>'Documents','storage'=>'Storage','shards'=>'Primary / Replica','copy'=>'Copy diagnostic summary','details'=>'Open details','empty'=>'No problematic indices found.','loading'=>'Loading...','error'=>'Unable to load problematic indices.','copied'=>'Copied.','copyFailed'=>'Copy failed.'];
@endphp
<section class="insight-card mb-4" id="problematic-indices-card" data-dashboard-widget="problematic">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div><h2 class="section-title mb-1">{{ $labels['title'] }}</h2><p class="small text-muted mb-0">{{ $labels['hint'] }}</p></div>
        <div class="d-flex gap-2"><span class="badge text-bg-danger" id="problematic-red-count">{{ $labels['red'] }}: —</span><span class="badge text-bg-warning" id="problematic-yellow-count">{{ $labels['yellow'] }}: —</span></div>
    </div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th>{{ $labels['index'] }}</th><th>{{ $labels['health'] }}</th><th class="text-end">{{ $labels['documents'] }}</th><th class="text-end">{{ $labels['storage'] }}</th><th>{{ $labels['shards'] }}</th><th class="text-end">{{ $labels['copy'] }}</th></tr></thead>
        <tbody id="problematic-indices-rows"><tr><td colspan="6" class="text-muted text-center py-4">{{ $labels['loading'] }}</td></tr></tbody>
    </table></div>
    <p class="small text-muted mt-2 mb-0" id="problematic-feedback" role="status" aria-live="polite"></p>
    <p class="small text-muted mb-0 mt-2 d-none" id="problematic-indices-empty">{{ $labels['empty'] }}</p>
</section>
<script>
(() => {
    const endpoint = @json(route('elasticsearch-monitoring.indices.problematic'));
    const indicesUrl = @json(route('elasticsearch-monitoring.indices', ['locale' => $locale]));
    const labels = @json($labels);
    const body = document.getElementById('problematic-indices-rows');
    const empty = document.getElementById('problematic-indices-empty');
    const redCount = document.getElementById('problematic-red-count');
    const yellowCount = document.getElementById('problematic-yellow-count');
    const feedback = document.getElementById('problematic-feedback');
    const formatBytes = value => {
        const bytes = Number(value || 0);
        if (bytes >= 1024 ** 3) return (bytes / 1024 ** 3).toFixed(2) + ' GB';
        if (bytes >= 1024 ** 2) return (bytes / 1024 ** 2).toFixed(2) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return bytes.toLocaleString() + ' B';
    };
    async function copyDiagnostics(index) {
        const summary = [
            'Elasticsearch index diagnostic',
            'Index: ' + index.name,
            'Health: ' + index.health,
            'Status: ' + index.status,
            'Documents: ' + Number(index.documents_count || 0).toLocaleString(),
            'Storage: ' + formatBytes(index.storage_bytes),
            'Primary shards: ' + Number(index.primaries || 0),
            'Replica shards: ' + Number(index.replicas || 0),
            'Collected at: ' + new Date().toISOString()
        ].join('\n');
        try {
            if (navigator.clipboard && window.isSecureContext) await navigator.clipboard.writeText(summary);
            else {
                const field = document.createElement('textarea');
                field.value = summary; field.style.position = 'fixed'; field.style.opacity = '0';
                document.body.appendChild(field); field.select();
                const copied = document.execCommand('copy'); field.remove();
                if (!copied) throw new Error('Clipboard unavailable');
            }
            feedback.textContent = labels.copied;
        } catch (error) {
            feedback.textContent = labels.copyFailed;
        }
    }
    async function refreshProblematicIndices() {
        try {
            const response = await fetch(endpoint, {headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const payload = await response.json();
            const indices = Array.isArray(payload.indices) ? payload.indices : [];
            redCount.textContent = labels.red + ': ' + Number(payload.counts?.red || 0).toLocaleString();
            yellowCount.textContent = labels.yellow + ': ' + Number(payload.counts?.yellow || 0).toLocaleString();
            body.replaceChildren();
            empty.classList.toggle('d-none', indices.length > 0);
            indices.forEach(index => {
                const row = document.createElement('tr');
                const nameCell = document.createElement('td');
                const link = document.createElement('a');
                const target = new URL(indicesUrl, window.location.href);
                target.searchParams.set('search', index.name);
                target.searchParams.set('open', index.name);
                link.href = target.toString(); link.className = 'fw-semibold text-decoration-none'; link.textContent = index.name;
                nameCell.appendChild(link);
                const healthCell = document.createElement('td');
                const badge = document.createElement('span');
                badge.className = 'badge ' + (index.health === 'red' ? 'text-bg-danger' : 'text-bg-warning');
                badge.textContent = String(index.health).toUpperCase(); healthCell.appendChild(badge);
                const docsCell = document.createElement('td'); docsCell.className = 'text-end'; docsCell.textContent = Number(index.documents_count || 0).toLocaleString();
                const storageCell = document.createElement('td'); storageCell.className = 'text-end'; storageCell.textContent = formatBytes(index.storage_bytes);
                const shardsCell = document.createElement('td'); shardsCell.textContent = Number(index.primaries || 0).toLocaleString() + ' / ' + Number(index.replicas || 0).toLocaleString();
                const actionCell = document.createElement('td'); actionCell.className = 'text-end';
                const copy = document.createElement('button'); copy.type = 'button'; copy.className = 'btn btn-sm btn-outline-secondary';
                copy.textContent = labels.copy; copy.addEventListener('click', () => copyDiagnostics(index));
                actionCell.appendChild(copy);
                row.append(nameCell, healthCell, docsCell, storageCell, shardsCell, actionCell);
                body.appendChild(row);
            });
        } catch (error) {
            body.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">' + labels.error + '</td></tr>';
            redCount.textContent = labels.red + ': —'; yellowCount.textContent = labels.yellow + ': —';
            empty.classList.add('d-none');
        }
    }
    refreshProblematicIndices();
    let timer = window.setInterval(() => { if (!document.hidden) refreshProblematicIndices(); }, 30000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) refreshProblematicIndices(); });
})();
</script>
