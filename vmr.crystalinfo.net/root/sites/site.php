<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   session_cache_limiter('nocache');
   require_valid_login();
   $conn = $cdb->getConnection();

   check_right("SITE_ADMIN");

   if ($act=="upd" && $id!="" && is_numeric($id)){
     $sql_str = "SELECT * FROM SITES WHERE SITE_ID = $id " ; 
     if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
   if (mysqli_num_rows($result)>0){
       $row = mysqli_fetch_object($result);
   }else{
      print_error("Belirtilen Kayıt Bulunamadı");
        exit;    
   }
   }
   
  cc_page_meta();
     echo "<center>";
     page_header();
     echo "<center><br>";
     table_header("Site Tanımları","65%");
?>
<script>
function submit_form() {
   if(check_form(document.sites_frm)){
    document.sites_frm.submit();
   }
}
</script>
<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
  <tr>
      <td>
      <center>
            <table class="formbg">
            <form name="sites_frm" method="post" onsubmit="return check_form(this);" action="site_db.php?act=<? echo  $_GET['act'] ?>">
            <input type="hidden" name="id" value="<?=$id?>">
         <tr>
          <td colspan="2" class="td1_koyu">
            Bilgilerin anlamları için mouse'unuzu soldaki isimlerin üzerine getiriniz.
          </td>
        </tr>
        <tr class="form">
                  <td width="50%" class="td1_koyu">Site Adı</td>
                   <td width="50%" ><input type="text" class="input1" size="20" name="SITE_NAME" VALUE="<?echo $row->SITE_NAME?>" Maxlength="50"></td> 
               </tr>
         <tr class="form">
                  <td class="td1_koyu" title="Sitede bulunan veri toplama aygıtının IP adresi">IP Adresi</td>
                   <td ><input type="text" class="input1" size="20" name="SITE_IP" VALUE="<?echo $row->SITE_IP?>" Maxlength="20"></td> 
               </tr>
         <tr class="form">
                  <td class="td1_koyu" title="Sitenin bulunduğu ilin kodu">Şehir Kodu</td>
                   <td ><input type="text" class="input1" size="5" name="SITE_CODE" VALUE="<?echo $row->SITE_CODE?>" Maxlength="10"></td> 
               </tr>
         <tr class="form">
                  <td class="td1_koyu" title="Bu şehirden arama yapıldığında şehiriçi kabul edilen kod">Lokal Kodu</td>
                   <td ><input type="text" class="input1" size="5" name="SITE_LOCAL_CODE" VALUE="<?echo $row->SITE_LOCAL_CODE?>" Maxlength="10"></td> 
               </tr>
                <tr class="form">
                    <td class="td1_koyu" title="Sitede bulunan santralin gönderdiği log tipi">Site Log Tipi</td>
                    <td>
                        <select name="SWITCH_LOG_TYPE_ID" class="select1">
                        <?
                            $strSQL = "SELECT ID, NAME FROM SANTRAL ";
                            echo    $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $row->SWITCH_LOG_TYPE_ID);
                        ?>
                        </select>
                    </td>
                </tr>
         <tr class="form">
                  <td class="td1_koyu" title="Sitenin bulunduğu ilin kodu">Yönetici E-Posta</td>
                   <td ><input type="text" class="input1" size="20" name="ADMIN_EMAIL" VALUE="<?echo $row->ADMIN_EMAIL?>" Maxlength="40"></td> 
               </tr>
         <tr class="form">
                  <td class="td1_koyu" title="Santralden Kaç Dk kayıt gelmediğinde sistem uyarsın. 0 girilirse uyarmaz.">Santral Aktivite Peryodu</td>
                   <td ><input type="text" class="input1" size="3" name="ACT_PERIOD" VALUE="<?echo $row->ACT_PERIOD?>" Maxlength="3"></td> 
               </tr>
         <tr class="form">
                  <td class="td1_koyu" title="Aylık Dahili Rapor gönderme günü. 0 girilirse gönderilmez.">Aylık Dahili Rapor Günü</td>
                   <td ><input type="text" class="input1" size="5" name="MONTHLY_MAILING_DAY" VALUE="<?echo $row->MONTHLY_MAILING_DAY?>" Maxlength="10"></td> 
               </tr>
         <tr class="form">
                  <td class="td1_koyu" title="Telefon ücretlerinin çarpılacağı katsayı.Normalde çarpılmaz.">Ücret Çarpanı</td>
                   <td ><input type="text" class="input1" size="5" name="PRICE_FACTOR" VALUE="<?echo $row->PRICE_FACTOR?>" Maxlength="10"></td> 
               </tr>
         <tr class="form">
                  <td class="td1_koyu" title="Departman sorumlularına aylık Departman Raporu gönderme günü. 0 girilirse gönderilmez.">Aylık Departman Rapor Günü</td>
                   <td ><input type="text" class="input1" size="2" name="MONTHLY_MAILING_DEPT_DAY" VALUE="<?echo $row->MONTHLY_MAILING_DEPT_DAY?>" Maxlength="2"></td> 
               </tr>
         <tr class="form">
                  <td class="td1_koyu" title="Kabul edilebilir en uzun görüşme süresi.Daha uzun süren görüşmeler hatalı kabul edilir.">En uzun Görüşme Süresi(Dk)</td>
                   <td><input type="text" class="input1" size="3" name="MAX_ACCE_DURATION" VALUE="<?echo $row->MAX_ACCE_DURATION?>" Maxlength="3"></td> 
               </tr>

              <tr>
          <td></td>
          <td><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()">
		  <?if($saved == "1"){?>
  <span style="color:red" id="spanKaydedildi">Kaydedildi!</span>
  <script>setTimeout(function(){ document.getElementById('spanKaydedildi').style.display = 'none'; }, 3000);</script>
  <?}?>
		  </td>
        </tr>
          </form>
            </table>
      </td>
  </tr>
