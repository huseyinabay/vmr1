<?php session_start();

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
            document.getElementById('create').checked = true;
            document.getElementById('select_box').disabled = true;

            // validate signup form on keyup and submit
            $("#login-form").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    }

                },
                messages: {
                    name: {
                        required: "Lütfen Veritabanı Adını girin",
                        minlength: "Veritabanı adı en az 3 karakterden oluşmak zorundadır"
                    }
                }
            });

        });
        function create_data() {
            document.getElementById("select_box").disabled = true;
            document.getElementById("name").disabled = false;

        }
        function select_data() {
            document.getElementById("select_box").disabled = false;
            document.getElementById("name").disabled = true;

        }
    </script>
    <style type="text/css">

    </style>
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

            <h1>VeriTabanı </h1>


        </div>
        <!-- login-intro -->

        <!-- Change this image to your own company's logo -->
        <!-- The logo will automatically be resized to 39px height. -->
        <a href="#" id="company-branding" class="fr"><img src="upload/posnic.png" alt="Depo Stok"/></a>

    </div>
    <!-- end full-width -->

</div>
<!-- end header -->


<!-- MAIN CONTENT -->
<div id="content">
    <?php
    if ((isset($_POST['host']) and isset($_POST['username']) and $_POST['host'] != "" and $_POST['username'] != "") or (isset($_SESSION['host']) and isset($_SESSION['user'])))
    {

    if (isset($_SESSION['host'])) {
        $host = $_SESSION['host'];
        $user = $_SESSION['user'];
        $pass = $_SESSION['pass'];
    }
    if (isset($_POST['host'])) {
        $host = trim($_POST['host']);
        $user = trim($_POST['username']);
        $pass = trim($_POST['password']);
    }
    $link = mysqli_connect("$host", "$user", "$pass");
    if (!$link) {
        $data = "VeriTabanı Ayarları Geçersiz";
        header("location: install.php?msg=$data");
        exit;
    }

    ?>
    <form action="setup_page.php" method="POST" id="login-form" class="cmxform" autocomplete="off">

        <fieldset>
            <p> <?php

                if (isset($_REQUEST['msg'])) {

                    $msg = $_REQUEST['msg'];
                    echo "<p style=color:red>$msg</p>";
                }
                ?>

            </p>

            <p>
                <?php
                $con = mysqli_connect("$host", "$user", "$pass");
                // Check connection
                $sql = "CREATE DATABASE MY_posnic_1234";
                if (mysqli_query($con, $sql)) {
                    $sql = "DROP DATABASE MY_posnic_1234";
                    mysqli_query($con, $sql);

                    ?>
                    <input type="radio" value="1" name="select[]" id="create"
                           onclick="create_data()">Yeni VeriTabanı Oluştur
                    <input type="text" id="name" class="round full-width-input" name="name" autofocus/>
                    <?php
                } else {
                    ?>

                    <input type="radio" disabled="disabled">Yeni VeriTabanı Oluştur
                    <input type="text" disabled="disabled" class="round full-width-input"
                           placeholder="No Permission To Create New Database" name="name" autofocus/>
                    <?php
                }
                ?>


            </p>

            <p>
                <input type="radio" name="select[]" id="select" onclick="select_data()">VeriTabanı Seç<br>
                <select name="select_box" class="round full-width-input" id="select_box"
                        style="padding: 5px 10px 5px 10px; border: 1px solid #D9DBDD;">
                    <?php


                    $dbh = new PDO("mysql:host=$host", $user, $pass);
                    $dbs = $dbh->query('SHOW DATABASES');

                    while (($db = $dbs->fetchColumn(0)) !== false) {
                        echo "<option value=" . $db . " style=margin:10px 10px 10px 10px;><p >$db</p></option>";
                    }
                    ?>
                </select>

            </p>
            <input type="hidden" name="host" value="<?php echo $host ?>">

            <input type="hidden" name="username" value="<?php echo $user ?>">
            <input type="hidden" name="password" value="<?php echo $pass ?>">

            <br>
            <input type="checkbox" name="dummy" value="1">Örnek veri ekle
            <br>
            <br>


            <!--<a href="dashboard.php" class="button round blue image-right ic-right-arrow">LOG IN</a>-->
            <input type="submit" class="button round blue image-right ic-right-arrow" name="submit" value="INSTALL"/>
        </fieldset>

    </form>

</div>
<!-- end content -->
<?php } ?>


<!-- FOOTER -->
<div id="footer">

    <p>Sorularınız için <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
    </p>


</div>
<!-- end footer -->

</body>
</html>