<?php
// Hata mesajlarının ekranda gösterilmesi ayarları yapılıyor
ini_set('display_errors', 'On'); // Ekrana hata mesajları gösterilsin
error_reporting(E_ALL ^ E_NOTICE); // NOTICE hataları hariç tüm hatalar rapor edilsin

// Oturum başlatılıyor
session_start(); // Yeni bir veya var olan oturum başlatılır

// config.php dosyası yoksa veya dahil edilemediyse, install.php'ye yönlendiriliyor
if (!file_exists("config.php") || !include_once "config.php") { // config.php dosyası var mı diye kontrol et
    header("location: install.php"); // Bulunamazsa install.php'ye yönlendir
}

// posnicEntry sabiti tanımlanmadıysa tanımlıyoruz
if (!defined('posnicEntry')) { // Sabit tanımlanmış mı?
    define('posnicEntry', true); // Tanımlı değilse bu değeri atıyoruz
}

// Eğer oturumda 'username' tanımlıysa ve kullanıcı tipi 'admin' ise dashboard.php'ye yönlendiriyoruz
if (isset($_SESSION['username'])) { // Oturumda 'username' var mı?
    if ($_SESSION['usertype'] == 'admin') // Kullanıcı tipi admin mi?
        header("location: dashboard.php"); // admin ise dashboard.php'ye yönlendir
}

// Dil desteği başlangıcı
if (!isset($_SESSION['lang'])) { // Oturumda 'lang' değişkeni yoksa
    $_SESSION['lang'] = 'tr'; // Varsayılan dili Türkçe yap
}

// GET isteğinde 'lang' parametresi varsa dil değiştirme işlemi
if (isset($_GET['lang'])) { // URL'de lang parametresi var mı?
    $lang = $_GET['lang']; // Değişkeni çek
    $_SESSION['lang'] = in_array($lang, ['tr', 'en']) ? $lang : 'tr'; // tr veya en değilse varsayılan tr
}

// Dil dosyası adını oluşturuyoruz
$lang_file = "lang_" . ($_SESSION['lang'] ?? 'tr') . ".php"; // Örneğin lang_tr.php veya lang_en.php

// Dil dosyası varsa dahil ediyoruz, yoksa hata veriyoruz
if (file_exists($lang_file)) { // Dil dosyası var mı?
    $lang = include $lang_file; // Dosyayı dahil et ve $lang değişkenine al
} else {
    die("Dil dosyası bulunamadı: " . $lang_file); // Dosya yoksa hata ver ve işlemi bitir
}

/*
// Bu blok yorum içinde kalanı satır satır bölelim (kullanıcı talebi doğrultusunda)

// include_once "lang_" . $_SESSION['lang'] . ".php";

// $lang_file = "lang_" . $_SESSION['lang'] . ".php";
// if (file_exists($lang_file)) {
//     include_once $lang_file;
// } else {
//     die("Dil dosyası bulunamadı: " . $lang_file);
// }

// if (!in_array($_SESSION['lang'], ['tr', 'en'])) {
//     $_SESSION['lang'] = 'tr'; // Geçersiz bir dil değerini engeller.
// }

// $lang = include_once "lang_" . $_SESSION['lang'] . ".php";
*/
?>
<!DOCTYPE html>

<html lang="<?php echo $_SESSION['lang']; ?>"><!-- Oturumda belirlenen dili html lang attribute'ına yansıtır -->
<head>
    <meta charset="utf-8"><!-- Karakter seti UTF-8 -->
    <title>VMR - <?php echo $lang['PAGE_TITLE']; ?></title><!-- Sayfa başlığı -->

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/style.css"><!-- Genel stil dosyası -->
    <link rel="stylesheet" href="css/cmxform.css"><!-- Form stil dosyası -->
    <link rel="stylesheet" href="js/lib/validationEngine.jquery.css"><!-- Form validasyon stil dosyası -->

    <!-- Scripts -->
    <script src="js/lib/jquery.min.js" type="text/javascript"></script><!-- jQuery kütüphanesi -->
    <script src="js/lib/jquery.validate.min.js" type="text/javascript"></script><!-- jQuery validate plugin -->

    <script>
        // Sayfa yüklendikten sonra form validasyon kuralları
        $(document).ready(function () {
            // "login-form" üzerinde geçerli giriş kriterlerini belirtiyoruz
            $("#login-form").validate({
                rules: {
                    username: {
                        required: true, // Zorunlu
                        minlength: 3    // En az 3 karakter
                    },
                    password: {
                        required: true, // Zorunlu
                        minlength: 3    // En az 3 karakter
                    }
                },
                messages: {
                    username: {
                        required: "<?php echo $lang['USERNAME_REQUIRED']; ?>", // Kullanıcı adı gerekli
                        minlength: "<?php echo $lang['USERNAME_MINLENGTH']; ?>" // Min. 3 karakter
                    },
                    password: {
                        required: "<?php echo $lang['PASSWORD_REQUIRED']; ?>", // Şifre gerekli
                        minlength: "<?php echo $lang['PASSWORD_MINLENGTH']; ?>" // Min. 3 karakter
                    }
                }
            });
        });
    </script>

    <!-- Mobil cihazlar için optimize et -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>


<!-- Dil Seçimi -->
<div id="language-switch" style="position: absolute; top: 20px; right: 10px; text-align: right;">
    <!-- GET parametrelerini koruyarak lang parametresini değiştiren linkler -->
    <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'tr'])); ?>">Türkçe</a> |
    <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'en'])); ?>">English</a>
