<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();

    check_right("SITE_ADMIN");
   
     if ($p=="" || $p < 1)
       $p = 1;

     $start = $cUtility->myMicrotime();

     cc_page_meta();
     echo "<center>";
     page_header();
     echo "<br><br>";
     table_header_mavi("Site Arama","50%");
?>
<center>
       <form name="site_src" method="post" action="site_src.php?act=src">
         <input type="hidden" name="p" VALUE="<?echo (int)$p?>">
         <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
         <table cellpadding="0" cellspacing="0" border="0" width="60%">
              <tr> 
                    <td width="50%" class="font_beyaz">Site Adı</td>
                    <td width="50%"><input type="text" class="input1" name="SITE_NAME" VALUE="<?echo $SITE_NAME?>" Maxlength="15"></td>
              </tr>
              <tr> 
                    <td width="50%" class="font_beyaz">Site Kodu</td>
                    <td width="50%"><input type="text" class="input1" name="SITE_CODE" VALUE="<?echo $SITE_CODE?>" Maxlength="30"></td>
              </tr>
              <tr> 
                    <td width="50%" class="font_beyaz">Site IP Adresi</td>
                    <td width="50%"><input type="text" class="input1" name="SITE_IP" VALUE="<?echo $SITE_IP?>" Maxlength="30"></td>
              </tr>
              <tr>
                  <td colspan=2 align=center><br>
                    <a href="javascript:submit_form('site_src');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
                  </td>
              </tr>
              </tr>
        </table>
  <table width="100%"> 
    <tr>
      <td width="100%" align="right">
      <a href="site.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit1.gif" style="cursor:hand;"></a>
      </td>
    </tr>  
  </table>        
       </form>
<?
   table_footer_mavi();
   if ($act == "src") {
         $kriter = "";   

         $kriter .= $cdb->field_query($kriter, "SITE_NAME",   "LIKE", "'%$SITE_NAME%'");
         $kriter .= $cdb->field_query($kriter, "SITE_CODE",     "=", "'$SITE_CODE'");
         $kriter .= $cdb->field_query($kriter, "SITE_IP",       "LIKE", "'%$SITE_IP%'");
      
         $sql_str  = "SELECT * FROM SITES";
         
         if ($kriter != ""){
               $sql_str .= " WHERE ". $kriter;  
         }
         if ($ORDERBY){
               $sql_str .= " ORDER BY ". $ORDERBY ;      
         }

         $rs = $cdb->get_Records($sql_str, (int)$p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();

?>
<br><br>
<?
table_arama_header("75%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page((int)$p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td>ID</td>
        <td>Site Adı</td>
        <td>Site Kodu</td>
        <td>Site IP'si</td>
        <td>Güncelle</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($rs)){
      $i++;
        echo " <tr class=\"".bgc($i)."\">".CR;
        echo " <td height=\"20\">$row->SITE_ID</td> ".CR;
         echo " <td>".SUBSTR($row->SITE_NAME,0,25)."</td>".CR;
       echo " <td>$row->SITE_CODE</td>".CR;
       echo " <td>$row->SITE_IP</td>".CR;        
         echo " <td><a HREF=\"site.php?act=upd&id=$row->SITE_ID\">Güncelle</td>".CR;
      echo " </tr>".CR;
    list_line(18);
   }
?>
</table>
<?table_arama_footer();   }?>
<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, (int)$p, "site_src", $ORDERBY);
        ?></td>
    </tr>
</table>
<?page_footer(0);?>
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
</html>

