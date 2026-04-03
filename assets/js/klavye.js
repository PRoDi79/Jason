// Klavye kontrol sistemi
let currentFocusRow = 0;

function showToast(msg, isError = false) {
    let toast = document.querySelector('.toast-notify');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'toast-notify';
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.style.backgroundColor = isError ? '#dc3545' : '#28a745';
    toast.style.opacity = '1';
    setTimeout(() => toast.style.opacity = '0', 2000);
}

// Hesap Makinesi
function openCalculator() {
    let modal = document.getElementById('calcModal');
    if (!modal) return;
    modal.style.display = 'flex';
    let input = document.getElementById('calcInput');
    if (input) input.value = '0';
    window.calcExpr = '';
}
function closeCalculator() {
    let modal = document.getElementById('calcModal');
    if (modal) modal.style.display = 'none';
}
function calcPress(val) {
    let input = document.getElementById('calcInput');
    if (!input) return;
    if (val === 'C') {
        window.calcExpr = '';
        input.value = '0';
    } else if (val === '=') {
        try {
            let result = eval(window.calcExpr);
            input.value = result;
            window.calcExpr = result.toString();
        } catch(e) { input.value = 'Hata'; window.calcExpr = ''; }
    } else {
        if (window.calcExpr === undefined || window.calcExpr === '0' && /[0-9]/.test(val)) window.calcExpr = val;
        else window.calcExpr += val;
        input.value = window.calcExpr;
    }
}

// Yeni satır ekle - 0 SORUNU ÇÖZÜLDÜ
function addNewRow() {
    let tbody = document.querySelector('#faturaTable tbody');
    if (!tbody) return;
    let newRow = document.createElement('tr');
    newRow.className = 'urun-satir';
    let mevcutIskonto = window.musteriIskonto || 0;
    newRow.innerHTML = `
        <td><input type="text" class="urun-kod" placeholder="Kod" style="width:100%"></td>
        <td><input type="text" class="urun-ad" readonly style="width:100%"></td>
        <td><input type="text" class="birim" readonly style="width:80px"></td>
        <td><input type="number" class="miktar" step="any" value="" placeholder="0" style="width:100%"></td>
        <td><input type="number" class="fiyat" step="any" value="" placeholder="0" style="width:100%"></td>
        <td><input type="number" class="iskonto" value="${mevcutIskonto}" step="any" style="width:100%"></td>
        <td><input type="text" class="tutar" readonly style="width:100px"></td>
    `;
    tbody.appendChild(newRow);
    attachRowEvents(newRow);
    return newRow;
}

function attachRowEvents(row) {
    let urunKod = row.querySelector('.urun-kod');
    let miktar = row.querySelector('.miktar');
    let fiyat = row.querySelector('.fiyat');
    let iskonto = row.querySelector('.iskonto');
    
    if (urunKod) urunKod.addEventListener('keydown', (e) => handleUrunKod(e, row));
    if (miktar) miktar.addEventListener('keydown', (e) => handleMiktar(e, row));
    if (fiyat) fiyat.addEventListener('keydown', (e) => handleFiyat(e, row));
    if (iskonto) iskonto.addEventListener('keydown', (e) => handleIskonto(e, row));
}

function handleUrunKod(e, row) {
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopPropagation();
        let kod = row.querySelector('.urun-kod').value.trim();
        if (kod) fetchUrun(kod, row, true);
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        e.stopPropagation();
        let kod = row.querySelector('.urun-kod').value.trim();
        if (kod) {
            fetchUrun(kod, row, false);
        }
        let nextRow = row.nextElementSibling;
        if (!nextRow) {
            nextRow = addNewRow();
        }
        setTimeout(() => {
            let newRow = row.nextElementSibling;
            if (newRow) newRow.querySelector('.urun-kod').focus();
        }, 50);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        e.stopPropagation();
        let kod = row.querySelector('.urun-kod').value.trim();
        if (kod) fetchUrun(kod, row, false);
        let prevRow = row.previousElementSibling;
        if (prevRow) prevRow.querySelector('.urun-kod').focus();
    }
}

