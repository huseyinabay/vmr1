<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";

   $cUtility = new Utility();
   $cdb = new db_layer();
   session_cache_limiter('nocache');
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
         $site_crt = " AND SITE_ID = ".$_SESSION['site_id'];
     }
     $sql_str = "SELECT * FROM ACCESS_CODES WHERE ACCESS_CODE_ID = $id".$site_crt;
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
     $SITE_ID = $_SESSION['site_id'];
   
    cc_page_meta();
     echo "<center>";
   $link["Ana Sayfa"]= "../main.php";
   $link["Admin Ana Sayfa"]= "../admin/main.php";
   $link["Erişim Kodları"]= "access_code_src.php";
   $link["Erişim Kodları Arama"]= "access_code.php";
   page_header($link);
     echo "<center><br>";
     table_header("Erişim Kodu","65%");
?>
<script>
function submit_form() {
   if(check_form(document.access_code_frm)){
    document.all("SITE_ID").disabled=false;
    document.access_code_frm.submit();
   }
}

function del_control(){
  var agree = confirm ("Bu Erişim Kodunu Silmek İstediğinize Emin misiniz.\nBu Kodu Silmek Ücretlendirme Sisteminde Yanlışlıklara Yol Açabilir.");
   if(agree){
    window.location.href  = 'access_code_db.php?act=del&id=<?=$id?>&SITE_ID=<?=$SITE_ID?>';
  }
}
</script>
<table width="75%" cellpadding="0" cellspacing="0" border="0" align="center">
  <tr>
      <td>
      <center>
            <table class="formbg">
            <form name="access_code_frm" method="post" onsubmit="return check_form(this);" action="access_code_db.php?act=<? echo  $_GET['act'] ?>">
            <input type="hidden" name="id" value="<?=$id?>">
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
                <tr class="form">
                  <td width="30%" class="td1_koyu">Erişim Kodu</td>
                   <td width="70%" ><input type="text" class="input1" size="10" name="ACCESS_CODE" VALUE="<?echo $row->ACCESS_CODE?>" Maxlength="15"></td> 
               </tr>
               <tr class="form">
                  <td width="30%" class="td1_koyu">Açıklama</td>
                   <td width="70%" ><TEXTAREA class="textarea1" NAME="ACCESS_CODE_DESC" COLS=35 ROWS=3><?=$row->ACCESS_CODE_DESC?></TEXTAREA></td>
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
    <td align="right"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" onclick="del_control();" style="cursor:hand;"></td>    
	<td align="right"><a href="access_code_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
    <td align="right"><a href="access_code.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
  </tr>
</table>
      <script language="javascript" src="/scripts/form_validate.js"></script>
	  <script language="javascript">
       form_fields[0] = Array ("SITE_ID", "Erişim Kodunun Bağlı Olduğu Siteyi Seçiniz.",TYP_DROPDOWN);
       form_fields[1] = Array ("ACCESS_CODE", "Erişim Kodunu Rakam Olarak Giriniz.", TYP_NOT_NULL+TYP_DIGIT);
     </script>
<?
table_footer();
page_footer(0);?>