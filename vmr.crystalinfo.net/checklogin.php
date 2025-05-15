<?php

 ini_set('display_errors', 'On');
 error_reporting(E_ALL ^ E_NOTICE);

session_start();

include("lib/db.class.php");
include_once "config.php";
$db = new DB($config['host'], $config['username'], $config['password'], $config['database']);
$tbl_name = "stock_user"; // Table name

// username and password sent from form 
$myusername = $_POST['username'] ?? '';
$mypassword = $_POST['password'] ?? '';


$myusername = mysqli_real_escape_string($db->getConnection(), $myusername);
$mypassword = mysqli_real_escape_string($db->getConnection(), $mypassword);

$stmt = $db->getConnection()->prepare("SELECT id, username, password, user_type FROM $tbl_name WHERE username = ?");
$stmt->bind_param("s", $myusername);
$stmt->execute();
$result = $stmt->get_result();

$stmt = $db->getConnection()->prepare("SELECT id, username, password, user_type FROM $tbl_name WHERE username = ?");
$stmt->bind_param("s", $myusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();

    // Şifre doğrulama
    if (password_verify($mypassword, $row['password'])) {
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['usertype'] = $row['user_type'];

        if ($row['user_type'] == "admin") {
            header("location: dashboard.php");
            exit();
        } else {
            die("Geçersiz Kullanıcı. uygulama yöneticinize danışın");
        }
    } else {
        header("location: index.php?msg=Yanlış%20kullanıcıadı%20veya%20Şifre&type=error");
        exit();
    }
} else {
    header("location: index.php?msg=Yanlış%20kullanıcıadı%20veya%20Şifre&type=error");
    exit();
}

/* düz şifreleme için db de açık olan pass

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();

    if ($mypassword === $row['password']) { // Düz metin şifre kontrolü
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['usertype'] = $row['user_type'];

        if ($row['user_type'] == "admin") {
            header("location: dashboard.php");
            exit();
        } else {
            die("Geçersiz Kullanıcı. uygulama yöneticinize danışın");
        }
    } else {
        header("location: index.php?msg=Yanlış%20kullanıcıadı%20veya%20Şifre&type=error");
        exit();
    }
} else {
    header("location: index.php?msg=Yanlış%20kullanıcıadı%20veya%20Şifre&type=error");
    exit();
}

*/
?>
