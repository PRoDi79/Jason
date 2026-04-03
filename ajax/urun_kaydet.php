<?php
include '../config/database.php';
$kod = $_POST['kod'] ?? '';
$ad = $_POST['ad'] ?? '';
$birim_ref = $_POST['birim_ref'] ?? 22;
$kdv = $_POST['kdv'] ?? 1;

if (empty($kod) || empty($ad)) {
    echo json_encode(['success' => false, 'error' => 'Kod ve ad gerekli']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO urunler (kod, ad, birim_ref, varsayilan_kdv) VALUES (?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE ad = ?, birim_ref = ?, varsayilan_kdv = ?");
    $stmt->execute([$kod, $ad, $birim_ref, $kdv, $ad, $birim_ref, $kdv]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>