<?php include 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>OFİS HAL - Ana Menü</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dark-theme.css" id="theme-css">
    <style>
        .card { transition: transform 0.2s; cursor: pointer; }
        .card:hover { transform: scale(1.02); }
        .display-1 { font-size: 4rem; }
        .theme-toggle { position: fixed; bottom: 20px; right: 20px; width: 50px; height: 50px; border-radius: 50%; font-size: 24px; background: #1e293b; border: 1px solid #334155; color: white; cursor: pointer; z-index: 9999; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row text-center mb-5"><div class="col-12"><h1>🏪 OFİS HAL</h1><p class="text-secondary">Klavye ile hızlı fatura kesme sistemi</p></div></div>
    <div class="row g-4">
        <div class="col-md-4"><div class="card text-center h-100 p-3" onclick="location.href='musteri_cari.php'"><div class="display-1">📋</div><h3>MÜŞTERİ CARİ</h3><button class="btn btn-primary mt-2">GİT →</button></div></div>
        <div class="col-md-4"><div class="card text-center h-100 p-3" onclick="location.href='satis_faturasi.php'"><div class="display-1">🧾</div><h3>SATIŞ İŞLEMLERİ</h3><button class="btn btn-success mt-2">FATURA KES →</button></div></div>
        <div class="col-md-4"><div class="card text-center h-100 p-3" onclick="location.href='ayarlar.php'"><div class="display-1">⚙️</div><h3>AYARLAR</h3><button class="btn btn-secondary mt-2">GİT →</button></div></div>
    </div>
    <div class="row mt-5"><div class="col-12 alert alert-info"><strong>⌨️ KLAVYE KISAYOLLARI:</strong><br><kbd>F1</kbd> Kaydet | <kbd>F2</kbd> İsim | <kbd>F3</kbd> Ürün | <kbd>F7</kbd> Peşin/Veresiye | <kbd>F9</kbd> Fatura Ara | <kbd>F10</kbd> Fatura Kes | <kbd>F11</kbd> Hesap Makinesi | <kbd>F12</kbd> Ürün Ara | <kbd>ESC</kbd> Ana menü</div></div>
</div>
<button class="theme-toggle" id="themeToggle" onclick="toggleTheme()">☀️</button>
<script src="assets/js/theme.js"></script>
<script>document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { e.preventDefault(); if (confirm('Çıkmak istiyor musunuz?')) window.location.href = 'about:blank'; } });</script>
</body>
</html>