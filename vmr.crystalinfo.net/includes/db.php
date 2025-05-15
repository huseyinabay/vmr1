<?php
$host = "localhost";
$port = 3306; // genelde MySQL'in varsayılan portu budur
$dbname = "DEPOSTOK";
$user = "root"; // kendi kullanıcı adını yaz
$pass = "";      // şifren varsa buraya yaz

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>
