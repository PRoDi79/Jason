<?php
include '../config/database.php';
$ara = $_GET['ara'] ?? '';
$sql = "SELECT u.*, b.birim_adi FROM urunler u JOIN birimler b ON u.birim_ref = b.ref_id";
if ($ara) {
    $sql .= " WHERE u.kod LIKE :ara OR u.ad LIKE :ara";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ara' => "%$ara%"]);
} else {
    $stmt = $pdo->query($sql);
}
$urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($urunler);
?>