<?php
include '../config/database.php';
$ara = $_GET['ara'] ?? '';
$sql = "SELECT * FROM musteriler";
if ($ara) {
    $sql .= " WHERE kod LIKE :ara OR ad LIKE :ara OR vergi_no LIKE :ara";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ara' => "%$ara%"]);
} else {
    $stmt = $pdo->query($sql);
}
$musteriler = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($musteriler);
?>