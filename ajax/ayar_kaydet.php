<?php
include '../config/database.php';

$firma_adi = $_POST['firma_adi'] ?? '';
$vergi_no = $_POST['vergi_no'] ?? '';
$vergi_daire = $_POST['vergi_daire'] ?? '';
$adres = $_POST['adres'] ?? '';
$tel = $_POST['tel'] ?? '';
$web_sitesi = $_POST['web_sitesi'] ?? '';
$email = $_POST['email'] ?? '';
$iban = $_POST['iban'] ?? '';
$banka_adi = $_POST['banka_adi'] ?? '';
$logo_path = $_POST['logo_path'] ?? '';
$varsayilan_kdv = $_POST['varsayilan_kdv'] ?? 1;

$stmt = $pdo->prepare("UPDATE ayarlar SET 
    firma_adi = ?, vergi_no = ?, vergi_daire = ?, adres = ?, tel = ?, 
    web_sitesi = ?, email = ?, iban = ?, banka_adi = ?, logo_path = ?, varsayilan_kdv = ?
    WHERE id = 1");
$stmt->execute([$firma_adi, $vergi_no, $vergi_daire, $adres, $tel, $web_sitesi, $email, $iban, $banka_adi, $logo_path, $varsayilan_kdv]);

echo json_encode(['success' => true]);
?>