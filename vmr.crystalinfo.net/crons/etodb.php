<?php

include_once("class.emailtodb.php");

// Veritabanı bilgileri
$cfg = [
    "db_host" => 'localhost',
    "db_user" => 'crinfo',
    "db_pass" => 'SiGmA*19',
    "db_name" => 'DEPOSTOK'
];

// Modern MySQLi bağlantısı
$conn = new mysqli($cfg["db_host"], $cfg["db_user"], $cfg["db_pass"], $cfg["db_name"]);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Veritabanından e-posta login bilgilerini çekme
$sql = "SELECT * FROM email_sets WHERE set_module='CCR'";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $set_server = $row['set_server'];
        $set_user = $row['set_user'];
        $set_protokol = $row['set_protokol'];
        $set_port = $row['set_port'];
        $set_pass = $row['set_pass'];
    }
} else {
    echo "e-mail tanim Sonuçu bulunamadı";
    $conn->close();
    exit;
}

$conn->close();

// IMAP bağlantısı ve işlemler
$edb = new EMAIL_TO_DB();
//$imap_connection = sprintf('/%s:%d/notls', $set_protokol, $set_port);

$imap_connection = sprintf(':%d/%s', $set_port, $set_protokol );

if ($edb->connect($set_server, $imap_connection, $set_user, $set_pass)) {
    echo "Bağlantı başarılı";
    $edb->do_action();
} else {
    echo "IMAP bağlantısı başarısız";
}

?>
