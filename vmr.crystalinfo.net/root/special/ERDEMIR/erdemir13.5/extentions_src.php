﻿<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();

   if ($p=="" || $p < 1)
       $p = 1;

//Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
   }
//Site Admin Hakkı Yoksa sadece kendisine bağlı kayıtları görsün   
     if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
         $SITE_ID = $_SESSION['site_id'];
     }

    $start = $cUtility->myMicrotime();
     cc_page_meta();
     echo "<center>";
     page_header();
    fillSecondCombo();
     echo "<br><br>";
     table_header_mavi("Dahili Arama","60%");
?>
<script>
function submit_form() {
   if(check_form(document.extention_arama)){
    document.all("SITE_ID").disabled=false;
  document.extention_arama.submit();
   }
}
</script>
<center>
       <form name="extention_arama" method="post" onsubmit="return check_form(this);" action="extentions_src.php?act=src">
         <input type="hidden" name="p" VALUE="<?echo $p?>">
         <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
         <table cellpadding="0" cellspacing="0" border="0" width="60%">
            <tr class="form">
                <td class="font_beyaz">Site Adı</td>
                <td>
                    <select name="SITE_ID" class="select1" style="width:250" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ this.value , '' , 'DEPT_ID' , this.value)">
                    <?
                        $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ";
                        echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true, $SITE_ID);
                    ?>
                    </select>
                   </td>
            </tr>
            <tr> 
                <td width="50%" class="font_beyaz">Dahili No</td>
                <td width="50%"><input type="text" class="input1" name="EXT_NO" VALUE="<?echo $EXT_NO?>" size="10" Maxlength="10"></td>
            </tr>
			
			<tr> 
                <td width="50%" class="font_beyaz">Hesap No</td>
                <td width="50%"><input type="text" class="input1" name="ACCOUNT_NO" VALUE="<?echo $ACCOUNT_NO?>" size="10" Maxlength="10"></td>
            </tr>
			
            <tr>
          <td width="50%" class="font_beyaz">Departman</td>
		  
		            <td>
                    <select name="DEPT_ID" class="select1" style="width:250" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ this.value , '' , 'DEPT_ID' , this.value);return false;">
                    <?
                        $strSQL = "SELECT DEPT_ID, DEPT_NAME FROM DEPTS ";
                        echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true, $DEPT_ID);
                    ?>
                    </select>
                   </td>
		  
		   <tr> 
                <td width="50%" class="font_beyaz">İsim</td>
                 <td width="50%"><input type="text" class="input1" name="DESCRIPTION" VALUE="<?echo $DESCRIPTION?>" Maxlength="30"></td>
             </tr>

           <tr> 
                <td width="50%" class="font_beyaz">PER NO</td>
                 <td width="50%"><input type="text" class="input1" name="PER_NO" VALUE="<?echo $PER_NO?>" Maxlength="30"></td>
             </tr>


            <tr> 
                <td width="50%" class="font_beyaz">TN</td>
                 <td width="50%"><input type="text" class="input1" name="TN" VALUE="<?echo $TN?>" Maxlength="30"></td>
             </tr>


         <tr> 
                <td width="50%" class="font_beyaz">YER</td>
                 <td width="50%"><input type="text" class="input1" name="YER" VALUE="<?echo $YER?>" Maxlength="30"></td>
	 
             </tr>
			 
			 
            <tr>
                 <td colspan=2 align=center><br>
                    <a href="javascript:submit_form('extention_arama');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
                </td>
            </tr>
            </tr>
        </table>
       </form>
  <table width="100%"> 
    <tr>
      <td width="100%" align="right">
      <a href="extentions.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit1.gif" style="cursor:hand;"></a>
      </td>
    </tr>  
  </table>
