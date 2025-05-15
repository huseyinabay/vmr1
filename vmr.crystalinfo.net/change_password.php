<!-->

<?php
include_once("init.php,header.php");
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DEPOSTOK - Ana Menü</title>

    <!-- Stylesheets -->

    <link rel="stylesheet" href="css/style.css">

    <!-- Optimize for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!-- jQuery & JS files -->
    <?php include_once("tpl/common_js.php"); ?>
    <script src="js/script.js"></script>
</head>
<body>

<!-- TOP BAR -->
<?php include_once("tpl/top_bar.php"); ?>
<!-- end top-bar -->


<!-- HEADER -->
<div id="header-with-tabs">

    <div class="page-full-width cf">

        <ul id="tabs" class="fl">
		<li><a href="dashboard.php" class=" active-tab dashboard-tab"><?php echo $lang['dashboard'] ?? 'Dashboard'; ?></a></li>
	<li><a href="crm.php" class=" active-tab dashboard-tab"><?php echo $lang['crm'] ?? 'CRM'; ?></a></li>
	<li><a href="vmr.php" class=" active-tab dashboard-tab"><?php echo $lang['a'] ?? 'VMR'; ?></a></li>
        </ul>
        <!-- end tabs -->

        <!-- Change this image to your own company's logo -->
        <!-- The logo will automatically be resized to 30px height. -->
        <a href="#" id="company-branding-small" class="fr"><img src="<?php if (isset($_SESSION['logo'])) {
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

    <div class="page-full-width cf">

        <div class="side-menu fl">

            <h3>KISAYOLLAR</h3>
            <ul>
                <li><a href="add_sales.php">Satış Ekle</a></li>
                <li><a href="add_purchase.php">Satınalma Ekle</a></li>
                <li><a href="add_supplier.php">Tedarikçi Ekle</a></li>
                <li><a href="add_customer.php">Müşteri Ekle</a></li>
                <li><a href="view_report.php">Raporla</a></li>
            </ul>

        </div>
        <!-- end side-menu -->

        <div class="side-content fr">

            <div class="content-module">

                <div class="content-module-heading cf">

                    <h3 class="fl">Şifre Değiştirme</h3>
                    <span class="fr expand-collapse-text">Daraltmak için tıkla</span>
                    <span class="fr expand-collapse-text initial-expand">Genişletmek için tıkla</span>

                </div>
                <!-- end content-module-heading -->

                <div class="content-module-main cf">

                    <?php
                    if (isset($_POST['old_pass']) and isset($_POST['new_pass']) and isset($_POST['confirm_pass'])) {
                        $username = $_SESSION['username'];
                        $old_pass = $_POST['old_pass'];
                        $count = $db->countOf("stock_user", "username='$username' and password='$old_pass'");
                        if ($count == 0) {
                            echo "<br><font color=red size=6px >Old Password is wrong!</font>";
                        } else {
                            if (trim($_POST['new_pass']) == trim($_POST['confirm_pass'])) {
                                $con = $_POST['confirm_pass'];
                                $db->query("update stock_user  SET password='$con' where username='$username'");
                                echo "<br><font color=green size=6px >Password is Successfuly updated!</font>";
                            } else {
                                echo "<br><font color=red size=6px >Confirm password is Wrong!</font>";
                            }
                        }
                    }
                    ?>
                    <form action="" method="post">
                        <table style="width:600px; margin-left:50px; float:left;" border="0" cellspacing="0"
                               cellpadding="0">

                            <tr>
                                <td>Eski Şifre</td>
                                <td><input type="password" name="old_pass" readonly="readonly"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Yeni Şifre</td>
                                <td><input type="password" name="new_pass" readonly="readonly"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Şifreyi Doğrula</td>
                                <td><input type="password" name="confirm_pass" readonly="readonly"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr></tr>
                            <tr>
                                <td>
                                    <input class="button round blue image-right ic-add text-upper" type="submit"
                                           name="Submit" name="change_pass" value="Kydet">
                                </td>
                                <td>
                                    <input class="button round red   text-upper" type="reset" name="Reset"
                                           value="Vazgeç"></td>
                            </tr>

                        </table>
                    </form>
                    <!--<ul class="temporary-button-showcase">
                                        <li><a href="#" class="button round blue image-right ic-add text-upper">Add</a></li>
                                        <li><a href="#" class="button round blue image-right ic-edit text-upper">Edit</a></li>
                                        <li><a href="#" class="button round blue image-right ic-delete text-upper">Delete</a></li>
                                        <li><a href="#" class="button round blue image-right ic-download text-upper">Download</a></li>
                                        <li><a href="#" class="button round blue image-right ic-upload text-upper">Upload</a></li>
                                        <li><a href="#" class="button round blue image-right ic-favorite text-upper">Favorite</a></li>
                                        <li><a href="#" class="button round blue image-right ic-print text-upper">Print</a></li>
                                        <li><a href="#" class="button round blue image-right ic-refresh text-upper">Refresh</a></li>
                                        <li><a href="#" class="button round blue image-right ic-search text-upper">Search</a></li>
                                    </ul>-->

                </div>
                <!-- end content-module-main -->


            </div>
            <!-- end content-module -->


        </div>
        <!-- end full-width -->

    </div>
</div>


<!-- FOOTER -->
<div id="footer">
    <div id="fb-root"></div>
    <div id="fb-root"></div>
  

   		<p>Sorularınız için <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
    </p>

</div>
<!-- end footer -->

</body>
</html>