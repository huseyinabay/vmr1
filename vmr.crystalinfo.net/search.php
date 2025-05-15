<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchtxt = $_POST['searchtxt'];

    if (!empty($searchtxt)) {
        // Veritabanı bağlantısı
        $conn = new mysqli("localhost", "username", "password", "database");

        // Bağlantı kontrolü
        if ($conn->connect_error) {
            die("Bağlantı başarısız: " . $conn->connect_error);
        }

        // Arama sorgusu
        $sql = "SELECT * FROM tablonuz WHERE kolon_adı LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchPattern = "%" . $searchtxt . "%";
        $stmt->bind_param("s", $searchPattern);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h3>Arama Sonuçları:</h3>";
            while ($row = $result->fetch_assoc()) {
                echo "<p>" . $row["kolon_adı"] . "</p>";
            }
        } else {
            echo "<p>Sonuç bulunamadı.</p>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<p>Lütfen bir arama metni girin.</p>";
    }
}
?>
