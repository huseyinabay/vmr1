<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();

   if (right_get("SITE_ADMIN")){
     if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
   }else{
     $SITE_ID = $_SESSION['site_id'];
   }

   if ($country_code=='')
     $country_code = get_country_code($SITE_ID);

       if ((right_get("ADMIN") || right_get("FIHRIST")) && !right_get("SITE_ADMIN")){
      $SITE_ID = $_SESSION["site_id"];
       }
    //Hiçbir hakkı yoksa sadece kaydettiği contakları görebilmeli.
    if (!right_get("SITE_ADMIN") && !right_get("ADMIN") && !right_get("FIHRIST")){
      $IS_GLOBAL ="0";
      $SITE_ID = $_SESSION["site_id"];
      $USER_ID = $_SESSION["user_id"];
    }

        if ($SITE_ID<>'-1')
       $kriter .= $cdb->field_query($kriter, "CONTACTS.SITE_ID"       ,  "=",     "'$SITE_ID'");
      if ($USER_ID<>'-1')
       $kriter .= $cdb->field_query($kriter, "CONTACTS.USER_ID"      ,  "=",    "'$USER_ID'");      
         $kriter .= $cdb->field_query($kriter, "CONTACTS.IS_GLOBAL"     ,  "=",     "$IS_GLOBAL");
        $kriter .= $cdb->field_query($kriter, "CONTACTS.NAME"          ,  "LIKE",  "'%$NAME%'");
       $kriter .= $cdb->field_query($kriter, "CONTACTS.SURNAME"       ,  "LIKE",  "'%$SURNAME%'");
        $kriter .= $cdb->field_query($kriter, "CONTACTS.COMPANY"       ,  "LIKE",  "'%$COMPANY%'");
        $kriter .= $cdb->field_query($kriter, "CONTACTS.PERSONAL_EMAIL",  "LIKE",  "'%$PERSONAL_EMAIL%'");
       $kriter .= $cdb->field_query($kriter, "CONTACTS.COMPANY_EMAIL" ,  "LIKE",  "'%$COMPANY_EMAIL%'");
       $kriter .= $cdb->field_query($kriter, "PHONES.CITY_CODE"       ,  "LIKE",  "'%$CITY_CODE%'");
        $kriter .= $cdb->field_query($kriter, "PHONES.PHONE_NUMBER"    ,  "LIKE",  "'%$PHONE_NUMBER%'");
         
         $sql_str  = "SELECT CONTACTS.NAME,CONTACTS.SURNAME,CONTACTS.COMPANY,
                           CONTACTS.PERSONAL_EMAIL,CONTACTS.COMPANY_EMAIL, PHONES.COUNTRY_CODE,
                           PHONES.CITY_CODE,PHONES.PHONE_NUMBER,CONTACTS.CONTACT_ID, PHONES.DESCRIPTION 
                      FROM CONTACTS
                        LEFT JOIN PHONES 
                           ON CONTACTS.CONTACT_ID=PHONES.CONTACT_ID
                        LEFT JOIN SITES ON CONTACTS.SITE_ID = SITES.SITE_ID
                      ";

         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;  

         if ($ORDERBY) {
               $sql_str .= " ORDER BY ". $ORDERBY ;      
         }

         $rs = $cdb->get_Records($sql_str, $p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();
?>
<html>
<body>
<?
   cc_page_meta();
   echo "<center>";
?>
<br>
     <form name="contact_search" method="post" action="contact_phones.php?act=src">
       <input type="hidden" name="p" VALUE="<?echo $p?>">
       <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
  <center>
      <table cellpadding="0" cellspacing="0" ALIGN="center" border="0" width="95%">
            <tr class="form">
                <td class="td1_koyu" align=center>Site Adı:&nbsp;
                    <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")){echo disabled;}?> onchange="FillSecondCombo('USER_ID' , 'ADI' , '08SITE_ID=' + this.value , '' , 'USER_ID' , '')">
                    <?
                    if (right_get("SITE_ADMIN")){
                        echo "<option value=\"-1\">-->Tamamı--<</option>";
          }
                    $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                    echo $cUtility->FillComboValuesWSQL($conn, $strSQL, false,  $SITE_ID);
                    ?>
                    </select><br><br>
                </td>
            </tr>
            <tr> 
               <td class="td1_koyu">Adı:&nbsp;<input class="input1" type="text" name="NAME" VALUE="<?echo $NAME?>" Maxlength="30" size=15>
               &nbsp;&nbsp;Soyadı:&nbsp;<input class="input1" type="text" name="SURNAME" VALUE="<?echo $SURNAME?>" Maxlength="30" size=15>
               &nbsp;&nbsp;Şehir Kodu:&nbsp;<input class="input1" type="text" size="5" name="CITY_CODE" VALUE="<?echo $CITY_CODE?>" Maxlength="5">
               &nbsp;&nbsp;Firma:&nbsp;<input class="input1" type="text" name="COMPANY" VALUE="<?echo $COMPANY?>" Maxlength="15" size=15></td>
            <tr>
               <td align=center><br>
                   <a href="javascript:submit_form('contact_search')">
          <img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
              </td>
            </tr>
      </table>
   </form>
<?
table_arama_header("95%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page($p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td><input type="checkbox" name="contcts" value="1" onclick="javascript:check_all();"></td>
        <td width="5%"><a href="javascript:submit_form('contact_search',1, 'CONTACTS.CONTACT_ID')">ID</a></td>
        <td width="15%"><a href="javascript:submit_form('contact_search',1, 'CONTACTS.NAME')">Adı</a></td>
        <td width="15%"><a href="javascript:submit_form('contact_search',1, 'CONTACTS.SURNAME')">Soyadı</a></td>
        <td width="20%"><a href="javascript:submit_form('contact_search',1, 'CONTACTS.COMPANY')">Firma</a></td>
        <td width="20%"><a href="javascript:submit_form('contact_search',1, 'CONTACTS.COMPANY_EMAIL')">Şirket E-Mail</a></td>
        <td width="15%"><a href="javascript:submit_form('contact_search',1, 'PHONES.PHONE_NUMBER')">Telefon</a></td>
        <td width="15%"><a href="javascript:submit_form('contact_search',1, 'PHONES.DESCRIPTION')">Açıklama</a></td>
    </tr>
<? 
   $i=0;
   while($row = mysqli_fetch_object($rs)){
       $i++;
       echo " <tr class=\"".bgc($i)."\" id=\"ctr_id".$i."\">".CR;?>
          <td><input type="checkbox" name="contcts_<?=$i?>" value="<?=$i?>">
          <input type="hidden" name="CountryCode_<?=$i?>" value="<?=$row->COUNTRY_CODE?>">
          <input type="hidden" name="LocalCode_<?=$i?>" value="<?=$row->CITY_CODE?>">
          <input type="hidden" name="PURE_NUMBER_<?=$i?>" value="<?=$row->PHONE_NUMBER?>">
          </td>
<?     echo " <td height=\"20\">$row->CONTACT_ID</td> ".CR;
       echo " <td>$row->NAME</td>".CR;
       echo " <td>$row->SURNAME</td>".CR;
       echo " <td>$row->COMPANY</td>".CR;
       echo " <td>$row->COMPANY_EMAIL</td>".CR;
       echo " <td>$row->CITY_CODE"." "."$row->PHONE_NUMBER</td>".CR;                      
       echo " <td>$row->DESCRIPTION</td>".CR;                      
       echo "</tr>".CR;
      list_line(18);           
   }
   $maxRows = $i;
?>

</table>

<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, $p, "contact_search", $ORDERBY);
        ?></td>
    </tr>
    <tr>
        <td align="center">
        <input type="button" name="send_me" value="Gönder" onclick="javascript:send_selected_vals(<?=$maxRows?>);">&nbsp;
        <input type="button" name="close_me" value="Kapat" onclick="javascript:self.close();">
        </td>
    </tr>
</table>



<?table_arama_footer(); ?>

<script >
<!--
   function check_all(){
      var newVal=false;
      var Upbnd=<?=$maxRows?>;
      var i, bgcol;
      if(document.all('contcts').checked){
        newVal = true;
      }else{
        newVal = false;
      }
      for(i=1;i<=Upbnd;i++){
        document.all('contcts_'+i).checked = newVal;
      }
   }
   
   function submit_form(form_name, page, sortby){
          document.all("ORDERBY").value = sortby;
          if (!sortby)
                document.all("ORDERBY").value = '';
          document.all("p").value = page;
          document.all(form_name).submit();
    }

   function send_value(CountryCode, LocalCode, PURE_NUMBER){

    var myList = window.opener.document.all('PHONE_NUMS[]');
    var ListText = CountryCode+' '+LocalCode+' '+PURE_NUMBER
    var ValueText = CountryCode+';'+LocalCode+';'+PURE_NUMBER
    var TempVal='', cnt;

    for(cnt=0;cnt<=myList.options.length-1;cnt++){
      TempVal = myList.options[cnt].value;
      if(TempVal==ValueText){
        return false;
      }
    }
    box = window.opener.document.getElementById("phn_list");
    newOpt = window.opener.document.createElement("option");
    newOpt.text = ListText;
    newOpt.value = ValueText;
    box.options[box.options.length] = newOpt;
//    myList.options[myList.options.length] = new Option(ListText, ValueText, false, false);

   } 

   function send_selected_vals(rowcnt){
     var i;
     var CountryCode, LocalCode, PURE_NUMBER;
     for(i=1;i<=rowcnt;i++){
        if(document.all('contcts_'+i).checked){
          CountryCode = document.all('CountryCode_'+i).value;
          LocalCode = document.all('LocalCode_'+i).value;
          PURE_NUMBER = document.all('PURE_NUMBER_'+i).value;
          send_value(CountryCode, LocalCode, PURE_NUMBER);
        }
      }
   }
    

//-->
</script>

</body>
</html>   