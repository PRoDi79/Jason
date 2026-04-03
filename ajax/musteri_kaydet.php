<?php
include '../config/database.php';
$data = json_decode(file_get_contents('php://input'), true);

$kod = $data['kod'] ?? '';
$ad = $data['ad'] ?? '';
$vergi_no = $data['vergi_no'] ?? '';
$vergi_daire = $data['vergi_daire'] ?? '';
$adres = $data['adres'] ?? '';
$il = $data['il'] ?? '';
$ilce = $data['ilce'] ?? '';
$gsm = $data['gsm'] ?? '';
$efatura_tipi = $data['efatura_tipi'] ?? 'e-arsiv';
$iskonto_orani = $data['iskonto_orani'] ?? 0;

if (empty($kod) || empty($ad)) {
    echo json_encode(['success' => false, 'error' => 'Kod ve ad gerekli']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO musteriler (kod, ad, vergi_no, vergi_daire, adres, il, ilce, gsm, efatura_tipi, iskonto_orani) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE 
                            ad = ?, vergi_no = ?, vergi_daire = ?, adres = ?, il = ?, ilce = ?, gsm = ?, efatura_tipi = ?, iskonto_orani = ?");
    $stmt->execute([$kod, $ad, $vergi_no, $vergi_daire, $adres, $il, $ilce, $gsm, $efatura_tipi, $iskonto_orani,
                    $ad, $vergi_no, $vergi_daire, $adres, $il, $ilce, $gsm, $efatura_tipi, $iskonto_orani]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>