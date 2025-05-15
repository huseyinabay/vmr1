<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   session_cache_limiter('nocache');
   require_valid_login();
   $conn = $cdb->getConnection();

    if (($act=='new' || $act=='') && $SITE_ID<>''){
        $sql_str = "SELECT SITE_CODE FROM SITES WHERE SITE_ID = '$SITE_ID'";
        if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
            print_error($error_msg);
            exit;
        }
        if (mysqli_num_rows($result)>0){
            $row = mysqli_fetch_object($result);
            $my_city_code = $row->SITE_CODE;
       }
   }
   if ($act=="upd" && $id!=="" && is_numeric($id)){
        $sql_str = "SELECT * FROM PHONES WHERE PHONE_ID = $id " ;
        if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
              print_error($error_msg);
              exit;
        }
        $row = mysqli_fetch_object($result);
  }

   cc_page_meta();   
   echo "<center>";
   table_header("Telefon Ekleme","85%");
   echo "</center>";
?>
<script language="javascript" src="/scripts/form_validate.js"></script>
<script>
  function submit_form() {
    if (check_form(document.all("phones")))
         document.all("phones").submit();
    }
</script>
<center>
<table width="60%" cellpadding="0" cellspacing="0">
      <tr>
        <td>
            <form name="phones" method="post" onsubmit="return check_form(this);" action="phone_db.php?act=<?=$act?>">
            <input type="hidden" name="id" value="<?=$id?>">
            <input type="hidden" name="SITE_ID" value="<?=$SITE_ID?>">
            <input type="hidden" name="CONTACT_ID" value="<?=$CONTACT_ID?>">
            <input type="hidden" name="country_code" value="<?if ($CountryCode ==''){echo get_country_code($SITE_ID);}else{echo $CountryCode;}?>">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                  <tr class="form">
                        <td width="40%" class="td1_koyu">Tipi</td>
                        <td width="60%" class="select1">
                              <select name="PHONE_TYPE_ID">
                                 <?
                                  $strSQL = "SELECT PHONE_TYPE_ID, TYPE_NAME FROM PHONE_TYPES ";
                                  echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $row->PHONE_TYPE_ID);
                                ?>
                              </select>
                        </td>
                  <tr>
                  <tr>
                        <td nowrap width="40%" class="td1_koyu">Ülke Kodu</td>
                        <td width="3%"><input size="5" class="input1" name="CountryCode" VALUE="<?if ($row->COUNTRY_CODE==""){echo "90";}else{echo $row->COUNTRY_CODE ;}?>" onblur="set_country();">
                           <a href="#"><span onclick="javascript:popup('phone_list.php?type=ulke','phone',500,500)">Bak</span></a>
                        </td> 
                  </tr>
                  <tr class="form">
                        <td width="40%" class="td1_koyu" NOWRAP>Opt.Kodu</td>
                        <td width="60%"><input size="5" class="input1" name="LocalCode" VALUE="<?if ($act=="new" AND $row->CITY_CODE==''){echo $my_city_code;}else{echo $row->CITY_CODE ;}?>">
                           <a href="#"><span onclick="javascript:popup('phone_list.php?type=sehir&country_code='+ document.all('country_code').value,'phone',500,500)">Bak</span></a>
                        </td> 
                  </tr>
                  <tr class="form">
                        <td width="40%" class="td1_koyu">Telefon</td>
                        <td width="60%"><input size="8" class="input1" maxlenght="20" onchange="javascript:phone_lenght(this.value)" name="PHONE_NUMBER" VALUE="<?=$row->PHONE_NUMBER ;?>"></td> 
                  </tr>
                  <tr class="form">
                        <td width="40%" class="td1_koyu">Dahili</td>
                        <td width="60%"><input size="5" class="input1" name="EXTENSION" VALUE="<?=$row->EXTENSION ;?>" Maxlength="7">  </td> 
                  </tr>
                  <tr class="form">
                        <td width="40%" class="td1_koyu">Açıklama</td>
                        <td width="60%">
                              <TEXTAREA NAME="DESCRIPTION" class="textarea1" COLS=25 ROWS=3><?=$row->DESCRIPTION?></TEXTAREA>
                        </td>
                  <tr>
            <td></td>
          <td><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()"></td>
                  </tr>
            </table>
        </form>
      </td>
</tr>
</table>
<b>Not:</b> Türkiye dışında bir Telefon kaydediyorsanız ülke kodu dışındaki
numaraları "Telefon" alanına kaydediniz.
<?table_footer();
?>

<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="javascript">
      form_fields[0] = Array ("PHONE_NUMBER", "Telefon alanını rakam olarak girmeniz gerekli.", TYP_NOT_NULL + TYP_DIGIT);
      form_fields[1] = Array ("PHONE_TYPE_ID", "Tipi alanını girmeniz gerekli.", TYP_DROPDOWN);
      form_fields[2] = Array ("CountryCode", "Ülke Kodu alanını rakam olarak girmeniz gerekli.", TYP_NOT_NULL + TYP_DIGIT);
      form_fields[3] = Array ("LocalCode", "Opt Kodu alanını rakam olarak girmeniz gerekli.", TYP_DIGIT);
    	
    function set_country(){
	  document.all('country_code').value = document.all('CountryCode').value;
	}
         
    
</script>