</table><br>
<table align="right" width="100%" border="0">
  <tr>
    <td colspan="4" width="70%"></td>
    <td align="right"><a href="site_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
    <td align="right"><a href="site.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
  </tr>
</table>
      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("SITE_NAME", "Site Adını Girmelisiniz.", TYP_NOT_NULL);
            form_fields[1] = Array ("SITE_IP", "Site'nin IP adresini Girmelisiniz.", TYP_NOT_NULL);
            form_fields[2] = Array ("SITE_CODE", "Sitenin Bulunduğu İl Kodunu Girmelisiniz.", TYP_NOT_NULL+TYP_DIGIT);
            form_fields[3] = Array ("SWITCH_LOG_TYPE_ID", "Site'nin Santral Log Tipini Girmelisiniz.", TYP_DROPDOWN);
            form_fields[4] = Array ("SITE_LOCAL_CODE", "Lokal Kodu alanına sadece rakam giriniz.", TYP_DIGIT);
            form_fields[5] = Array ("ADMIN_EMAIL", "Yönetici E-Posta alanını e-posta formatında giriniz.", TYP_EMAIL);
            form_fields[6] = Array ("ACT_PERIOD", "Santral aktivite peryodu alanına sadece rakam giriniz.", TYP_DIGIT);
            form_fields[7] = Array ("MONTHLY_MAILING_DAY", "Aylık Dahili Rapor Günü alanına sadece rakam giriniz.", TYP_DIGIT);
            form_fields[8] = Array ("MONTHLY_MAILING_DEPT_DAY", "Aylık Departman Rapor Günü alanına sadece rakam giriniz.", TYP_DIGIT);
            form_fields[9] = Array ("MAX_ACCE_DURATION", "En uzun Görüşme Süresi alanına sadece rakam giriniz.", TYP_DIGIT);
      </script>
<?
table_footer();
page_footer(0);?>