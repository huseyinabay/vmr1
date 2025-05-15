<?php
/**
 * get_magic_quotes_gpc() PHP 7.4+ ve 8+’da kaldırıldığı için
 * GUMP tarafından çağrıldığında hata vermemesi adına bir polyfill
 * (sahte fonksiyon) tanımlıyoruz.
 */
if(!function_exists('get_magic_quotes_gpc')){
    function get_magic_quotes_gpc(){
        return false;
    }
}

include_once("init.php");
include_once("tpl/top_bar.php");
include_once("tpl/header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DEPOSTOK - Kişi Ekle</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Optimize for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style type="text/css">
        body {
            margin:0; 
            padding:0;
            background-color: #FFFFFF;
        }
        #vertmenu {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 100%;
            width: 160px;
            padding: 0;
            margin: 0;
        }
        #vertmenu h1 {
            display: block;
            background-color: #FF9900;
            font-size: 90%;
            padding: 3px 0 5px 3px;
            border: 1px solid #000;
            color: #333;
            margin: 0;
            width: 159px;
        }
        #vertmenu ul {
            list-style: none;
            margin: 0;
            padding: 0;
            border: none;
        }
        #vertmenu ul li {
            margin: 0;
            padding: 0;
        }
        #vertmenu ul li a {
            font-size: 80%;
            display: block;
            border-bottom: 1px dashed #C39C4E;
            padding: 5px 0 2px 4px;
            text-decoration: none;
            color: #666;
            width: 160px;
        }
        #vertmenu ul li a:hover, #vertmenu ul li a:focus {
            color: #000;
            background-color: #eee;
        }
        .style1 {
            color: #000;
        }
        div.pagination {
            padding: 3px;
            margin: 3px;
        }
        div.pagination a {
            padding: 2px 5px;
            margin: 2px;
            border: 1px solid #AAAADD;
            text-decoration: none;
            color: #000099;
        }
        div.pagination a:hover, div.pagination a:active {
            border: 1px solid #000099;
            color: #000;
        }
        div.pagination span.current {
            padding: 2px 5px;
            margin: 2px;
            border: 1px solid #000099;
            font-weight: bold;
            background-color: #000099;
            color: #FFF;
        }
        div.pagination span.disabled {
            padding: 2px 5px;
            margin: 2px;
            border: 1px solid #EEE;
            color: #DDD;
        }
    </style>
    <!-- jQuery & JS files -->
    <?php include_once("tpl/common_js.php"); ?>
    <script src="js/script.js"></script>
    <script>
        $(document).ready(function () {
            // jQuery Validation
            $("#form1").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3,
                        maxlength: 200
                    },
                    address: {
                        minlength: 3,
                        maxlength: 500
                    },
                    contact1: {
                        minlength: 3,
                        maxlength: 20
                    },
                    phone1: {
                        minlength: 3,
                        maxlength: 20
                    },
					phone2: {
                        minlength: 3,
                        maxlength: 20
                    },
					phone3: {
                        minlength: 3,
                        maxlength: 20
                    },
					phone4: {
                        minlength: 3,
                        maxlength: 20
                    }
					
                },
                messages: {
                    name: {
                        required: "<?php echo $lang['error_name']; ?>",
                        minlength: "<?php echo $lang['error-minname']; ?>"
                    },
                    address: {
                        minlength: "Adres en az 3 karakterden oluşmak zorundadır",
                        maxlength: "Adres çok uzun"
                    }
                }
            });
        });
    </script>
</head>
<body>

<!-- TOP BAR -->
<?php include_once("tpl/top_bar.php"); ?>
<!-- end top-bar -->

<!-- HEADER -->
<div id="header-with-tabs">

    <div class="page-full-width cf">

     
        <!-- end tabs -->

        <a href="#" id="company-branding-small" class="fr">
            <img src="<?php 
                if (isset($_SESSION['logo'])) {
                    echo "upload/" . $_SESSION['logo'];
                } else {
                    echo "upload/depostok.png";
                }
            ?>" alt="Depo Stok"/>
        </a>

    </div>
    <!-- end full-width -->
</div>
<!-- end header -->

