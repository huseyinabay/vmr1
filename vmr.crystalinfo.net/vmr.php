<?php
// fetch_wav.php'yi arka planda tetikler
// PHP başlangıcı, betik kodunun burada başladığını gösterir.
include_once("init.php"); // Başlangıç ve konfigürasyon dosyası yükleniyor (veritabanı bağlantısı, oturum yönetimi gibi ayarlar içerir).
include_once("tpl/top_bar.php"); // Üst menü çubuğu yükleniyor (navigasyon için kullanılır).
include_once("tpl/header.php"); // Sayfa başlığı ve üst kısım yükleniyor.
?>

<!DOCTYPE html> <!-- HTML5 standardına uygun bir belge olduğunu belirtir -->
<html lang="en"> <!-- Sayfanın dilini İngilizce ayarlar -->
<head>
		<meta charset="utf-8"> <!-- Sayfa karakter setini UTF-8 olarak belirler, Türkçe karakter desteği sağlar -->
		<title>DEPOSTOK - Müşteriler</title> <!-- Tarayıcı sekmesinde gözükecek başlığı ayarlar -->


		<link rel="stylesheet" href="css/style.css"> <!-- Harici stil dosyasını dahil eder, sayfanın CSS kurallarını yükler -->
    
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/> <!-- Sayfanın mobil uyumluluğunu sağlar -->


		<?php include_once("tpl/common_js.php"); ?> <!-- Ortak JavaScript dosyalarını içeren PHP dosyasını yükler -->
		<script src="js/script.js"></script> <!-- Genel JavaScript fonksiyonlarını içeren harici script dosyasını yükler -->
		<script LANGUAGE="JavaScript">
	
       
        function confirmSubmit() { // Bir işlem için kullanıcıdan onay almak için fonksiyon tanımlanıyor.
            var agree = confirm("<?php echo $lang['are_you_sure_you_want_to_delete_this_record']; ?>"); // Kullanıcıya "Tamam" veya "İptal" seçeneği sunan onay kutusu gösteriliyor.
            if (agree) // Kullanıcı "Tamam" seçerse aşağıdaki kod çalışır.
                return true; // Fonksiyon "true" döndürerek işlemin devam etmesini sağlar.
            else // Kullanıcı "İptal" seçerse aşağıdaki kod çalışır.
                return false; // Fonksiyon "false" döndürerek işlemin iptal edilmesini sağlar.

        }
		
		
		
        function confirmDeleteSubmit() { // Silme işlemi için onay almayı sağlayan fonksiyon başlatılıyor.
            var flag = 0; // Seçili öğelerin sayısını tutan değişken başlatılıyor.
            var field = document.forms.deletefiles; // Formdaki tüm elemanlar "field" değişkenine atanıyor.
            for (i = 0; i < field.length; i++) { // Formdaki tüm elemanlar üzerinde döngü başlatılıyor.
                if (field[i].checked == true) { // Eğer bir öğe seçiliyse (checkbox işaretlenmişse),
                    flag = flag + 1; // Seçili öğe sayısını 1 artır.
                }
            }
            if (flag < 1) { // Eğer hiçbir öğe seçilmemişse,

                alert("<?php echo $lang['you_must_select_at_least_one_record']; ?>"); // Kullanıcıya uyarı mesajı göster.

                return false; // İşlemi durdur.

            } else { // En az bir öğe seçilmişse,

                var agree = confirm("<?php echo $lang['are_you_sure_you_want_to_delete_this_record']; ?>"); // Kullanıcıdan silme onayı al.
                if (agree) // Eğer kullanıcı "Tamam" derse,


                    document.deletefiles.submit(); // Formu gönder.
                else // Kullanıcı "İptal" derse,

                    return false; // İşlemi iptal et.
            }
        }
		
		function confirmLimitSubmit() {
		  const val = document.getElementById('search_limit').value.trim();
		  if (val === '' || isNaN(val) || Number(val) < 1) {
			alert("<?php echo $lang['please_enter_a_valid_number']; ?>");
			return false;
		  }
		  // direkt olarak formu gönder
		  document.getElementById('limit_go').submit();
		  return true;
		}
		
		
        // Formdaki tüm checkbox'ları işaretleyen fonksiyon.
        function checkAll() { 
            var field = document.forms.deletefiles; // Formdaki tüm elemanları al.

            for (i = 0; i < field.length; i++) // Tüm elemanlar için döngü başlat.

                field[i].checked = true; // Her elemanı işaretle.
        }
		
		// Formdaki tüm checkbox işaretlerini kaldıran fonksiyon.
        function uncheckAll() {

            var field = document.forms.deletefiles; // Formdaki tüm elemanları al.
            for (i = 0; i < field.length; i++) // Tüm elemanlar için döngü başlat.

                field[i].checked = false; // Her elemandaki işareti kaldır.
        }
        // -->
    </script>
	<script>
		function saveNote(id, note) {
			fetch('update_note.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: 'id=' + encodeURIComponent(id) + '&note=' + encodeURIComponent(note)
			}).then(response => {
				if (!response.ok) {
					alert("Not kaydedilemedi.");
				}
			});
		}
		</script>

	

	
	
	<script>
		let isAllChecked = false; // Tüm kutucukların seçili olup olmadığını takip eden değişken (başlangıçta seçili değil).

		function toggleCheckAll() { // Tüm kutucukların seçimini aç/kapat fonksiyonu başlatılır.
			let checkboxes = document.querySelectorAll('input[name="checklist[]"]'); // Sayfadaki tüm checklist[] checkboxlarını seç.

			isAllChecked = !isAllChecked; // Değeri tersine çevir (true ise false yap, false ise true yap).

			checkboxes.forEach((el) => el.checked = isAllChecked); // Her bir checkbox'a sırayla git ve isAllChecked değerine göre işaretle/kaldır.

			const button = document.querySelector('input[name="selectall"]'); // "selectall" adlı butonu bul.
			if (button) { // Eğer buton varsa:
				button.value = isAllChecked ? LANG_CANCEL_SELECTION : LANG_SELECT_ALL; // Seçili duruma göre buton metnini güncelle: "Seçimi Kaldır" veya "Tümünü Seç".
			}
		}

	</script>

