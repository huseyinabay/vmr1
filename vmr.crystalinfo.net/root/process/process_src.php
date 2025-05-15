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
    $sql_str = "SELECT * FROM PROCESS ORDER BY PROCESS_DATE DESC";
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
      print_error($error_msg);
      exit;
    }
    cc_page_meta();
    echo "<center>";
    page_header();
    echo "<br><br>";
?>
<a href="process.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a>
<?
?>
<?table_arama_header("75%");?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="header_beyaz">
        <td>ID</td>
        <td>Tarih</td>
        <td>Konu</td>
        <td>Detay</td>
        <td>İncele</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($result)){
     echo " <tr class=\"".bgc($i)."\">".CR;
     echo " <td height=\"20\">$row->PROCESS_ID</td> ".CR;
     echo " <td height=\"20\">".db2normal($row->PROCESS_DATE,'/')."</td> ".CR;
     echo " <td>".substr($row->SUBJECT,0,30)."</td>".CR;
     echo " <td>".substr($row->DETAIL,0,30)."</td>".CR;
     echo " <td><a HREF=\"process.php?act=upd&id=$row->PROCESS_ID\">İncele</td>".CR;
     echo "</tr>".CR;
     list_line(18);
   }
?>
</table>
<?table_arama_footer(); ?>
<?page_footer(0);?>

