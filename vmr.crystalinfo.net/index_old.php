<?php

 ini_set('display_errors', 'On');
 error_reporting(E_ALL ^ E_NOTICE);
 
session_start();
if (!file_exists("config.php") || !include_once "config.php") {
    header("location: install.php");

}
if (!defined('posnicEntry')) {
    define('posnicEntry', true);

}
if (isset($_SESSION['username'])) {
    if ($_SESSION['usertype'] == 'admin') // if session variable "username" does not exist.
        header("location: dashboard.php"); // 1 Re-direct to index.php
		
}



?>
<!DOCTYPE html>

<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>DEPOSTOK - Anasyafa</title>

    <!-- Stylesheets -->

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cmxform.css">
    <link rel="stylesheet" href="js/lib/validationEngine.jquery.css">

    <!-- Scripts -->
    <script src="js/lib/jquery.min.js" type="text/javascript"></script>
    <script src="js/lib/jquery.validate.min.js" type="text/javascript"></script>

    <script>
        /*$.validator.setDefaults({
         submitHandler: function() { alert("submitted!"); }
         });*/

        $(document).ready(function () {

            // validate signup form on keyup and submit
            $("#login-form").validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 3
                    },
                    password: {
                        required: true,
                        minlength: 3
                    }
                },
                messages: {
                    username: {
                        required: "Lütfen bir kullanıcı adı girin",
                        minlength: "Kullanıcı adınız en az 3 karakterden oluşmak zorundadır"
                    },
                    password: {
                        required: "Lütfen bir şifre tanımlayın",
                        minlength: "Şifreniz en az 3 karakterden oluşmak zorundadır"
                    }
                }
            });

        });

    </script>

    <!-- Optimize for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>

<!--    Only Index Page for Analytics   -->

<!-- TOP BAR -->
<div id="top-bar">

    <div class="page-full-width">

        <!--<a href="#" class="round button dark ic-left-arrow image-left ">See shortcuts</a>-->

    </div>
    <!-- end full-width -->

</div>
<!-- end top-bar -->


<!-- HEADER -->
<div id="header">

    <div class="page-full-width cf">

        <div id="login-intro" class="fl">

            <h1>Ana Sayfaya Giriş</h1>
            <h5>Lütfen kullanıcı bilgilerinizi girin</h5>

        </div>
        <!-- login-intro -->

        <!-- Change this image to your own company's logo -->
        <!-- The logo will automatically be resized to 39px height. -->
        <a href="#" id="company-branding" class="fr"><img src="<?php if (isset($_SESSION['logo'])) {
                echo "upload/" . $_SESSION['logo'];
            } else {
                echo "upload/depostok.png";
            } ?>" alt="Depo Stok"/></a>

    </div>
    <!-- end full-width -->

</div>
<!-- end header -->


<!-- MAIN CONTENT -->
<div id="content">

    <form action="checklogin.php" method="POST" id="login-form" class="cmxform" autocomplete="off">

        <fieldset>
            <p> <?php

                if (isset($_REQUEST['msg']) && isset($_REQUEST['type'])) {

                    if ($_REQUEST['type'] == "error")
                        $msg = "<div class='error-box round'>" . $_REQUEST['msg'] . "</div>";
                    else if ($_REQUEST['type'] == "warning")
                        $msg = "<div class='warning-box round'>" . $_REQUEST['msg'] . "</div>";
                    else if ($_REQUEST['type'] == "confirmation")
                        $msg = "<div class='confirmation-box round'>" . $_REQUEST['msg'] . "</div>";
                    else if ($_REQUEST['type'] == "information")
                        $msg = "<div class='information-box round'>" . $_REQUEST['msg'] . "</div>";

                    echo $msg;
                }
                ?>

            </p>

            <p>
                <label for="login-username">KULLANICIADI</label>
                <input type="text" id="login-username" class="round full-width-input" placeholder="admin"
                       name="username" autofocus/>
            </p>

            <p>
                <label for="login-password">ŞİFRE</label>
				<input type="password" id="login-password" name="password" placeholder="admin" class="round full-width-input" />
				<button type="button" id="toggle-password" class="button">Göster</button>
            </p>

            <a href="forget_pass.php" class="button ">Şifrenizi mi unuttunuz?</a>

            <!--<a href="dashboard.php" class="button round blue image-right ic-right-arrow">LOG IN</a>-->
            <input type="submit" class="button round blue image-right ic-right-arrow" name="submit" value="GİRİŞ"/>
        </fieldset>

        <br/>

        <div class="information-box round">Giriş yapmak için GİRİŞ butonuna tıklayınız.
        </div>

    </form>

</div>
<!-- end content -->


<!-- FOOTER -->

<div id="footer">


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordField = document.getElementById("login-password");
        const toggleButton = document.getElementById("toggle-password");

        toggleButton.addEventListener("click", function () {
            const type = passwordField.type === "password" ? "text" : "password";
            passwordField.type = type;
            toggleButton.textContent = type === "password" ? "Göster" : "Gizle";
        });
    });
</script>

<!--  Halil
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=286371564842269";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>


	
  <div id="fb-root"></div>
    <div class="fb-like" data-href="https://www.facebook.com/posnic.point.of.sale" data-width="450"
         data-show-faces="true" data-send="true"></div>
    <div class="g-plusone" data-href="https://plus.google.com/u/0/107268519615804538483"></div>
  -->
    <script type="text/javascript">
        (function () {
            var po = document.createElement('script');
            po.type = 'text/javascript';
            po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(po, s);
        })();
    </script>
    <p>Destek ve sorularınız için <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
    </p>


</div>
<!-- end footer -->

</body>
</html>
