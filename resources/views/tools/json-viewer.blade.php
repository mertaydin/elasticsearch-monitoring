@php
    $locale = request()->query('locale', request()->cookie('elasticsearch_monitoring_locale', config('elasticsearch-monitoring.dashboard.locale', 'en')));
    $locale = in_array($locale, ['tr', 'en'], true) ? $locale : 'en';
    $text = [
        'tr' => ['title'=>'JSON Görüntüleyici','description'=>'JSON dosyalarını tarayıcınızda açın, inceleyin ve düzenleyin. Verileriniz sunucuya gönderilmez.','back'=>'İzleme paneli','open'=>'Dosya aç','paste'=>'JSON yapıştır','search'=>'Anahtar veya değer ara…','format'=>'Biçimlendir','minify'=>'Sıkıştır','copy'=>'Kopyala','download'=>'İndir','tree'=>'Ağaç görünümü','source'=>'Kaynak','valid'=>'Geçerli JSON','invalid'=>'Geçersiz JSON','empty'=>'Bir JSON dosyası açın veya metin alanına yapıştırın.','placeholder'=>'JSON içeriğini buraya yapıştırın…','apply'=>'JSON'ı yükle','noResults'=>'Eşleşme bulunamadı.','matches'=>'eşleşme','copied'=>'Panoya kopyalandı.','copyFailed'=>'Kopyalama başarısız.','fileError'=>'Dosya okunamadı.','parseError'=>'JSON ayrıştırılamadı','size'=>'Boyut','nodes'=>'Düğüm','depth'=>'Derinlik','expand'=>'Tümünü aç','collapse'=>'Tümünü daralt','clear'=>'Temizle','localOnly'=>'Yerel işlem · Sunucuya veri gönderilmez'],
        'en' => ['title'=>'JSON Viewer','description'=>'Open, inspect and edit JSON files in your browser. Your data is never uploaded.','back'=>'Monitoring dashboard','open'=>'Open file','paste'=>'Paste JSON','search'=>'Search keys or values…','format'=>'Pretty print','minify'=>'Minify','copy'=>'Copy','download'=>'Download','tree'=>'Tree view','source'=>'Source','valid'=>'Valid JSON','invalid'=>'Invalid JSON','empty'=>'Open a JSON file or paste content into the editor.','placeholder'=>'Paste JSON content here…','apply'=>'Load JSON','noResults'=>'No matches found.','matches'=>'matches','copied'=>'Copied to clipboard.','copyFailed'=>'Copy failed.','fileError'=>'Unable to read file.','parseError'=>'Unable to parse JSON','size'=>'Size','nodes'=>'Nodes','depth'=>'Depth','expand'=>'Expand all','collapse'=>'Collapse all','clear'=>'Clear','localOnly'=>'Local processing · No data is sent to the server'],
    ][$locale];
@end
<!doctype html>
<html lang="{{ $locale }}">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $text['title'] }} · Elasticsearch Monitoring</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root{color-scheme:light}body{background:#f5f7fb;color:#182230}.tool-shell{max-width:1440px;margin:32px auto;padding:0 20px}.tool-card{background:#fff;border:1px solid #e6eaf0;border-radius:14px;box-shadow:0 4px 18px #18223008}.tool-head{padding:22px 24px;border-bottom:1px solid #edf0f4}.tool-body{padding:20px 24px}.muted{color:#718096}.toolbar{display:flex;gap:8px;flex-wrap:wrap;align-items:center}.editor{width:100%;min-height:440px;resize:vertical;border:1px solid #dce2ea;border-radius:10px;padding:14px;font:13px/1.6 ui-monospace,SFMono-Regular,Menlo,monospace;white-space:pre;tab-size:2}.tree-pane{min-height:440px;max-height:68vh;overflow:auto;border:1px solid #e2e8f0;border-radius:10px;padding:14px;font:13px/1.7 ui-monospace,SFMono-Regular,Menlo,monospace}.tree-row{display:flex;align-items:flex-start;gap:6px;min-height:23px;white-space:pre}.tree-children{margin-left:19px;border-left:1px solid #e7ebf1;padding-left:10px}.tree-toggle{width:16px;flex:0 0 16px;border:0;background:transparent;color:#718096;padding:0;line-height:1.7}.tree-key{color:#7c3aed}.tree-string{color:#16804a;white-space:pre-wrap;overflow-wrap:anywhere}.tree-number{color:#2563eb}.tree-boolean{color:#b45309}.tree-null{color:#64748b}.tree-punctuation{color:#64748b}.tree-match{background:#fff0b3;border-radius:3px}.tree-path{color:#8492a6;font-size:11px;margin-left:6px}.status{font-size:13px}.status.good{color:#16804a}.status.bad{color:#c0392b}.search-results{max-height:180px;overflow:auto}.search-result{display:block;width:100%;text-align:left;border:0;background:#fff;padding:6px 9px;border-radius:6px;font:12px ui-monospace,monospace}.search-result:hover{background:#f1f5f9}.nav-pills .nav-link{color:#526071}.nav-pills .nav-link.active{background:#253858}.stat{background:#f8fafc;border-radius:8px;padding:8px 12px;font-size:12px}.btn-soft{background:#eef2f7;border:1px solid #e2e8f0;color:#334155}.drop-zone{border:1px dashed #b8c3d1;border-radius:10px;padding:12px 16px;background:#fafcff}.drop-zone.drag{border-color:#5276b7;background:#f0f5ff}@media(max-width:700px){.tool-shell{padding:0 10px;margin:16px auto}.tool-head,.tool-body{padding:16px}.editor,.tree-pane{min-height:360px}}
</style>
</head>
<body>
<div class="tool-shell">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <a class="text-decoration-none text-secondary small" href="{{ route('elasticsearch-monitoring.index', ['locale'=>$locale]) }}">← {{ $text['back'] }}</a>
    <span class="small text-success">● {{ $text['localOnly'] }}</span>
  </div>
  <section class="tool-card">
    <header class="tool-head">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div><h1 class="h4 mb-1">{{ $text['title'] }}</h1><p class="muted mb-0">{{ $text['description'] }}</p></div>
        <div class="toolbar">
          <label class="btn btn-primary btn-sm mb-0">{{ $text['open'] }} <input id="json-file" type="file" accept=".json,application/json,text/plain" hidden></label>
          <button class="btn btn-soft btn-sm" id="paste-btn" type="button">{{ $text['paste'] }}</button>
          <button class="btn btn-soft btn-sm" id="clear-btn" type="button">{{ $text['clear'] }}</button>
        </div>
      </div>
    </header>
    <div class="tool-body">
      <div class="drop-zone mb-3" id="drop-zone"><span class="small muted">JSON dosyasını buraya sürükleyip bırakabilirsiniz.</span></div>
      <div class="row g-2 align-items-center mb-3">
        <div class="col-12 col-lg"><input id="json-search" class="form-control form-control-sm" type="search" placeholder="{{ $text['search'] }}" disabled></div>
        <div class="col-auto"><button id="format-btn" class="btn btn-outline-secondary btn-sm" type="button" disabled>{{ $text['format'] }}</button></div>
        <div class="col-auto"><button id="minify-btn" class="btn btn-outline-secondary btn-sm" type="button" disabled>{{ $text['minify'] }}</button></div>
        <div class="col-auto"><button id="copy-btn" class="btn btn-outline-secondary btn-sm" type="button" disabled>{{ $text['copy'] }}</button></div>
        <div class="col-auto"><button id="download-btn" class="btn btn-outline-secondary btn-sm" type="button" disabled>{{ $text['download'] }}</button></div>
      </div>
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <ul class="nav nav-pills nav-sm" role="tablist">
          <li class="nav-item"><button class="nav-link active py-1 px-3" id="tree-tab" type="button">{{ $text['tree'] }}</button></li>
          <li class="nav-item"><button class="nav-link py-1 px-3" id="source-tab" type="button">{{ $text['source'] }}</button></li>
        </ul>
        <div class="toolbar"><button id="expand-btn" class="btn btn-link btn-sm p-0 text-decoration-none" type="button" disabled>{{ $text['expand'] }}</button><span class="text-secondary">·</span><button id="collapse-btn" class="btn btn-link btn-sm p-0 text-decoration-none" type="button" disabled>{{ $text['collapse'] }}</button></div>
      </div>
      <div id="empty-state" class="text-center muted py-5">{{ $text['empty'] }}</div>
      <div id="tree-pane" class="tree-pane d-none" aria-live="polite"></div>
      <div id="source-pane" class="d-none"><textarea id="source-editor" class="editor" spellcheck="false" placeholder="{{ $text['placeholder'] }}"></textarea><div class="d-flex justify-content-end mt-2"><button id="apply-btn" class="btn btn-primary btn-sm" type="button">{{ $text['apply'] }}</button></div></div>
      <div id="search-results" class="search-results mt-2 d-none"></div>
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 border-top mt-3 pt-3">
        <div id="status" class="status muted">{{ $text['empty'] }}</div>
        <div class="d-flex gap-2"><span class="stat"><strong id="stat-size">—</strong> {{ $text['size'] }}</span><span class="stat"><strong id="stat-nodes">—</strong> {{ $text['nodes'] }}</span><span class="stat"><strong id="stat-depth">—</strong> {{ $text['depth'] }}</span></div>
      </div>
    </div>
  </section>
</div>
<script>
(() => {
 const t=@json($text), $=id=>document.getElementById(id);
 let data=null, raw='', fileName='data.json', expanded=new Set(['$']), nodeCount=0, maxDepth=0;
 const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 const pathKey=(path,key)=>typeof key==='number'?path+'['+key+']':path+'.'+String(key).replace(/([.\\])/g,'\\$1');
 function stats(value,depth=0){nodeCount++;maxDepth=Math.max(maxDepth,depth);if(value&&typeof value==='object')for(const k of Object.keys(value))stats(value[k],depth+1);}
 function valueType(v){if(v===null)return 'null';if(Array.isArray(v))return 'array';return typeof v==='object'?'object':typeof v;}
 function valueText(v){if(typeof v==='string')return JSON.stringify(v);return String(v);}
 function makeNode(key,value,path,depth){
   const wrap=document.createElement('div'), row=document.createElement('div');row.className='tree-row';
   const type=valueType(value), isBranch=type==='object'||type==='array', keys=isBranch?Object.keys(value):[];
   const open=expanded.has(path), toggle=document.createElement('button');toggle.className='tree-toggle';toggle.type='button';toggle.textContent=isBranch?(open?'▾':'▸'):'';
   if(isBranch)toggle.addEventListener('click',()=>{open?expanded.delete(path):expanded.add(path);renderTree();});
   row.append(toggle);
   if(key!==null){const keyEl=document.createElement('span');keyEl.className='tree-key';keyEl.textContent=typeof key==='number'?'['+key+']':JSON.stringify(key);row.append(keyEl,document.createTextNode(': '));}
   if(isBranch){const p=document.createElement('span');p.className='tree-punctuation';p.textContent=type==='array'?'[':'{';row.append(p);if(!open){const summary=document.createElement('span');summary.className='tree-path';summary.textContent=keys.length+' '+(type==='array'?'items':'keys')+(type==='array'?']':'}');row.append(summary);}else if(!keys.length){const close=document.createElement('span');close.className='tree-punctuation';close.textContent=type==='array'?']':'}';row.append(close);}}
   else{const val=document.createElement('span');val.className='tree-'+(type==='string'?'string':type==='number'?'number':type==='boolean'?'boolean':'null');val.textContent=valueText(value);row.append(val);}
   wrap.append(row);
   if(isBranch&&open&&keys.length){const children=document.createElement('div');children.className='tree-children';for(const k of keys)children.append(makeNode(Array.isArray(value)?Number(k):k,value[k],pathKey(path,Array.isArray(value)?Number(k):k),depth+1));const close=document.createElement('div');close.className='tree-row tree-punctuation';close.style.paddingLeft='22px';close.textContent=type==='array'?']':'}';children.append(close);wrap.append(children);}
   return wrap;
 }
 function renderTree(){const pane=$('tree-pane');pane.replaceChildren();if(data!==null||raw==='null')pane.append(makeNode(null,data,'$',0));}
 function setStatus(message,ok=true){$('status').textContent=message;$('status').className='status '+(ok?'good':'bad');}
 function load(text,name){try{const parsed=JSON.parse(text);data=parsed;raw=text;fileName=name||'data.json';expanded=new Set(['$']);nodeCount=0;maxDepth=0;stats(data);$('source-editor').value=JSON.stringify(data,null,2);$('empty-state').classList.add('d-none');$('tree-pane').classList.remove('d-none');$('search-results').classList.add('d-none');renderTree();$('stat-size').textContent=formatBytes(new Blob([text]).size);$('stat-nodes').textContent=nodeCount.toLocaleString();$('stat-depth').textContent=maxDepth.toLocaleString();setStatus(t.valid+' · '+nodeCount.toLocaleString()+' '+t.nodes);['json-search','format-btn','minify-btn','copy-btn','download-btn','expand-btn','collapse-btn'].forEach(id=>$(id).disabled=false);return true;}catch(e){setStatus(t.parseError+': '+e.message,false);return false;}}
 function formatBytes(n){if(n<1024)return n+' B';const u=['KB','MB','GB'];let i=-1;do{n/=1024;i++;}while(n>=1024&&i<u.length-1);return n.toFixed(1)+' '+u[i];}
 function currentText(){return $('source-tab').classList.contains('active')?$('source-editor').value:JSON.stringify(data,null,2);}
 function showTab(tab){const tree=tab==='tree';$('tree-tab').classList.toggle('active',tree);$('source-tab').classList.toggle('active',!tree);$('tree-pane').classList.toggle('d-none',!tree||data===null);$('source-pane').classList.toggle('d-none',tree);$('empty-state').classList.toggle('d-none',data!==null);if(!tree&&data!==null)$('source-editor').value=JSON.stringify(data,null,2);}
 function search(q){const box=$('search-results');box.replaceChildren();if(!q){box.classList.add('d-none');renderTree();return;}const term=q.toLocaleLowerCase(),found=[];function walk(v,path){if(found.length>=500)return;if(v&&typeof v==='object'){for(const k of Object.keys(v)){const p=pathKey(path,Array.isArray(v)?Number(k):k);if(String(k).toLocaleLowerCase().includes(term))found.push({path:p,label:p+' (key)'});walk(v[k],p);}}else if(String(v).toLocaleLowerCase().includes(term))found.push({path,label:path+' = '+String(v).slice(0,100)});}walk(data,'$');if(!found.length){box.textContent=t.noResults;box.classList.remove('d-none');return;}const heading=document.createElement('div');heading.className='small text-secondary mb-1';heading.textContent=found.length+(found.length===500?'+':'')+' '+t.matches;box.append(heading);for(const item of found){const b=document.createElement('button');b.type='button';b.className='search-result';b.textContent=item.label;b.addEventListener('click',()=>{expanded=new Set(['$']);const parts=item.path.match(/(?:[^.[\\]]+|\[(\d+)\])/g)||[];let p='$';for(const part of parts){const k=part.startsWith('[')?Number(part.slice(1,-1)):part;p=pathKey(p,k);expanded.add(p.slice(0,p.lastIndexOf('[')>p.lastIndexOf('.')?p.lastIndexOf('['):p.lastIndexOf('.'))||'$');}renderTree();box.classList.add('d-none');});box.append(b);}box.classList.remove('d-none');}
 $('json-file').addEventListener('change',async e=>{const f=e.target.files[0];if(!f)return;try{const txt=await f.text();load(txt,f.name);}catch{setStatus(t.fileError,false);}e.target.value='';});
 $('drop-zone').addEventListener('dragover',e=>{e.preventDefault();$('drop-zone').classList.add('drag');});$('drop-zone').addEventListener('dragleave',()=>$('drop-zone').classList.remove('drag'));$('drop-zone').addEventListener('drop',async e=>{e.preventDefault();$('drop-zone').classList.remove('drag');const f=e.dataTransfer.files[0];if(f)load(await f.text(),f.name);});
 $('paste-btn').addEventListener('click',()=>{showTab('source');$('source-editor').focus();});$('clear-btn').addEventListener('click',()=>{data=null;raw='';$('source-editor').value='';$('tree-pane').replaceChildren();$('tree-pane').classList.add('d-none');$('source-pane').classList.remove('d-none');$('empty-state').classList.remove('d-none');$('search-results').classList.add('d-none');$('json-search').value='';['json-search','format-btn','minify-btn','copy-btn','download-btn','expand-btn','collapse-btn'].forEach(id=>$(id).disabled=true);['stat-size','stat-nodes','stat-depth'].forEach(id=>$(id).textContent='—');setStatus(t.empty);});
 $('tree-tab').addEventListener('click',()=>showTab('tree'));$('source-tab').addEventListener('click',()=>showTab('source'));
 $('apply-btn').addEventListener('click',()=>{if(load($('source-editor').value,fileName))showTab('tree');});
 $('format-btn').addEventListener('click',()=>{const txt=JSON.stringify(data,null,2);$('source-editor').value=txt;showTab('source');$('source-editor').value=txt;});
 $('minify-btn').addEventListener('click',()=>{const txt=JSON.stringify(data);$('source-editor').value=txt;showTab('source');$('source-editor').value=txt;});
 $('copy-btn').addEventListener('click',async()=>{try{await navigator.clipboard.writeText(currentText());setStatus(t.copied);}catch{setStatus(t.copyFailed,false);}});
 $('download-btn').addEventListener('click',()=>{const blob=new Blob([currentText()],{type:'application/json'}),url=URL.createObjectURL(blob),a=document.createElement('a');a.href=url;a.download=fileName.replace(/\.json$/i,'')+'.json';a.click();URL.revokeObjectURL(url);});
 $('expand-btn').addEventListener('click',()=>{expanded=new Set(['$']);function walk(v,p){if(v&&typeof v==='object'){expanded.add(p);for(const k of Object.keys(v))walk(v[k],pathKey(p,Array.isArray(v)?Number(k):k));}}walk(data,'$');renderTree();});
 $('collapse-btn').addEventListener('click',()=>{expanded=new Set();renderTree();});
 $('json-search').addEventListener('input',e=>search(e.target.value.trim()));
})();
</script>
</body></html>
