<?php include 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>OFİS HAL - Fatura Dökümü</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dark-theme.css" id="theme-css">
    <style>
        .fatura-row:hover { background-color: #0f3460; cursor: pointer; }
        .theme-toggle { position: fixed; bottom: 20px; right: 20px; width: 50px; height: 50px; border-radius: 50%; font-size: 24px; background: #0f3460; border: 1px solid #2a2a3a; color: white; cursor: pointer; z-index: 9999; }
    </style>
</head>
<body>
<div class="container mt-3"><div class="card"><div class="card-header"><h3>📄 FATURA DÖKÜMÜ</h3></div><div class="card-body">
    <div class="row mb-3"><div class="col-md-4"><input type="text" id="araFaturaNo" class="form-control" placeholder="Fatura No ara..."></div><div class="col-md-4"><input type="text" id="araMusteri" class="form-control" placeholder="Müşteri ara..."></div><div class="col-md-4"><input type="date" id="araTarih" class="form-control"></div></div>
    <div id="faturaListesi" class="table-responsive"></div>
</div></div></div>
<button class="theme-toggle" id="themeToggle" onclick="toggleTheme()">☀️</button>
<script src="assets/js/theme.js"></script>
<script>
function listele(){ let fNo=document.getElementById('araFaturaNo').value; let mus=document.getElementById('araMusteri').value; let t=document.getElementById('araTarih').value; fetch('ajax/fatura_listele.php?fatura_no='+encodeURIComponent(fNo)+'&musteri='+encodeURIComponent(mus)+'&tarih='+t).then(r=>r.json()).then(data=>{ let html='<table class="table table-bordered table-striped"><thead class="table-secondary"><tr><th>Fatura No</th><th>Müşteri</th><th>Tarih</th><th>Toplam</th><th>Ödeme Tipi</th><th>İşlemler</th></tr></thead><tbody>'; data.forEach(f=>{ html+='<tr><td>'+escapeHtml(f.fatura_no)+'</td><td>'+escapeHtml(f.musteri_adi)+'</td><td>'+f.tarih+'</td><td class="text-end">'+parseFloat(f.genel_toplam).toFixed(2)+' TL</td><td>'+f.odeme_tipi+'</td><td><button class="btn btn-sm btn-info" onclick="window.location.href=\'satis_faturasi.php?duzenle='+encodeURIComponent(f.fatura_no)+'\'">✏️ Düzenle</button> <button class="btn btn-sm btn-success" onclick="excelExport('+f.id+')">📎 Excel</button> <button class="btn btn-sm btn-danger" onclick="pdfExport('+f.id+')">📄 PDF</button> <button class="btn btn-sm btn-warning" onclick="faturaSil('+f.id+')">🗑️ Sil</button></td></tr>'; }); html+='</tbody></table>'; document.getElementById('faturaListesi').innerHTML=html; }); }
function excelExport(id){ window.location.href='exports/excel_export.php?fatura_id='+id; }
function pdfExport(id){ window.location.href='exports/pdf_export.php?fatura_id='+id; }
function faturaSil(id){ if(confirm('Silmek istediğinize emin misiniz?')){ fetch('ajax/fatura_sil.php?id='+id).then(r=>r.json()).then(data=>{ if(data.success){ alert('Silindi'); listele(); }else{ alert('Hata: '+data.error); } }); } }
function escapeHtml(s){ if(!s) return ''; return s.replace(/[&<>]/g, m=>m==='&'?'&amp;':m==='<'?'&lt;':'&gt;'); }
document.getElementById('araFaturaNo').addEventListener('keyup',listele); document.getElementById('araMusteri').addEventListener('keyup',listele); document.getElementById('araTarih').addEventListener('change',listele);
document.addEventListener('keydown',e=>{ if(e.key==='Escape'){ e.preventDefault(); if(confirm('Ana menüye dön?')) window.location.href='index.php'; } });
listele();
</script>
</body>
</html>