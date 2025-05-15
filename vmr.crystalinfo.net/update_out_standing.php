<?php
include_once("init.php");

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DEPOSTOK - Ödeme Güncelleme</title>

    <!-- Stylesheets -->

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="js/date_pic/date_input.css">

    <!-- Optimize for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!-- jQuery & JS files -->
    <?php include_once("tpl/common_js.php"); ?>
    <script src="js/date_pic/jquery.date_input.js"></script>
    <script src="js/script.js"></script>
    <script>
        /*$.validator.setDefaults({
         submitHandler: function() { alert("submitted!"); }
         });*/
        $(document).ready(function () {
            $('#test1').jdPicker();
            // validate signup form on keyup and submit
            $("#form1").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3,
                        maxlength: 200
                    },

                    cost: {
                        required: true

                    },
                    sell: {
                        required: true

                    }
                },
                messages: {
                    name: {
                        required: "Lütfen bir Ürün Adı girin",
                        minlength: "Ürün en az 3 karakterden oluşmak zorundadır"
                    },
                    cost: {
                        required: "Lütfen bir Maliyet Fiyatı Girin"
                    },
                    sell: {
                        required: "Lütfen Bir Satış Fyatı Girin"
                    }
                }
            });

        });
        function numbersonly(e) {
            var unicode = e.charCode ? e.charCode : e.keyCode
            if (unicode != 8 && unicode != 46 && unicode != 37 && unicode != 38 && unicode != 39 && unicode != 40) { //if the key isn't the backspace key (which we should allow)
                if (unicode < 48 || unicode > 57)
                    return false
            }
        }
        function change_balance() {
            if (parseFloat(document.getElementById('new_payment').value) > parseFloat(document.getElementById('balance').value)) {
                document.getElementById('new_payment').value = parseFloat(document.getElementById('balance').value);
            }
        }
    </script>

</head>
<body>

<!-- TOP BAR -->
<?php include_once("tpl/top_bar.php"); ?>
<!-- end top-bar -->


