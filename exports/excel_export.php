<?php
include '../config/database.php';

$fatura_id = $_GET['fatura_id'] ?? 0;

if (!$fatura_id) {
    die('Fatura ID gerekli');
}

// Fatura bilgilerini al
$fatura = $pdo->prepare("SELECT f.*, m.ad as musteri_adi FROM faturalar f JOIN musteriler m ON f.musteri_id = m.id WHERE f.id = ?");
$fatura->execute([$fatura_id]);
$f = $fatura->fetch(PDO::FETCH_ASSOC);

if (!$f) {
    die('Fatura bulunamadı');
}

// Satırları al
$satirlar = $pdo->prepare("SELECT fs.*, u.kod as urun_kod, b.birim_adi, b.ref_id as birim_ref 
                           FROM fatura_satirlari fs 
                           LEFT JOIN urunler u ON fs.urun_id = u.id 
                           LEFT JOIN birimler b ON u.birim_ref = b.ref_id 
                           WHERE fs.fatura_id = ? 
                           ORDER BY fs.sira");
$satirlar->execute([$fatura_id]);
$rows = $satirlar->fetchAll(PDO::FETCH_ASSOC);

// Excel çıktısı (verdiğiniz formatta: HIZMET_URUN, BIRIM_KODU, BIRIM_REF, MIKTAR, BIRIM_FIYAT, ISKONTO_ORANI, KDV_ORANI)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="fatura_' . $f['fatura_no'] . '_' . date('Ymd_His') . '.xlsx"');

echo "HIZMET_URUN\tBIRIM_KODU\tBIRIM_REF\tMIKTAR\tBIRIM_FIYAT\tISKONTO_ORANI\tKDV_ORANI\n";

foreach ($rows as $row) {
    $birim_kodu = $row['birim_adi'] ?? 'Kilogram';
    $birim_ref = $row['birim_ref'] ?? 22;
    
    echo $row['urun_adi'] . "\t";
    echo $birim_kodu . "\t";
    echo $birim_ref . "\t";
    echo $row['miktar'] . "\t";
    echo $row['birim_fiyat'] . "\t";
    echo $row['iskonto_orani'] . "\t";
    echo $row['kdv_orani'] . "\n";
}
?>