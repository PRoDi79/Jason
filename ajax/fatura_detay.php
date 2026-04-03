<?php
include '../config/database.php';
$id = $_GET['id'] ?? 0;

$fatura = $pdo->prepare("SELECT f.*, m.ad as musteri_adi FROM faturalar f JOIN musteriler m ON f.musteri_id = m.id WHERE f.id = ?");
$fatura->execute([$id]);
$data = $fatura->fetch(PDO::FETCH_ASSOC);

if ($data) {
    $satirlar = $pdo->prepare("SELECT * FROM fatura_satirlari WHERE fatura_id = ? ORDER BY sira");
    $satirlar->execute([$id]);
    $data['satirlar'] = $satirlar->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($data);
?>