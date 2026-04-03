<?php include 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>OFİS HAL - Ayarlar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dark-theme.css" id="theme-css">
    <style>
        .preview-logo { max-width: 200px; max-height: 100px; margin-top: 10px; }
        .theme-toggle { position: fixed; bottom: 20px; right: 20px; width: 50px; height: 50px; border-radius: 50%; font-size: 24px; background: #0f3460; border: 1px solid #2a2a3a; color: white; cursor: pointer; z-index: 9999; }
        .toast-notify { position: fixed; bottom: 20px; right: 80px; background: #22c55e; color: white; padding: 10px 20px; border-radius: 8px; z-index: 9999; opacity: 0; transition: 0.3s; }
    </style>
</head>
<body>
<div class="container mt-3"><div class="card"><div class="card-header"><h3>⚙️ AYARLAR</h3></div><div class="card-body">
<?php $stmt=$pdo->query("SELECT * FROM ayarlar WHERE id=1"); $ayar=$stmt->fetch(PDO::FETCH_ASSOC); if(!$ayar){ $pdo->exec("INSERT INTO ayarlar (id,firma_adi,varsayilan_kdv) VALUES (1,'OFİS HAL',1)"); $ayar=['firma_adi'=>'OFİS HAL','vergi_no'=>'','vergi_daire'=>'','adres'=>'','tel'=>'','web_sitesi'=>'','email'=>'','iban'=>'','banka_adi'=>'','logo_path'=>'','varsayilan_kdv'=>1]; } ?>
<form id="ayarForm"><div class="row"><div class="col-md-6"><label>Firma Adı</label><input type="text" name="firma_adi" class="form-control" value="<?= htmlspecialchars($ayar['firma_adi']) ?>"></div><div class="col-md-3"><label>Vergi No</label><input type="text" name="vergi_no" class="form-control" value="<?= htmlspecialchars($ayar['vergi_no']) ?>"></div><div class="col-md-3"><label>Vergi Dairesi</label><input type="text" name="vergi_daire" class="form-control" value="<?= htmlspecialchars($ayar['vergi_daire']) ?>"></div></div>
<div class="row mt-2"><div class="col-12"><label>Adres</label><input type="text" name="adres" class="form-control" value="<?= htmlspecialchars($ayar['adres']) ?>"></div></div>
<div class="row mt-2"><div class="col-md-3"><label>Telefon</label><input type="text" name="tel" class="form-control" value="<?= htmlspecialchars($ayar['tel']) ?>"></div><div class="col-md-3"><label>Web Sitesi</label><input type="text" name="web_sitesi" class="form-control" value="<?= htmlspecialchars($ayar['web_sitesi']) ?>"></div><div class="col-md-3"><label>E-Posta</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($ayar['email']) ?>"></div><div class="col-md-3"><label>Varsayılan KDV (%)</label><input type="number" name="varsayilan_kdv" class="form-control" value="<?= $ayar['varsayilan_kdv'] ?>"></div></div>
<div class="row mt-2"><div class="col-md-4"><label>Banka Adı</label><input type="text" name="banka_adi" class="form-control" value="<?= htmlspecialchars($ayar['banka_adi']) ?>"></div><div class="col-md-8"><label>IBAN</label><input type="text" name="iban" class="form-control" value="<?= htmlspecialchars($ayar['iban']) ?>"></div></div>
<div class="row mt-2"><div class="col-md-6"><label>Logo</label><input type="file" id="logo" class="form-control" accept="image/*"><input type="hidden" name="logo_path" id="logo_path" value="<?= htmlspecialchars($ayar['logo_path']) ?>"><?php if($ayar['logo_path']): ?><img src="<?= $ayar['logo_path'] ?>" class="preview-logo" id="logoPreview"><?php else: ?><img src="" class="preview-logo" id="logoPreview" style="display:none"><?php endif; ?></div></div>
<div class="row mt-4"><div class="col-12"><button type="submit" class="btn btn-primary btn-lg">💾 Kaydet</button></div></div></form>
<hr class="mt-4"><h4>🎨 Görünüm Ayarları</h4><div class="btn-group w-100"><button class="btn btn-dark btn-lg" onclick="setTheme('dark')">🌙 Koyu Tema</button><button class="btn btn-light btn-lg" onclick="setTheme('light')">☀️ Açık Tema</button></div>
</div></div></div>
<button class="theme-toggle" id="themeToggle" onclick="toggleTheme()">☀️</button>
<script src="assets/js/theme.js"></script>
<script>
function showToast(msg,isError){ let t=document.querySelector('.toast-notify'); if(!t){ t=document.createElement('div'); t.className='toast-notify'; document.body.appendChild(t); } t.textContent=msg; t.style.backgroundColor=isError?'#dc2626':'#22c55e'; t.style.opacity='1'; setTimeout(()=>t.style.opacity='0',2000); }
document.getElementById('logo').addEventListener('change',function(e){ let fd=new FormData(); fd.append('logo',e.target.files[0]); fetch('ajax/logo_upload.php',{method:'POST',body:fd}).then(r=>r.json()).then(data=>{ if(data.success){ document.getElementById('logo_path').value=data.path; document.getElementById('logoPreview').src=data.path; document.getElementById('logoPreview').style.display='block'; showToast('Logo yüklendi'); }else{ showToast('Hata: '+data.error,true); } }); });
document.getElementById('ayarForm').addEventListener('submit',function(e){ e.preventDefault(); let fd=new FormData(this); fetch('ajax/ayar_kaydet.php',{method:'POST',body:fd}).then(r=>r.json()).then(data=>{ if(data.success) showToast('Ayarlar kaydedildi'); else showToast('Hata: '+data.error,true); }); });
document.addEventListener('keydown',function(e){ if(e.key==='Escape'){ e.preventDefault(); if(confirm('Ana menüye dön?')) window.location.href='index.php'; } });
</script>
</body>
</html>