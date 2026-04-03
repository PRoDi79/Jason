<?php
include '../config/database.php';
$fatura_no = $_GET['fatura_no'] ?? '';

$stmt = $pdo->prepare("SELECT id FROM faturalar WHERE fatura_no = ?");
$stmt->execute([$fatura_no]);
$exists = $stmt->fetch() ? true : false;

echo json_encode(['exists' => $exists, 'duzenleme' => false]);
?>