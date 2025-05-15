<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   check_right("ADMIN");
   if ($p=="" || $p < 1)
     $p = 1;

   $start = $cUtility->myMicrotime();

    cc_page_meta();
     echo "<center>";
     page_header();
     echo "<br><br>";

   $act = "src";
   if ($act == "src") {
     $sql_str  = "SELECT * FROM TRate WHERE Rateid<10";

     $rs = $cdb->get_Records($sql_str, $p, $page_size,  $pageCount, $recCount);    
     $stop = $cUtility->myMicrotime();

?>
<br>
<?
table_arama_header("80%");
?>
<form name="sysprm_arama" method="post" action="tariff_sure_src.php?act=src">
         <input type="hidden" name="p" VALUE="<?echo $p?>">
         <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page($p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
</form>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td>ID</td>
        <td>Tarife Adı</td>
        <td>Birim Süre</td>
        <td>İşlemler</td>
    </tr>
<? 
	$i="";
   $i;
   while($row = mysqli_fetch_object($rs)){
      $i++;
      echo "<tr class=\"".bgc($i)."\" title=\"$row->RateName : $row->RateName\">".CR;
      echo " <td height=\"20\">$row->Rateid</td>".CR;
      echo " <td>$row->RateName</td>".CR;
      echo " <td>$row->BasicCntPerd</td>".CR;
      echo " <td><a HREF=\"#\"  onclick=\"javascript:popup('tariff_sure.php?act=upd&id=$row->Rateid','system',450,250)\">Güncelle</td>".CR;
      echo "</tr>";
          list_line(18);
   }
?>
</table>
<?table_arama_footer();   }?>

<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
		$ORDERBY="1";
        echo $cdb->get_paging($pageCount, $p, "sysprm_arama", $ORDERBY);
        ?></td>
    </tr>
</table>
<? page_footer(0);?>

<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="JavaScript" type="text/javascript">
<!--

    function submit_form(form_name, page, sortby){
          document.all("ORDERBY").value = sortby;
          if (!sortby)
                document.all("ORDERBY").value = '';

          document.all("p").value = page;
          document.all(form_name).submit();
    }
//-->
</script>