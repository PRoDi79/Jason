<?php
require_once '../vendor/tcpdf/tcpdf.php';
include '../config/database.php';

$fatura_id = $_GET['fatura_id'] ?? 0;

if (!$fatura_id) {
    die('Fatura ID gerekli');
}

// Fatura bilgilerini al
$fatura = $pdo->prepare("SELECT f.*, m.ad as musteri_adi, m.vergi_no, m.vergi_daire, m.adres, m.efatura_tipi 
                         FROM faturalar f 
                         JOIN musteriler m ON f.musteri_id = m.id 
                         WHERE f.id = ?");
$fatura->execute([$fatura_id]);
$f = $fatura->fetch(PDO::FETCH_ASSOC);

if (!$f) {
    die('Fatura bulunamadı');
}

// Satırları al
$satirlar = $pdo->prepare("SELECT fs.*, u.kod as urun_kod 
                           FROM fatura_satirlari fs 
                           LEFT JOIN urunler u ON fs.urun_id = u.id 
                           WHERE fs.fatura_id = ? 
                           ORDER BY fs.sira");
$satirlar->execute([$fatura_id]);
$rows = $satirlar->fetchAll(PDO::FETCH_ASSOC);

// Firma ayarlarını al
$ayar = $pdo->query("SELECT * FROM ayarlar WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

// PDF oluştur
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('OFİS HAL');
$pdf->SetAuthor('OFİS HAL');
$pdf->SetTitle('Fatura ' . $f['fatura_no']);
$pdf->SetSubject('Fatura');
$pdf->SetKeywords('Fatura, OFİS HAL');

$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('dejavusans', '', 9);
$pdf->AddPage();

// Logo
if (!empty($ayar['logo_path']) && file_exists('../' . $ayar['logo_path'])) {
    $pdf->Image('../' . $ayar['logo_path'], 15, 10, 40, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
}

// Firma bilgileri
$pdf->SetY(10);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 6, $ayar['firma_adi'] ?? 'OFİS HAL', 0, 1, 'R');
$pdf->SetFont('dejavusans', '', 8);
$pdf->Cell(0, 4, 'Tel: ' . ($ayar['tel'] ?? ''), 0, 1, 'R');
$pdf->Cell(0, 4, 'Web: ' . ($ayar['web_sitesi'] ?? ''), 0, 1, 'R');
$pdf->Cell(0, 4, 'E-Posta: ' . ($ayar['email'] ?? ''), 0, 1, 'R');
$pdf->Cell(0, 4, 'Vergi Dairesi: ' . ($ayar['vergi_daire'] ?? ''), 0, 1, 'R');
$pdf->Cell(0, 4, 'VKN: ' . ($ayar['vergi_no'] ?? ''), 0, 1, 'R');

$pdf->Ln(5);

// Fatura tipi başlığı
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->Cell(0, 8, strtoupper($f['efatura_tipi'] ?? 'E-ARSİV FATURA'), 0, 1, 'C');

$pdf->Ln(5);

// Fatura bilgileri tablosu
$pdf->SetFont('dejavusans', '', 9);
$pdf->Cell(40, 6, 'Fatura No:', 0, 0, 'L');
$pdf->Cell(50, 6, $f['fatura_no'], 0, 0, 'L');
$pdf->Cell(30, 6, 'Tarih:', 0, 0, 'L');
$pdf->Cell(40, 6, date('d.m.Y', strtotime($f['tarih'])), 0, 1, 'L');

$pdf->Cell(40, 6, 'Saat:', 0, 0, 'L');
$pdf->Cell(50, 6, date('H:i:s'), 0, 0, 'L');
$pdf->Cell(30, 6, 'Vade:', 0, 0, 'L');
$pdf->Cell(40, 6, $f['vade'] . ' Gün', 0, 1, 'L');

$pdf->Ln(5);

// Müşteri bilgileri
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(0, 6, 'SAYIN', 0, 1, 'L');
$pdf->SetFont('dejavusans', '', 9);
$pdf->MultiCell(0, 5, $f['musteri_adi'], 0, 'L');
if (!empty($f['adres'])) {
    $pdf->MultiCell(0, 5, $f['adres'], 0, 'L');
}
$pdf->Cell(0, 5, 'Vergi Dairesi: ' . ($f['vergi_daire'] ?? '-'), 0, 1, 'L');
$pdf->Cell(0, 5, 'VKN: ' . ($f['vergi_no'] ?? '-'), 0, 1, 'L');

$pdf->Ln(5);

// Ürün tablosu
$pdf->SetFont('dejavusans', 'B', 9);
$html = '<table border="1" cellpadding="4" style="border-collapse:collapse; width:100%;">
<thead>
<tr style="background-color:#ddd;">
<th width="16%">Ürün Kodu</th>
<th width="18%">Ürün Adı</th>
<th width="16%">Miktar</th>
<th width="16%">Birim Fiyat</th>
<th width="10%">İskonto%</th>
<th width="20%">Tutar</th>
</tr>
</thead>
<tbody>';

foreach ($rows as $row) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($row['urun_kod'] ?? '') . '</td>';
    $html .= '<td>' . htmlspecialchars($row['urun_adi']) . '</td>';
    $html .= '<td align="right">' . number_format($row['miktar'], 2, ',', '.') . '</td>';
    $html .= '<td align="right">' . number_format($row['birim_fiyat'], 2, ',', '.') . '</td>';
    $html .= '<td align="center">' . $row['iskonto_orani'] . '%</td>';
    $html .= '<td align="right">' . number_format($row['tutar'], 2, ',', '.') . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody>
<tfoot>
<tr style="background-color:#eee;">
<th colspan="5" align="right">Mal Hizmet Toplam Tutarı:</th>
<th align="right">' . number_format($f['toplam_tutar'], 2, ',', '.') . '</th>
</tr>
<tr>
<th colspan="5" align="right">KDV (%' . ($rows[0]['kdv_orani'] ?? 1) . '):</th>
<th align="right">' . number_format($f['toplam_kdv'], 2, ',', '.') . '</th>
</tr>
<tr style="background-color:#ddd;">
<th colspan="5" align="right">Fatura Toplamı:</th>
<th align="right">' . number_format($f['genel_toplam'], 2, ',', '.') . '</th>
</tr>
</tfoot>
</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Yazı ile toplam
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 9);
$yaziToplam = number_format($f['genel_toplam'], 2, ',', '.');
$pdf->Cell(0, 6, '#' . str_replace(',', ',', $yaziToplam) . ' TL#', 0, 1, 'C');

// Alt bilgi
$pdf->Ln(10);
$pdf->SetFont('dejavusans', 'I', 8);

// Çıktı
$pdf->Output('fatura_' . $f['fatura_no'] . '.pdf', 'D');
?>