<?php
include '../config/database.php';
$q = $_GET['q'] ?? '';

if (strlen($q) < 1) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT f.*, m.ad as musteri_adi, m.kod as musteri_kod, m.vergi_no, m.efatura_tipi, m.iskonto_orani 
        FROM faturalar f 
        JOIN musteriler m ON f.musteri_id = m.id 
        WHERE f.fatura_no LIKE :q 
        ORDER BY f.id DESC 
        LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute(['q' => "%$q%"]);
$faturalar = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Her fatura için satırları getir
foreach ($faturalar as &$fatura) {
    $satirStmt = $pdo->prepare("SELECT fs.*, u.kod as urun_kod, b.birim_adi 
                                 FROM fatura_satirlari fs 
                                 LEFT JOIN urunler u ON fs.urun_id = u.id 
                                 LEFT JOIN birimler b ON u.birim_ref = b.ref_id 
                                 WHERE fs.fatura_id = ? 
                                 ORDER BY fs.sira");
    $satirStmt->execute([$fatura['id']]);
    $fatura['satirlar'] = $satirStmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($faturalar);
?>