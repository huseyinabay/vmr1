<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();
//Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
   }

  if ($act=="upd" && $id!="" && is_numeric($id)){
  //Admin Hakkı versa ve Site Admin hakkı yoksa sadece kendi sitesine ait bilgiyi görebilmeli
     if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
         $site_crt = " AND SITE_ID = ".$SESSION['site_id'];
     }
     $sql_str = "SELECT * FROM QUOTAS WHERE QUOTA_ID = $id".$site_crt;
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
//Action upd ise SITE_ID db den gelen değilse Session'dan gelen olmalı.
   if($act=='upd')
     $SITE_ID = $row->SITE_ID;
   else
     $SITE_ID = $SESSION['site_id'];

   cc_page_meta();
   echo "<center>";
   page_header();
   echo "<center><br>";
   table_header("Kotalar","50%");
?>
<script language="javascript">
function submit_form() {
    if(check_form(quota)){
      document.all("SITE_ID").disabled=false;  
        document.quota.submit();
  }  
}
</script>
  <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
  <form name="quota" action="quota_db.php?act=<? echo $act ?>" method="post" onsubmit="return check_form(this);" >
  <INPUT TYPE="hidden" value="<?=$id?>" name=id> 
    <tr> 
       <td class="td1_koyu" colspan="2" width="100%">Kota Tanımlamaları Aylık Olarak Yapılmaktadır.</td>
    </tr> 
    <tr height="10">
      <td colspan="2"></td>
    </tr> 
     <tr class="form">
           <td class="td1_koyu">Site Adı</td>
            <td>
             <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>>
               <?
                   $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                   echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $SITE_ID);
               ?>
              </select>
          </td>
      </tr>
    <tr> 
      <td class="td1_koyu" width="25%">Kota Adı</td>
       <td width="75%"><input class="input1" type="text" value="<? echo $row->QUOTA_NAME; ?>" name="QUOTA_NAME" size="30" maxlength="30"></td>
    </tr>  
    <tr> 
       <td class="td1_koyu" width="25%">Ş.İ. Limit</td>
        <td width="75%">
        <input class="input1" type="text" value="<? echo calculate_time($row->INCITY_LIMIT,"hour"); ?>" name="INCITY_HOUR" size="2" maxlength="2">&nbsp&nbspSaat&nbsp&nbsp
        <input class="input1" type="text" value="<? echo calculate_time($row->INCITY_LIMIT,"min"); ?>" name="INCITY_MINUTE" size="2" maxlength="2">&nbsp&nbspDk&nbsp&nbsp
      </td>
    </tr>  
    <tr> 
      <td class="td1_koyu" width="25%">Ş.A. Limit</td>
      <td width="75%">
        <input class="input1" type="text" value="<? echo calculate_time($row->INTERCITY_LIMIT,"hour"); ?>" name="INTERCITY_HOUR" size="2" maxlength="2">&nbsp&nbspSaat&nbsp&nbsp
        <input class="input1" type="text" value="<? echo calculate_time($row->INTERCITY_LIMIT,"min"); ?>" name="INTERCITY_MINUTE" size="2" maxlength="2">&nbsp&nbspDk&nbsp&nbsp
      </td>
    </tr>  
    <tr> 
      <td class="td1_koyu" width="25%">GSM Limit</td>
       <td width="75%">
        <input class="input1" type="text" value="<? echo calculate_time($row->GSM_LIMIT,"hour"); ?>" name="GSM_HOUR" size="2" maxlength="2">&nbsp&nbspSaat&nbsp&nbsp
        <input class="input1" type="text" value="<? echo calculate_time($row->GSM_LIMIT,"min"); ?>" name="GSM_MINUTE" size="2" maxlength="2">&nbsp&nbspDk&nbsp&nbsp
      </td>
    </tr>  
    <tr> 
      <td class="td1_koyu" width="25%">U.A. Limit</td>
      <td width="75%">
        <input class="input1" type="text" value="<? echo calculate_time($row->INTERNATIONAL_LIMIT,"hour"); ?>" name="INTERNATIONAL_HOUR" size="2" maxlength="2">&nbsp&nbspSaat&nbsp&nbsp
        <input class="input1" type="text" value="<? echo calculate_time($row->INTERNATIONAL_LIMIT,"min"); ?>" name="INTERNATIONAL_MINUTE" size="2" maxlength="2">&nbsp&nbspDk&nbsp&nbsp
      </td>
    </tr>  
    <tr> 
      <td class="td1_koyu" width="25%">Ücret Limiti</td>
       <td width="75%"><input class="input1" type="text" value="<? echo $row->PRICE_LIMIT; ?>" name="PRICE_LIMIT" size="12" maxlength="12"></td>
    </tr>  
    <tr height="10">
        <td align="center" width="100%" colspan="3"></td>
    </tr>
    <tr>
      <td align="center" width="100%" colspan="3">
          <img border="0" STYLE="cursor:hand" src="<?=IMAGE_ROOT ?>kaydet.gif" onclick="javascript:submit_form()">
		    <?if($saved == "1"){?>
  <span style="color:red" id="spanKaydedildi">Kaydedildi!</span>
  <script>setTimeout(function(){ document.getElementById('spanKaydedildi').style.display = 'none'; }, 3000);</script>
  <?}?>
       </td>
    </tr>
    <?if ($act=="upd"){?>
    <tr>
       <td align="center" width="100%" colspan="3">
          <a href="quota_assign.php?q_id=<?=$id?>">Bu Kota İçin Atama Yap</a>
      </td>
    <tr>  
      <td align="center" width="100%" colspan="3">
           <a href="#" onclick="javascript:popup('quota_report.php?id=<?=$id?>','contacts',550,450)">Bu Kotanın Atama Raporu</a>
        </td>
    </tr>
    <?}?>
  </form>
  </table><br>
  <table align="right" width="100%" border="0">
    <tr>
      <td colspan="4" width="70%"></td>
      <td align="right"><a href="quota_db.php?act=del&id=<?=$id?>&SITE_ID=<?=$SITE_ID?>"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="quota_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="quota.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
    </tr>
  </table>            
