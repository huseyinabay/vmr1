<?php
include_once("init.php");

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>POSNIC - Update Supplier</title>

    <!-- Stylesheets -->

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="js/date_pic/date_input.css">
    <link rel="stylesheet" href="lib/auto/css/jquery.autocomplete.css">

    <!-- Optimize for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!-- jQuery & JS files -->
    <?php include_once("tpl/common_js.php"); ?>
    <script src="js/date_pic/jquery.date_input.js"></script>
    <script src="lib/auto/js/jquery.autocomplete.js "></script>
    <script src="js/script.js"></script>
    <script>
        /*$.validator.setDefaults({
         submitHandler: function() { alert("submitted!"); }
         });*/
        $(document).ready(function () {

            // validate signup form on keyup and submit
            $("#form1").validate({
                rules: {
                    bill_no: {
                        required: true,
                        minlength: 3

                    },
                    stockid: {
                        required: true
                    },
                    grand_total: {
                        required: true
                    },
                    supplier: {
                        required: true,
                    }
                },
                messages: {
                    supplier: {
                        required: "L�tfen Tedarik�i Girin"
                    },
                    stockid: {
                        required: "L�tfen Stok Numaras� Girin"
                    },
                    grand_total: {
                        required: "�r�n Ekle"
                    },
                    bill_no: {
                        required: "L�tfen Fi� Numaras�n� Girin",
                        minlength: "Fi� Numaras� en az 3 karakterden olu�mak zorundad�r"
                    }
                }
            });

        });
        $(function () {
            $("#supplier").autocomplete("supplier1.php", {
                width: 160,
                autoFill: true,
                selectFirst: true
            });
            $("#item").autocomplete("stock_purchse.php", {
                width: 160,
                autoFill: true,
                mustMatch: true,
                selectFirst: true
            });
            $("#item").blur(function () {
                document.getElementById('total').value = document.getElementById('cost').value * document.getElementById('quty').value
            });
            $("#item").blur(function () {


                $.post('check_item_details.php', {stock_name1: $(this).val()},
                    function (data) {
                    	$("#unit").val(data.cost);
                        $("#cost").val(data.cost);
                        $("#sell").val(data.sell);
                        $("#stock").val(data.stock);
                        $('#guid').val(data.guid);
                        if (data.cost != undefined)
                            $("#0").focus();


                    }, 'json');


            });
            $("#supplier").blur(function () {


                $.post('check_supplier_details.php', {stock_name1: $(this).val()},
                    function (data) {

                        $("#address").val(data.address);
                        $("#contact1").val(data.contact1);
                        $("#phone1").val(data.phone1);
                        $("#province").val(data.province);
                        $("#district").val(data.district);

                        if (data.address != undefined)
                            $("#0").focus();

                    }, 'json');


            });
            $('#test1').jdPicker();
            $('#test2').jdPicker();


            var hauteur = 0;
            $('.code').each(function () {
                if ($(this).height() > hauteur) hauteur = $(this).height();
            });

            $('.code').each(function () {
                $(this).height(hauteur);
            });
        });

        function numbersonly(e) {
            var unicode = e.charCode ? e.charCode : e.keyCode
            if (unicode != 8 && unicode != 46 && unicode != 37 && unicode != 38 && unicode != 39 && unicode != 40) { //if the key isn't the backspace key (which we should allow)
                if (unicode < 48 || unicode > 57)
                    return false
            }
        }
        function edit_stock_details(id) {
            document.getElementById('display').style.display = "block";

            document.getElementById('item').value = document.getElementById(id + 'st').value;
            document.getElementById('quty').value = document.getElementById(id + 'q').value;
            document.getElementById('unit').value = document.getElementById(id + 'u').value;
            document.getElementById('cost').value = document.getElementById(id + 'c').value;
            document.getElementById('sell').value = document.getElementById(id + 's').value;
            document.getElementById('stock').value = document.getElementById(id + 'p').value;
            document.getElementById('total').value = document.getElementById(id + 'to').value;
            document.getElementById('posnic_total').value = document.getElementById(id + 'to').value;

            document.getElementById('guid').value = id;
            document.getElementById('edit_guid').value = id;

        }
        function clear_data() {
            document.getElementById('display').style.display = "none";

            document.getElementById('item').value = "";
            document.getElementById('quty').value = "";
            document.getElementById('unit').value = "";
            document.getElementById('cost').value = "";
            document.getElementById('sell').value = "";
            document.getElementById('stock').value = "";
            document.getElementById('total').value = "";
            document.getElementById('posnic_total').value = "";

            document.getElementById('guid').value = "";
            document.getElementById('edit_guid').value = "";

        }
        function add_values() {
            if (unique_check()) {

                if (document.getElementById('edit_guid').value == "") {
                    if (document.getElementById('item').value != "" && document.getElementById('quty').value != "" && document.getElementById('cost').value != "" && document.getElementById('total').value != "") {
                        code = document.getElementById('item').value;

                        quty = document.getElementById('quty').value;
                        unit = document.getElementById('unit').value;
                        cost = document.getElementById('cost').value;
                        sell = document.getElementById('sell').value;
                        disc = document.getElementById('stock').value;
                        total = document.getElementById('total').value;
                        item = document.getElementById('guid').value;
                        main_total = document.getElementById('posnic_total').value;

                        $('<tr id=' + item + '><td><input type=hidden value=' + item + ' id=' + item + 'id ><input type=text name="stock_name[]"  id=' + item + 'st style="width: 150px" class="round  my_with" ></td><td><input type=text name=quty[] readonly="readonly" value=' + quty + ' id=' + item + 'q class="round  my_with" style="text-align:right;" ></td><td><input type=text name=unit[] readonly="readonly" value=' + unit + ' id=' + item + 'u class="round  my_with" style="text-align:right;"></td><td><input type=text name=cost[] readonly="readonly" value=' + cost + ' id=' + item + 'c class="round  my_with" style="text-align:right;"></td><td><input type=text name=sell[] readonly="readonly" value=' + sell + ' id=' + item + 's class="round  my_with" style="text-align:right;"  ></td><td><input type=text name=stock[] readonly="readonly" value=' + disc + ' id=' + item + 'p class="round  my_with" style="text-align:right;" ></td><td><input type=text name=jibi[] readonly="readonly" value=' + total + ' id=' + item + 'to class="round  my_with" style="width: 120px;margin-left:20px;text-align:right;" ><input type=hidden name=total[] id=' + item + 'my_tot value=' + main_total + '> </td><td><input type=button value="" id=' + item + ' style="width:30px;border:none;height:30px;background:url(images/edit_new.png)" class="round" onclick="edit_stock_details(this.id)"  ></td><td><input type=button value="" id=' + item + ' style="width:72px;border:none;height:72px;background:url(images/barcode/barcode_add.png)" class="round" onclick="edit_stock_details(this.id)"  ></td><td><input type=button value="" id=' + item + ' style="width:30px;border:none;height:30px;background:url(images/close_new.png)" class="round" onclick= $(this).closest("tr").remove() ></td></tr>').fadeIn("slow").appendTo('#item_copy_final');
                        document.getElementById('quty').value = "";
                        document.getElementById('unit').value = "";
                        document.getElementById('cost').value = "";
                        document.getElementById('sell').value = "";
                        document.getElementById('stock').value = "";
                        document.getElementById('total').value = "";
                        document.getElementById('item').value = "";
                        document.getElementById('guid').value = "";
                        if (document.getElementById('grand_total').value == "") {
                            document.getElementById('grand_total').value = main_total;
                        } else {
                            document.getElementById('grand_total').value = parseFloat(document.getElementById('grand_total').value) + parseFloat(main_total);
                        }
                        document.getElementById('main_grand_total').value = '$ ' + parseFloat(document.getElementById('grand_total').value).toFixed(2);
                        document.getElementById(item + 'st').value = code;
                        document.getElementById(item + 'to').value = total;

                    } else {
                        alert('L�tfen Bir �r�n Se�in');
                    }
                } else {
                    id = document.getElementById('edit_guid').value;
                    document.getElementById(id + 'st').value = document.getElementById('item').value;
                    document.getElementById(id + 'q').value = document.getElementById('quty').value;
                    document.getElementById(id + 'u').value = document.getElementById('unit').value;
                    document.getElementById(id + 'c').value = document.getElementById('cost').value;
                    document.getElementById(id + 's').value = document.getElementById('sell').value;
                    document.getElementById(id + 'p').value = document.getElementById('stock').value;
                    data1 = parseFloat(document.getElementById('grand_total').value) + parseFloat(document.getElementById('posnic_total').value) - parseFloat(document.getElementById(id + 'my_tot').value);
                    document.getElementById('main_grand_total').value = data1;
                    document.getElementById('grand_total').value = data1;
                    document.getElementById(id + 'to').value = document.getElementById('total').value;
                    // document.getElementById('grand_total').value=parseFloat(document.getElementById('grand_total').value)+parseFloat(document.getElementById('total').value);
//alert(data1);
//alert(parseFloat(document.getElementById(id+'my_tot').value));
//alert(parseFloat(document.getElementById('posnic_total').value));
                    balance_amount();

                    document.getElementById(id + 'my_tot').value = document.getElementById('posnic_total').value
                    document.getElementById('quty').value = "";
                    document.getElementById('unit').value = "";
                    document.getElementById('cost').value = "";
                    document.getElementById('sell').value = "";
                    document.getElementById('stock').value = "";
                    document.getElementById('total').value = "";
                    document.getElementById('item').value = "";
                    document.getElementById('guid').value = "";
                    document.getElementById('edit_guid').value = "";
                }
                document.getElementById('display').style.display = "none";

            }
        }
        function unique_check() {
            if (!document.getElementById(document.getElementById('guid').value) || document.getElementById('edit_guid').value == document.getElementById('guid').value) {
                return true;

            } else {

                alert("Bu �r�n bu sat�nalma i�in daha �nceden eklenmi�");
                document.getElementById('item').focus();
                id = document.getElementById('edit_guid').value;

                document.getElementById('item').focus();
                document.getElementById('item').value = document.getElementById(id + 'st').value;
                document.getElementById('quty').value = document.getElementById(id + 'q').value;
                document.getElementById('unit').value = document.getElementById(id + 'u').value;
                document.getElementById('cost').value = document.getElementById(id + 'c').value;
                document.getElementById('sell').value = document.getElementById(id + 's').value;
                document.getElementById('stock').value = document.getElementById(id + 'p').value;
                document.getElementById('total').value = document.getElementById(id + 'to').value;
                document.getElementById('guid').value = id;
                document.getElementById('edit_guid').value = id;
                return false;


            }
        }
        function total_amount() {


            document.getElementById('total').value = document.getElementById('cost').value * document.getElementById('quty').value
            document.getElementById('posnic_total').value = document.getElementById('total').value;
            // document.getElementById('total').value = '$ ' + parseFloat(document.getElementById('total').value).toFixed(2);
            balance_amount();
        }
        function balance_amount() {
            if (document.getElementById('grand_total').value != "" && document.getElementById('payment').value != "") {
                data = parseFloat(document.getElementById('grand_total').value);
                document.getElementById('balance').value = data - parseFloat(document.getElementById('payment').value);
                console.log();
                if (parseFloat(document.getElementById('grand_total').value) >= parseFloat(document.getElementById('payment').value)) {

                    document.getElementById('balance').value = parseFloat(document.getElementById('grand_total').value) - parseFloat(document.getElementById('payment').value);
                } else {
                    if (document.getElementById('grand_total').value != "") {
                        document.getElementById('balance').value = '000.00';
                        document.getElementById('payment').value = parseFloat(document.getElementById('grand_total').value);
                    } else {
                        document.getElementById('balance').value = '000.00';
                        document.getElementById('payment').value = "";
                    }
                }
            } else {
                document.getElementById('balance').value = "";
            }


        }
        function quantity_chnage(e) {
            var unicode = e.charCode ? e.charCode : e.keyCode
            if (unicode != 13 && unicode != 9) {
            }
            else {
                add_values();
                document.getElementById("item").focus();

            }
            if (unicode != 27) {
            }
            else {

                document.getElementById("item").focus();
            }
        }

        function numbersonly(e) {
            var unicode = e.charCode ? e.charCode : e.keyCode
            if (unicode != 8 && unicode != 46 && unicode != 37 && unicode != 27 && unicode != 38 && unicode != 39 && unicode != 40 && unicode != 9) { //if the key isn't the backspace key (which we should allow)
                if (unicode < 48 || unicode > 57)
                    return false
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
            <li><a href="dashboard.php" class="active-tab dashboard-tab">Ana Panel</a></li>
            <li><a href="view_sales.php" class="sales-tab">Sat��lar</a></li>
            <li><a href="view_customers.php" class=" customers-tab">M��teriler</a></li>
            <li><a href="view_purchase.php" class="purchase-tab">Sat�nalma</a></li>
            <li><a href="view_supplier.php" class=" supplier-tab">Tedarik�iler</a></li>
            <li><a href="view_product.php" class=" stock-tab">Stoklar / �r�nler</a></li>
            <li><a href="view_payments.php" class="payment-tab">�demeler / Bor�lar</a></li>
            <li><a href="view_report.php" class="report-tab">Raporlar</a></li>
        </ul>
        <!-- end tabs -->

        <!-- Change this image to your own company's logo -->
        <!-- The logo will automatically be resized to 30px height. -->
        <a href="#" id="company-branding-small" class="fr"><img src="<?php if (isset($_SESSION['logo'])) {
                echo "upload/" . $_SESSION['logo'];
            } else {
                echo "upload/posnic.png";
            } ?>" alt="Point of Sale"/></a>

    </div>
    <!-- end full-width -->

</div>
<!-- end header -->


<!-- MAIN CONTENT -->
<div id="content">

    <div class="page-full-width cf">

        <div class="side-menu fl">

            <h3>Sat�nalma Y�netimi</h3>
            <ul>
                <li><a href="add_purchase.php">Sat�nalma Ekle</a></li>
                <li><a href="view_purchase.php">Sat�nalmalar� G�ster </a></li>
            </ul>

        </div>
        <!-- end side-menu -->

        <div class="side-content fr">

            <div class="content-module">

                <div class="content-module-heading cf">

                    <h3 class="fl">Sat�nalma G�ncelle</h3>
                    <span class="fr expand-collapse-text">Daraltmak i�in t�kla</span>
                    <span class="fr expand-collapse-text initial-expand">Geni�letmek i�in t�kla</span>

                </div>
                <!-- end content-module-heading -->

                <div class="content-module-main cf">

                    <?php
                    if (isset($_POST['supplier']) and isset($_POST['stock_name'])) {
                        $billnumber = mysqli_real_escape_string($db->connection, $_POST['bill_no']);
                        $autoid = mysqli_real_escape_string($db->connection, $_POST['id']);

                        $supplier = mysqli_real_escape_string($db->connection, $_POST['supplier']);

                        $payment = mysqli_real_escape_string($db->connection, $_POST['payment']);
                        $balance = mysqli_real_escape_string($db->connection, $_POST['balance']);
                        $address = mysqli_real_escape_string($db->connection, $_POST['address']);
                        $district = mysqli_real_escape_string($db->connection, $_POST['district']);
                        $province = mysqli_real_escape_string($db->connection, $_POST['province']);
                        $contact = mysqli_real_escape_string($db->connection, $_POST['contact']);
                        $count = $db->countOf("supplier_details", "supplier_name='$supplier'");
                        if ($count == 0) {
                            $db->query("insert into supplier_details(supplier_name,supplier_address,supplier_district,supplier_province,supplier_contact1,supplier_phone1) values('$supplier','$address','$district','$province','$contact','$phone')");
                        }
                        $temp_balance = $db->queryUniqueValue("SELECT balance FROM supplier_details WHERE supplier_name='$supplier'");
                        $temp_balance = (int)$temp_balance + (int)$balance;
                        $db->execute("UPDATE supplier_details SET balance='$temp_balance' WHERE supplier_name='$supplier'");
                        $selected_date = $_POST['due'];
                        $selected_date = strtotime($selected_date);
                        $mysqldate = date('Y-m-d H:i:s', $selected_date);
                        $due = $mysqldate;
                        $mode = mysqli_real_escape_string($db->connection, $_POST['mode']);
                        $description = mysqli_real_escape_string($db->connection, $_POST['description']);

                        $namet = $_POST['stock_name'];
                        $quantityt = isset($_POST['quanitity']) ? $_POST['quanitity'] : '';
                        $bratet = $_POST['cost'];
                        $sratet = $_POST['sell'];
                        $totalt = $_POST['total'];

                        $subtotal = mysqli_real_escape_string($db->connection, $_POST['subtotal']);

                        $username = $_SESSION['username'];

                        $i = 0;
                        $j = 1;


                        $selected_date = $_POST['date'];
                        $selected_date = strtotime($selected_date);
                        $mysqldate = date('Y-m-d H:i:s', $selected_date);

                        foreach ($namet as $name1) {

                            $quantity = $_POST['quantity'][$i];
                            $brate = $_POST['cost'][$i];
                            $srate = $_POST['sell'][$i];
                            $total = $_POST['total'][$i];
                            $sysid = $_POST['gu_id'][$i];


                            $count = $db->countOf("stock_avail", "name='$name1'");

                            $amount = $db->queryUniqueValue("SELECT quantity FROM stock_avail WHERE name='$name1'");
                            $oldquantity = $db->queryUniqueValue("SELECT quantity FROM stock_entries WHERE id='$sysid' ");
                            $amount1 = ($amount + $quantity) - $oldquantity;


                            $db->execute("UPDATE stock_avail SET quantity='$amount1' WHERE name='$name1'");
                            $db->query("UPDATE stock_entries SET stock_name='$name1', stock_supplier_name='$supplier', quantity='$quantity', company_price='$brate', selling_price='$srate', opening_stock='$amount', closing_stock='$amount1', date='$mysqldate', username='$username', type='entry', total='$total', payment='$payment', balance='$balance', mode='$mode', description='$description', due='$due', subtotal='$subtotal',billnumber='$billnumber',unit='$unit' WHERE id='$sysid'");
                            //INSERT INTO `stock`.`stock_entries` (`id`, `stock_id`, `stock_name`, `stock_supplier_name`, `category`, `quantity`, `company_price`, `selling_price`, `opening_stock`, `closing_stock`, `date`, `username`, `type`, `salesid`, `total`, `payment`, `balance`, `mode`, `description`, `due`, `subtotal`, `count1`)
                            //VALUES (NULL, '$autoid1', '$name1', '$supplier', '', '$quantity', '$brate', '$srate', '$amount', '$amount1', '$mysqldate', 'sdd', 'entry', 'Sa45', '432.90', '2342.90', '24.34', 'cash', 'sdflj', '2010-03-25 12:32:02', '45645', '1');


                            $i++;
                            $j++;
                        }
                        echo "<br><font color=green size=+1 >Sat�nalma bilgileri g�ncellenmi�tir Ref: [ $autoid] !</font>";


                    }
                    ?>
                    <?php
                    if (isset($_GET['sid']))
                        $id = $_GET['sid'];
                    $line = $db->queryUniqueObject("SELECT * FROM stock_entries WHERE stock_id='$id'");
                    ?>
                    <form name="form1" method="post" id="form1" action="">
                        <input type="hidden" id="posnic_total">
                        <input type="hidden" name="id" value="<?php echo $id ?>">

                        <table class="form" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <?php
                                $max = $db->maxOfAll("id", "stock_sales");
                                $max = $max + 1;
                                $autoid = "AL" . $max . "";
                                ?>
                                <td>Stok&nbsp;No:</td>
                                <td><input name="stockid" type="text" id="stockid" readonly="readonly" maxlength="200"
                                           class="round default-width-input" style="width:130px "
                                           value="<?php echo $line->stock_id; ?>"/></td>

                                <td>Tarih:</td>
                                <td><input name="date" id="test1" placeholder="" value="<?php echo $line->date; ?> "
                                           type="text" id="name" maxlength="200" class="round default-width-input" style="width:130px "/></td>
                               
                               
                                <td><span class="man">*</span>Tedarik�i:</td>
                                <td><input name="supplier" placeholder="TEDAR�K�� G�R" type="text" id="supplier"
                                           value="<?php echo $line->stock_supplier_name; ?> " maxlength="200"
                                           class="round default-width-input" style="width:130px "/></td>


                                <td><span class="man">*</span>Fi�&nbsp;No:</td>
                                <td><input name="bill_no" placeholder="F�� NO G�R" type="text" id="bill_no"
                                           maxlength="200" value="<?php echo $line->billnumber; ?> "
                                           class="round default-width-input" style="width:120px "/></td>
                                
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>

                            </tr>
                            <tr>
                            
                                <td>Addres:</td>
                                <td><input name="address" placeholder="ADRES G�R" type="text"
                                           value="<?php $quantity = $db->queryUniqueValue("SELECT supplier_address FROM supplier_details WHERE supplier_name='" . $line->stock_supplier_name . "'");
                                           echo $quantity; ?>" id="address" maxlength="200"
                                           class="round default-width-input" style="width:130px "/></td>

				<td>�l�e:</td>
                                <td><input name="district" placeholder="�L�E G�R" type="text"
                                           value="<?php $quantity = $db->queryUniqueValue("SELECT supplier_district FROM supplier_details WHERE supplier_name='" . $line->stock_supplier_name . "'");
                                           echo $quantity; ?>" id="district" maxlength="200"
                                           class="round default-width-input" style="width:130px "/></td>


				<td>�l:</td>
                                <td><input name="province" placeholder="�L G�R" type="text"
                                           value="<?php $quantity = $db->queryUniqueValue("SELECT supplier_province FROM supplier_details WHERE supplier_name='" . $line->stock_supplier_name . "'");
                                           echo $quantity; ?>" id="province" maxlength="200"
                                           class="round default-width-input" style="width:130px "/></td>


                                <td>Yetkili:</td>
                                <td><input name="contact" placeholder="YETK�L� G�R" type="text"
                                           value="<?php $quantity = $db->queryUniqueValue("SELECT supplier_contact1 FROM supplier_details WHERE supplier_name='" . $line->stock_supplier_name . "'");
                                           echo $quantity; ?>" id="contact1" maxlength="200"
                                           class="round default-width-input" onkeypress="return numbersonly(event)"
                                           style="width:120px "/></td>
                                           

                                <td>Telefon:</td>
                                <td><input name="phone" placeholder="TELEFON G�R" type="text"
                                           value="<?php $quantity = $db->queryUniqueValue("SELECT supplier_phone1 FROM supplier_details WHERE supplier_name='" . $line->stock_supplier_name . "'");
                                           echo $quantity; ?>" id="phone1" maxlength="200"
                                           class="round default-width-input" onkeypress="return numbersonly(event)"
                                           style="width:120px "/></td>                                           
                                           

                            </tr>
                        </table>
                        <input type="hidden" id="guid">
                        <input type="hidden" id="edit_guid">
                        <table id="hideen_display">
                            <tr>
                                <td>�r�n:</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td>Miktar:</td>
                                <td> &nbsp;</td>
                                <td>Birim:</td>
                                <td> &nbsp;</td>
                                <td>Maliyet:</td>
                                <td> &nbsp;</td>
                                <td>Fiyat�:</td>
                                <td> &nbsp;</td>
                                <td>Mevcut Stok:</td>
                                <td>Toplam</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>           
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>                        
                            
                            </tr>
                        </table>
                        <table class="form" id="display" style="display:none">
                            <tr>

                                <td><input name="" type="text" id="item" maxlength="200" class="round my_with "
                                           style="width: 150px"
                                           value="<?php echo isset($supplier) ? $supplier : ''; ?>"/></td>

                                <td><input name="" type="text" id="quty" maxlength="200" class="round  my_with"
                                           onKeyPress="quantity_chnage(event);return numbersonly(event)"
                                           onkeyup="total_amount();unique_check()"
                                           value="<?php echo isset($category) ? $category : ''; ?>"/></td>

                                <td><input name="" type="text" id="unit" readonly="readonly" maxlength="200"
                                           class="round my_with"
                                           value="<?php echo isset($category) ? $category : ''; ?>"/></td>

                                <td><input name="" type="text" id="cost" readonly="readonly" maxlength="200"
                                           class="round my_with"
                                           value="<?php echo isset($category) ? $category : ''; ?>"/></td>


                                <td><input name="" type="text" id="sell" readonly="readonly" maxlength="200"
                                           class="round  my_with"
                                           value="<?php echo isset($category) ? $category : ''; ?>"/></td>


                                <td><input name="" type="text" id="stock" readonly="readonly" maxlength="200"
                                           class="round  my_with"
                                           value="<?php echo isset($category) ? $category : ''; ?>"/></td>
                                           
                                <td><input name="" type="text" id="total" maxlength="200"
                                           class="round default-width-input " style="width:120px;  margin-left: 20px"
                                           value="<?php echo isset($category) ? $category : ''; ?>"/></td>
                                           
                                <td><input type="button" onclick="add_values()" onkeyup=" balance_amount();"
                                           id="add_new_code"
                                           style="margin-left:20px; width:30px;height:30px;border:none;background:url(images/save.png)"
                                           class="round">
                                           
                                </td>
                                <td><input type="button" value="" id="cancel" onclick="clear_data()"
                                           style="width:30px;float: right; border:none;height:30px;background:url(images/close_new.png)">
                                </td>

                            </tr>
                        </table>
                        <input type="hidden" id="guid">
                        <input type="hidden" id="edit_guid">


                        <div style="overflow:auto ;max-height:300px;  ">
                            <table class="form" id="item_copy_final">

                                <?php
                                $sid = $line->stock_id;
                                $max = $db->maxOf("count1", "stock_entries", "stock_id='$sid'");

                                for ($i = 1; $i <= $max; $i++) {
                                    $line1 = $db->queryUniqueObject("SELECT * FROM stock_entries WHERE stock_id='$sid' and count1=$i");

                                    $item = $db->queryUniqueValue("SELECT stock_id FROM stock_details WHERE stock_name='" . $line1->stock_name . "'");
                                    ?>

                                    <tr>

                                        <td><input name="stock_name[]" type="text" id="<?php echo $item . "st" ?>"
                                                   maxlength="20" style="width: 150px" readonly="readonly"
                                                   class="round "
                                                   value="<?php echo $line1->stock_name; ?>"/></td>

                                        <td><input name="quantity[]" type="text" id="<?php echo $item . "q" ?>"
                                                   maxlength="20" class="round my_with"
                                                   value="<?php echo $line1->quantity; ?>" readonly="readonly"
                                                   onkeypress="return numbersonly(event)"/></td>

                                        <td><input name="unit[]" type="text" id="<?php echo $item . "u" ?>"
                                                   maxlength="20" class="round my_with"
                                                   value="<?php echo $line1->unit; ?>" readonly="readonly"
                                                   onkeypress="return numbersonly(event)"/></td>

                                        <td><input name="cost[]" type="text" id="<?php echo $item . "c" ?>"
                                                   maxlength="20" class="round my_with"
                                                   value="<?php echo $line1->company_price; ?>" readonly="readonly"
                                                   onkeypress="return numbersonly(event)"/></td>


                                        <td><input name="sell[]" type="text" id="<?php echo $item . "s" ?>"
                                                   maxlength="20" readonly="readonly" class="round my_with"
                                                   value="<?php echo $line1->selling_price; ?>"
                                                   onkeypress="return numbersonly(event)"/></td>
                                                   
                                        <td><input name="stock[]" type="text" id="<?php echo $item . "p" ?>"
                                                   readonly="readonly" maxlength="200" class="round  my_with"
                                                   value="<?php $quantity = $db->queryUniqueValue("SELECT quantity FROM stock_avail WHERE name='" . $line1->stock_name . "'");
                                                   echo $quantity; ?>"/></td>

                                        <td><input name="total[]" type="text" id="<?php echo $item . "to" ?>"
                                                   readonly="readonly" maxlength="20"
                                                   style="margin-left:20px;width: 120px" class="round "
                                                   value="<?php echo $line1->total; ?>"/></td>
                                                   
                                        <td><input type="hidden" id="<?php echo $item . "my_tot" ?>" maxlength="20"
                                                   style="margin-left:20px;width: 120px" class="round "
                                                   value="<?php echo $line1->total; ?>"/></td>
                                                   
                                        <td><input type="hidden" id="<?php echo $item; ?>"><input type="hidden"
                                                   name="gu_id[]" value="<?php echo $line1->id ?>"></td>
                                                   
                                        <td><input type=button value="" id="<?php echo $item; ?>"
                                                   style="width:30px;border:none;height:30px;background:url(images/edit_new.png)"
                                                   class="round" onclick="edit_stock_details(this.id)"></td>
                                                   
                                        <td><input type=button value="" id="<?php echo $item; ?>"
                                                   style="width:72px;border:none;height:72px;background:url(images/barcode/barcode_add.png)"
                                                   class="round" onclick="edit_stock_details(this.id)"></td> 
                                                   
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>


                        <table class="form">
                            <tr>
                                <td> &nbsp;</td>
                                <td>�deme:<input type="text" class="round" value="<?php echo $line->payment; ?>"
                                                   onkeyup=" balance_amount(); return numbersonly(event);"
                                                   name="payment" id="payment">
                                </td>
                                <td> &nbsp;</td>
                                <td>Bakiye:<input type="text" class="round" value="<?php echo $line->balance; ?>"
                                                   id="balance" name="balance">
                                </td>
                                <td> &nbsp;</td>

                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td>Genel Toplam:<input type="hidden" readonly="readonly" id="grand_total"
                                                       value="<?php echo $line->subtotal; ?>" name="subtotal">
                                    <input type="text" id="main_grand_total" class="round default-width-input"
                                           value="<?php echo $line->subtotal; ?>" style="text-align:right;width: 120px">
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td>�deme&nbsp;T�r�&nbsp;</td>
                                <td>
                                    <select name="mode">
                                        <option value="cheque">�ek</option>
                                        <option value="cheque">Nakit</option>
                                        <option value="cheque">Di�er</option>
                                    </select>
                                </td>
                                <td>
                                    Biti� Tarihi:<input type="text" name="due" id="test2"
                                                    value="<?php echo date('d-m-Y'); ?>" class="round">
                                </td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>

                                <td>A��klama</td>
                                <td><textarea name="description"><?php echo $line->description; ?></textarea></td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
                            </tr>
                        </table>
                        <table class="form">
                            <tr>
                                <td>
                                    <input class="button round blue image-right ic-add text-upper" type="submit"
                                           name="Submit" value="Ekle">
                                </td>
                                <td> (Control + S)
                                    <input class="button round red   text-upper" type="reset" name="Reset"
                                           value="Vazge�"></td>
                                <td> &nbsp;</td>
                                <td> &nbsp;</td>
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
        <p>Sorular�n�z i�in <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Y�netim%20Sistemi">halil.mutlu@outlook.com</a>.
        </p>

    </div>
    <!-- end footer -->

</body>
</html>