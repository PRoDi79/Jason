<?php
include '../config/database.php';
$birim_adi = $_POST['birim_adi'] ?? '';
if (empty($birim_adi)) {
    echo json_encode(['success' => false, 'error' => 'Birim adı gerekli']);
    exit;
}
// Yeni ref_id bul (max + 1 veya 1000'den başla)
$stmt = $pdo->query("SELECT MAX(ref_id) as max_id FROM birimler");
$max = $stmt->fetch(PDO::FETCH_ASSOC)['max_id'];
$new_ref = max(100, $max + 1);

$stmt = $pdo->prepare("INSERT INTO birimler (ref_id, birim_adi) VALUES (?, ?)");
$stmt->execute([$new_ref, $birim_adi]);
echo json_encode(['success' => true, 'ref_id' => $new_ref]);
?>