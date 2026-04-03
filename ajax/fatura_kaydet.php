<?php
include '../config/database.php';
header('Content-Type: application/json');

$fatura_no = $_POST['fatura_no'] ?? '';
$musteri_kod = $_POST['musteri_kod'] ?? '';
$tarih = $_POST['tarih'] ?? date('Y-m-d');
$vade = $_POST['vade'] ?? 0;
$odeme_tipi = $_POST['odeme_tipi'] ?? 'Peşin';
$genel_toplam = $_POST['genel_toplam'] ?? 0;
$guncelleme = $_POST['guncelleme'] ?? 0;

$urun_kodlari = $_POST['urun_kod'] ?? [];
$miktarlar = $_POST['miktar'] ?? [];
$fiyatlar = $_POST['fiyat'] ?? [];
$iskontolar = $_POST['iskonto'] ?? [];

if (empty($fatura_no) || empty($musteri_kod)) {
    echo json_encode(['success' => false, 'error' => 'Fatura no ve müşteri kodu gerekli']);
    exit;
}

// Benzersiz ref_no oluştur (REF + yıl + ay + gün + sıra)
function generateRefNo($pdo) {
    $prefix = 'REF';
    $year = date('y');
    $month = date('m');
    $day = date('d');
    
    // Bugünkü fatura sayısını bul
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM faturalar WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    $count = $stmt->fetchColumn() + 1;
    
    $ref_no = $prefix . $year . $month . $day . str_pad($count, 4, '0', STR_PAD_LEFT);
    
    // Benzersiz mi kontrol et
    $check = $pdo->prepare("SELECT id FROM faturalar WHERE ref_no = ?");
    $check->execute([$ref_no]);
    if ($check->fetch()) {
        // Benzersiz değilse microtime ekle
        $ref_no = $prefix . $year . $month . $day . time();
    }
    return $ref_no;
}

// Müşteri ID bul
$musteri = $pdo->prepare("SELECT id FROM musteriler WHERE kod = ?");
$musteri->execute([$musteri_kod]);
$musteri_id = $musteri->fetchColumn();
if (!$musteri_id) {
    echo json_encode(['success' => false, 'error' => 'Müşteri bulunamadı: ' . $musteri_kod]);
    exit;
}

try {
    if ($guncelleme) {
        // Önce eski satırları sil
        $fatura_id = $pdo->prepare("SELECT id FROM faturalar WHERE fatura_no = ?");
        $fatura_id->execute([$fatura_no]);
        $fid = $fatura_id->fetchColumn();
        if ($fid) {
            $sil = $pdo->prepare("DELETE FROM fatura_satirlari WHERE fatura_id = ?");
            $sil->execute([$fid]);
            $guncelle = $pdo->prepare("UPDATE faturalar SET musteri_id = ?, tarih = ?, vade = ?, odeme_tipi = ?, genel_toplam = ? WHERE fatura_no = ?");
            $guncelle->execute([$musteri_id, $tarih, $vade, $odeme_tipi, $genel_toplam, $fatura_no]);
            $fatura_id = $fid;
        } else {
            $guncelleme = false;
        }
    }
    
    if (!$guncelleme) {
        // Aynı fatura no kontrolü
        $check = $pdo->prepare("SELECT id FROM faturalar WHERE fatura_no = ?");
        $check->execute([$fatura_no]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Bu fatura no zaten kullanılıyor']);
            exit;
        }
        
        // Benzersiz ref_no oluştur
        $ref_no = generateRefNo($pdo);
        
        // Yeni fatura başlık kaydet
        $stmt = $pdo->prepare("INSERT INTO faturalar (fatura_no, ref_no, musteri_id, tarih, vade, odeme_tipi, genel_toplam) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fatura_no, $ref_no, $musteri_id, $tarih, $vade, $odeme_tipi, $genel_toplam]);
        $fatura_id = $pdo->lastInsertId();
    }
    
    // Satırları kaydet
    for ($i = 0; $i < count($urun_kodlari); $i++) {
        $kod = trim($urun_kodlari[$i]);
        if (empty($kod)) continue;
        
        // Ürün ID bul (yoksa oluştur)
        $urun = $pdo->prepare("SELECT id, ad, varsayilan_kdv FROM urunler WHERE kod = ?");
        $urun->execute([$kod]);
        $urun_data = $urun->fetch(PDO::FETCH_ASSOC);
        
        if (!$urun_data) {
            // Yeni ürün oluştur (basit)
            $yeniUrun = $pdo->prepare("INSERT INTO urunler (kod, ad, birim_ref, varsayilan_kdv) VALUES (?, ?, 22, 1)");
            $yeniUrun->execute([$kod, $kod]);
            $urun_id = $pdo->lastInsertId();
            $urun_adi = $kod;
            $kdv = 1;
        } else {
            $urun_id = $urun_data['id'];
            $urun_adi = $urun_data['ad'];
            $kdv = $urun_data['varsayilan_kdv'];
        }
        
        $miktar = floatval($miktarlar[$i] ?? 0);
        $fiyat = floatval($fiyatlar[$i] ?? 0);
        $iskonto = floatval($iskontolar[$i] ?? 0);
        $tutar = $miktar * $fiyat;
        if ($iskonto > 0) $tutar = $tutar * (1 - $iskonto / 100);
        
        $satir = $pdo->prepare("INSERT INTO fatura_satirlari (fatura_id, urun_id, urun_adi, miktar, birim_fiyat, iskonto_orani, kdv_orani, tutar, sira) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $satir->execute([$fatura_id, $urun_id, $urun_adi, $miktar, $fiyat, $iskonto, $kdv, $tutar, $i]);
    }
    
    echo json_encode(['success' => true, 'fatura_id' => $fatura_id, 'ref_no' => $ref_no ?? null]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>