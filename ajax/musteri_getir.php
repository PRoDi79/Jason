<?php
include '../config/database.php';
$kod = $_GET['kod'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM musteriler WHERE kod = ?");
$stmt->execute([$kod]);
$musteri = $stmt->fetch(PDO::FETCH_ASSOC);
if($musteri) {
    echo json_encode([
        'success' => true, 
        'ad' => $musteri['ad'], 
        'vergi_no' => $musteri['vergi_no'], 
        'efatura_tipi' => $musteri['efatura_tipi'],
        'iskonto_orani' => $musteri['iskonto_orani'] ?? 0
    ]);
} else {
    echo json_encode(['success' => false]);
}
?>