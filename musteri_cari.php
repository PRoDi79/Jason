<?php include 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>OFİS HAL - Müşteri Cari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dark-theme.css" id="theme-css">
    <style>
        .theme-toggle { position: fixed; bottom: 20px; right: 20px; width: 50px; height: 50px; border-radius: 50%; font-size: 24px; background: #0f3460; border: 1px solid #2a2a3a; color: white; cursor: pointer; z-index: 9999; }
        .toast-notify { position: fixed; bottom: 20px; right: 80px; background: #22c55e; color: white; padding: 10px 20px; border-radius: 8px; z-index: 9999; opacity: 0; transition: 0.3s; }
    </style>
</head>
<body>
<div class="container mt-3"><div class="card"><div class="card-header"><h3>📋 MÜŞTERİ CARİ</h3></div><div class="card-body">
    <div class="row mb-4">
        <div class="col-md-3"><button class="btn btn-primary w-100" onclick="formGoster('kayit')">A) Müşteri Kayıt</button></div>
        <div class="col-md-3"><button class="btn btn-warning w-100" onclick="formGoster('duzeltme')">B) Müşteri Düzeltme</button></div>
        <div class="col-md-3"><button class="btn btn-info w-100" onclick="alert('Yapım aşamasında')">C) Veresiye Kayıt</button></div>
        <div class="col-md-3"><button class="btn btn-secondary w-100" onclick="alert('Yapım aşamasında')">E) Veresiye Defteri</button></div>
    </div>
    <div id="musteriForm" style="display:none;" class="border p-3 rounded mb-3"><h4 id="formTitle">Müşteri Kayıt</h4>
        <div class="row"><div class="col-md-3"><label>Kod</label><input type="text" id="kod" class="form-control"></div><div class="col-md-4"><label>Ad/Unvan</label><input type="text" id="ad" class="form-control"></div><div class="col-md-3"><label>Vergi No</label><input type="text" id="vergi_no" class="form-control"></div><div class="col-md-2"><label>Vergi Dairesi</label><input type="text" id="vergi_daire" class="form-control"></div></div>
        <div class="row mt-2"><div class="col-md-6"><label>Adres</label><input type="text" id="adres" class="form-control"></div><div class="col-md-2"><label>İl</label><input type="text" id="il" class="form-control"></div><div class="col-md-2"><label>İlçe</label><input type="text" id="ilce" class="form-control"></div><div class="col-md-2"><label>GSM</label><input type="text" id="gsm" class="form-control"></div></div>
        <div class="row mt-2"><div class="col-md-3"><label>E-Fatura Tipi</label><select id="efatura_tipi" class="form-control"><option value="e-arsiv">e-Arşiv</option><option value="e-fatura">e-Fatura</option></select></div><div class="col-md-3"><label>İskonto (%)</label><input type="number" id="iskonto_orani" class="form-control" value="0"></div></div>
        <div class="row mt-3"><div class="col-md-6"><button class="btn btn-success w-100" onclick="musteriKaydet()">Kaydet</button></div><div class="col-md-6"><button class="btn btn-secondary w-100" onclick="formKapat()">İptal</button></div></div>
    </div>
    <div class="mt-4"><h4>Müşteri Listesi</h4><input type="text" id="ara" class="form-control mb-2" placeholder="Ara..." onkeyup="listele()"><div id="musteriListesi" class="table-responsive"></div></div>
</div></div></div>
<button class="theme-toggle" id="themeToggle" onclick="toggleTheme()">☀️</button>
<script src="assets/js/theme.js"></script>
<script>
function showToast(msg,isError){ let t=document.querySelector('.toast-notify'); if(!t){ t=document.createElement('div'); t.className='toast-notify'; document.body.appendChild(t); } t.textContent=msg; t.style.backgroundColor=isError?'#dc2626':'#22c55e'; t.style.opacity='1'; setTimeout(()=>t.style.opacity='0',2000); }
function formGoster(tip){ document.getElementById('musteriForm').style.display='block'; if(tip==='kayit'){ document.getElementById('formTitle').innerText='Yeni Müşteri Kayıt'; temizle(); }else{ document.getElementById('formTitle').innerText='Müşteri Düzeltme'; } }
function temizle(){ ['kod','ad','vergi_no','vergi_daire','adres','il','ilce','gsm'].forEach(id=>document.getElementById(id).value=''); document.getElementById('efatura_tipi').value='e-arsiv'; document.getElementById('iskonto_orani').value='0'; }
function formKapat(){ document.getElementById('musteriForm').style.display='none'; }
function musteriKaydet(){ let data={ kod:document.getElementById('kod').value, ad:document.getElementById('ad').value, vergi_no:document.getElementById('vergi_no').value, vergi_daire:document.getElementById('vergi_daire').value, adres:document.getElementById('adres').value, il:document.getElementById('il').value, ilce:document.getElementById('ilce').value, gsm:document.getElementById('gsm').value, efatura_tipi:document.getElementById('efatura_tipi').value, iskonto_orani:document.getElementById('iskonto_orani').value }; fetch('ajax/musteri_kaydet.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)}).then(r=>r.json()).then(r=>{ if(r.success){ showToast('Müşteri kaydedildi'); formKapat(); listele(); }else{ showToast('Hata: '+r.error,true); } }); }
function listele(){ let ara=document.getElementById('ara').value; fetch('ajax/musteri_listele.php?ara='+encodeURIComponent(ara)).then(r=>r.json()).then(data=>{ let html='<table class="table table-bordered table-striped"><thead><tr><th>Kod</th><th>Unvan</th><th>Vergi No</th><th>Tip</th><th>İskonto%</th><th>İşlem</th></tr></thead><tbody>'; data.forEach(m=>{ html+='<tr><td>'+escapeHtml(m.kod)+'</td><td>'+escapeHtml(m.ad)+'</td><td>'+escapeHtml(m.vergi_no||'-')+'</td><td>'+m.efatura_tipi+'</td><td>%'+(m.iskonto_orani||0)+'</td><td><button class="btn btn-sm btn-warning" onclick="duzenle(\''+m.kod+'\')">Düzenle</button></td></tr>'; }); html+='</tbody></table>'; document.getElementById('musteriListesi').innerHTML=html; }); }
function duzenle(kod){ fetch('ajax/musteri_getir.php?kod='+kod).then(r=>r.json()).then(data=>{ if(data.success){ document.getElementById('kod').value=kod; document.getElementById('ad').value=data.ad; document.getElementById('vergi_no').value=data.vergi_no||''; document.getElementById('vergi_daire').value=data.vergi_daire||''; document.getElementById('adres').value=data.adres||''; document.getElementById('il').value=data.il||''; document.getElementById('ilce').value=data.ilce||''; document.getElementById('gsm').value=data.gsm||''; document.getElementById('efatura_tipi').value=data.efatura_tipi||'e-arsiv'; document.getElementById('iskonto_orani').value=data.iskonto_orani||0; formGoster('duzeltme'); } }); }
function escapeHtml(s){ if(!s) return ''; return s.replace(/[&<>]/g, m=>m==='&'?'&amp;':m==='<'?'&lt;':'&gt;'); }
document.getElementById('ara').addEventListener('keyup',listele);
document.addEventListener('keydown',e=>{ if(e.key==='Escape'){ e.preventDefault(); if(document.getElementById('musteriForm').style.display==='block') formKapat(); else if(confirm('Ana menüye dön?')) window.location.href='index.php'; } if(e.key==='F12'){ e.preventDefault(); document.getElementById('ara').focus(); } });
listele();
</script>
</body>
</html>