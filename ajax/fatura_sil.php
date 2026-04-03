<?php
include '../config/database.php';
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM faturalar WHERE id = ?");
$stmt->execute([$id]);
echo json_encode(['success' => true]);
?>