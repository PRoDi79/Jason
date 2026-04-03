<?php
include '../config/database.php';
$kod = $_GET['kod'] ?? '';
$stmt = $pdo->prepare("SELECT u.*, b.birim_adi FROM urunler u JOIN birimler b ON u.birim_ref = b.ref_id WHERE u.kod = ?");
$stmt->execute([$kod]);
$urun = $stmt->fetch(PDO::FETCH_ASSOC);
if($urun) {
    echo json_encode(['success'=>true, 'ad'=>$urun['ad'], 'birim_adi'=>$urun['birim_adi'], 'birim_ref'=>$urun['birim_ref']]);
} else {
    echo json_encode(['success'=>false]);
}
?>