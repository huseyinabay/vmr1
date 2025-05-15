<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();

//Site Admin bu tanımı değilse yapamamalı
   if (!right_get("SITE_ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
   }

   if ($act=="upd" && $id!=="" && is_numeric($id)){
       $sql_str = "SELECT * FROM SANTRAL WHERE ID = $id";
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
    fillSecondCombo();
     echo "<center><br>";
     table_header("Santral Tanımları","50%");
?>
<script>function submit_form() {
    if (check_form(document.santral))
        document.santral.submit();
  }
</script>
  <center>
  <table width="100%" cellpadding="0" cellspacing="0">
    <form name="santral" method="post" onsubmit="return check_form(this);" action="switch_db.php?act=<? echo  $_GET['act'] ?>">
    <input type="hidden" name="id" value="<?=$id?>">
       <tr class="form">
           <td class="td1_koyu">Santral Adı</td>
           <td>
                <input type="text" name="SWITCH_NAME" class="input1" size="25" value="<?=$row->NAME?>">
          </td>
    </tr>
       <tr class="form">
           <td class="td1_koyu">Satır Sayısı :</td>
           <td>
                <input type="text" name="ROWS" class="input1" size="25" value="<?=$row->ROWS?>">
          </td>
    </tr>
    <tr height="15">
      <td></td>
    </tr>
       <tr>
        <td></td>
      <td><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()"></td>
        </tr>
  </table><br>
  <table align="right" width="100%" border="0">
    <tr>
      <td colspan="4" width="70%"></td>
      <td align="right"><a href="switch_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="switch.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
    </tr>
  </table>

  </form>
<?table_footer();
page_footer(0);?>  

<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="javascript">
  form_fields[0] = Array ("SWITCH_NAME", "Santral Adını girmeniz gerekli.", TYP_NOT_NULL);
  form_fields[1] = Array ("ROWS",   "Santralin Kaç Satır Log Gönderdiğini.", TYP_DIGIT);
</script>