<script>
    const LANG_SELECT_ALL = "<?php echo $lang['select_all']; ?>";
    const LANG_CANCEL_SELECTION = "<?php echo $lang['cancel_selection']; ?>";
</script>


	<script>
		// TÜMÜNÜ SİLMEK İÇİN JavaScript fonksiyonu
		function deleteAllBySelection() {
			// Sayfadaki tüm checkbox'ları seç (adı "checklist[]" olanlar)
			const checkboxes = document.querySelectorAll('input[name="checklist[]"]');

			// Eğer hiç checkbox yoksa (yani listelenecek kayıt yoksa), uyarı ver ve işlemi durdur
			if (checkboxes.length === 0) {
				alert("<?php echo $lang['no_records_found_to_delete']; ?>"); // Dil dosyasından uyarı mesajı
				return; // Fonksiyonu sonlandır
			}

			// Tüm checkbox'ları işaretli (checked) hale getir (tüm kayıtları silmek için)
			checkboxes.forEach(cb => cb.checked = true);

			// Kullanıcıdan onay al: "Tüm kayıtları silmek istediğinize emin misiniz?"
			if (confirm("<?php echo $lang['are_you_sure_you_want_to_delete_all_records']; ?>")) {
				// Eğer kullanıcı "Tamam" dediyse, "deletefiles" adlı formu gönder (POST)
				document.forms['deletefiles'].submit();
			}
		}
	</script>


	<script>
		// SEÇİLENİ İNDİRMEK İÇİN JavaScript
		function downloadSelected() {
			// Tüm seçili (checked) checkbox'ları alır
			const checkboxes = document.querySelectorAll('input[name="checklist[]"]:checked');

			// Eğer hiçbiri seçilmemişse, uyarı verir ve işlemi durdurur
			if (checkboxes.length === 0) {
				alert("<?php echo $lang['please_select_at_least_one_record_to_download']; ?>"); // Dil dosyasından gelen uyarı mesajı
				return; // Fonksiyonu sonlandırır
			}

			// Seçilen kayıtları göndereceğimiz formu seçiyoruz
			const form = document.getElementById('downloadForm');

			// Form içeriğini sıfırla ve tablo adını gizli input olarak ekle
			form.innerHTML = '<input type="hidden" name="table" value="voice_records">';

			// Her seçili checkbox için, form içine gizli input olarak ID değerlerini ekle
			checkboxes.forEach(cb => {
				const input = document.createElement('input'); // Yeni input elemanı oluştur
				input.type = 'hidden';                         // Gizli input olarak ayarla
				input.name = 'checklist[]';                    // Name değeri, backend'de aynı şekilde yakalanacak
				input.value = cb.value;                        // Değeri checkbox'ın value'si (seçilen ID)
				form.appendChild(input);                       // Oluşturulan input'u forma ekle
			});

			// Formu gönder (POST)
			form.submit();
		}

	</script>
	
	
	<script>
		// TÜM KAYITLARI İNDİRMEK İÇİN JavaScript
		function downloadAll() {
			// Sayfadaki tüm checkbox'ları seç (tüm kayıtlar)
			const checkboxes = document.querySelectorAll('input[name="checklist[]"]');

			// Eğer hiç kayıt (checkbox) yoksa, uyarı ver ve işlemi durdur
			if (checkboxes.length === 0) {
				alert("<?php echo $lang['no_records_found_to_download']; ?>"); // Dil dosyasından mesaj
				return;
			}

			// Daha önceki formları temizle, sadece tablo adı bilgisi kalsın
			const form = document.getElementById('downloadForm');
			form.innerHTML = '<input type="hidden" name="table" value="voice_records">';

			// Tüm checkbox'ları gizli input olarak form içine ekle
			checkboxes.forEach(cb => {
				const input = document.createElement('input'); // Yeni input elementi oluştur
				input.type = 'hidden';                         // Gizli olarak tanımla
				input.name = 'checklist[]';                    // İsim checkbox listesi ile aynı olmalı
				input.value = cb.value;                        // Checkbox'ın value'si (örneğin kayıt ID’si)
				form.appendChild(input);                       // Form içine ekle
			});

			// Formu gönder (POST olarak)
			form.submit();
		}

	</script>
	
