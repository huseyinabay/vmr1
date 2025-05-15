<?php
if (!empty($_POST['checklist'])) {
    $ids = $_POST['checklist'];

    include_once("init.php"); // Veritabanı bağlantısı

    // ID'leri güvenli hale getir
    $ids = array_map('intval', $ids);
    $idList = implode(',', $ids);

    // Veritabanından gerçek dosya yollarını al
    $query = "SELECT record_path FROM voice_records WHERE id IN ($idList)";
    $result = mysqli_query($db->getConnection(), $query);

    // ZIP dosyası oluştur
    $zip = new ZipArchive();
    $zipName = 'records_' . time() . '.zip';

    if ($zip->open($zipName, ZipArchive::CREATE) === TRUE) {
        while ($row = mysqli_fetch_assoc($result)) {
            $fullPath = $row['record_path'];
            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, basename($fullPath)); // zip içindeki adı sadeleştir
            }
        }
        $zip->close();

        // Zip'i indir
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipName . '"');
        header('Content-Length: ' . filesize($zipName));
        readfile($zipName);

        unlink($zipName); // geçici zip dosyasını sil
        exit;
    } else {
        echo "Zip oluşturulamadı.";
    }
} else {
    echo "İndirmek için kayıt seçilmedi.";
}
?>
