<?php
include '../config/database.php';
$q = $_GET['q'] ?? '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM musteriler 
                       WHERE kod LIKE :q 
                       OR ad LIKE :q 
                       OR vergi_no LIKE :q 
                       LIMIT 20");
$stmt->execute(['q' => "%$q%"]);
$musteriler = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($musteriler);
?>