<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();
?>  
<? 
  cc_page_meta();
     echo "<center>";
     page_header();
  echo "<br>";
     echo "<center>";
     table_header("Haber Girişi","75%");
?>
<script>
function submit_form() {
    if(check_form(document.news_entry)){
        document.news_entry.submit();
    }
}
</script>
  <br>
     <table border="0" cellspacing="1" cellpadding="1" align="center" width="100%">
     <form name="news_entry" action="news_entry_save.php" method="post">
        <tr> 
          <td class="td1_koyu" width="25%">Kullanıcı</td>
            <td class="td1"><? echo $_SESSION["adi"]." ".$_SESSION["soyadi"];?></td>
        </tr>
            <td class="td1_koyu">Önemi</td>
            <td>
                     <select class="select1" size="1" name="frm_level">
                     <option value="5">+5) Son Derece Olumlu</option>
                     <option value="4">+4) ...</option>
                     <option value="3">+3) ...</option>
                     <option value="2">+2) Biraz Olumlu</option>
                     <option value="1">+1) ...</option>
                     <option value="0"> 0 ) Nötr</option>
                     <option value="-1">-1) ...</option>
                     <option value="-2">-2) Biraz Olumsuz</option>
                     <option value="-3">-3) ...</option>
                     <option value="-4">-4) ...</option>
                     <option value="-5">-5) Son Derece Olumsuz</option>
                     
                     </select>
            </td>
        </tr>
        <tr> 
            <td class="td1_koyu">Başlık</td>
            <td><input class="input1" type="text" name="frm_baslik" size="60" maxlength="100"> </td>
        </tr>
        <tr> 
            <td class="td1_koyu">Detay</td>
            <td><textarea class="textarea1" rows="11" name="frm_detay" cols="60"></textarea> </td>
        </tr>
        <tr>
            <td align="center" width="100%" colspan="3">
             <p align="center"><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()"></td>
        </tr>
        </form>
      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("frm_baslik", "Başlık alanını girmeniz gerekli.", TYP_NOT_NULL);
            form_fields[1] = Array ("frm_detay", "Detay alanını girmeniz gerekli.", TYP_NOT_NULL);
      </script>
  </table>
<?
  table_footer();    
  page_footer(0);
?>
