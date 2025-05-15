<?  //INCLUDES
   require_once $_SERVER['DOCUMENT_ROOT'].'/cgi-bin/functions.php';
   
   require_valid_login();
    
   $cUtility = new Utility();
   $cdb = new db_layer();

   $conn = $cdb->getConnection();
 
     //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
     check_right("SITE_ADMIN");
   cc_page_meta();
   echo "<center>";
   page_header();
   echo "<br>"
   
?>
<table border="0" width="85%" align="center">
  <tr>
    <td width="100%">
        <table border="0" width="650" bgcolor="#FFFFFF" cellspacing="1" cellpadding="4">
            <tr class="rep2_tr">
             
			 <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Parametreler</td>
        <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">E-Posta Ayarları</td>
      </tr>
	  

  
	  
      <tr>  
        <td class="rep2_cells1" align="center" width="15%">
          <a href="systemprm_src.php" onmouseover="javascript:show_hide(12)" onmouseout="javascript:show_hide(12)">
        <img border="0" src="<?=IMAGE_ROOT?>admin/sistem_prm.gif" alt=""></a> 
        </td>
        <td class="rep2_cells2" width="35%">Sistem Parametrelerini <br>Düzenleme<br>
            Ekranı<br>
          
        </td>
		

		

        <td class="rep2_cells1" width="15%" align="center" valign="top">
            <a href="email_set.php?act=upd&id=1" onmouseover="javascript:show_hide(15)" onmouseout="javascript:show_hide(15)">
        <img border="0" src="<?=IMAGE_ROOT?>admin/email_sets.gif" alt=""></a>             
        </td>
        <td class="rep2_cells2" width="35%">Mail Sunucusu<br>ve Diğer Mail Gönderim<br>Bilgilerinin Tanımlandığı<br>Yer
        </td>
      </tr>
	  
	   <tr class="rep2_tr">
              <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Tarife Yönetimi (Süre)</td>
        <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Tarife Yönetimi (Ücret)</td>
      </tr>
	  

  
	  
      <tr>  
        <td class="rep2_cells1" align="center" width="15%">
          <a href="tariff_sure_src.php" onmouseover="javascript:show_hide(12)" onmouseout="javascript:show_hide(12)">
        <img border="0" src="<?=IMAGE_ROOT?>admin/gsm_tariff.gif" alt=""></a> 
        </td>
        <td class="rep2_cells2" width="35%">Tarife Sürelerini<br>Düzenleme<br>
            Ekranı<br>
          
        </td>
		

		
        <td class="rep2_cells1" align="center" width="15%">
          <a href="tariff_ucret_src.php" onmouseover="javascript:show_hide(12)" onmouseout="javascript:show_hide(12)">
        <img border="0" src="<?=IMAGE_ROOT?>admin/gsm_tariff.gif" alt=""></a> 
        </td>
        <td class="rep2_cells2" width="35%">Tarife Ücretlerini <br>Düzenleme<br>
            Ekranı<br>
          
        </td>
      </tr>
	  
	  
	  	   <tr class="rep2_tr">
              <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Veri Yükleme</td>
        <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Yedekleme</td>
      </tr>
	  
	  
	  
	      <tr> 
		  
      <td align="center" valign="top" bgcolor="#B6C7D3">
        <a href="text_to_db.php" onmouseover="javascript:show_hide(13)" onmouseout="javascript:show_hide(13)">
        <img border="0" src="<?=IMAGE_ROOT?>admin/veri_yukle.gif" alt=""></a>            
      </td>
        <td class="rep2_cells2" width="35%">Veritabanına bağlantıda sorun olması durumunda, dosyalara yazılmış olan verilerin tekrar veritabanına atılmasını sağlayan bölümdür.
          
        </td>
		

		

      <td align="center" valign="top" bgcolor="#CEDBE3">
        <a href="/admin/download.php" onmouseover="javascript:show_hide(20)" onmouseout="javascript:show_hide(20)">
        <img border="0" src="<?=IMAGE_ROOT?>backup.gif" alt=""></a>            
      </td>
        <td class="rep2_cells2" width="35%">Yedeği alınmış olan verilerin indirilerek kullanıcının bilgisayarına kaydedilmesini sağlayan bölümdür.
        </td>
      </tr>
	  
	  
	  
	  
	  	   <tr class="rep2_tr">
              <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Santral Format Düzenleme</td>
        <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">SQL Query</td>
      </tr>
	  
	  
	  
	      <tr> 
		  
      <td align="center" valign="top" bgcolor="#B6C7D3">
        <a href="/switches/switch_src.php" onmouseover="javascript:show_hide(13)" onmouseout="javascript:show_hide(13)">
        <img border="0" src="<?=IMAGE_ROOT?>admin/sites.gif" alt=""></a>            
      </td>
        <td class="rep2_cells2" width="35%">Santralı Formatı Ekleme ve Düzenleme işlemlerinin yapıldığı bölümdür.
          
        </td>
		

		

      <td align="center" valign="top" bgcolor="#CEDBE3">
        <a href="/admin/sql_executer.php" onmouseover="javascript:show_hide(20)" onmouseout="javascript:show_hide(20)">
        <img border="0" src="<?=IMAGE_ROOT?>admin/sistem_loglari1.gif" alt=""></a>            
      </td>
        <td class="rep2_cells2" width="35%">SQL Query çalıştırmayı sağlayan bölümdür.
        </td>
      </tr>
	  
	  

      </table>
    </td>
  </tr>
  
  
  
  
 
</table>
    
<?
   page_footer(0);
?>
