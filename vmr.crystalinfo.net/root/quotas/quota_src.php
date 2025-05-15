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
//Site Admin Hakkı Yoksa sadece kendisine bağlı kayıtları görsün   
     if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
         $SITE_ID = $SESSION['site_id'];
     }
//Site Admin Hakkı var ve SITE_ID boşsa kendi sitesine gitsin.  
     if (right_get("SITE_ADMIN") && ($SITE_ID=="" || $SITE_ID=="-1")){
         $SITE_ID = $SESSION['site_id'];
     }
  
   $conn = $cdb->getConnection();
   if ($p=="" || $p < 1)
       $p = 1;

   $start = $cUtility->myMicrotime();

   cc_page_meta();
   echo "<center>";
   page_header();
   echo "<BR><BR>";
   table_header_mavi("Kota Arama","50%");
?>
     <form name="quota_search" method="post" action="quota_src.php?act=src" onsubmit="return check_form(this);">
  <input type="hidden" name="p" VALUE="<?echo (int)$p?>">
    <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
  <center>
       <table cellpadding="0" cellspacing="0" ALIGN="center" border="0">
            <tr class="form">
                <td class="font_beyaz">Site Adı</td>
                <td>
                    <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>>
                    <?
                        $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ";
                        echo $cUtility->FillComboValuesWSQL($conn, $strSQL, false, $SITE_ID);
                    ?>
                    </select>
                   </td>
            </tr>
          <tr> 
             <td class="font_beyaz" width="35%">Kota Adı</td>
            <td width="65%"><input class="input1" type="text" value="<? echo $QUOTA_NAME; ?>" name="QUOTA_NAME" size="30" maxlength="30"></td>
         </tr>  
          <tr> 
            <td class="font_beyaz" width="35%">Ş.İ. Limit</td>
             <td width="65%" class="font_beyaz">
          <select class="select1" name="compare_incity">
            <option value="<" <?if ($compare_incity =="<") echo "selected"?>> < </option>
            <option value="=" <?if ($compare_incity =="=") echo "selected"?>> = </option>
            <option value=">" <?if ($compare_incity ==">") echo "selected"?>> > </option>
          </select>&nbsp&nbsp&nbsp
          <input class="input1" type="text" value="<? echo $INCITY_HOUR; ?>" name="INCITY_HOUR" size="2" maxlength="2">Saat
          <input class="input1" type="text" value="<? echo $INCITY_MINUTE; ?>" name="INCITY_MINUTE" size="2" maxlength="2">Dk
        </td>
          </tr>  
        <tr> 
          <td class="font_beyaz" width="35%">Ş.A. Limit</td>
               <td width="65%" class="font_beyaz">
          <select class="select1" name="compare_intercity">
            <option value="<" <?if ($compare_intercity =="<") echo "selected"?>> < </option>
            <option value="=" <?if ($compare_intercity =="=") echo "selected"?>> = </option>
            <option value=">" <?if ($compare_intercity ==">") echo "selected"?>> > </option>
          </select>&nbsp&nbsp&nbsp
          <input class="input1" type="text" value="<? echo $INTERCITY_HOUR; ?>" name="INTERCITY_HOUR" size="2" maxlength="2">Saat
          <input class="input1" type="text" value="<? echo $INTERCITY_MINUTE; ?>" name="INTERCITY_MINUTE" size="2" maxlength="2">Dk
        </td>
          </tr>  
          <tr> 
              <td class="font_beyaz" width="35%">GSM Limit</td>
               <td width="65%" class="font_beyaz">
          <select class="select1" name="compare_gsm">
            <option value="<" <?if ($compare_gsm =="<") echo "selected"?>> < </option>
            <option value="=" <?if ($compare_gsm =="=") echo "selected"?>> = </option>
            <option value=">" <?if ($compare_gsm ==">") echo "selected"?>> > </option>
          </select>&nbsp&nbsp&nbsp
          <input class="input1" type="text" value="<? echo $GSM_HOUR; ?>" name="GSM_HOUR" size="2" maxlength="2">Saat
          <input class="input1" type="text" value="<? echo $GSM_MINUTE; ?>" name="GSM_MINUTE" size="2" maxlength="2">Dk
        </td>
          </tr>  
         <tr> 
             <td class="font_beyaz" width="35%">U.A. Limit</td>
             <td width="65%" class="font_beyaz">
          <select class="select1" name="compare_international">
            <option value="<" <?if ($compare_international =="<") echo "selected"?>> < </option>
            <option value="=" <?if ($compare_international =="=") echo "selected"?>> = </option>
            <option value=">" <?if ($compare_international ==">") echo "selected"?>> > </option>
          </select>&nbsp&nbsp&nbsp
          <input class="input1" type="text" value="<? echo $INTERNATIONAL_HOUR; ?>" name="INTERNATIONAL_HOUR" size="2" maxlength="2">Saat
          <input class="input1" type="text" value="<? echo $INTERNATIONAL_MINUTE; ?>" name="INTERNATIONAL_MINUTE" size="2" maxlength="2">Dk
        </td>
           </tr>          
          <tr class="form">
            <td width="30%" class="font_beyaz">Tutar Limiti</td>
              <td width="70%" ><input type="text" class="input1" size="13" name="PRICE_LIMIT" VALUE="<?echo $PRICE_LIMIT?>" Maxlength="15"></td> 
          </tr>
      <tr>
              <td colspan=2 align=center><br>
                  <a href="javascript:submit_form('quota_search',1,'')">
              <img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif" onclick="submit_form('quota_search',1,'')">
            </a>
               </td>
            </tr>
       </table>
   </form>
       <table width="100%"> 
      <tr>
        <td width="100%" align="right">
        <a href="quota.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit1.gif" style="cursor:hand;"></a>
        </td>
      </tr>  
    </table>

