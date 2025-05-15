<?php
include("init.php");

function deleteFileFromDB($id, $table, $db) {
    $id = intval($id);
    $table = mysqli_real_escape_string($db->getConnection(), $table);

    // Dosya yolunu önce al
    $query = "SELECT record_path FROM `$table` WHERE id = $id";
    $result = mysqli_query($db->getConnection(), $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $file = $row['record_path'];
        if (file_exists($file)) {
            unlink($file); // sunucudan dosyayı sil
        }
    }
}

// === Tekli Silme (GET) ===
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'], $_GET['table'], $_GET['return'])) {
    $id = intval($_GET['id']);
    $table = mysqli_real_escape_string($db->getConnection(), $_GET['table']);
    $return_page = mysqli_real_escape_string($db->getConnection(), $_GET['return']);

    // önce dosyayı sil
    deleteFileFromDB($id, $table, $db);

    // sonra DB'den sil
    $query = "DELETE FROM `$table` WHERE id = $id";
    mysqli_query($db->getConnection(), $query);

    header("Location: $return_page?success=1");
    exit();
}

// === Çoklu Silme (POST) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checklist'], $_POST['table'], $_POST['return'])) {
    $table = mysqli_real_escape_string($db->getConnection(), $_POST['table']);
    $return_page = mysqli_real_escape_string($db->getConnection(), $_POST['return']);
    $ids = array_map('intval', $_POST['checklist']);

    if (!empty($ids)) {
        foreach ($ids as $id) {
            deleteFileFromDB($id, $table, $db);
        }

        // topluca DB'den sil
        $id_list = implode(',', $ids);
        $query = "DELETE FROM `$table` WHERE id IN ($id_list)";
        mysqli_query($db->getConnection(), $query);

        header("Location: $return_page?success=1");
        exit();
    }
}

// Eksik parametre
header("Location: index.php?error=missing_parameters");
exit();
