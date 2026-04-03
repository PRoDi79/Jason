<?php
$upload_dir = '../assets/uploads/logos/';
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

if ($_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    $filename = 'logo_' . time() . '.' . $ext;
    $path = 'assets/uploads/logos/' . $filename;
    move_uploaded_file($_FILES['logo']['tmp_name'], '../' . $path);
    echo json_encode(['success' => true, 'path' => $path]);
} else {
    echo json_encode(['success' => false, 'error' => 'Yükleme hatası']);
}
?>