<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="JavaScript">
<!--
    function submit_form(form_name, page, sortby){
      if (check_form(document.quota_search)){
      form_fields[0] = Array ("SITE_ID", "Erişim Kodunun bağlı olduğu siteyi seçiniz.",TYP_DROPDOWN);
        form_fields[1] = Array ("PRICE_LIMIT", "Ücret Limitini rakam olarak girmeniz gerekli.",TYP_DIGIT);
            document.all("ORDERBY").value = sortby;
            if (!sortby)
                document.all("ORDERBY").value = '';
            document.all("p").value = page;
            document.all(form_name).submit();
      }
  }
//-->
</script>
<?
   table_footer_mavi();

   if ($act == "src") {
         $kriter = "";   
    
    if ($INCITY_HOUR <> "" or $INCITY_MINUTE <> "")           $INCITY_LIMIT      = $INCITY_HOUR*3600 + $INCITY_MINUTE*60; 
    if ($INTERCITY_HOUR <> "" or $INTERCITY_MINUTE <> "")        $INTERCITY_LIMIT   = $INTERCITY_HOUR*3600 + $INTERCITY_MINUTE*60; 
      if ($GSM_HOUR <> "" or $GSM_MINUTE <> "")            $GSM_LIMIT        = $GSM_HOUR*3600 + $GSM_MINUTE*60; 
    if ($INTERNATIONAL_HOUR <> "" or $INTERNATIONAL_MINUTE <> "")  $INTERNATIONAL_LIMIT = $INTERNATIONAL_HOUR*3600 + $INTERNATIONAL_MINUTE*60;

     $kriter .= $cdb->field_query($kriter, "SITE_ID"         ,  "=",    "'$SITE_ID'");
     $kriter .= $cdb->field_query($kriter, "QUOTA_NAME"       ,  "LIKE",  "'%$QUOTA_NAME%'");
         $kriter .= $cdb->field_query($kriter, "INCITY_LIMIT"       ,    "$compare_incity",  "$INCITY_LIMIT");
         $kriter .= $cdb->field_query($kriter, "INTERCITY_LIMIT"    ,    "$compare_intercity",  "$INTERCITY_LIMIT");
         $kriter .= $cdb->field_query($kriter, "GSM_LIMIT"         ,    "$compare_gsm",  "$GSM_LIMIT");
         $kriter .= $cdb->field_query($kriter, "INTERNATIONAL_LIMIT",    "$compare_international",  "$INTERNATIONAL_LIMIT");
      
         if($PRICE_LIMIT<>''){
       if ($kriter<>'')
         $kriter = $kriter." OR (PRICE_LIMIT >=".$PRICE_LIMIT.")"; 
       else 
         $kriter = "PRICE_LIMIT >=".$PRICE_LIMIT; 
     }
     
         $sql_str  = "SELECT * FROM QUOTAS ";
         
         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;  
       
         if ($ORDERBY) {
               $sql_str .= " ORDER BY ". $ORDERBY ;      
         }
//echo $sql_str;exit;
         $rs = $cdb->get_Records($sql_str, (int)$p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();

?>
<br><br>
<?
table_arama_header("90%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page((int)$p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td><a href="javascript:submit_form('quota_search',1, 'QUOTA_ID')">ID</a></td>
        <td><a href="javascript:submit_form('quota_search',1, 'QUOTA_NAME')">Kota Adı</a></td>
        <td><a href="javascript:submit_form('quota_search',1, 'INCITY_LIMIT')">Ş.İ. Limit</a></td>
        <td><a href="javascript:submit_form('quota_search',1, 'INTERCITY_LIMIT')">Ş.A. Limit</a></td>
        <td><a href="javascript:submit_form('quota_search',1, 'GSM_LIMIT')">GSM. Limit</a></td>
        <td><a href="javascript:submit_form('quota_search',1, 'INTERNATIONAL_LIMIT')">U.A. Limit</a></td>
        <td><a href="javascript:submit_form('quota_search',1, 'PRICE_LIMIT')">Tutar Limiti</a></td>
        <td>Güncelle</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($rs)){
         $i++;
     if ($row->INCITY_LIMIT <> ""){
         $INCITY_HOUR    =calculate_time($row->INCITY_LIMIT,"hour");
        $INCITY_MINUTE    =calculate_time($row->INCITY_LIMIT,"min");
    }
    if ($row->INTERCITY_LIMIT <> ""){
          $INTERCITY_HOUR    =calculate_time($row->INTERCITY_LIMIT,"hour");
          $INTERCITY_MINUTE    =calculate_time($row->INTERCITY_LIMIT,"min");
      }
    if ($row->GSM_LIMIT <> ""){
        $GSM_HOUR       =calculate_time($row->GSM_LIMIT,"hour"); 
        $GSM_MINUTE      =calculate_time($row->GSM_LIMIT,"min"); 
    }
    if ($row->INTERNATIONAL_LIMIT <> ""){
        $INTERNATIONAL_HOUR  =calculate_time( $row->INTERNATIONAL_LIMIT,"hour");
        $INTERNATIONAL_MINUTE  =calculate_time( $row->INTERNATIONAL_LIMIT,"min");
    }   
    echo " <tr class=\"".bgc($i)."\">".CR;
        echo "<td height=\"20\">$row->QUOTA_ID</td> ".CR;
        echo "<td>$row->QUOTA_NAME</td>".CR;
        echo "<td>$INCITY_HOUR : $INCITY_MINUTE </td>".CR;
       echo "<td>$INTERCITY_HOUR : $INTERCITY_MINUTE</td>".CR;
        echo "<td>$GSM_HOUR : $GSM_MINUTE</td>".CR;
        echo "<td>$INTERNATIONAL_HOUR : $INTERNATIONAL_MINUTE</td>".CR;
        echo "<td>".$row->$PRICE_LIMIT."</td>".CR;
       echo " <td><a href=\"quota.php?act=upd&id=$row->QUOTA_ID\">Güncelle</a></td>".CR;        
        echo "</tr>".CR;
        list_line(18);           
   }
?>
</table>
<?table_arama_footer(); }?>
<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, (int)$p, "quota_search", $ORDERBY);
        ?></td>
    </tr>
</table>
<?page_footer(0);?>