<? table_footer();
   page_footer(0);
?>
  <script language="javascript" src="/scripts/form_validate.js"></script>
    <script language="javascript">
      form_fields[0] = Array ("SITE_ID", "Kotanın tanımlı olduğu siteyi seçmeniz gerekli.", TYP_DROPDOWN);
      form_fields[1] = Array ("QUOTA_NAME", "Kota alanını girmeniz gerekli.", TYP_NOT_NULL);
      form_fields[2] = Array ("INCITY_HOUR", "Şehiriçi Limit Saat Alanını Rakam Olarak Giriniz.", TYP_DIGIT);
      form_fields[3] = Array ("INCITY_MINUTE", "Şehiriçi Limit Dakika Alanını Rakam Olarak Giriniz.", TYP_DIGIT);
      form_fields[4] = Array ("INTERCITY_HOUR", "Şehirlerarası Limit Saat Alanını Rakam Olarak Giriniz.", TYP_DIGIT);
      form_fields[5] = Array ("INTERCITY_MINUTE", "Şehirlerarası Limit Dakika Alanını Rakam Olarak Giriniz.", TYP_DIGIT);        
      form_fields[6] = Array ("GSM_HOUR", "GSM Limit Saat Alanını Rakam Olarak Giriniz.", TYP_DIGIT);
      form_fields[7] = Array ("GSM_MINUTE", "GSM Limit Dakika Alanını Rakam Olarak Giriniz.", TYP_DIGIT);        
      form_fields[8] = Array ("INTERNATIONAL_HOUR", "Uluslararası Limit Saat Alanını Rakam Olarak Giriniz.", TYP_DIGIT);
      form_fields[9] = Array ("INTERNATIONAL_MINUTE", "Uluslararası Limit Dakika Alanını Rakam Olarak Giriniz.", TYP_DIGIT);        
      form_fields[10] = Array ("PRICE_LIMIT", "Ücret Limiti alanını rakam olarak giriniz.", TYP_DIGIT);        
    </script>
</body>
</html>