</head>

<body>
	<!-- ÜST MENÜ ÇUBUĞU -->
	<?php include_once("tpl/top_bar.php"); ?> 
	<!-- Üst menü çubuğunu içeren dosyayı dahil eder. Üst menüde kullanıcı işlemleri, dil seçenekleri vb. olabilir.-->
	<!-- Üst menü çubuğu bitiş -->

	<!-- BAŞLIK KISMI -->
	<div id="header-with-tabs">
		<div class="page-full-width cf"> <!--  Sayfanın tamamını kaplayan bir konteyner oluşturur.-->
			<a href="#" id="company-branding-small" class="fr"> <!--// Şirketin logosu için bağlantı etiketi. Sağ tarafa hizalanır.-->
				<img src="<?php if (isset($_SESSION['logo'])) { echo "upload/" . $_SESSION['logo']; } else { echo "upload/depostok.png"; } ?>" alt="Depo Stok"/>
			   <!--  Eğer oturum değişkeni (session) olarak 'logo' varsa, bu logoyu gösterir.-->
				<!-- Eğer oturumda logo yoksa, varsayılan olarak "upload/depostok.png" gösterilir.-->
			</a>
		</div>
	</div>
	<!-- Başlık kısmı bitiş -->

	<!-- ANA İÇERİK -->
	<div id="content">
		<label class="switch"> <!--  Aç/Kapat butonu (toggle switch) için etiket.-->
			<input type="checkbox" data-name="show" id="toggle" checked> 
			<!--  Varsayılan olarak açık gelen bir checkbox.-->
			<div class="slider round"></div> 
			<!-- Toggle butonunun tasarımını sağlayan div.-->
		</label>

    <div class="page-full-width cf">
        <!-- SOL MENÜ -->
        <div class="side-menu fl" id="sidebar"> <!--  Sol tarafta yer alan menü bölümü.-->
            <h3><?php echo $lang['vmr']; ?></h3> <!-- Menü başlığı. Dil dosyasından çekilir. -->
            <ul>
                <li><a href="deneme.php"> <?php echo $lang['voice_records']; ?> </a></li> 
               <!--  Menü öğesi. Kullanıcı "deneme.php" sayfasına yönlendirilir.-->
            </ul>
        </div>
        <!-- Sol menü bitiş -->

        <!-- ANA İÇERİK ALANI -->
        <div class="side-content fr"> <!--  İçeriğin sağ tarafta yer almasını sağlayan div.-->
            <div class="content-module" id="map"> <!--  İçerik modülü. Harita veya liste içerebilir.-->
                <div class="content-module-heading cf">
                    <h3 class="fl"> <?php echo $lang['voice_records']; ?> </h3>
                    <span class="fr expand-collapse-text"> <?php echo $lang['click_to_collapse']; ?> </span>
                   <!--  İçeriğin daraltılıp genişletilebilmesini sağlayan metin.-->
                    <span class="fr expand-collapse-text initial-expand"> <?php echo $lang['click_to_expand']; ?> </span>
                    <!-- İlk açılışta genişletme seçeneğini gösterir.-->
                </div>
                <!-- İçerik modülü başlığı bitiş -->

        <div class="content-module-main cf"> <!--İçeriğin ana bölümünü kapsayan div.-->
            
			
			
		<table> <!--  İçerik tablosu başlatılır.-->

		<!-- Tarih Seçme Formu -->
		<form action="" method="get" name="date_range"> <!-- Sayfa kendine GET yöntemiyle form gönderiyor -->

			<?php if (!empty($_GET['page'])): ?> 
				<!-- Eğer URL'de sayfa bilgisi varsa, formda da gizli input olarak koru -->
				<input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']); ?>">
			<?php endif; ?>

			<?php if (!empty($_GET['searchtxt'])): ?>
				<!-- Eğer URL'de arama metni varsa, formda gizli input olarak koru -->
				<input type="hidden" name="searchtxt" value="<?php echo htmlspecialchars($_GET['searchtxt']); ?>">
			<?php endif; ?>

			<?php if (!empty($_GET['limit'])): ?>
				<!-- Eğer sayfa başına limit ayarı varsa, formda gizli input olarak koru -->
				<input type="hidden" name="limit" value="<?php echo htmlspecialchars($_GET['limit']); ?>">
			<?php endif; ?>

			<!-- Başlangıç tarihi etiketi ve input'u -->
			<label for="start_date"><?php echo $lang['start_date']; ?></label>
			<input type="date" id="start_date" name="start_date" 
				   value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
			<!-- Eğer URL'de start_date varsa input'a yaz, yoksa boş bırak -->

			<!-- Bitiş tarihi etiketi ve input'u -->
			<label for="end_date"><?php echo $lang['end_date']; ?></label>
			<input type="date" id="end_date" name="end_date" 
				   value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
			<!-- Eğer URL'de end_date varsa input'a yaz, yoksa boş bırak -->

			<!-- Ayarla (Filtrele) butonu -->
			<button type="submit" class="round blue my_button text-upper">
				<?php echo $lang['set']; ?> <!-- Buton yazısı dil dosyasından gelir -->
			</button>

		</form>



		   <!-- Arama Formu -->
		<form action="" method="get" name="search"> <!-- Sayfa kendisine GET yöntemiyle arama parametrelerini gönderir -->

			<?php if (!empty($_GET['page'])): ?>
				<!-- Sayfa numarası URL'de varsa, onu formda gizli input olarak sakla -->
				<input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']); ?>">
			<?php endif; ?>

			<?php if (!empty($_GET['start_date'])): ?>
				<!-- Başlangıç tarihi URL'de varsa, formda sakla -->
				<input type="hidden" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date']); ?>">
			<?php endif; ?>

			<?php if (!empty($_GET['end_date'])): ?>
				<!-- Bitiş tarihi URL'de varsa, formda sakla -->
				<input type="hidden" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date']); ?>">
			<?php endif; ?>

			<?php if (!empty($_GET['limit'])): ?>
				<!-- Sayfa başı gösterim limiti URL'de varsa, formda koru -->
				<input type="hidden" name="limit" value="<?php echo htmlspecialchars($_GET['limit']); ?>">
			<?php endif; ?>

			<!-- Arama metni girilen kutu -->
			<input name="searchtxt" type="text" class="round my_text_box"
				   placeholder="<?php echo $lang['search']; ?>" 
				   value="<?php echo isset($_GET['searchtxt']) ? htmlspecialchars($_GET['searchtxt']) : ''; ?>">
			<!-- Eğer arama yapılmışsa, metin kutusunda aynı arama terimi kalmaya devam eder -->

			<!-- Arama butonu -->
			<input name="Search" type="submit" class="my_button round blue text-upper" 
				   value="<?php echo $lang['search']; ?>">
			<!-- Butonun etiketi dil dosyasından gelir -->

		</form>



		<!-- Sayfa Limiti Ayarlama Formu -->
		<form action="" method="get" name="limit_go" id="limit_go"> 
		<!-- Form GET yöntemiyle kendisine gönderiliyor, limit ayarı bu şekilde işleniyor -->
		<input type="hidden" name="page" value="1">
		   

			<?php if (!empty($_GET['searchtxt'])): ?>
				<!-- Arama kelimesi varsa, onu da formda gizli input olarak koruyor -->
				<input type="hidden" name="searchtxt" value="<?php echo htmlspecialchars($_GET['searchtxt']); ?>">
			<?php endif; ?>

			<?php echo $lang['records_per_page']; ?> 
			<!-- Dil dosyasından "Sayfa başına kayıt" gibi bir açıklama yazısı geliyor -->

			<!-- Kullanıcının sayfa başına kaç kayıt gösterileceğini belirttiği input kutusu -->
			<input name="limit" type="text" class="round my_text_box" id="search_limit"
				   style="margin-left:5px;" 
				   value="<?php echo isset($_GET['limit']) ? htmlspecialchars($_GET['limit']) : '10'; ?>" 
				   size="3" maxlength="3">
			<!-- Eğer URL'de limit bilgisi varsa input’a yazılır, yoksa varsayılan olarak 10 yazılır -->

			<?php if (!empty($_GET['start_date'])): ?>
				<!-- Başlangıç tarihi filtresi varsa gizli olarak forma eklenir -->
				<input type="hidden" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date']); ?>">
			<?php endif; ?>

			<?php if (!empty($_GET['end_date'])): ?>
				<!-- Bitiş tarihi filtresi varsa gizli olarak forma eklenir -->
				<input type="hidden" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date']); ?>">
			<?php endif; ?>

			<!-- Kullanıcının limiti uygulamak için tıkladığı buton -->
			<input name="go" type="button" value="<?php echo $lang['set']; ?>" 
				   class="round blue my_button text-upper" 
				   onclick="return confirmLimitSubmit()"> 
			<!-- onclick ile JavaScript’te limitin geçerliliğini kontrol eden fonksiyon tetiklenir -->

		</form>



		<!-- Tüm kayıtları seçmek için buton -->
		<input type="button" name="selectall" value="<?php echo $lang['select_all']; ?>"
			   class="my_button round blue text-upper"
			   onClick="toggleCheckAll()" style="margin-left:5px;" />
		<!-- onClick: toggleCheckAll() fonksiyonu tüm checkbox'ları seçer -->

		<!-- Tüm kayıtları indirmek için buton -->
		<input type="button" name="download_all" value="<?php echo $lang['download_all']; ?>"
			   class="my_button round blue text-upper"
			   onClick="downloadAll()" style="margin-left:5px;" />
		<!-- onClick: downloadAll() fonksiyonu, tüm kayıtları (checkbox'ları) form içine ekleyip indirir -->

		<!-- Tüm kayıtları silmek için buton -->
		<input type="button" name="delete_all" value="<?php echo $lang['delete_all']; ?>"
			   class="my_button round blue text-upper"
			   onClick="deleteAllBySelection()" style="margin-left:5px;" />
		<!-- onClick: deleteAllBySelection() fonksiyonu, tüm checkbox'ları işaretleyip silme onayı alır -->

		<!-- Seçimi kaldırmak için buton -->
		<input type="button" name="unselectall" value="<?php echo $lang['cancel_selection']; ?>"
			   class="my_button round blue text-upper"
			   onClick="uncheckAll()" style="margin-left:5px;" />
		<!-- onClick: uncheckAll() fonksiyonu tüm checkbox'ların seçimini kaldırır -->

		<!-- Seçili kayıtları silmek için buton -->
		<input name="dsubmit" type="button" value="<?php echo $lang['delete_selected']; ?>"
			   class="my_button round blue text-upper" style="margin-left:5px;"
			   onclick="return confirmDeleteSubmit()" />
		<!-- onClick: confirmDeleteSubmit() fonksiyonu kullanıcıya "Silmek istiyor musunuz?" onayı verir -->

		<!-- Seçili kayıtları indirmek için buton -->
		<input name="dsubmit" type="button" value="<?php echo $lang['download_selected']; ?>"
			   class="my_button round blue text-upper" style="margin-left:5px;"
			   onclick="downloadSelected()" />
		<!-- onClick: downloadSelected() fonksiyonu sadece seçili checkbox'lara ait kayıtları indirir -->

							</table>
                            <!-- Müşteri bilgilerini gösterecek tablonun başlangıcı.-->
							<table>
							<?php

						// Tarih filtresi varsa
						$sql = "SELECT * FROM voice_records"; // Tüm kayıtları alacak temel SQL sorgusunu hazırla
						$where = [];						  // WHERE koşullarını biriktirecek boş bir dizi tanımla

						// Tarih filtresi girilmiş mi diye kontrol et
						if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
							$startDate = mysqli_real_escape_string($db->getConnection(), $_GET['start_date']);      // Başlangıç tarihini SQL enjeksiyonuna karşı güvenli hâle getir
							$endDate = mysqli_real_escape_string($db->getConnection(), $_GET['end_date']);		    // Bitiş tarihini SQL enjeksiyonuna karşı güvenli hâle getir
							$where[] = "DATE(call_date) BETWEEN '$startDate' AND '$endDate'";					    //Tarih aralığı filtresini WHERE koşulları dizisine ekle
						}

						// Arama metni girilmiş mi diye kontrol et
						if (isset($_GET['searchtxt']) && trim($_GET['searchtxt']) != "") {						  
							$search = mysqli_real_escape_string($db->getConnection(), $_GET['searchtxt']);			//Arama terimini SQL enjeksiyonuna karşı güvenli hâle getir
							$where[] = "(customer_name LIKE '%$search%' OR caller LIKE '%$search%' OR callee LIKE '%$search%' OR note LIKE '%$search%')"; //Müşteri adı, arayan numara üzerinde aranacak, aranan metni içeren kayıtlar için OR koşullarını WHERE dizisine ekle
						}
						//Eğer en az bir filtre koşulu varsa, SQL sorgusuna WHERE ile birlikte JOIN et
						if (!empty($where)) {
							$sql .= " WHERE " . implode(" AND ", $where);											//Tüm koşulları "AND" ile birleştirip SQL'e ekle
						}





						// Eğer arama yapıldıysa ve arama metni boş değilse filtreleme yap
						if (isset($_GET['searchtxt']) && trim($_GET['searchtxt']) != "") {

							$SQL = "SELECT * FROM  voice_records 
									WHERE customer_name LIKE '%" . $_GET['searchtxt'] . "%'  
									OR caller LIKE '%" . $_GET['searchtxt'] . "%' 
									OR callee LIKE '%" . $_GET['searchtxt'] . "%' 
									ORDER BY id DESC";
						}

						// Kullanılan tablo ismini belirler.
						$tbl_name = "voice_records"; 

						// Sayfalama için kaç bitişik sayfanın gösterileceğini belirler.
						$adjacents = 3;

						// Toplam müşteri sayısını almak için sorgu hazırlar.
						$query = "SELECT COUNT(*) as num FROM voice_records";
						$where = [];

						if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
							$startDate = mysqli_real_escape_string($db->getConnection(), $_GET['start_date']);
							$endDate = mysqli_real_escape_string($db->getConnection(), $_GET['end_date']);
							$where[] = "DATE(call_date) BETWEEN '$startDate' AND '$endDate'";
						}

						if (isset($_GET['searchtxt']) && trim($_GET['searchtxt']) != "") {
							$search = mysqli_real_escape_string($db->getConnection(), $_GET['searchtxt']);
							$where[] = "(customer_name LIKE '%$search%' OR caller LIKE '%$search%' OR callee LIKE '%$search%' OR note LIKE '%$search%')";

						}

						if (!empty($where)) {
							$query .= " WHERE " . implode(" AND ", $where);
						}



						// Toplam müşteri sayısını alır.
						$total_pages = mysqli_fetch_array(mysqli_query($db->getConnection(), $query));
						$total_pages = $total_pages['num'];

						// Sayfalama için gerekli değişkenleri tanımlar.
						$targetpage = "vmr.php";  // Sayfalama yapılacak sayfa// Sayfa limiti belirle
						
						$extraParams = '';

						if (!empty($_GET['start_date'])) {
							$extraParams .= '&start_date=' . urlencode($_GET['start_date']);
						}

						if (!empty($_GET['end_date'])) {
							$extraParams .= '&end_date=' . urlencode($_GET['end_date']);
						}

						if (!empty($_GET['limit'])) {
							$extraParams .= '&limit=' . urlencode($_GET['limit']);
						}

						// Eğer arama formunu POST yerine GET'e çevirmezsen burayı POST yerine GET yapmazsan çalışmaz!
						if (!empty($_GET['searchtxt'])) {
							$extraParams .= '&searchtxt=' . urlencode($_GET['searchtxt']);
						}

						
						$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10;

						// Mevcut sayfa belirle
						$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
						$start = ($page - 1) * $limit;

						// Temel SQL ve filtre dizisi
						$where = [];

						// Tarih filtresi varsa
						if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
							$startDate = mysqli_real_escape_string($db->getConnection(), $_GET['start_date']);
							$endDate = mysqli_real_escape_string($db->getConnection(), $_GET['end_date']);
							$where[] = "DATE(call_date) BETWEEN '$startDate' AND '$endDate'";
						}

						// Arama filtresi varsa
						if (isset($_GET['searchtxt']) && trim($_GET['searchtxt']) != "") {
							$search = mysqli_real_escape_string($db->getConnection(), $_GET['searchtxt']);
							$where[] = "(customer_name LIKE '%$search%' OR caller LIKE '%$search%' OR callee LIKE '%$search%' OR note LIKE '%$search%')";

						}

						// Where koşullarını birleştir
						$whereSQL = '';
						if (!empty($where)) {
							$whereSQL = " WHERE " . implode(" AND ", $where);
						}

						// Toplam kayıt sayısı (sayfalama için)
						$queryTotal = "SELECT COUNT(*) AS num FROM voice_records $whereSQL";
						$total_result = mysqli_fetch_assoc(mysqli_query($db->getConnection(), $queryTotal));
						$total_pages = $total_result['num'];

						// Sonuçları al (sayfalama ile)
						$sql = "SELECT * FROM voice_records $whereSQL ORDER BY call_date DESC LIMIT $start, $limit";
						$result = mysqli_query($db->getConnection(), $sql);


						// Sayfa numarasını belirler. Eğer 0 ise varsayılan olarak 1 yapar.
						if ($page == 0) $page = 1;

						// Önceki ve sonraki sayfaların numaralarını belirler.
						$prev = $page - 1;
						$next = $page + 1;

						// Son sayfa numarasını hesaplar.
						$lastpage = ceil($total_pages / $limit);
						$lpm1 = $lastpage - 1; // Son sayfadan bir önceki sayfa

						// Sayfalama (pagination) için HTML içeriğini oluşturur.
						$pagination = "";

						if ($lastpage > 1) { // Eğer birden fazla sayfa varsa sayfalama oluşturur.
							$pagination .= "<div >";

						// Önceki sayfa butonunu ekler.
						if ($page > 1) {
							$pagination .= "<a href=\"vmr.php?page=$prev$extraParams\" class=my_pagination >Önceki</a>";

						} else {
							$pagination .= "<span class=my_pagination>Önceki</span>";
						}

						// Sayfa numaralarını ekler.
						if ($lastpage < 7 + ($adjacents * 2)) { // Eğer toplam sayfa sayısı çok değilse, hepsini gösterir.
							for ($counter = 1; $counter <= $lastpage; $counter++) {
								if ($counter == $page) {
									$pagination .= "<span class=my_pagination>$counter</span>";
								} else {
						$pagination .= "<a href=\"vmr.php?page=" . htmlspecialchars($counter) . $extraParams . "\" class=my_pagination>$counter</a>";
								}
							}
						} elseif ($lastpage > 5 + ($adjacents * 2)) { // Eğer çok fazla sayfa varsa, bazılarını gizler.
							if ($page < 1 + ($adjacents * 2)) {

								  for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) { // Sayfanın başında kaç tane sayfa numarası gösterileceğini belirleyen döngü.
							if ($counter == $page) // Eğer döngüdeki sayfa numarası, mevcut sayfa numarasına eşitse,
								$pagination .= "<span class=my_pagination>$counter</span>"; // Seçili sayfa olduğu için vurgulanan bir <span> etiketi ekleniyor.
							else
						$pagination .= "<a href=\"vmr.php?page=" . htmlspecialchars($counter) . $extraParams . "\" class=my_pagination>$counter</a>";
							}
							$pagination .= "..."; // Orta kısımdaki sayfalar gizleneceği için üç nokta ekleniyor.
							$pagination .= "<a href=\"vmr.php?page=$lpm1&limit=$limit\" class=my_pagination>$lpm1</a>"; // Sondan bir önceki sayfa ekleniyor.
							$pagination .= "<a href=\"vmr.php?page=$lastpage&limit=$limit\" class=my_pagination>$lastpage</a>"; // Son sayfa ekleniyor.
							} elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) { // Kullanıcı ortada bir sayfadaysa, baştan ve sondan bazı sayfalar gizleniyor.
								$pagination .= "<a href=\"vmr.php?page=1&limit=$limit\" class=my_pagination>1</a>"; // İlk sayfa bağlantısı ekleniyor.
								$pagination .= "<a href=\"vmr.php?page=2&limit=$limit\" class=my_pagination>2</a>"; // İkinci sayfa bağlantısı ekleniyor.
								$pagination .= "..."; // Sayfa numaraları arasında üç nokta ile boşluk bırakılıyor.
								for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) { // Mevcut sayfanın etrafındaki sayfa numaralarını gösteren döngü.
									if ($counter == $page) // Eğer döngüdeki sayfa numarası, mevcut sayfa numarasıyla eşleşirse,
										$pagination .= "<span class=my_pagination>$counter</span>"; // Seçili sayfa olduğu için vurgulanan <span> etiketi ekleniyor.
									else
						$pagination .= "<a href=\"vmr.php?page=" . htmlspecialchars($counter) . $extraParams . "\" class=my_pagination>$counter</a>";
								}
								$pagination .= "..."; // Son sayfalar öncesinde üç nokta ile ayrım ekleniyor.
								$pagination .= "<a href=\"vmr.php?page=$lpm1&limit=$limit\" class=my_pagination>$lpm1</a>"; // Sondan bir önceki sayfa ekleniyor.
								$pagination .= "<a href=\"vmr.php?page=$lastpage&limit=$limit\" class=my_pagination>$lastpage</a>"; // Son sayfa bağlantısı ekleniyor.
							} else { // Kullanıcı son sayfalara yaklaştığında, baştaki sayfalar gizlenerek son sayfalar gösteriliyor.
								$pagination .= "<a href=\"vmr.php?page=1&limit=$limit\" class=my_pagination>1</a>"; // İlk sayfa bağlantısı ekleniyor.
								$pagination .= "<a href=\"vmr.php?page=2&limit=$limit\" class=my_pagination>2</a>"; // İkinci sayfa bağlantısı ekleniyor.
								$pagination .= "..."; // Sayfa numaraları arasında üç nokta eklenerek aralık belirtiliyor.
								for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) { // Son sayfaların gösterileceği döngü başlatılıyor.
									if ($counter == $page) // Eğer döngüdeki sayfa numarası mevcut sayfa numarası ile eşleşirse,
										$pagination .= "<span class=my_pagination>$counter</span>"; // Seçili sayfa olduğu için vurgulanan bir <span> etiketi ekleniyor.
									else
										$pagination .= "<a href=\"$targetpage?page=$counter&limit=$limit\" class=my_pagination>$counter</a>"; // Sayfa bağlantısı oluşturuluyor.
								}
												}

											}


							// Sonraki sayfa butonu ekleniyor.
							if ($page < $counter - 1) // Eğer mevcut sayfa, toplam sayfa numarasından küçükse,
								$pagination .= "<a href=\"vmr.php?page=$next$extraParams\" class=my_pagination>Sonraki</a>"; // "Sonraki" bağlantısı ekleniyor.
							else
								$pagination .= "<span class=my_pagination>Sonraki</span>"; // Kullanıcı son sayfadaysa buton pasif hale getiriliyor.

							$pagination .= "</div>\n"; // Sayfalama div'i kapatılıyor.

                                }
                                ?>
								

						<!-- Tüm Ayarları Sıfırlamak için -->
						<?php if (!empty($_GET['searchtxt']) || !empty($_GET['start_date']) || !empty($_GET['end_date']) || !empty($_GET['limit'])): ?>
							<form action="vmr.php" method="get" style="display:inline;">
								<button type="submit" class="my_button round red text-upper" style="margin-left:5px;">
									<?php echo $lang['reset_filters']; ?>
								</button>
							</form>
						<?php endif; ?>							
							<form name="deletefiles" action="delete.php" method="post" onsubmit="return confirmSubmit();">
							<input type="hidden" name="table" value="voice_records">
							<input type="hidden" name="return" value="vmr.php">
							
							<table>
							
							
							<!-- Müşteri kayıtları için bir tablo başlığı oluşturuluyor.-->
                                <tr>
                                    <th><?php echo $lang['date']; ?></th>
									<th><?php echo $lang['name']; ?></th>
                                    <th><?php echo $lang['caller_number']; ?></th>
                                    <th><?php echo $lang['calle_number']; ?></th>
									<th><?php echo $lang['voice_record']; ?></th>
									<th><?php echo $lang['note']; ?></th>
									<th><?php echo $lang['delete']; ?></th>
                                    <th><?php echo $lang['select']; ?></th>
                                </tr>
                                
                                <!-- Müşteri kayıtlarını tablo içinde listeleme -->
                                <?php $i = 1;
                                while ($row = mysqli_fetch_array($result)) { // Müşteri kayıtlarını döngü ile tabloya ekliyor
								?>
									<tr>
										<td><?php echo $row['call_date']; ?></td>
										<td><?php echo $row['customer_name']; ?></td>
										<td><?php echo $row['caller']; ?></td>
										<td><?php echo $row['callee']; ?></td>
										<td>
										<?php
											// Tam path'i tarayıcıdan erişilebilir hale getir
											$relativePath = str_replace("/usr/local/wwwroot/vmr.crystalinfo.net/", "", $row['record_path']);
										?>
										<audio controls>
											<source src="<?php echo htmlspecialchars($relativePath); ?>" type="audio/wav">
											Tarayıcınız ses oynatmayı desteklemiyor.
										</audio>
										</td>
										
										<td>
										<input type="text" name="note" value="<?php echo htmlspecialchars($row['note'] ?? ''); ?>" style="width:150px; height:30px" 
											onblur="saveNote(<?php echo $row['id']; ?>, this.value)">
										</td>	   
                                        
										<td>
											<a href="delete.php?id=<?php echo $row['id']; ?>&table=voice_records&return=vmr.php" onclick="return confirmSubmit();" class="table-actions-button ic-table-delete"></a>
                                        </td>

                                        <td><input type="checkbox" value="<?php echo $row['id']; ?>" name="checklist[]"/></td>
                                    </tr>
                                   
								   <?php $i++;
                                } ?>
                            
							</table>
							</form>
						
							
							
							<table>
                                <tr>

                                    <td align="center">
                                        <div style="margin-left:20px;"><?php echo $pagination; ?></div>
                                    </td>

                                </tr>
                            </table>
							
                        </table>
						
                </div>
            </div>
        </div>
    </div>
	<form id="downloadForm" method="post" action="download.php"></form>
</div>
</body>
</html>