<!-- MAIN CONTENT -->
<div id="content">
<label class="switch">
      <input type="checkbox" data-name="show" id="toggle" checked> <!-- Toggle checkbox; başlangıçta işaretli -->
      <div class="slider round"></div> <!-- Toggle düğmesinin stilize edilmiş slider kısmı -->
    </label>
    <div class="page-full-width cf">
        <div class="side-menu fl" id="sidebar">
            <h3>CRM</h3>
            <ul>
                <li><a href="add_customer.php"><?php echo $lang['add_customer']; ?></a></li>
                <li><a href="view_customers.php"><?php echo $lang['view_customer']; ?></a></li>
            </ul>
        </div>
        <!-- end side-menu -->

        <div class="side-content fr">

            <div class="content-module"id="map">

                <div class="content-module-heading cf">

                    <h3 class="fl"><?php echo $lang['person']; ?></h3>
                    <span class="fr expand-collapse-text"><?php echo $lang['click_to_collapse']; ?></span>
                    <span class="fr expand-collapse-text initial-expand"><?php echo $lang['click_to_expand']; ?></span>

                </div>
                <!-- end content-module-heading -->

                <div class="content-module-main cf">

                    <?php
                    // GUMP ile form verilerini doğrulama
                    if (isset($_POST['name'])) {
                        // POST verilerini sanitize ediyoruz
                        $_POST = $gump->sanitize($_POST);

                        // Validation kuralları
                        $gump->validation_rules(array(
                            'name'     => 'required|max_len,100|min_len,3',
                            'address'  => 'max_len,200',
                            'contact1' => 'max_len,30',
                            'phone1'   => 'alpha_numeric|max_len,20',
							'phone2'   => 'alpha_numeric|max_len,20',
							'phone3'   => 'alpha_numeric|max_len,20',
							'phone4'   => 'alpha_numeric|max_len,20',
                            'cid'      => 'max_len,40',
                            'type'     => 'max_len,11',
                            'district' => 'max_len,41',
                            'province' => 'max_len,41'
                        ));

                        // Filtre kuralları
                        $gump->filter_rules(array(
                            'name'     => 'trim|sanitize_string|mysqli_escape',
                            'address'  => 'trim|sanitize_string|mysqli_escape',
                            'contact1' => 'trim|sanitize_string|mysqli_escape',
                            'phone1'   => 'trim|sanitize_string|mysqli_escape',
							'phone2'   => 'trim|sanitize_string|mysqli_escape',
							'phone3'   => 'trim|sanitize_string|mysqli_escape',
							'phone4'   => 'trim|sanitize_string|mysqli_escape',
                            'cid'      => 'trim|sanitize_string|mysqli_escape',
                            'type'     => 'trim|sanitize_string|mysqli_escape',
                            'district' => 'trim|sanitize_string|mysqli_escape',
                            'province' => 'trim|sanitize_string|mysqli_escape'
                        ));

                        $validated_data = $gump->run($_POST);

                        // Değişkenleri tanımlayalım (hata halinde form dolu kalır)
                        $name     = isset($_POST['name'])     ? $_POST['name']     : '';
                        $cid      = isset($_POST['cid'])      ? $_POST['cid']      : '';
                        $address  = isset($_POST['address'])  ? $_POST['address']  : '';
                        $district = isset($_POST['district']) ? $_POST['district'] : '';
                        $province = isset($_POST['province']) ? $_POST['province'] : '';
                        $contact1 = isset($_POST['contact1']) ? $_POST['contact1'] : '';
                        $phone1   = isset($_POST['phone1'])   ? $_POST['phone1']   : '';
						$phone2   = isset($_POST['phone2'])   ? $_POST['phone2']   : '';
						$phone3   = isset($_POST['phone3'])   ? $_POST['phone3']   : '';
						$phone4   = isset($_POST['phone4'])   ? $_POST['phone4']   : '';
                        $type     = isset($_POST['type'])     ? $_POST['type']     : '';

                        if ($validated_data === false) {
                            // GUMP hatalarını göster
                            echo $gump->get_readable_errors(true);
                        } else {
                            // veritabanı için gerçek kaçış
                            $name     = mysqli_real_escape_string($db->getConnection(), $name);
                            $cid      = mysqli_real_escape_string($db->getConnection(), $cid);
                            $address  = mysqli_real_escape_string($db->getConnection(), $address);
                            $district = mysqli_real_escape_string($db->getConnection(), $district);
                            $province = mysqli_real_escape_string($db->getConnection(), $province);
                            $contact1 = mysqli_real_escape_string($db->getConnection(), $contact1);
                            $phone1   = mysqli_real_escape_string($db->getConnection(), $phone1);
							$phone2   = mysqli_real_escape_string($db->getConnection(), $phone2);
							$phone3   = mysqli_real_escape_string($db->getConnection(), $phone3);
							$phone4   = mysqli_real_escape_string($db->getConnection(), $phone4);
                            $type     = mysqli_real_escape_string($db->getConnection(), $type);

                            // Mükerrer kayıt kontrolü (customer_name’e göre)
                            $count = $db->countOf("customer_details", "customer_name='$name'");
                            if ($count == 1) {
                                echo "<div class='error-box round'>Mükerrer kayıt. Lütfen başka bir isim girin.</div>";
                            } else {
                                // Ekleme sorgusu
                                $insertQuery = "INSERT INTO customer_details 
                                    (id, customer_name, customer_cid, customer_address, customer_district, customer_province, 
                                     customer_contact1, customer_phone1, customer_phone2, customer_phone3, customer_phone4, customer_type, balance)
                                    VALUES
                                    (NULL, '$name', '$cid', '$address', '$district', '$province', '$contact1', '$phone1', '$phone2', '$phone3', '$phone4', '$type', 0)";
                                
                                if ($db->query($insertQuery)) {
                                    echo "<div class='confirmation-box round'>[ $name ] Bilgiler Eklendi!</div>";
                                } else {
                                    echo "<div class='error-box round'>Veritabanına ekleme sırasında hata oluştu!</div>";
                                }
                            }
                        }
                    }
                    ?>

                    <form name="form1" method="post" id="form1" action="">
                        <p><strong><?php echo $lang['add_contact_information']; ?></strong> <?php echo $lang['shortcut_with_ctrla']; ?> </p>
                        <table class="form" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><span class="man"></span><?php echo $lang['company_name']; ?> :</td>
                                <td>
                                    <input name="name" placeholder="<?php echo $lang['company_name']; ?>" type="text" id="name"
                                           maxlength="200" class="round default-width-input"
                                           value="<?php echo isset($name) ? $name : ''; ?>"/>
                                </td>
                                <td></span><?php echo $lang['supervisor']; ?> :</td>
                                <td>
                                    <input name="contact1" placeholder="<?php echo $lang['supervisor']; ?>" type="text"
                                           id="contact1" maxlength="20" class="round default-width-input"
                                           value="<?php echo isset($contact1) ? $contact1 : ''; ?>"/>
                                </td>
                            </tr> 
							<tr>   
							    <td><?php echo $lang['phone1']; ?> 1 :</td>
                                <td>
                                    <input name="phone1" placeholder="<?php echo $lang['phone1']; ?>" type="text"
                                           id="phone1" maxlength="20" class="round default-width-input"
                                           value="<?php echo isset($phone1) ? $phone1 : ''; ?>"/>
                                </td>
								
								 
								<td><?php echo $lang['phone1']; ?> 2 :</td>
                                <td>
                                    <input name="phone2" placeholder="<?php echo $lang['phone1']; ?>" type="text"
                                           id="phone2" maxlength="20" class="round default-width-input"
                                           value="<?php echo isset($phone2) ? $phone2 : ''; ?>"/>
                                </td>
							</tr> 
							<tr> 
								 <td><?php echo $lang['phone1']; ?> 3 :</td>
                                <td>
                                    <input name="phone3" placeholder="<?php echo $lang['phone1']; ?>" type="text"
                                           id="phone3" maxlength="20" class="round default-width-input"
                                           value="<?php echo isset($phone3) ? $phone3 : ''; ?>"/>
                                </td>
								
								 <td><?php echo $lang['phone1']; ?> 4 :</td>
                                <td>
                                    <input name="phone4" placeholder="<?php echo $lang['phone1']; ?>" type="text"
                                           id="phone4" maxlength="20" class="round default-width-input"
                                           value="<?php echo isset($phone4) ? $phone4 : ''; ?>"/>
                                </td>
							</tr> 	
							
                            <tr>
                                <td><?php echo $lang['type']; ?> :</td>
                                <td>
                                    <select name="type" style="width: 250px">
                                        <option value="" disabled selected><?php echo $lang['please_select_a_type']; ?></option>
                                        <option value="normal" <?php if(isset($type) && $type=='normal') echo 'selected'; ?>>Normal</option>
                                        <option value="VIP"    <?php if(isset($type) && $type=='VIP')    echo 'selected'; ?>>VIP</option>
                                        <option value="other"  <?php if(isset($type) && $type=='other')  echo 'selected'; ?>>Diğer</option>
                                    </select>
                                </td>
                                <td><?php echo $lang['address']; ?> :</td>
                                <td>
                                    <textarea name="address" placeholder="<?php echo $lang['address']; ?>" cols="10"
                                              class="round full-width-textarea"><?php 
                                        echo isset($address) ? $address : ''; 
                                    ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo $lang['district']; ?> :</td>
                                <td>
                                    <input name="district" placeholder="<?php echo $lang['district']; ?>" type="text" id="district"
                                           maxlength="40" class="round default-width-input"
                                           value="<?php echo isset($district) ? $district : ''; ?>"/>
                                </td>
                                <td><?php echo $lang['city']; ?> :</td>
                                <td>
                                    <input name="province" placeholder="<?php echo $lang['city']; ?>" type="text"
                                           id="province" maxlength="40" class="round default-width-input"
                                           value="<?php echo isset($province) ? $province : ''; ?>"/>
                                </td>
                            </tr>
                            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <input class="button round blue image-right ic-add text-upper" 
                                           type="submit" name="Submit" value="<?php echo $lang['add']; ?>">
                                    (Ctrl + S)
                                </td>
                                <td>&nbsp;</td>
                                <td align="right">
                                    <input class="button round red text-upper" type="reset" name="Reset" value="<?php echo $lang['cancel']; ?>">
                                </td>
                            </tr>
                        </table>
                    </form>

                </div>
                <!-- end content-module-main -->
            </div>
            <!-- end content-module -->
        </div>
        <!-- end side-content -->
    </div>
    <!-- end page-full-width -->
</div>
<!-- end content -->

<!-- FOOTER -->
<div id="footer">
    <p><a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
    </p>
</div>
<!-- end footer -->

</body>
</html>
