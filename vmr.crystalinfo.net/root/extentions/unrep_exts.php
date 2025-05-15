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
         $site_cr = " AND SITE_ID = ".$_SESSION['site_id'];
     }
     $sql_str = "SELECT * FROM UNREP_EXTS WHERE UNREP_EXT_ID = $id".$site_cr;
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
     page_header();
     echo "<center><br>";
     table_header("Raporlanmayacak Dahililer","50%");
?>
<script>function submit_form() {
    if (check_form(document.extention))    
      document.all("SITE_ID").disabled=false;
        document.unrep_ext.submit();
}
</script>
  <center>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <form name="unrep_ext" method="post" onsubmit="return check_form(this);" action="unrep_exts_db.php?act=<? echo  $_GET['act'] ?>">
        <input type="hidden" name="id" value="<?=$id?>">
        <tr class="form">
            <td class="td1_koyu">Site Adı</td>
            <td>
                <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>>
                <?
                    $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                    echo $cUtility->FillComboValuesWSQL($conn, $strSQL,true,  $SITE_ID);
                ?>
                </select>
            </td>
        </tr>
        <tr class="form">
           <td class="td1_koyu">Dahili</td>
           <td>
                <input type="text" name="UNREP_EXT_NO" class="input1" size="10" value="<?=$row->UNREP_EXT_NO?>">
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
      <td colspan="4" width="40%"></td>
            <td align="right"><a href="unrep_exts_db.php?act=del&id=<?=$id?>"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="unrep_exts_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="unrep_exts.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
    </tr>
  </table>

  </form>
<?table_footer();
page_footer(0);?>  

      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("UNREP_EXT_NO", "Dahili No'yu girmeniz gerekli.", TYP_NOT_NULL);
            form_fields[0] = Array ("SITE_ID", "Site Alanını seçmeniz gerekli.", TYP_DROPDOWN);
      </script>
