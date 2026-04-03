<?php
include '../config/database.php';
$fatura_no = $_GET['fatura_no'] ?? '';
$musteri = $_GET['musteri'] ?? '';
$tarih = $_GET['tarih'] ?? '';

$sql = "SELECT f.*, m.ad as musteri_adi FROM faturalar f JOIN musteriler m ON f.musteri_id = m.id WHERE 1=1";
$params = [];

if ($fatura_no) {
    $sql .= " AND f.fatura_no LIKE ?";
    $params[] = "%$fatura_no%";
}
if ($musteri) {
    $sql .= " AND m.ad LIKE ?";
    $params[] = "%$musteri%";
}
if ($tarih) {
    $sql .= " AND f.tarih = ?";
    $params[] = $tarih;
}
$sql .= " ORDER BY f.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$faturalar = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($faturalar);
?>