function handleMiktar(e, row) {
    let miktarInput = row.querySelector('.miktar');
    let fiyatInput = row.querySelector('.fiyat');
    
    if (e.key === 'Enter') {
        e.preventDefault();
        let miktar = parseFloat(miktarInput.value);
        if (!isNaN(miktar) && miktar > 0) {
            fiyatInput.focus();
        } else {
            miktarInput.value = '';
            miktarInput.focus();
        }
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        let nextRow = row.nextElementSibling;
        if (!nextRow) nextRow = addNewRow();
        setTimeout(() => {
            let newRow = row.nextElementSibling;
            if (newRow) newRow.querySelector('.miktar').focus();
        }, 50);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        let prevRow = row.previousElementSibling;
        if (prevRow) prevRow.querySelector('.miktar').focus();
    }
}

function handleFiyat(e, row) {
    let fiyatInput = row.querySelector('.fiyat');
    let miktarInput = row.querySelector('.miktar');
    
    if (e.key === 'Enter') {
        e.preventDefault();
        let fiyat = parseFloat(fiyatInput.value);
        let miktar = parseFloat(miktarInput.value);
        
        if (!isNaN(fiyat) && fiyat > 0 && !isNaN(miktar) && miktar > 0) {
            hesaplaTutar(row);
            let nextRow = row.nextElementSibling;
            if (!nextRow) nextRow = addNewRow();
            setTimeout(() => {
                let newRow = row.nextElementSibling;
                if (newRow) newRow.querySelector('.urun-kod').focus();
            }, 50);
        } else if (!isNaN(fiyat) && fiyat > 0 && (!miktar || miktar === 0)) {
            miktarInput.focus();
        } else {
            fiyatInput.value = '';
            fiyatInput.focus();
        }
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        hesaplaTutar(row);
        let nextRow = row.nextElementSibling;
        if (!nextRow) nextRow = addNewRow();
        setTimeout(() => {
            let newRow = row.nextElementSibling;
            if (newRow) newRow.querySelector('.fiyat').focus();
        }, 50);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        hesaplaTutar(row);
        let prevRow = row.previousElementSibling;
        if (prevRow) prevRow.querySelector('.fiyat').focus();
    }
}

function handleIskonto(e, row) {
    if (e.key === 'Enter' || e.key === 'ArrowDown') {
        e.preventDefault();
        hesaplaTutar(row);
        let nextRow = row.nextElementSibling;
        if (!nextRow) nextRow = addNewRow();
        setTimeout(() => {
            let newRow = row.nextElementSibling;
            if (newRow) newRow.querySelector('.urun-kod').focus();
        }, 50);
    }
}

