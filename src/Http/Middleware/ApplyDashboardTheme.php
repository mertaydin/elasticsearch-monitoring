<?php

namespace ElasticsearchMonitoring\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApplyDashboardTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $contentType = (string) $response->headers->get('Content-Type', '');

        if (! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $html = $response->getContent();

        if (! is_string($html) || ! str_contains($html, '</head>') || ! str_contains($html, '</body>')) {
            return $response;
        }

        $styles = <<<'CSS'
<style id="elasticsearch-monitoring-theme-css">
html[data-theme="dark"]{color-scheme:dark;background:#0b1220}
html[data-theme="dark"] body,html[data-theme="dark"] body.bg-light{background:#0b1220!important;color:#e5eaf3!important}
html[data-theme="dark"] main,html[data-theme="dark"] .container{color:#e5eaf3}
html[data-theme="dark"] .dashboard-header,html[data-theme="dark"] .dashboard-nav,html[data-theme="dark"] .card,html[data-theme="dark"] .metric-card,html[data-theme="dark"] .summary-card,html[data-theme="dark"] .chart-card,html[data-theme="dark"] .insight-card,html[data-theme="dark"] .table,html[data-theme="dark"] .modal-content,html[data-theme="dark"] .dropdown-menu,html[data-theme="dark"] .list-group-item,html[data-theme="dark"] .accordion-item,html[data-theme="dark"] .offcanvas,html[data-theme="dark"] .bg-white{background-color:#151f30!important;color:#e5eaf3!important;border-color:#2b394d!important}
html[data-theme="dark"] .dashboard-nav-link{color:#aab8cc}
html[data-theme="dark"] .dashboard-nav-link:hover,html[data-theme="dark"] .dashboard-nav-link.active{background:#273653;color:#c7d2fe}
html[data-theme="dark"] .text-dark,html[data-theme="dark"] .text-body,html[data-theme="dark"] h1,html[data-theme="dark"] h2,html[data-theme="dark"] h3,html[data-theme="dark"] h4,html[data-theme="dark"] h5,html[data-theme="dark"] h6,html[data-theme="dark"] label,html[data-theme="dark"] strong,html[data-theme="dark"] th,html[data-theme="dark"] td{color:#e5eaf3!important}
html[data-theme="dark"] .text-muted,html[data-theme="dark"] small,html[data-theme="dark"] .form-text{color:#9aa9bd!important}
html[data-theme="dark"] .table{--bs-table-color:#dbe4f0;--bs-table-bg:#151f30;--bs-table-border-color:#2b394d;--bs-table-striped-bg:#1b293d;--bs-table-hover-bg:#22324a}
html[data-theme="dark"] .table th,html[data-theme="dark"] .table td{background-color:transparent!important;border-color:#2b394d!important;color:#dbe4f0!important}
html[data-theme="dark"] input,html[data-theme="dark"] select,html[data-theme="dark"] textarea,html[data-theme="dark"] .form-control,html[data-theme="dark"] .form-select{background-color:#101a29!important;color:#e5eaf3!important;border-color:#394a62!important}
html[data-theme="dark"] input::placeholder,html[data-theme="dark"] textarea::placeholder{color:#8292a8!important}
html[data-theme="dark"] .bg-light{background-color:#101a29!important;color:#dbe4f0!important}
html[data-theme="dark"] .btn-light,html[data-theme="dark"] .btn-outline-secondary{background:#24334a!important;color:#dbe4f0!important;border-color:#40516a!important}
html[data-theme="dark"] .pagination .page-link{background:#172438;color:#cbd5e1;border-color:#33445b}
html[data-theme="dark"] .apexcharts-tooltip,html[data-theme="dark"] .apexcharts-menu{background:#172438!important;color:#e5eaf3!important;border-color:#394a62!important}
html[data-theme="dark"] .apexcharts-tooltip-title{background:#22324a!important;border-color:#394a62!important}
html[data-theme="dark"] .apexcharts-text{fill:#aebbd0!important}
#package-theme-picker{position:fixed;right:18px;bottom:18px;z-index:99999;display:flex;align-items:center;gap:8px;padding:7px 10px;border:1px solid #dbe2ec;border-radius:12px;background:#fff;color:#344153;box-shadow:0 8px 28px rgba(15,23,42,.16);font:600 12px/1.2 system-ui,sans-serif}
#package-theme-picker select{max-width:130px;border:0;background:transparent;color:inherit;font:inherit;outline:none;cursor:pointer}
html[data-theme="dark"] #package-theme-picker,html[data-theme="dark"] #package-theme-picker select{background:#172438;color:#e5eaf3;border-color:#40516a}
@media(max-width:600px){#package-theme-picker{right:10px;bottom:10px;padding:6px 8px}}
</style>
CSS;
        $earlyScript = <<<'JS'
<script id="elasticsearch-monitoring-theme-early">
(function(){try{var p=localStorage.getItem('elasticsearch-monitoring-theme');document.documentElement.dataset.theme=(p==='dark'||(p!=='light'&&window.matchMedia('(prefers-color-scheme: dark)').matches))?'dark':'light'}catch(e){}})();
</script>
JS;
        $pickerScript = <<<'JS'
<script id="elasticsearch-monitoring-theme">
(function(){
    var key='elasticsearch-monitoring-theme',root=document.documentElement,media=window.matchMedia('(prefers-color-scheme: dark)'),preference='system';
    var translations={tr:['Gündüz','Gece','Sistem'],en:['Light','Dark','System'],es:['Claro','Oscuro','Sistema'],'zh-cn':['浅色','深色','跟随系统'],hi:['लाइट','डार्क','सिस्टम'],ar:['فاتح','داكن','النظام']};
    try{var saved=localStorage.getItem(key);if(['light','dark','system'].includes(saved))preference=saved}catch(e){}
    function apply(){root.dataset.theme=preference==='dark'||(preference==='system'&&media.matches)?'dark':'light'}
    apply();
    if(media.addEventListener)media.addEventListener('change',function(){if(preference==='system')apply()});
    function mount(){
        if(document.getElementById('package-theme-picker'))return;
        var language=(root.lang||'en').toLowerCase(),labels=translations[language]||translations[language.split('-')[0]]||translations.en;
        var wrapper=document.createElement('label');wrapper.id='package-theme-picker';wrapper.innerHTML='<span aria-hidden="true">◐</span><select aria-label="Theme"><option value="light">'+labels[0]+'</option><option value="dark">'+labels[1]+'</option><option value="system">'+labels[2]+'</option></select>';
        var select=wrapper.querySelector('select');select.value=preference;
        select.addEventListener('change',function(){preference=select.value;try{localStorage.setItem(key,preference)}catch(e){}apply()});
        document.body.appendChild(wrapper);
    }
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',mount,{once:true});else mount();
})();
</script>
JS;
        $html = str_ireplace('</head>', $styles.$earlyScript.'</head>', $html);
        $html = str_ireplace('</body>', $pickerScript.'</body>', $html);
        $response->setContent($html);

        return $response;
    }
}
