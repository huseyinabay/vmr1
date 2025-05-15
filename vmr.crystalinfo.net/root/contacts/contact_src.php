<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	 
	 
	//ini_set('display_errors', 'On');
    //error_reporting(E_ALL);
	 
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();

     $conn = $cdb->getConnection();
     if ($p=="" || $p < 1)
       $p = 1;

  //Admin veya Fihrist hakkı varsa ve Site Admin hakkı yoksa 
  //sadece kendi sitesine ait bilgiyi görebilmeli
     if ((right_get("ADMIN") || right_get("FIHRIST")) && !right_get("SITE_ADMIN")){
    $SITE_ID = $_SESSION["site_id"];
    $site_crt = " AND CONTACTS.SITE_ID = ".$SITE_ID;
     }
  //Hiçbir hakkı yoksa sadece kaydettiği contakları görebilmeli.
  if (!right_get("SITE_ADMIN") && !right_get("ADMIN") && !right_get("FIHRIST")){
    $SITE_ID = $_SESSION["site_id"];
    $USER_ID = $_SESSION["user_id"];
         $site_crt = $site_crt." AND CONTACTS.USER_ID = ".$SITE_ID;
  }

     $start = $cUtility->myMicrotime();

     cc_page_meta();
  echo "<center>";
    fillSecondCombo();
     page_header();
     echo "<br><br>";
     table_header_mavi("Fihrist Arama","65%");
?>
     <form name="contact_search" method="post" action="contact_src.php?act=src">
       <input type="hidden" name="p" VALUE="<?echo (int)$p?>">
       <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
  <center>
      <table cellpadding="0" cellspacing="0" ALIGN="center" border="0" width="65%">
            <tr class="form">
                <td class="font_beyaz">Site Adı</td>
                <td>
                    <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")){echo "disabled";}?> onchange="FillSecondCombo('USER_ID' , 'ADI' , '08SITE_ID=' + this.value , '' , 'USER_ID' , '')">
                    <?
                    if (right_get("SITE_ADMIN")){
                        echo "<option value=\"-1\">-->Tamamı--<</option>";
          }
                    $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                    echo $cUtility->FillComboValuesWSQL($conn, $strSQL, false,  $SITE_ID);
                    ?>
                    </select>
                </td>
            </tr>
          <tr> 
            <td class="font_beyaz" width="25%">Kaydeden</td>
            <td width="25%">  
          <select name="USER_ID" style="width:150" class="select1"  <?if (!right_get("FIHRIST") && !right_get("ADMIN") && !right_get("SITE_ADMIN")){echo "disabled";}?>>
                            <?
                             $strSQL = "SELECT USER_ID, USERNAME FROM USERS ";
                             echo    $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $USER_ID);
                           ?>
                </select>
        </td>
          </tr>
          <tr> 
              <td class="font_beyaz" width="25%">Tel Tipi</td>
                 <td width="75%">
          <select name="IS_GLOBAL" class="select1" <?if (!right_get("FIHRIST") && !right_get("ADMIN") && !right_get("SITE_ADMIN")){echo "disabled";}?>>
                    <option value="-1" <?if ($IS_GLOBAL=='-1') {echo "selected";} ?>>Tamamı</option>
                    <option value="1"  <?if ($IS_GLOBAL=='1')  {echo "selected";} ?>>Global</option>
                    <option value="0"  <?if ((!right_get("FIHRIST") && !right_get("ADMIN") && !right_get("SITE_ADMIN")) or ($IS_GLOBAL=='0') ){echo "selected";}?>>Özel</option>            
                </select>
        </td>
            </tr>
            <tr> 
               <td width="50%" class="font_beyaz">Adı</td>
               <td width="50%"><input class="input1" type="text" name="NAME" VALUE="<?echo $NAME?>" Maxlength="30"></td>
            </tr>
            <tr> 
                 <td width="50%" class="font_beyaz">Soyadı</td>
                 <td width="50%"><input class="input1" type="text" name="SURNAME" VALUE="<?echo $SURNAME?>" Maxlength="15"></td>
            </tr>
            <tr> 
               <td width="50%" class="font_beyaz">Şehir Kodu</td>
                <td width="50%"><input class="input1" type="text" size="5" name="CITY_CODE" VALUE="<?echo $CITY_CODE?>" Maxlength="5"></td>
             </tr>
      <tr> 
               <td width="50%" class="font_beyaz">Tel</td>
                <td width="50%"><input class="input1" type="text" name="PHONE_NUMBER" VALUE="<?echo $PHONE_NUMBER?>" Maxlength="15"></td>
           </tr>
          <tr> 
                <td width="50%" class="font_beyaz">Firma</td>
              <td width="50%"><input class="input1" type="text" name="COMPANY" VALUE="<?echo $COMPANY?>" Maxlength="15"></td>
          </tr>
            <tr> 
              <td width="50%" class="font_beyaz">Kişisel E-Mail</td>
               <td width="50%"><input class="input1" type="text" name="PERSONAL_EMAIL" VALUE="<?echo $PERSONAL_EMAIL?>" Maxlength="15"></td>
            </tr>
             <tr> 
                <td width="50%" class="font_beyaz">Şirket E-Mail</td>
               <td width="50%"><input class="input1" type="text" name="COMPANY_EMAIL" VALUE="<?echo $COMPANY_EMAIL?>" Maxlength="15"></td>
            </tr>
            <tr>
               <td colspan=2 align=center><br>
                   <a href="javascript:submit_form('contact_search')">
          <img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
              </td>
            </tr>
      </table>
   </form>
  <table width="100%"> 
    <tr>
      <td width="100%" align="right">
      <a href="contacts.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit1.gif" style="cursor:hand;"></a>
      </td>
    </tr>  
  </table>
  <script language="javascript">
        FillSecondCombo('USER_ID', 'ADI',     '08SITE_ID=' +' <?=$SITE_ID?>' , '<?=$USER_ID?>' , 'USER_ID' , '<?=$USER_ID?>');   
  </script>
