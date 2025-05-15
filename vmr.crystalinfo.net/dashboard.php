<?php
include_once("init.php");
include_once("tpl/top_bar.php");
include_once("tpl/header.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$stmt = $db->getConnection()->prepare("
    SELECT 
        (SELECT COUNT(*) FROM stock_avail) AS total_stock_items,
        (SELECT COUNT(*) FROM stock_serial) AS total_serial_items,
        (SELECT COUNT(*) FROM personal_details) AS total_personals,
        (SELECT COUNT(*) FROM stock_serial_log) AS total_logs,
        (SELECT COUNT(*) FROM stock_user) AS total_users
");
$stmt->execute();

// Sonuçları değişkenlere bağlama
$stmt->bind_result($total_stock_items, $total_serial_items, $total_personals, $total_logs, $total_users);

// Verileri bir döngüyle çekme (tek satır döndüğü için tek bir fetch yeterlidir)
if ($stmt->fetch()) {
    $counts = [
        'total_stock_items' => $total_stock_items,
        'total_serial_items' => $total_serial_items,
        'total_personals'    => $total_personals,
        'total_logs'         => $total_logs,
        'total_users'        => $total_users,
    ];
} else {
    $counts = [
        'total_stock_items' => 0,
        'total_serial_items' => 0,
        'total_personals'    => 0,
        'total_logs'         => 0,
        'total_users'        => 0,
    ];
}

$stmt->close();


?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DEPOSTOK - Ana Menü</title>

    <!-- Stylesheets -->

    <link rel="stylesheet" href="css/style.css?v=1.0.0">

    <!-- Optimize for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!-- jQuery & JS files -->
    <?php include_once("tpl/common_js.php"); ?>
    <script src="js/script.js?v=1.0.0"></script>
</head>
<body>

<?php 
		//gpt onerisi
		$line = $db->queryUniqueObject("SELECT * FROM store_details");
		if (!$line) {
			error_log("store_details tablosundan veri alınamadı.");
			die("Veritabanı hatası, lütfen daha sonra tekrar deneyin.");
		}
        
		define('UPLOAD_PATH', 'upload/');
		$logoPath = UPLOAD_PATH . ($_SESSION['logo'] ?? 'depostok.png');
		//$_SESSION['logo'] = $line->log ?? "depostok.png";
		//$logoPath = "upload/" . ($_SESSION['logo'] ?? "depostok.png");
        ?>
		<a href="#" id="company-branding-small" class="fr"><img src="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>" alt="Depo Stok"/></a>

    </div>
    <!-- end full-width -->

</div>
<!-- end header -->


<!-- MAIN CONTENT -->
<div id="content">

    <div class="page-full-width cf">

        <div class="side-menu fl">

            <h3>Kısa Yollar</h3>
            <ul>
                <li><a href="update_debit_speed.php" target="_blank" onclick="var w=window.open(this.href,this.target); return w?false:true">Hızlı Zimmet</a></li>
                <li><a href="add_purchase.php">Ürün Girişi Ekle</a></li>
                <li><a href="add_supplier.php">Tedarikçi Ekle</a></li>
                <li><a href="add_customer.php">Müşteri / Personel Ekle</a></li>
                <li><a href="view_report.php">Raporlar</a></li>
            </ul>

<h3>Kullanıcı Bilgileri</h3>
<ul>
    <li style="font-size:12px; color:Tomato; ">
        <?php 
        echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') . " - ";
        $ip = $_SERVER['REMOTE_ADDR'];
        echo htmlspecialchars($ip, ENT_QUOTES, 'UTF-8');
        ?>
    </li>
</ul>

        </div>
        <!-- end side-menu -->

        <div class="side-content fr">

            <div class="content-module">

                <div class="content-module-heading cf">

                    <h3 class="fl">istatistikler</h3>
                    <span class="fr expand-collapse-text">Daraltmak için tıkla</span>
                    <span class="fr expand-collapse-text initial-expand">Genişletmek için tıkla</span>

                </div>
                <!-- end content-module-heading -->

                <div class="content-module-main cf">


                    <table style="width:350px; float:left;" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="250" align="left">&nbsp;</td>
                            <td width="50" align="left">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="350" align="left">Toplam&nbsp;Ürün&nbsp;Kalem&nbsp;Adedi</td>
                            <td width="50" align="left"><?php echo $counts['total_stock_items']; ?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr>                        
                        <tr>
                            <td align="left">Toplam&nbsp;Serili&nbsp;Ürün&nbsp;Sayısı</td>
                            <td align="left"><?php echo $counts['total_serial_items']; ?></td>
                        </tr>                        
                        
                        <tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="left">Toplam Personel Sayısı</td>
                            <td align="left"><?php echo $counts['total_personals'];?></td>
                        </tr>
                        <tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="left">Toplam Ürün Hareket Sayısı</td>
                            <td align="left"><?php echo $counts['total_logs']; ?></td>
                        </tr>
                        <tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="left">Toplam Depostok Kullanıcı Sayısı</td>
                            <td align="left"><?php  echo $counts['total_users']; ?></td>
                        </tr>
                        <tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr>
						<tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr> 

                    </table>

                    <table style="width:600px; margin-left:50px; float:left;" border="0" cellspacing="0"
                           cellpadding="0">
						   
						 <tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr>   
                        <tr>
                            
                            <td width="100" align="left">Başa (Ctrl+0)</td>
                            <td width="100" align="left">Ürün Girişi Ekle(Ctrl+1)</td>
                        </tr>
						<tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr> 
                        <tr>
                           
                            <td width="100" align="left">Stok Ekle(Ctrl+2)</td>
                            <td align="left">Zimmet Ekle(Ctrl+3)</td>

                        </tr>
						<tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr> 
                        <tr>
                            
                            <td align="left">Kategori Ekle (Ctrl+4 )</td>
                            <td align="left">Tedarikçi Ekle (Ctrl+5 )</td>

                        </tr>
						<tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr> 
                        <tr>
                           
                            <td align="left">Müşteri Ekle (Ctrl+6)</td>
                            <td align="left">Stokları Göster (Ctrl+7)</td>

                        </tr>
						<tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr> 
                        <tr>
                            
                            <td align="left">Satışları Göster (Ctrl+8)</td>
                            <td align="left">Ürün Girişiları Göster (Ctrl+9)</td>

                        </tr>
						<tr>
                            <td align="left">&nbsp;</td>
                            <td align="left">&nbsp;</td>
                        </tr> 
                        <tr>
                            <td align="left">Yeni Ekle(Ctrl+a)</td>
                            <td align="left">Kaydet ( Ctrl+s )</td>

                        </tr>
						<tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>

                        </tr>

                    </table>
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


    <p>Sorularınız için <a href="mailto:halil.mutlu@outlook.com?subject=Stok%20Yönetim%20Sistemi">halil.mutlu@outlook.com</a>.
    </p>

</div>
<!-- end footer -->

</body>
</html>