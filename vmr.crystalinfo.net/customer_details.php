<?php
/**
 * get_magic_quotes_gpc() PHP 7.4+ ve 8+’da kaldırıldığı için
 * GUMP tarafından çağrıldığında hata vermemesi adına bir polyfill
 */
if(!function_exists('get_magic_quotes_gpc')){
    function get_magic_quotes_gpc(){
        return false;
    }
}

include_once("init.php"); // veritabanı bağlantısı, GUMP vb.
z
?>z
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DEPOSTOK - Etkileşim Ekle</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Optimize for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- Ortak JS -->
    <?php include_once("tpl/common_js.php"); ?>
    <script src="js/script.js"></script>

    <!-- jQuery Validation -->
    <script>
    $(document).ready(function () {
        $("#form1").validate({
            rules: {
                customer_id: {
                    required: true
                },
                interaction_date: {
                    required: true,
                    date: true
                },
                interaction_type: {
                    required: true
                },
                notes: {
                    minlength: 3
                }
            },
            messages: {
                customer_id: {
                    required: "Lütfen bir müşteri seçin"
                },
                interaction_date: {
                    required: "Lütfen etkileşim tarihini girin",
                    date: "Tarih formatı hatalı"
                },
                interaction_type: {
                    required: "Lütfen etkileşim türünü seçin"
                },
                notes: {
                    minlength: "Notlar en az 3 karakter olmalıdır"
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
        <ul id="tabs" class="fl">
            <li><a href="dashboard.php" class="dashboard-tab">Ana Sayfa</a></li>
            <li><a href="view_purchase.php" class="purchase-tab">Ürün Girişi</a></li>
            <li><a href="view_debits.php" class="debits-tab">Zimmet</a></li>
            <li><a href="view_personals.php" class="personals-tab">Personel</a></li>
            <li><a href="crm.php" class="customers-tab">Müşteriler</a></li>
            <li><a href="view_product.php" class="stock-tab">Stoklar/Ürünler</a></li>
            <li><a href="view_report.php" class="report-tab">Raporlar</a></li>
            <!-- Yeni sekme: Etkileşimler -->
            <li><a href="view_interactions.php" class="active-tab">Etkileşimler</a></li>
        </ul>
        <a href="#" id="company-branding-small" class="fr">
            <img src="<?php 
                if (isset($_SESSION['logo'])) {
                    echo 'upload/' . $_SESSION['logo'];
                } else {
                    echo 'upload/depostok.png';
                }
            ?>" alt="Depo Stok"/>
        </a>
    </div>
</div>
<!-- end header -->

<!-- MAIN CONTENT -->
<div id="content">
    <div class="page-full-width cf">
        <div class="side-menu fl">
            <h3>Etkileşim Yönetimi</h3>
            <ul>
                <li><a href="add_interaction.php" class="side-menu-active">Etkileşim Ekle</a></li>
                <li><a href="view_interactions.php">Etkileşim Göster</a></li>
            </ul>
        </div>
        <!-- end side-menu -->

        <div class="side-content fr">
            <div class="content-module">
                <div class="content-module-heading cf">
                    <h3 class="fl">Etkileşim Ekle</h3>
                    <span class="fr expand-collapse-text">Daraltmak için tıkla</span>
                    <span class="fr expand-collapse-text initial-expand">Genişletmek için tıkla</span>
                </div>
                <!-- end content-module-heading -->

                <div class="content-module-main cf">

                    <?php
                    if (isset($_POST['customer_id'])) {
                        // GUMP ile verileri doğrula
                        $_POST = $gump->sanitize($_POST);

                        $gump->validation_rules(array(
                            'customer_id'       => 'required|integer',
                            'interaction_date'  => 'required|date',
                            'interaction_type'  => 'required|max_len,50',
                            'notes'             => 'max_len,500'
                        ));

                        $gump->filter_rules(array(
                            'customer_id'       => 'trim|sanitize_numbers',
                            'interaction_date'  => 'trim|sanitize_string|mysqli_escape',
                            'interaction_type'  => 'trim|sanitize_string|mysqli_escape',
                            'notes'             => 'trim|sanitize_string|mysqli_escape'
                        ));

                        $validated_data = $gump->run($_POST);

                        $customer_id      = $_POST['customer_id'] ?? '';
                        $interaction_date = $_POST['interaction_date'] ?? '';
                        $interaction_type = $_POST['interaction_type'] ?? '';
                        $notes           = $_POST['notes'] ?? '';

                        if ($validated_data === false) {
                            echo $gump->get_readable_errors(true);
                        } else {
                            $customer_id      = mysqli_real_escape_string($db->getConnection(), $customer_id);
                            $interaction_date = mysqli_real_escape_string($db->getConnection(), $interaction_date);
                            $interaction_type = mysqli_real_escape_string($db->getConnection(), $interaction_type);
                            $notes           = mysqli_real_escape_string($db->getConnection(), $notes);

                            // Ekleme sorgusu
                            $insertQuery = "INSERT INTO customer_interactions 
                                            (customer_id, interaction_date, interaction_type, notes)
                                            VALUES
                                            ('$customer_id', '$interaction_date', '$interaction_type', '$notes')";
                            if ($db->query($insertQuery)) {
                                echo "<div class='confirmation-box round'>Etkileşim başarıyla eklendi!</div>";
                            } else {
                                echo "<div class='error-box round'>Veritabanına ekleme sırasında hata oluştu!</div>";
                            }
                        }
                    }
                    ?>

                    <form name="form1" method="post" id="form1" action="">
                        <p><strong>Yeni Etkileşim Kaydı Ekle</strong></p>
                        <table class="form" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><span class="man">*</span>Müşteri Seç:</td>
                                <td>
                                    <select name="customer_id" class="round default-width-input">
                                        <option value="" disabled selected>-- Müşteri Seçin --</option>
                                        <?php
                                        // Tüm müşterileri çekip listeleyelim
                                        $custResult = mysqli_query($db->getConnection(), "SELECT id, customer_name FROM customer_details");
                                        while($row = mysqli_fetch_assoc($custResult)){
                                            echo "<option value='".$row['id']."'>".$row['customer_name']."</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="man">*</span>Etkileşim Tarihi:</td>
                                <td>
                                    <input name="interaction_date" type="date" class="round default-width-input" 
                                           placeholder="YYYY-MM-DD" 
                                           value="<?php echo isset($interaction_date) ? $interaction_date : ''; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td><span class="man">*</span>Etkileşim Türü:</td>
                                <td>
                                    <select name="interaction_type" class="round default-width-input">
                                        <option value="" disabled selected>-- Seçim Yapın --</option>
                                        <option value="Telefon">Telefon</option>
                                        <option value="Email">Email</option>
                                        <option value="Toplantı">Toplantı</option>
                                        <option value="Diğer">Diğer</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Notlar:</td>
                                <td>
                                    <textarea name="notes" class="round full-width-textarea" 
                                        placeholder="Etkileşim ile ilgili detaylar"><?php 
                                            echo isset($notes) ? $notes : ''; 
                                        ?></textarea>
                                </td>
                            </tr>
                            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <input class="button round blue image-right ic-add text-upper" 
                                           type="submit" name="Submit" value="Kaydet">
                                    <span style="font-size: 12px;">(Ctrl + S kısayolu ile de gönderebilirsiniz)</span>
                                    &nbsp;&nbsp;
                                    <input class="button round red text-upper" type="reset" 
                                           name="Reset" value="Vazgeç">
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
    <p>Sorularınız için <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
    </p>
</div>
<!-- end footer -->
</body>
</html>