<?
   table_footer_mavi();
   if ($act == "src") {
      $kriter = "";   
     
     if ($IS_GLOBAL=='-1')  
         $IS_GLOBAL='';

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
                           CONTACTS.PERSONAL_EMAIL,CONTACTS.COMPANY_EMAIL,
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

         $rs = $cdb->get_Records($sql_str, (int)$p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();

?>
<br><br>
<?
table_arama_header("95%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page((int)$p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td width="5%"><a href="javascript:submit_form('contact_search',1, 'CONTACTS.CONTACT_ID')">ID</a></td>
        <td width="15%"><a href="javascript:submit_form('contact_search',1, 'CONTACTS.NAME')">Adı</a></td>
        <td width="15%"><a href="javascript:submit_form('contact_search',1, 'CONTACTS.SURNAME')">Soyadı</a></td>
        <td width="20%"><a href="javascript:submit_form('contact_search',1, 'CONTACTS.COMPANY')">Firma</a></td>
        <td width="20%"><a href="javascript:submit_form('contact_search',1, 'CONTACTS.COMPANY_EMAIL')">Şirket E-Mail</a></td>
        <td width="15%"><a href="javascript:submit_form('contact_search',1, 'PHONES.PHONE_NUMBER')">Telefon</a></td>
        <td width="15%"><a href="javascript:submit_form('contact_search',1, 'PHONES.DESCRIPTION')">Açıklama</a></td>
        <td>Güncelle</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($rs)){
       $i++;
       echo " <tr class=\"".bgc($i)."\">".CR;
       echo " <td height=\"20\">$row->CONTACT_ID</td> ".CR;
       echo " <td>$row->NAME</td>".CR;
       echo " <td>$row->SURNAME</td>".CR;
       echo " <td>$row->COMPANY</td>".CR;
       echo " <td>$row->COMPANY_EMAIL</td>".CR;
       echo " <td>$row->CITY_CODE"." "."$row->PHONE_NUMBER</td>".CR;                      
       echo " <td>$row->DESCRIPTION</td>".CR;                      
       echo " <td><a HREF=\"contacts.php?act=upd&id=$row->CONTACT_ID\">Güncelle</td>".CR;
       echo "</tr>".CR;
      list_line(18);           
   }
?>

</table>

<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, (int)$p, "contact_search", $ORDERBY);
        ?></td>
    </tr>
</table>



<?table_arama_footer(); }?>

<script >
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

<?page_footer(0);?>