<?
   table_footer_mavi();
   if ($act == "src") {
         $kriter = "";   

         if ($SITE_ID<>'-1'){
             $kriter .= $cdb->field_query($kriter, "SITES.SITE_ID",       "=",    "'$SITE_ID'");
         }
         $kriter .= $cdb->field_query($kriter, "EXTENTIONS.EXT_NO", "LIKE", "'$EXT_NO%'");
         $kriter .= $cdb->field_query($kriter, "EXTENTIONS.ACCOUNT_NO", "LIKE", "'%".$ACCOUNT_NO."%'");
         $kriter .= $cdb->field_query($kriter, "EXTENTIONS.DEPT_ID", "=", "$DEPT_ID");
         $kriter .= $cdb->field_query($kriter, "EXTENTIONS.DESCRIPTION", " LIKE ", "'%$DESCRIPTION%'");
		 $kriter .= $cdb->field_query($kriter, "EXTENTIONS.PER_NO", " LIKE ", "'%$PER_NO%'");
         $kriter .= $cdb->field_query($kriter, "EXTENTIONS.TN", " LIKE ", "'%$TN%'");
         $kriter .= $cdb->field_query($kriter, "EXTENTIONS.YER", "LIKE", "'$YER%'");
      
         $sql_str  = "SELECT EXTENTIONS.EXT_ID,EXTENTIONS.ACCOUNT_NO,
				 EXTENTIONS.YER,EXTENTIONS.TN,EXTENTIONS.PER_NO,                            
				 EXTENTIONS.EXT_NO,EXTENTIONS.DEPT_ID,
                             EXTENTIONS.DESCRIPTION,
                             DEPTS.DEPT_NAME,SITES.SITE_NAME
                      FROM EXTENTIONS
                      INNER JOIN SITES ON EXTENTIONS.SITE_ID = SITES.SITE_ID                      
                      LEFT JOIN DEPTS  ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                      ";
         
         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;  
       
         if ($ORDERBY) {
               $sql_str .= " ORDER BY ". $ORDERBY ;      
         }

         $rs = $cdb->get_Records($sql_str, $p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();

?>
<br><br>
<?
table_arama_header("75%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page($p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td><a href="javascript:submit_form('extention_arama',1, 'EXTENTIONS.EXT_NO')">Dahili No</a></td>
        <td><a href="javascript:submit_form('extention_arama',1, 'EXTENTIONS.ACCOUNT_NO')">Hesap No</a></td>
        <td><a href="javascript:submit_form('extention_arama',1, 'EXTENTIONS.DESCRIPTION')">İsim</a></td>       
        <td><a href="javascript:submit_form('extention_arama',1, 'EXTENTIONS.YER')">YER</a></td>
        <td><a href="javascript:submit_form('extention_arama',1, 'EXTENTIONS.TN')">TN</a></td>
        <td><a href="javascript:submit_form('extention_arama',1, 'EXTENTIONS.PER_NO')">PER NO</a></td>
        <td>Güncelle</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($rs)){
      $i++;
       echo " <tr class=\"".bgc($i)."\">".CR;
       echo " <td>$row->EXT_NO</td>".CR;   
       echo " <td>$row->ACCOUNT_NO</td>".CR;  
       echo " <td>".substr($row->DESCRIPTION,0,25)."</td>".CR;
       echo " <td height=\"20\">$row->YER</td> ".CR;
       echo " <td>".substr($row->TN,0,25)."</td>".CR;       
       //echo " <td>".substr($row->DEPT_NAME,0,25)."</td>".CR;
       echo " <td>".substr($row->PER_NO,0,25)."</td>".CR;  
       echo " <td><a HREF=\"extentions.php?act=upd&id=$row->EXT_ID\">Güncelle</td>".CR;
       echo "</tr>".CR;
         echo "</tr>".CR;
       list_line(38);
   }
?>
</table>
<?table_arama_footer();   }?>
<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, $p, "extention_arama", $ORDERBY);
        ?></td>
    </tr>
</table>
<?page_footer(0);?>

<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="JavaScript" type="text/javascript">
<!--
    FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ '<?=$SITE_ID?>' , '<?=$DEPT_ID?>' , 'DEPT_ID' , '<?= $DEPT_ID?>')

    function submit_form(form_name, page, sortby){
          document.all("ORDERBY").value = sortby;
          if (!sortby)
                document.all("ORDERBY").value = '';

          document.all("p").value = page;
          document.all(form_name).submit();
    }
//-->
</script>
