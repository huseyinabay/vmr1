<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();

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

    $sql_str = "SELECT ALERT_DEF_NAME, ALERT_DEF_ID, SITE_ID FROM ALERT_DEFS 
        WHERE ALERT_DEFS.ALERT_ID = 13 ".$site_crt;
     if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
   
    $start = $cUtility->myMicrotime();

     cc_page_meta();
     echo "<center>";
     page_header();
     echo "<br><br>";
?>
<center>
<a href="week_summary_alert.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a>
<?table_arama_header("75%");?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td>ID</td>
        <td>Site Adı</td>        
        <td>Uyarı Adı</td>
        <td>Güncelle</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($result)){
      $i++;
       echo " <tr class=\"".bgc($i)."\">".CR;
      echo " <td height=\"20\">$row->ALERT_DEF_ID</td> ".CR;
       echo " <td>".substr(get_site_name($row->SITE_ID),0,30)."</td>".CR;
       echo " <td>".substr($row->ALERT_DEF_NAME,0,30)."</td>".CR;
       echo " <td><a HREF=\"week_summary_alert.php?act=upd&id=$row->ALERT_DEF_ID\">Güncelle</td>".CR;
         echo "</tr>".CR;
       list_line(18);
   }
?>
</table>
<?table_arama_footer(); ?>
<?page_footer(0);?>

