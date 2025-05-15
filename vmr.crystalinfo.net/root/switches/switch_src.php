<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();


    //ini_set('display_errors', 'On');
    //error_reporting(E_ALL);
   

   
//Site Admin yoksa bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
   }
   
   if ((int)$p=="" || (int)$p < 1)
       (int)$p = 1;

   $start = $cUtility->myMicrotime();

     cc_page_meta();
     echo "<center>";
     page_header();
     echo "<br><br>";
     table_header_mavi("Santral Arama","60%");
?>
<center>
       <form name="santral_arama" method="post" action="switch_src.php?act=src">
         <input type="hidden" name="p" VALUE="<?echo (int)$p?>">
         <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
         <table cellpadding="0" cellspacing="0" border="0" width="65%">
            <tr> 
                    <td width="50%" class="font_beyaz">Santral Adı</td>
                    <td width="50%"><input type="text" class="input1" name="SWITCH_NAME" VALUE="<?echo $SWITCH_NAME?>" size="20" Maxlength="20"></td>
            </tr>
            <tr>
                 <td colspan=2 align=center><br>
                    <a href="javascript:submit_form('santral_arama');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
                </td>
            </tr>
            </tr>
        </table>
       </form>
    <table width="100%"> 
      <tr>
        <td width="100%" align="right">
        <a href="switch.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit1.gif" style="cursor:hand;"></a>
        </td>
      </tr>  
    </table>
<?
   table_footer_mavi();
   if ($act == "src") {
         $kriter = "";   

         $kriter .= $cdb->field_query($kriter, "NAME", "LIKE", "'%$SWITCH_NAME%'");
      
         $sql_str  = "SELECT ID, NAME, SANTRAL.ROWS
                      FROM SANTRAL";
         
         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;  
       
         if ($ORDERBY) {
               $sql_str .= " ORDER BY ". $ORDERBY ;      
         }

         $rs = $cdb->get_Records($sql_str, (int)$p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();

?>
<br><br>
<?
table_arama_header("60%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page((int)$p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td><a href="javascript:submit_form('santral_arama',1, 'ID')">ID</a></td>
        <td><a href="javascript:submit_form('santral_arama',1,'NAME')">Santral Adı</a></td>       
        <td><a href="javascript:submit_form('santral_arama',1, 'ROWS')">Satır Sayısı</a></td>
        <td>Güncelle</td>
        <td>Log Tipleri</td>
    </tr>
<? 
   $i;
   if(mysqli_num_rows($rs)>0){
       while($row = mysqli_fetch_object($rs)){
          $i++;
           echo " <tr class=\"".bgc($i)."\">".CR;
          echo " <td height=\"20\">$row->ID</td> ".CR;
             echo " <td>".substr($row->NAME,0,25)."</td>".CR;
           echo " <td>".$row->ROWS."</td>".CR;
           echo " <td><a HREF=\"switch.php?act=upd&id=$row->ID\">Güncelle</td>".CR;
           echo " <td><a HREF=\"log_types.php?act=new&sw_id=$row->ID\">Log Tipleri</td>".CR;
             echo "</tr>".CR;
           list_line(18);
       }
    }
?>
</table>
<?table_arama_footer();   }?>
<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, (int)$p, "santral_arama", $ORDERBY);
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
