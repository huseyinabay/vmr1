<?php
include("init.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $note = mysqli_real_escape_string($db->getConnection(), $_POST['note'] ?? '');

    $sql = "UPDATE voice_records SET note = '$note' WHERE id = $id";
    if (mysqli_query($db->getConnection(), $sql)) {
        http_response_code(200); // Başarılı
        echo "success";
    } else {
        http_response_code(500); // Hata
        echo "error";
    }
    exit;
}
?>