function fetchUrun(kod, row, enterMod) {
    fetch(`ajax/urun_getir.php?kod=${encodeURIComponent(kod)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                row.querySelector('.urun-ad').value = data.ad;
                row.querySelector('.birim').value = data.birim_adi;
                row.querySelector('.miktar').value = '';
                row.querySelector('.fiyat').value = '';
                row.querySelector('.miktar').placeholder = '0';
                row.querySelector('.fiyat').placeholder = '0';
                if (enterMod) {
                    row.querySelector('.miktar').focus();
                }
                showToast(`${data.ad} eklendi`);
            } else {
                if (confirm(`"${kod}" kayıtlı değil. Yeni ürün eklemek ister misiniz?`)) {
                    urunModalGoster(kod);
                }
                row.querySelector('.urun-kod').focus();
            }
        });
}

function hesaplaTutar(row) {
    let miktarInput = row.querySelector('.miktar');
    let fiyatInput = row.querySelector('.fiyat');
    let iskontoInput = row.querySelector('.iskonto');
    let tutarInput = row.querySelector('.tutar');
    
    let miktar = parseFloat(miktarInput.value) || 0;
    let fiyat = parseFloat(fiyatInput.value) || 0;
    let iskonto = parseFloat(iskontoInput.value) || 0;
    
    if (miktar > 0 && fiyat > 0) {
        let tutar = miktar * fiyat;
        if (iskonto > 0) tutar = tutar * (1 - iskonto / 100);
        tutarInput.value = tutar.toFixed(2);
    } else {
        tutarInput.value = '';
    }
    genelToplamHesapla();
}

function genelToplamHesapla() {
    let toplam = 0;
    document.querySelectorAll('.tutar').forEach(t => {
        let val = parseFloat(t.value);
        if (!isNaN(val) && val > 0) {
            toplam += val;
        }
    });
    let kdvOran = document.getElementById('kdvOran') ? parseFloat(document.getElementById('kdvOran').value) : 1;
    let kdv = toplam * kdvOran / 100;
    let genel = toplam + kdv;
    if (document.getElementById('malToplam')) document.getElementById('malToplam').innerText = toplam.toFixed(2);
    if (document.getElementById('kdvTutar')) document.getElementById('kdvTutar').innerText = kdv.toFixed(2);
    if (document.getElementById('genelToplam')) document.getElementById('genelToplam').innerText = genel.toFixed(2);
}

// F12 Ürün Modal
function urunModalGoster(yeniKod = '') {
    let modal = document.getElementById('urunModal');
    if (!modal) return;
    modal.style.display = 'flex';
    if (yeniKod) {
        document.getElementById('urunKod').value = yeniKod;
        document.getElementById('urunAd').focus();
    }
    urunListele();
}

function urunListele() {
    fetch('ajax/urun_listele.php')
        .then(res => res.json())
        .then(data => {
            let liste = document.getElementById('urunListesi');
            if (!liste) return;
            liste.innerHTML = '';
            data.forEach(urun => {
                let div = document.createElement('div');
                div.className = 'list-group-item list-group-item-action';
                div.innerHTML = `<strong>${urun.kod}</strong> - ${urun.ad} (${urun.birim_adi}) KDV:${urun.varsayilan_kdv}%`;
                div.onclick = () => {
                    document.getElementById('urunKod').value = urun.kod;
                    document.getElementById('urunAd').value = urun.ad;
                    document.getElementById('urunBirimRef').value = urun.birim_ref;
                    document.getElementById('urunKdv').value = urun.varsayilan_kdv;
                };
                liste.appendChild(div);
            });
        });
}

function urunKaydet() {
    let kod = document.getElementById('urunKod').value;
    let ad = document.getElementById('urunAd').value;
    let birimRef = document.getElementById('urunBirimRef').value;
    let kdv = document.getElementById('urunKdv').value;
    fetch('ajax/urun_kaydet.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `kod=${encodeURIComponent(kod)}&ad=${encodeURIComponent(ad)}&birim_ref=${birimRef}&kdv=${kdv}`
    }).then(res => res.json()).then(data => {
        if (data.success) {
            showToast('Ürün kaydedildi');
            urunModalKapat();
        } else {
            showToast('Hata: ' + data.error, true);
        }
    });
}

function urunModalKapat() {
    document.getElementById('urunModal').style.display = 'none';
}

function yeniBirimEkle() {
    let yeniBirim = prompt('Yeni birim adı girin (örn: Kutu, Demet):');
    if (yeniBirim) {
        fetch('ajax/birim_ekle.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `birim_adi=${encodeURIComponent(yeniBirim)}`
        }).then(res => res.json()).then(data => {
            if (data.success) {
                showToast(`Birim eklendi. Ref ID: ${data.ref_id}`);
            } else {
                showToast('Hata: ' + data.error, true);
            }
        });
    }
}

// Ana klavye dinleyici
document.addEventListener('keydown', (e) => {
    let key = e.key;
    if (key === 'F1') { e.preventDefault(); let btn = document.querySelector('#kaydetBtn'); if(btn) btn.click(); }
    else if (key === 'F2') { e.preventDefault(); let input = document.querySelector('#musteriKod'); if(input) input.focus(); }
    else if (key === 'F3') { e.preventDefault(); let firstRow = document.querySelector('#faturaTable .urun-kod'); if(firstRow) firstRow.focus(); }
    else if (key === 'F7') { e.preventDefault(); let btn = document.querySelector('#toggleOdeme'); if(btn) btn.click(); }
    else if (key === 'F8') { e.preventDefault(); showToast('Tahsilat ekranı açılacak'); }
    else if (key === 'F9') { e.preventDefault(); window.location.href = 'satis_faturasi_dokumu.php'; }
    else if (key === 'F10') { e.preventDefault(); let btn = document.querySelector('#kaydetBtn'); if(btn) btn.click(); }
    else if (key === 'F11') { e.preventDefault(); openCalculator(); }
    else if (key === 'F12') { e.preventDefault(); urunModalGoster(); }
    else if (key === 'Escape') { 
        if (document.getElementById('calcModal') && document.getElementById('calcModal').style.display === 'flex') closeCalculator();
        if (document.getElementById('urunModal') && document.getElementById('urunModal').style.display === 'flex') urunModalKapat();
    }
});

// Sayfa yüklendiğinde ilk satıra event ekle
document.addEventListener('DOMContentLoaded', () => {
    let firstRow = document.querySelector('#faturaTable .urun-satir');
    if (firstRow) attachRowEvents(firstRow);
    let firstInput = document.querySelector('#faturaTable .urun-kod');
    if (firstInput) firstInput.focus();
});