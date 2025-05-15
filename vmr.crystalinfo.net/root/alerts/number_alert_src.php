<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $start = $cUtility->myMicrotime();

   //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
     }

  if ($SITE_ID<>'-1' && $SITE_ID<>""){
       $site_crt = " AND ALERT_DEFS.SITE_ID = ".$SITE_ID;  
  }
     if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
       $site_crt = " AND ALERT_DEFS.SITE_ID = ".$_SESSION['site_id'];
        $SITE_ID = $_SESSION['site_id'];
     }

    $sql_str = "SELECT ALERT_DEFS.ALERT_DEF_NAME,ALERT_DEFS.ALERT_DEF_ID FROM ALERT_DEFS 
        WHERE ALERT_DEFS.ALERT_ID = 1 ".$site_crt;
     if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }

     cc_page_meta();
     echo "<center>";
     page_header();
     echo "<br><br>";
?>
<script langoage="javascript">
  function submit_me(){
    document.site_src.action = 'number_alert_src.php?act=src&SITE_ID=' + document.all('SITE_ID').value;
    document.site_src.submit();
  }
</script>
<center>
<a href="number_alert.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a>
<?
?>
<form name="site_src" method="post" action="number_alert_src.php">
<table width="75%" border="0" align="center" cellspacing="0" cellpadding="0">  
  <tr class="form">
       <td class="td1_koyu" width="30%" align="right">Site Adı</td>
       <td class="td1_koyu" width="10%"></td>
      <td width="60%">
           <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")){echo disabled;}?> onchange="javascript:submit_me();">
          <?
            $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
            echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $SITE_ID);
          ?>
           </select>
       </td>
  </tr>
</table>
</form>
<?table_arama_header("75%");?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="header_beyaz">
        <td>ID</td>
        <td>Site Adı</td>
        <td>Uyarı Adı</td>
        <td>Ülke Kodu</td>
        <td>Şehir Kodu</td>
        <td>Telefon No</td>
        <td>Güncelle</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($result)){
      $i++;
       $sql_str = "SELECT * FROM ALERT_CRT WHERE ALERT_DEF_ID = $row->ALERT_DEF_ID";
     if (!($cdb->execute_sql($sql_str,$result1,$error_msg))){
          print_error($error_msg);
          exit;
       }
     while ($row1 = mysqli_fetch_object($result1)){
      switch ($row1->FIELD_NAME){  
        case 'SITE_ID':
          $SITE_ID = $row1->VALUE;
          break;
        case 'CountryCode':
          $CountryCode = $row1->VALUE;
          break;
        case 'LocalCode':
          $LocalCode = $row1->VALUE;
          break;
        case 'PURE_NUMBER':
          $PURE_NUMBER = $row1->VALUE;
          break;
        default:
       }
     }

     echo " <tr class=\"".bgc($i)."\">".CR;
      echo " <td height=\"20\">$row->ALERT_DEF_ID</td> ".CR;
       echo " <td>".substr(get_site_name($SITE_ID),0,30)."</td>".CR;
       echo " <td>".substr($row->ALERT_DEF_NAME,0,30)."</td>".CR;
        echo " <td>$CountryCode</td>".CR;
        echo " <td>$LocalCode</td>".CR;
        echo " <td>$PURE_NUMBER</td>".CR;
          echo " <td><a HREF=\"number_alert.php?act=upd&id=$row->ALERT_DEF_ID\">Güncelle</td>".CR;
         echo "</tr>".CR;
       list_line(18);
   }
?>
</table>
<?table_arama_footer(); ?>
<?page_footer(0);?>