<!-- HEADER -->
<div id="header-with-tabs">

    <div class="page-full-width cf">

        <ul id="tabs" class="fl">
            <li><a href="dashboard.php" class="dashboard-tab">Ana Sayfa</a></li>
			<li><a href="view_purchase.php" class="purchase-tab">Ürün Girişi</a></li>
			<li><a href="view_debits.php" class="debits-tab">Zimmet</a></li>
            <li><a href="view_personals.php" class="personals-tab">Personel</a></li>
            
            <li><a href="view_product.php" class=" stock-tab">Stoklar/Ürünler</a></li>
            <li><a href="view_report.php" class="report-tab">Raporlar</a></li>
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

            <h3>Stok Yönetimi</h3>
            <ul>
                <li><a href="view_payments.php">Ödemeler</a></li>
                <li><a href="view_out_standing.php">Borçlar</a></li>
            </ul>


        </div>
        <!-- end side-menu -->

        <div class="side-content fr">

            <div class="content-module">

                <div class="content-module-heading cf">

                    <h3 class="fl">Borç Güncelleme</h3>
                    <span class="fr expand-collapse-text">Daraltmak için tıkla</span>
                    <span class="fr expand-collapse-text initial-expand">Genişletmek için tıkla</span>

                </div>
                <!-- end content-module-heading -->

                <div class="content-module-main cf">
                    <form name="form1" method="post" id="form1" action="">

                        <table class="form" border="0" cellspacing="0" cellpadding="0">
                            <?php
                            if (isset($_POST['id'])) {

                                $id = mysqli_real_escape_string($db->getConnection(), $_POST['id']);
                                $balance = mysqli_real_escape_string($db->getConnection(), $_POST['balance']);
                                $payment = mysqli_real_escape_string($db->getConnection(), $_POST['payment']);
                                $supplier = mysqli_real_escape_string($db->getConnection(), $_POST['supplier']);
                                $subtotal = mysqli_real_escape_string($db->getConnection(), $_POST['total']);
                                $newpayment = mysqli_real_escape_string($db->getConnection(), $_POST['new_payment']);
                                $selected_date = $_POST['date'];
                                $selected_date = strtotime($selected_date);
                                $mysqldate = date('Y-m-d H:i:s', $selected_date);
                                $due = $mysqldate;
                                $balance = (int)$balance - (int)$newpayment;
                                $payment = (int)$payment + (int)$newpayment;

                                if ($db->query("UPDATE stock_entries  SET balance='$balance',payment='$payment',due='$due' where stock_id='$id'")) {
                                    $db->query("INSERT INTO transactions(type,supplier,payment,balance,rid,due,subtotal) values('entry','$supplier','$newpayment','$balance','$id','$due','$subtotal')");
                                    echo "<br><font color=green size=+1 > [ $id ] Supplier Details Updated!</font>";
                                } else
                                    echo "<br><font color=red size=+1 >Problem in Updation !</font>";


                            }
                            ?>
                            <?php
                            if (isset($_GET['sid']))
                                $id = $_GET['sid'];

                            $line = $db->queryUniqueObject("SELECT * FROM stock_entries WHERE stock_id='$id'");
                            ?>
                            <form name="form1" method="post" id="form1" action="">

                                <input name="id" type="hidden" value="<?php echo $_GET['sid']; ?>">
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>Satış&nbsp;No</td>
                                    <td><input name="stock_id" type="text" readonly="readonly" readonly="readonly"
                                               id="stockid" maxlength="200" class="round default-width-input"
                                               value="<?php echo $line->stock_id; ?> "/>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>Müşteri</td>
                                    <td><input name="customer" type="text" id="customer" maxlength="200"
                                               readonly="readonly" class="round default-width-input"
                                               value="<?php echo $line->stock_supplier_name; ?> "/></td>
                                    <td>Toplam</td>
                                    <td><input name="total" type="text" id="tatal" maxlength="20" readonly="readonly"
                                               class="round default-width-input"
                                               value="<?php echo $line->subtotal; ?>"/></td>
                                </tr>

                                <tr>
                                    <td>&nbsp;</td>
                                    <td>Ödenen</td>
                                    <td><input name="paid" type="text" id="paid" maxlength="20" readonly="readonly"
                                               class="round default-width-input"
                                               value="<?php echo $line->payment; ?>"
                                               onkeypress="return numbersonly(event)"/></td>
                                    <td>Bakiye</td>
                                    <td><input name="balance" type="text" id="balance" readonly="readonly"
                                               maxlength="20" class="round default-width-input"
                                               value="<?php echo $line->balance; ?>"
                                               onkeypress="return numbersonly(event)"/></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Yeni Tarih</td>
                                    <td><input name="date" type="text" id="test1" maxlength="20"
                                               class="round default-width-input"
                                               value="<?php echo date("Y/m/d"); ?>"/></td>
                                    <td>Yeni Ödeme</td>
                                    <td><input name="new_payment" id="new_payment" type="text"
                                               onkeypress="return numbersonly(event)" maxlength="20"
                                               onkeyup="change_balance()" class="round default-width-input"
                                            /></td>
                                </tr>


                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input class="button round blue image-right ic-add text-upper" type="submit"
                                               name="Submit" value="Kaydet">
                                        (Control + S)
                                    </td>
                                    <td align="right"><input class="button round red   text-upper" type="reset"
                                                             name="Reset" value="Vazgeç">
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                        </table>
                    </form>


                </div>
                <!-- end content-module-main -->


            </div>
            <!-- end content-module -->


        </div>
        <!-- end full-width -->

    </div>
    <!-- end content -->


    <!-- FOOTER -->
    <div id="footer">
        <p>Sorularınız için <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
        </p>

    </div>
    <!-- end footer -->

</body>
</html>