</div>



<!-- TOP BAR -->
<div id="top-bar">
    <div class="page-full-width">
        <!-- Buraya bir şey eklenmemiş, boş bırakılmış -->
    </div>
    <!-- end full-width -->
</div>
<!-- end top-bar -->


<!-- HEADER -->
<div id="header">
    <div class="page-full-width cf">
        <div id="login-intro" class="fl">
            <h1><?php echo $lang['LOGIN_HEADER']; ?></h1><!-- "Giriş Yapın" gibi bir başlık -->
            <h5><?php echo $lang['LOGIN_SUBHEADER']; ?></h5><!-- "Bilgilerinizi giriniz." gibi bir alt başlık -->
        </div>
        <!-- login-intro -->

        <!-- Logo alanı -->
        <!-- Resim otomatik olarak 39px yüksekliğe ayarlanacak diyor -->
        <a href="#" id="company-branding" class="fr">
            <img src="<?php if (isset($_SESSION['logo'])) {
                echo "upload/" . $_SESSION['logo']; // Oturumda logo tanımlıysa onu göster
            } else {
                echo "upload/depostok.png"; // Değilse varsayılan logo
            } ?>" alt="Depo Stok"/>
        </a>
    </div>
    <!-- end full-width -->
</div>
<!-- end header -->


<!-- MAIN CONTENT -->
<div id="content">
    <!-- Giriş kontrolü yapılan sayfaya yönlendirilecek form -->
    <form action="checklogin.php" method="POST" id="login-form" class="cmxform" autocomplete="off">
        <fieldset>
            <p>
                <?php
                // Mesaj göstermek için
                if (isset($_REQUEST['msg']) && isset($_REQUEST['type'])) { // msg ve type parametresi gelmiş mi?
                    if ($_REQUEST['type'] == "error")
                        $msg = "<div class='error-box round'>" . $_REQUEST['msg'] . "</div>"; // Hata kutusu
                    else if ($_REQUEST['type'] == "warning")
                        $msg = "<div class='warning-box round'>" . $_REQUEST['msg'] . "</div>"; // Uyarı kutusu
                    else if ($_REQUEST['type'] == "confirmation")
                        $msg = "<div class='confirmation-box round'>" . $_REQUEST['msg'] . "</div>"; // Onay kutusu
                    else if ($_REQUEST['type'] == "information")
                        $msg = "<div class='information-box round'>" . $_REQUEST['msg'] . "</div>"; // Bilgi kutusu

                    echo $msg; // Mesajı ekrana bas
                }
                ?>
            </p>

            <p>
                <label for="login-username"><?php echo $lang['USERNAME']; ?></label><!-- "Kullanıcı Adı" etiketi -->
                <input type="text" id="login-username" class="round full-width-input" placeholder="user"
                       name="username" autofocus/>
            </p>

            <p>
                <label for="login-password"><?php echo $lang['PASSWORD']; ?></label><!-- "Şifre" etiketi -->
                <div style="display:inline-block flex; align-items: center; gap: 10px;">
                    <input type="password" id="login-password" name="password"
                           placeholder="password" class="round full-width-input"
                           maxlength="50" style="flex: 1;" />
                    <button type="button" id="toggle-password" class="button"
                            aria-label="<?php echo $lang['SHOW']; ?>"
                            style="padding: 8px 12px; font-size: 14px;"><?php echo $lang['SHOW']; ?></button>
                </div>
            </p>

            <script>
                // DOM yüklendiğinde şifre göster/gizle butonu fonksiyonelliği
                document.addEventListener("DOMContentLoaded", function () {
                    const passwordField = document.getElementById("login-password"); // Şifre inputunu al
                    const toggleButton = document.getElementById("toggle-password"); // Toggle butonunu al

                    toggleButton.addEventListener("click", function () {
                        // input'un tipini password/text arasında değiştir
                        const type = passwordField.type === "password" ? "text" : "password";
                        passwordField.type = type;
                        // Butonun metnini "Göster" veya "Gizle" olarak değiştir
                        toggleButton.textContent = type === "password" ? "<?php echo $lang['SHOW']; ?>" : "<?php echo $lang['HIDE']; ?>";
                        // Erişim etiketi
                        toggleButton.setAttribute("aria-label", type === "password" ? "<?php echo $lang['SHOW']; ?>" : "<?php echo $lang['HIDE']; ?>");
                    });
                });
            </script>

            <!-- Şifremi unuttum linki -->
            <a href="forget_pass.php" style="margin-left:-10px" class="button "><?php echo $lang['FORGOT_PASSWORD']; ?></a>

            <!-- Giriş butonu -->
            <input type="submit" style="margin-left:100px" class="button round blue image-right ic-right-arrow" name="submit" value="<?php echo $lang['LOGIN']; ?>"/>
        </fieldset>

        <br/>

        <!-- Bilgilendirme kutusu -->
        <div class="information-box round"><?php echo $lang['LOGIN_INFO']; ?></div>
    </form>
</div>
<!-- end content -->


<!-- FOOTER -->
<div id="footer">
    <!-- İletişim bilgisi -->
    <p><?php echo $lang['FOOTER_CONTACT']; ?>
        <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
    </p>
</div>
<!-- end footer -->

</body>
</html>
