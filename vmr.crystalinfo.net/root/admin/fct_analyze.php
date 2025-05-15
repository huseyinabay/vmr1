<?php
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";

    
    $cUtility = new Utility();
    $cdb = new db_layer();
    $capp = new  application();
    require_valid_login();

  //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Burayı Görme Hakkınız Yok");
    exit;
   }
   $conn = $cdb->getConnection();
   if (!right_get("SITE_ADMIN"))
       $SITE_ID = $_SESSION['site_id'];
   if (right_get("SITE_ADMIN") && ($SITE_ID=="" || $SITE_ID=="-1"))
       $SITE_ID = $_SESSION['site_id'];

    $sql_str1="SELECT SITE_NAME, MAX_ACCE_DURATION FROM SITES WHERE SITE_ID = ".$SITE_ID; 
    if (!($cdb->execute_sql($sql_str1,$result1,$error_msg))){
        print_error($error_msg);
        exit;
    }
    if (mysqli_num_rows($result1)>0){
    $row1 = mysqli_fetch_object($result1);
        $company = $row1->SITE_NAME;
        $max_acc_dur =  ($row1->MAX_ACCE_DURATION)*60;
    }else{
    print_error("Site paramatreleri bulunamadı.");
    exit;
  }
    add_time_crt();
    ECHO $sql_str = "SELECT * FROM CDR_MAIN_DATA 
                WHERE CDR_MAIN_DATA.SITE_ID = $SITE_ID AND ERR_CODE = 0 AND CALL_TYPE = 1 
                AND DURATION < $max_acc_dur".$kriter;
     cc_page_meta(0);
   page_header();
   echo "<br>";
   table_header("FCT Analiz Raporu", "500");
?>
<table border="0" width="600" height="400" ALIGN="left" >
  <form name="site" action="fct_analyze.php" method="post">
  <tr class="form">
        <td class="td1_koyu" width="40%" align="right">Site Adı</td>
        <td>
            <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="javascript:reloadme();">
                <?
                 if (right_get("SITE_ADMIN"))
            $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                 else
            $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES WHERE SITE_ID='$SITE_ID'";
                    echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $SITE_ID);
                ?>
            </select>
        </td>
    </tr>
    <TR><td>
      <table>
        <tr id="tarih_bas"> 
                    <td width="50%" colspan="1" class="td1_koyu">Baş. Tarihi:</td>
          <td width="50%" colspan="6">
               <input type="text" size=17 name="t0" VALUE="<?echo $t0?>" readonly><a href="javascript://"><img align="absmiddle" src="<?=IMAGE_ROOT?>takvim_icon.gif" onclick="javascript:show_calendar(document.all('t0').name,null,null,null,event.screenX,window.screenY,1);" border="0"></a>
          </td>
        </tr>
        <tr id="tarih_bit">
          <td width="50%" colspan="1" class="td1_koyu">Bit. Tarihi:</td>
          <td width="50%" colspan="6">
            <input type="text" size=17 name="t1" VALUE="<?echo $t1?>" readonly><a href="javascript://"><img align="absmiddle" src="<?=IMAGE_ROOT?>takvim_icon.gif" onclick="javascript:show_calendar(document.all('t1').name,null,null,null,event.screenX,event.screenY,1);" border="0"></a>
          </td>
        </tr>  
      </table>  
    </td></TR>
  </form>
  <tr>
    </tr>
</table>    
<script language=javascript>
function reloadme(){
  document.site.action = "fct_analyze.php?SITE_ID=" + document.all("SITE_ID").value;
  document.site.submit();
}
</script>
<?
   table_footer();
    page_footer("");
?>