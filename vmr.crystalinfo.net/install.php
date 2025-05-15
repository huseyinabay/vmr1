<?php

?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DEPOSTOK - Ana Sayfaya Giriş</title>

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
                    host: {
                        required: true,
                        minlength: 3
                    },
                    username: {
                        required: true,
                        minlength: 3
                    }
                },
                messages: {
                    host: {
                        required: "Ana Makineyi Girin",
                        minlength: "Ana Makine en az 3 karakterden oluşmak zorundadır"
                    },
                    username: {
                        required: "Lütfen Kullanıcı Adını Girin",
                        minlength: "Kullanıcı Adı en az 3 karakterden oluşmak zorundadır"
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

            <h1>Kurulum </h1>


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

    <form action="database_install.php" method="POST" id="login-form" class="cmxform" autocomplete="off">

        <fieldset>
            <p> <?php

                if (isset($_REQUEST['msg'])) {

                    $msg = $_REQUEST['msg'];
                    echo "<p style=color:red>$msg</p>";
                }
                ?>

            </p>

            <p>
                <label for="login-host">VeriTabanı Makine Adı</label>
                <input type="text" id="host" class="round full-width-input" placeholder="ANA MAKİNE ADI GİRİN"
                       name="host" autofocus/>
            </p>

            <p>
                <label for="login-user">VeriTabanı Kullanıcı Adı</label>
                <input type="text" id="username" name="username" placeholder="VERİTABANI KULLANICI ADI"
                       class="round full-width-input"/>
            </p>

            <p>
                <label for="login-password">VeriTabanı Şifresi</label>
                <input type="password" id="password" name="password" placeholder="VERİTABANI ŞİFRESİ"
                       class="round full-width-input"/>
            </p>


            <!--<a href="dashboard.php" class="button round blue image-right ic-right-arrow">LOG IN</a>-->
            <input type="submit" class="button round blue image-right ic-right-arrow" name="submit" value="KUR"/>
        </fieldset>

    </form>

</div>
<!-- end content -->


<!-- FOOTER -->
<div id="footer">

    <p>Sorularınız İçin <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
    </p>


</div>
<!-- end footer -->

</body>
</html>