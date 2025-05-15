<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   session_cache_limiter('nocache');
   $conn = $cdb->getConnection();

   //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (right_get("SITE_ADMIN") || right_get("ADMIN")){
     print_error("Bu sayfaya erişim hakkınız yok!");
     exit;
   }

   if ($act=="upd" && is_numeric($id)){
	 $sql_str = "SELECT * FROM PROCESS WHERE PROCESS_ID = $id";
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
   table_header("Yapılan İşlem","75%");

?>
<script>
function submit_form() {
    if (check_form(document.process))
        document.process.submit();
}
</script>
<script LANGUAGE="javascript" src="/scripts/popup.js"></script>
  <center>
  <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
          <td>
              <form name="process" method="post" onsubmit="return check_form(this);" action="process_db.php?act=<? echo  $_GET['act'] ?>">
              <input type="hidden" name="id" value="<?=$id?>">
              <table class="formbg">
                <tr>
                  <td class="td1_koyu">İşlem Tarihi</td>
                  <td class="td1_koyu">
				    <input type="text" class="input1" name="PROCESS_DATE" VALUE="<?echo db2normal($row->PROCESS_DATE,'/')?>" size="10" readonly>
 					<a href="#"><img align="absmiddle" src="<?=IMAGE_ROOT?>takvim_icon.gif" onclick="javascript:show_calendar(document.all('PROCESS_DATE').name,null,null,null,window.event.screenX,window.event.screenY,0);" border="0"></a>
	              </td>
	            </tr>
                <tr>
                    <td class="td1_koyu">Konu</td>
                      <td><input type="text" class="input1" name="SUBJECT" VALUE="<?echo $row->SUBJECT?>" size="50" Maxlength="100">  </td> 
                    </tr>
                    <tr class="form">
                      <td class="td1_koyu">Detay</td>
                      <td><TEXTAREA NAME="DETAIL" class="textarea1" COLS=50 ROWS=8><?=$row->DETAIL?></TEXTAREA></td>
                    <tr>
                      <td></td>
                      <td><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()"></td>
                    </tr>
              </table>
            </form>
          </td>
    </tr>
  </table><br>
  <table align="right" width="100%" border="0">
    <tr>
      <td colspan="4" width="70%"></td>
      <td align="right"><a href="process_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="process.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
    </tr>
  </table>
      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("SUBJECT", "Konu alanını girmeniz gerekli.", TYP_NOT_NULL);
            form_fields[1] = Array ("DETAIL", "Detay alanını seçmeniz gerekli.", TYP_NOT_NULL);
            form_fields[2] = Array ("PROCESS_DATE", "Tarih alanını girmeniz gerekli.", TYP_NOT_NULL);            
      </script>
<?table_footer();
page_footer(0);?>  
