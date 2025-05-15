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
    
if ($act=="del" && $id!=="" && is_numeric($id)){
?>
<script>
  window.location.href = 'add_email_db.php?act=del&id=<?=$id?>';
</script>  
<?}

   if ($act=="upd" && $id!=="" && is_numeric($id)){
  $sql_str = "SELECT MAIL FROM ALERT_TO_EMAIL WHERE AEM_ID = $id";
     if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
  $row = mysqli_fetch_object($result);
   }  
  cc_page_meta();
     echo "<center>";
     echo "<br>";

?>
<script>
    function submit_form() {
        if(check_form(document.add_email))
            document.add_email.submit();
    }   
</script>

  <center>
  <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
          <td>
        <form name="add_email" method="post" action="add_email_db.php?act=<? echo  $HTTP_GET_VARS['act'] ?>">
        <input name="ALERT_DEF_ID" type="hidden" value="<?=$ALERT_DEF_ID?>">
        <input name ="id" type="hidden" value="<?=$id?>">
                <tr class="form">
                        <td width="20%" class="td1_koyu">E Posta</td>
                        <td width="80%"><input size="40" class="input1" maxlenght="250" name="EMAIL" VALUE="<?=$row->MAIL?>">  </td> 
                  </tr>
          <tr height="15"></tr>
          <tr>
          <td></td>
          <td><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()"></td>
                  </tr>
              </table>
            </form>
          </td>
    </tr>
</table>
      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("EMAIL", "E Posta alanını girmeniz gerekli.", TYP_EMAIL + TYP_NOT_NULL);
      </script>
<?table_footer();?>  
