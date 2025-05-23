<?php
include_once("init.php");
include_once("tpl/top_bar.php");
include_once("tpl/header.php");
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DEPOSTOK - Müşteriler</title>

    <!-- Stylesheets -->
    <!---->
    <link rel="stylesheet" href="css/style.css">

    <!-- Optimize for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!-- jQuery & JS files -->
    <?php include_once("tpl/common_js.php"); ?>
    <script src="js/script.js"></script>


    <script LANGUAGE="JavaScript">
        <!--
        // Nannette Thacker http://www.shiningstar.net
        function confirmSubmit() {
            var agree = confirm("Bu kaydı silmek istediğinize emin misiniz?");
            if (agree)
                return true;
            else
                return false;
        }

        function confirmDeleteSubmit() {
            var flag = 0;
            var field = document.forms.deletefiles;
            for (i = 0; i < field.length; i++) {
                if (field[i].checked == true) {
                    flag = flag + 1;

                }

            }
            if (flag < 1) {
                alert("Sadece tek bir kayıt seçmek zorundasınız!");
                return false;
            } else {
                var agree = confirm("Bu kaydı silmek istediğinize emin misiniz?");
                if (agree)

                    document.deletefiles.submit();
                else
                    return false;

            }
        }
        function confirmLimitSubmit() {
            if (document.getElementById('search_limit').value != "") {

                document.limit_go.submit();

            } else {
                return false;
            }
        }


        function checkAll() {

            var field = document.forms.deletefiles;
            for (i = 0; i < field.length; i++)
                field[i].checked = true;
        }

        function uncheckAll() {
            var field = document.forms.deletefiles;
            for (i = 0; i < field.length; i++)
                field[i].checked = false;
        }
        // -->
    </script>
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
                    }
                },
                messages: {
                    name: {
                        required: "Lütfen bir müşteri adı girin",
                        minlength: "Müşteri adı en az 3 karakterden oluşmak zorundadır"
                    },
                    address: {
                        minlength: "Müşteri adresi en az 3 karakterden oluşmak zorundadır",
                        maxlength: "Müşteri adresi en az 3 karakterden oluşmak zorundadır"
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

        <div class="side-menu fl"id="sidebar">

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


                    <table>
                        <form action="" method="post" name="search">
                            <input name="searchtxt" type="text" class="round my_text_box" placeholder=" <?php echo $lang['search']; ?>">
                            &nbsp;&nbsp;<input name="Search" type="submit" class="my_button round blue   text-upper"
                                               value="<?php echo $lang['search']; ?>">
                        </form>
                        <form action="" method="get" name="limit_go">
                            <?php echo $lang['records_per_page']; ?><input name="limit" type="text" class="round my_text_box" id="search_limit"
                                                  style="margin-left:5px;"
                                                  value="<?php if (isset($_GET['limit'])) echo $_GET['limit']; else echo " 10"; ?>"
                                                  size="3" maxlength="3">
                            <input name="go" type="button" value="<?php echo $lang['set']; ?>" class=" round blue my_button  text-upper"
                                   onclick="return confirmLimitSubmit()">
                        </form>

                        <form name="deletefiles" action="delete.php" method="post">

                            <input type="hidden" name="table" value="customer_details">
                            <input type="hidden" name="return" value="view_customers.php">
                            <input type="button" name="selectall" value="<?php echo $lang['select_all']; ?>"
                                   class="my_button round blue   text-upper" onClick="checkAll()"
                                   style="margin-left:5px;"/>
                            <input type="button" name="unselectall" value="<?php echo $lang['cancel_selection']; ?>"
                                   class="my_button round blue   text-upper" onClick="uncheckAll()"
                                   style="margin-left:5px;"/>
                            <input name="dsubmit" type="button" value="<?php echo $lang['delete_selected']; ?>"
                                   class="my_button round blue   text-upper" style="margin-left:5px;"
                                   onclick="return confirmDeleteSubmit()"/>


                            <table>
                                <?php


								$SQL = "SELECT * FROM  customer_details ORDER BY id DESC";
                                if (isset($_POST['Search']) AND trim($_POST['searchtxt']) != "") {

                                    $SQL = "SELECT * FROM  customer_details WHERE customer_name  LIKE '%" . $_POST['searchtxt'] . "%' OR customer_address LIKE '%" . $_POST['searchtxt'] . "%' OR customer_contact1 LIKE '%" . $_POST['searchtxt'] . "%' OR customer_contact1 LIKE '%" . $_POST['searchtxt'] . "%' ORDER BY id DESC";


                                }

                                $tbl_name = "customer_details";        //your table name

                                // How many adjacent pages should be shown on each side?

                                $adjacents = 3;


                                /*

                                   First get total number of rows in data table.

                                   If you have a WHERE clause in your query, make sure you mirror it here.

                                */

                                $query = "SELECT COUNT(*) as num FROM $tbl_name";
                                if (isset($_POST['Search']) AND trim($_POST['searchtxt']) != "") {

                                    $query = "SELECT COUNT(*) as num FROM  customer_details WHERE customer_name  LIKE '%" . $_POST['searchtxt'] . "%' OR customer_address LIKE '%" . $_POST['searchtxt'] . "%' OR customer_contact1 LIKE '%" . $_POST['searchtxt'] . "%' OR customer_contact1 LIKE '%" . $_POST['searchtxt'] . "%'";


                                }


                                $total_pages = mysqli_fetch_array(mysqli_query($db->getConnection(), $query));

                                $total_pages = $total_pages['num'];


                                /* Setup vars for query. */

                                $targetpage = "view_customers.php";    //your file name  (the name of this file)

                                $limit = 10;                                //how many items to show per page
                                if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
                                    $limit = $_GET['limit'];
                                    $_GET['limit'] = 10;
                                }

                                $page = isset($_GET['page']) ? $_GET['page'] : 0;


                                if ($page)

                                    $start = ($page - 1) * $limit;            //first item to display on this page

                                else

                                    $start = 0;                                //if no page var is given, set start to 0


                                /* Get data. */

                                $sql = "SELECT * FROM customer_details  ORDER BY id DESC  LIMIT $start, $limit";
                                if (isset($_POST['Search']) AND trim($_POST['searchtxt']) != "") {

                                    $sql = "SELECT * FROM  customer_details WHERE customer_name  LIKE '%" . $_POST['searchtxt'] . "%' OR customer_address LIKE '%" . $_POST['searchtxt'] . "%' OR customer_contact1 LIKE '%" . $_POST['searchtxt'] . "%' OR customer_contact1 LIKE '%" . $_POST['searchtxt'] . "%' ORDER BY id DESC  LIMIT $start, $limit";


                                }


                                $result = mysqli_query($db->getConnection(), $sql);


                                /* Setup page vars for display. */

                                if ($page == 0) $page = 1;                    //if no page var is given, default to 1.

                                $prev = $page - 1;                            //previous page is page - 1

                                $next = $page + 1;                            //next page is page + 1

                                $lastpage = ceil($total_pages / $limit);        //lastpage is = total pages / items per page, rounded up.

                                $lpm1 = $lastpage - 1;                        //last page minus 1


                                /*

                                    Now we apply our rules and draw the pagination object.

                                    We're actually saving the code to a variable in case we want to draw it more than once.

                                */

                                $pagination = "";

                                if ($lastpage > 1) {

                                    $pagination .= "<div >";

                                    //previous button

                                    if ($page > 1)

                                        $pagination .= "<a href=\"view_customers.php?page=$prev&limit=$limit\" class=my_pagination >Önceki/a>";

                                    else

                                        $pagination .= "<span class=my_pagination>Önceki</span>";


                                    //pages

                                    if ($lastpage < 7 + ($adjacents * 2))    //not enough pages to bother breaking it up

                                    {

                                        for ($counter = 1; $counter <= $lastpage; $counter++) {

                                            if ($counter == $page)

                                                $pagination .= "<span class=my_pagination>$counter</span>";

                                            else

                                                $pagination .= "<a href=\"view_customers.php?page=$counter&limit=$limit\" class=my_pagination>$counter</a>";

                                        }

                                    } elseif ($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some

                                    {

                                        //close to beginning; only hide later pages

                                        if ($page < 1 + ($adjacents * 2)) {

                                            for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {

                                                if ($counter == $page)

                                                    $pagination .= "<span class=my_pagination>$counter</span>";

                                                else

                                                    $pagination .= "<a href=\"view_customers.php?page=$counter&limit=$limit\" class=my_pagination>$counter</a>";

                                            }

                                            $pagination .= "...";

                                            $pagination .= "<a href=\"view_customers.php?page=$lpm1&limit=$limit\" class=my_pagination>$lpm1</a>";

                                            $pagination .= "<a href=\"view_customers.php?page=$lastpage&limit=$limit\" class=my_pagination>$lastpage</a>";

                                        } //in middle; hide some front and some back

                                        elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

                                            $pagination .= "<a href=\"view_customers.php?page=1&limit=$limit\" class=my_pagination>1</a>";

                                            $pagination .= "<a href=\"view_customers.php?page=2&limit=$limit\" class=my_pagination>2</a>";

                                            $pagination .= "...";

                                            for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {

                                                if ($counter == $page)

                                                    $pagination .= "<span  class=my_pagination>$counter</span>";

                                                else

                                                    $pagination .= "<a href=\"view_customers.php?page=$counter&limit=$limit\" class=my_pagination>$counter</a>";

                                            }

                                            $pagination .= "...";

                                            $pagination .= "<a href=\"view_customers.php?page=$lpm1&limit=$limit\" class=my_pagination>$lpm1</a>";

                                            $pagination .= "<a href=\"view_customers.php?page=$lastpage&limit=$limit\" class=my_pagination>$lastpage</a>";

                                        } //close to end; only hide early pages

                                        else {

                                            $pagination .= "<a href=\"$view_customers.php?page=1&limit=$limit\" class=my_pagination>1</a>";

                                            $pagination .= "<a href=\"$view_customers.php?page=2&limit=$limit\" class=my_pagination>2</a>";

                                            $pagination .= "...";

                                            for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {

                                                if ($counter == $page)

                                                    $pagination .= "<span class=my_pagination >$counter</span>";

                                                else

                                                    $pagination .= "<a href=\"$targetpage?page=$counter&limit=$limit\" class=my_pagination>$counter</a>";

                                            }

                                        }

                                    }


                                    //next button

                                    if ($page < $counter - 1)

                                        $pagination .= "<a href=\"view_customers.php?page=$next&limit=$limit\" class=my_pagination>Sonraki</a>";

                                    else

                                        $pagination .= "<span class= my_pagination >Sonraki</span>";

                                    $pagination .= "</div>\n";

                                }

                                ?>
                                <tr>
                                    <th>No</th>
                                    <th><?php echo $lang['name']; ?></th>									
                                    <th><?php echo $lang['supervisor']; ?></th>
									<th><?php echo $lang['phone1']; ?></th>
                                    <th><?php echo $lang['type']; ?></th>
                                    <th><?php echo $lang['edit_delete']; ?></th>
                                    <th><?php echo $lang['select']; ?></th>
                                </tr>

                                <?php $i = 1;
                                $no = $page - 1;
                                $no = $no * $limit;
                                while ($row = mysqli_fetch_array($result)) {
                                    ?>
                                    <tr>
                                        <td> <?php echo $no + $i; ?></td>
                                        <td><?php echo $row['customer_name']; ?></td>						
                                        <td> <?php echo $row['customer_contact1']; ?></td>
										<td> <?php echo $row['customer_phone1']; ?></td>
										<td> <?php echo $row['customer_type']; ?></td>
                                        <!--<td> <?php //echo $row['balance']; ?></td> -->
                                        <td>
                                            <a href="update_customer_details.php?sid=<?php echo $row['id']; ?>&table=customer_details&return=view_customers.php"
                                               class="table-actions-button ic-table-edit">
                                            </a>
                                            <a onclick="return confirmSubmit()"
                                               href="delete.php?id=<?php echo $row['id']; ?>&table=customer_details&return=view_customers.php"
                                               class="table-actions-button ic-table-delete"></a>
                                        </td>
                                        <td><input type="checkbox" value="<?php echo $row['id']; ?>" name="checklist[]"
                                                   id="check_box"/></td>

                                    </tr>
                                    <?php $i++;
                                } ?>
                                
                            	</table>
                                <table>
                                <tr>

                                    <td align="center">
                                        <div style="margin-left:20px;"><?php echo $pagination; ?></div>
                                    </td>

                                </tr>
                            </table>
                        </form>


                </div>
            </div>
            <div id="footer">
                <p> <?php echo $lang['for_your_questions']; ?> <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
                </p>

            </div>
            <!-- end footer -->

</body>
</html>