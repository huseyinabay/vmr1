<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Oturum çerez ayarları
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0, // Tarayıcı kapanana kadar geçerli
        'path' => '/',
        'domain' => '', // Çoğunlukla boş bırakılır
        'secure' => isset($_SERVER['HTTPS']), // HTTPS kullanılıyorsa true
        'httponly' => true, // JavaScript erişimini engeller
    ]);
    session_start(); // Oturumu burada başlatın.
} else {
    error_log("Oturum zaten başlatılmış.");
}

//Dil ayarlari
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'tr'; // Varsayılan dil
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['tr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}


$lang_file = "lang_" . ($_SESSION['lang'] ?? 'tr') . ".php";
if (!file_exists($lang_file)) {
    die("Dil dosyası bulunamadı: " . $lang_file);
}

$lang = include_once "lang_" . $_SESSION['lang'] . ".php";

if (!is_array($lang)) {
    die("Dil dosyası düzgün yüklenemedi veya bir dizi değil.");
}

if (!isset($lang['HELP'])) {
    $lang['HELP'] = 'Help'; // Varsayılan değer
}




if (session_status() !== PHP_SESSION_ACTIVE) {
    die("Oturum başlatılamadı. Lütfen sunucu yapılandırmasını kontrol edin.");
}
if (!isset($_SESSION['username']) || !isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'admin') {
    header("location: index.php?msg=Lütfen%20Yetkili%20bir%20kullanıcı%20ile%20giriş%20%20yapın%20!&type=error");
    exit();
}
//error_reporting(0);
include("lib/db.class.php");
if (!file_exists("config.php")) {
    header("location: install.php");
    exit();
}
include_once "config.php";

require_once("lib/db.class.php");


// DB nesnesini oluştur
$db = new DB($config['host'], $config['username'], $config['password'], $config['database']);




if (!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: index.php?msg=Lütfen%20Yetkili%20bir%20kullanıcı%20ile%20giriş%20yapın%20!&type=error");
    exit();
}

if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time(); // İlk başlatma
}

if (time() - $_SESSION['last_activity'] > 1800) { // 30 dakika
    session_unset();
    session_destroy();
    header("Location: index.php?msg=Oturum%20Süresi%20Doldu&type=error");
    exit();
}

// Oturum süresi dolmadıysa, son etkinlik zamanını güncelle
$_SESSION['last_activity'] = time();

session_regenerate_id(true);

if (!isset($db)) {
    die("DB nesnesi mevcut değil. init.php dosyasını kontrol edin.");
}






function getPagination($currentPage, $totalPages, $limit, $targetPage) {
    $pagination = "<div>";
    if ($currentPage > 1) {
        $pagination .= "<a href=\"$targetPage?page=" . ($currentPage - 1) . "&limit=$limit\">Önceki</a>";
    }
    for ($i = 1; $i <= $totalPages; $i++) {
        $pagination .= $i == $currentPage ? "<span>$i</span>" : "<a href=\"$targetPage?page=$i&limit=$limit\">$i</a>";
    }
    if ($currentPage < $totalPages) {
        $pagination .= "<a href=\"$targetPage?page=" . ($currentPage + 1) . "&limit=$limit\">Sonraki</a>";
    }
    $pagination .= "</div>";
    return $pagination;
}


// Open the base (construct the object):
//$db = new DB($config['database'], $config['host'], $config['username'], $config['password']);
/*
try {
    $db = new DB($config['database'], $config['host'], $config['username'], $config['password']);
	
	echo $db; die;
	
} catch (Exception $e) {
    error_log("Veritabanı bağlantısı başarısız: " . $e->getMessage());
    die("Bir hata oluştu. Lütfen sistem yöneticisine başvurun.");
}
*/
# Note that filters and validators are separate rule sets and method calls. There is a good reason for this.

if (!file_exists("lib/gump.class.php")) {
    die("GUMP sınıfı bulunamadı. Lütfen dosyaların eksiksiz olduğundan emin olun.");
}
require "lib/gump.class.php";

$gump = new GUMP();


// Messages Settings
$DEPOSTOK = array();
$DEPOSTOK['username'] = $_SESSION['username'];
$DEPOSTOK['usertype'] = $_SESSION['usertype'];
$DEPOSTOK['msg'] = '';

$msg = $_REQUEST['msg'] ?? '';
$type = $_REQUEST['type'] ?? '';

if (!empty($msg) && !empty($type)) {
    if ($type == "error")
        $DEPOSTOK['msg'] = "<div class='error-box round'>" . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . "</div>";
    else if ($type == "warning")
        $DEPOSTOK['msg'] = "<div class='warning-box round'>" . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . "</div>";
    else if ($type == "confirmation")
        $DEPOSTOK['msg'] = "<div class='confirmation-box round'>" . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . "</div>";
    else if ($type == "infomation")
        $DEPOSTOK['msg'] = "<div class='information-box round'>" . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . "</div>";
}
?>