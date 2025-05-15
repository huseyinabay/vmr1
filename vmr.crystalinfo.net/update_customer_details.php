<?php
include_once("init.php");
include_once("tpl/top_bar.php");
include_once("tpl/header.php");
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DEPOSTOK - Müşteri Güncelleme</title>

    <!-- Stylesheets -->

    <link rel="stylesheet" href="css/style.css">

    <!-- Optimize for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!-- jQuery & JS files -->
    <?php include_once("tpl/common_js.php"); ?>
    <script src="js/script.js"></script>
    <script>
	
        /*$.validator.setDefaults({
         submitHandler: function() { alert("submitted!"); }
         });*/
        $(document).ready(function () {

            // validate signup form on keyup and submit
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
                        required: "Lütfen bir Firma Aı Girin",
                        minlength: "Firma Adı en az 3 karakterden oluşmak zorundadır"
                    },
                    address: {
                        minlength: " Adres en az 3 karakterden oluşmak zorundadır",
                        maxlength: " Adres en az 3 karakterden oluşmak zorundadır"
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
                    <form name="form1" method="post" id="form1" action="">
                        <p><strong><?php echo $lang['add_contact_information']; ?></strong> <?php echo $lang['shortcut_with_ctrla']; ?> </p>
                        <table class="form" border="0" cellspacing="0" cellpadding="0">
                            <?php
                            if (isset($_POST['id'])) {

                                $id = mysqli_real_escape_string($db->getConnection(), $_POST['id']);
                                $name = trim(mysqli_real_escape_string($db->getConnection(), $_POST['name']));
                                $address = trim(mysqli_real_escape_string($db->getConnection(), $_POST['address']));
                                $contact1 = trim(mysqli_real_escape_string($db->getConnection(), $_POST['contact1']));
                                $phone1 = trim(mysqli_real_escape_string($db->getConnection(), $_POST['phone1']));
								$phone2 = trim(mysqli_real_escape_string($db->getConnection(), $_POST['phone2']));
								$phone3 = trim(mysqli_real_escape_string($db->getConnection(), $_POST['phone3']));
								$phone4 = trim(mysqli_real_escape_string($db->getConnection(), $_POST['phone4']));
								$cid = mysqli_real_escape_string($db->getConnection(), $_POST['cid']);
								$district = mysqli_real_escape_string($db->getConnection(), $_POST['district']);
								$province = mysqli_real_escape_string($db->getConnection(), $_POST['province']);
								$type = mysqli_real_escape_string($db->getConnection(), $_POST['type']);

                                if ($db->query("UPDATE customer_details  SET customer_name='$name',customer_cid='$cid',customer_address='$address',customer_district='$district',customer_province='$province',customer_contact1='$contact1',customer_phone1='$phone1',customer_phone2='$phone2',customer_phone3='$phone3',customer_phone4='$phone4',customer_type='$type' where id='$id'"))
                                    echo "<br><font color=green size=+1 > [ $name ] Müşteri bilgileri güncellenmiştir!</font>";
                                else
                                    echo "<br><font color=red size=+1 >Güncelleme Problemi !</font>";


                            }

                            ?>
                            <?php
                            if (isset($_GET['sid']))
                                $id = $_GET['sid'];

                            $line = $db->queryUniqueObject("SELECT * FROM customer_details WHERE id=$id");
                            ?>
                            <form name="form1" method="post" id="form1" action="">
                                <input name="id" type="hidden" value="<?php echo $_GET['sid']; ?>">
                                <tr>
                                    <td><?php echo $lang['company_name']; ?></td>
                                    <td><input name="name" type="text" id="name" maxlength="200"
                                               class="round default-width-input"
                                               value="<?php echo $line->customer_name; ?> "/></td>
									   
											   
                                    <td><?php echo $lang['supervisor']; ?></td>
                                    <td><input name="contact1" type="text" id="contact1" maxlength="20"
                                               class="round default-width-input"
                                               value="<?php echo $line->customer_contact1; ?>"/></td>
								</tr>
									
								<tr>
									<td><?php echo $lang['phone1']; ?> 1 :</td>
                                    <td><input name="phone1" type="text" id="phone1" maxlength="20"
                                               class="round default-width-input"
                                               value="<?php echo $line->customer_phone1; ?>"/>
									</td>
									<td><?php echo $lang['phone1']; ?> 2 :</td>
                                    <td><input name="phone2" type="text" id="phone2" maxlength="20"
                                               class="round default-width-input"
                                               value="<?php echo $line->customer_phone2; ?>"/>
								</tr>

                                <tr>

									</td>
									<td><?php echo $lang['phone1']; ?> 3 :</td>
                                    <td><input name="phone3" type="text" id="phone3" maxlength="20"
                                               class="round default-width-input"
                                               value="<?php echo $line->customer_phone3; ?>"/>
									</td>
									<td><?php echo $lang['phone1']; ?> 4 :</td>
                                    <td><input name="phone4" type="text" id="phone4" maxlength="20"
                                               class="round default-width-input"
                                               value="<?php echo $line->customer_phone4; ?>"/>
									</td>
											   
                                </tr>

                                <tr>
								

							<td><?php echo $lang['type']; ?> :</td>
							<td>	<p class="formRight">
						
			                            <select name="type" width="60" style="width: 250px" >
                                        <option value="<?php echo $line->customer_type; ?>"><?php echo $line->customer_type; ?></option>
                                        <option value="Normal">Normal</option>
                                        <option value="VIP">VIP</option>
                                        <option value="other">Diğer</option>
                                    </select>
									</p>
									</td>		   
											   
											   
								
                                    <td><?php echo $lang['address']; ?></td>
                                    <td><textarea name="address" cols="15"
                                                  class="round full-width-textarea"><?php echo $line->customer_address; ?>
										</textarea></td>

                                </tr>
								
								<tr>			   
                                    <td><?php echo $lang['district']; ?></td>
                                    <td><input name="district" type="text" id="district" maxlength="20"
                                               class="round default-width-input"
                                               value="<?php echo $line->customer_district; ?>"/></td>
									<td><?php echo $lang['city']; ?></td>
                                    <td><input name="province" type="text" id="province" maxlength="20"
                                               class="round default-width-input"
                                               value="<?php echo $line->customer_province; ?>"/></td>
											   
                                </tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								
                                <tr>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td>
                                        <input class="button round blue image-right ic-add text-upper" type="submit"
                                               name="Submit" value="<?php echo $lang['add']; ?>">
                                        (Control + S)
                                    </td>
                                    <td align="right"><input class="button round red   text-upper" type="reset"
                                                             name="Reset" value="<?php echo $lang['cancel']; ?>"></td>
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
        <p><?php echo $lang['for_your_questions']; ?> <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
        </p>

    </div>
    <!-- end footer -->

</body>
</html>