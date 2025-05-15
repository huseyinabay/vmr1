<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   session_cache_limiter('nocache');
   require_valid_login();
   $conn = $cdb->getConnection();

  //Admin veya Fihrist hakkı varsa ve Site Admin hakkı yoksa 
  //sadece kendi sitesine ait bilgiyi görebilmeli
     if ((right_get("ADMIN") || right_get("FIHRIST")) && !right_get("SITE_ADMIN")){
    $site_crt = " AND CONTACTS.SITE_ID = ".$_SESSION['site_id'];
     }
  //Hiçbir hakkı yoksa sadece kaydettiği contakları görebilmeli.
  if (!right_get("SITE_ADMIN") && !right_get("ADMIN") && !right_get("FIHRIST")){
         $site_crt = $site_crt." AND CONTACTS.USER_ID = ".$_SESSION['user_id'];
  }
  if ($act=="upd" && $id!="" && is_numeric($id)){
     $sql_str = "SELECT CONTACTS.* ,USERS.NAME UNAME, CONTACTS.USER_ID, USERS.SURNAME AS USURNAME
                     FROM CONTACTS 
                     LEFT JOIN USERS ON CONTACTS.USER_ID = USERS.USER_ID
                     WHERE CONTACT_ID = $id ".$site_crt;
   if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
  if (mysqli_num_rows($result)>0){
       $row = mysqli_fetch_object($result);
        $USER_ID = $row->USER_ID;
    $SITE_ID = $row->SITE_ID;
  }else{
      print_error("Belirtilen Kayıt Bulunamadı");
        exit;
  }
  }else{
    $act="new";
  $USER_ID = $_SESSION["user_id"];
  $SITE_ID = $_SESSION["site_id"];
  }

   cc_page_meta();
   echo "<center>";
   page_header();
   fillSecondCombo();
   echo "<center><br>";
   table_header("Fihrist","75%");if (!right_get("FIHRIST"))
?>
<script language="javascript">
function submit_form() {
   if(check_form(document.contact)){
        document.all('USER_ID').disabled = false;
        document.all('IS_GLOBAL').disabled = false;
        document.all('SITE_ID').disabled = false;
      document.contact.submit();
   }
}

function change_company(incoming_val){
     if(incoming_val==1){
        document.all('NAME1').style.display = '';
        document.all('SURNAME1').style.display = '';
        document.all('COMPANY1').style.display = '';
        document.all('COMPANY2').style.display = 'none';
     }else{
        document.all('COMPANY2').style.display = '';
        document.all('NAME1').style.display = 'none';
        document.all('SURNAME1').style.display = 'none';
        document.all('COMPANY1').style.display = 'none';

     }
}
</script>
<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
<form name="contact" action="contacts_db.php?act=<? echo $act ?>" method="post" onsubmit="return check_form(this);">
<INPUT TYPE="hidden" value="<?=$id?>" name=id> 
     <tr class="form">
         <td class="td1_koyu">Site Adı</td>
           <td>
              <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="FillSecondCombo('USER_ID', 'ADI', '08SITE_ID='+ this.value , '' , 'USER_ID' , this.value)">
               <?
                  $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                   echo $cUtility->FillComboValuesWSQL($conn, $strSQL,true,  $SITE_ID);
                ?>
               </select>
           </td>
       </tr>
        <tr> 
          <td class="td1_koyu" width="25%">Kaydeden</td>
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
           <td class="td1_koyu" width="25%">Şirket Kontağı</td>
          <td width="75%"><input type="checkbox" class="input1" value="1" name="IS_GLOBAL" <? if($row->IS_GLOBAL > '0') {echo ' checked'; }?> <?if (!right_get("FIHRIST")){echo "disabled";}?>></td>
       </tr>
      <tr> 
           <td class="td1_koyu" width="25%">Kayıt Tipi</td>
          <td width="75%">
         Şirket<input type="radio" class="input1" value="1" name="IS_COMPANY" <? if($row->IS_COMPANY > '0') {echo 'checked'; }?> onclick='change_company(1);'>
         Şahıs<input type="radio" class="input1" value="0" name="IS_COMPANY" <? if($row->IS_COMPANY == '0') {echo 'checked'; }?> onclick='change_company(2);'>
         </td>
       </tr>

       <tr> 
         <td class="td1_koyu" width="25%"><span id=NAME1  STYLE="display:none"> Kontak Kişi </span><span id=NAME2> Adı</span></td>
           <td width="75%"><input class="input1" type="text" value="<? echo $row->NAME; ?>" name="NAME" size="20" maxlength="20"></td>
       </tr>  
        <tr> 
            <td class="td1_koyu" width="25%"><span id=SURNAME1  STYLE="display:none"> Kontak Kişi</span> Soyadı</td>
            <td width="75%"><input class="input1" type="text" value="<? echo $row->SURNAME; ?>" name="SURNAME" size="20" maxlength="20"></td>
       </tr>  
       <tr> 
            <td class="td1_koyu" width="25%"><Span  id=COMPANY1  STYLE="display:">Firma </SPAN><span id=COMPANY2  STYLE="display:none"> Çalıştığı Yer </span></td>
            <td width="75%"><input class="input1" type="text" value="<? echo $row->COMPANY; ?>" name="COMPANY" size="30" maxlength="30"></td>
        </tr>  
        <tr> 
           <td class="td1_koyu" width="25%">Görevi</td>
            <td width="75%"><input class="input1" type="text" value="<? echo $row->POSITION; ?>" name="POSITION" size="30" maxlength="20"></td>
       </tr>  
        <tr> 
            <td class="td1_koyu" width="25%">Kişisel E-Mail</td>
           <td width="75%"><input class="input1" type="text" value="<? echo $row->PERSONAL_EMAIL; ?>" name="PERSONAL_EMAIL" size="30" maxlength="30"></td>
        </tr>  
       <tr> 
            <td class="td1_koyu" width="25%">Şirket E-Mail</td>
            <td width="75%"><input class="input1" type="text" value="<? echo $row->COMPANY_EMAIL; ?>" name="COMPANY_EMAIL" size="30" maxlength="30"></td>
       </tr> 
       <tr> 
            <td class="td1_koyu" width="25%">Web Sitesi</td>
            <td class="input1" width="75%"><input class="input1" type="text" value="<? echo $row->WEB_SITE; ?>" name="WEB_SITE" size="30" maxlength="30"></td>
      </tr>  
        <tr> 
            <td class="td1_koyu" width="25%">Adres</td>
            <td width="75%"><textarea class ="textarea1" cols="45" rows="3"  name="ADDRESS"><? echo $row->ADDRESS; ?></textarea></td>
        </tr>  
       <tr>
          <td></td>
      <td>
               <img border="0" style="cursor:hand;" src="<?=IMAGE_ROOT?>kaydet.gif" onclick="javascript:submit_form()">
			   
  <?if($saved == "1"){?>
  <span style="color:red" id="spanKaydedildi">Kaydedildi!</span>
  <script>setTimeout(function(){ document.getElementById('spanKaydedildi').style.display = 'none'; }, 3000);</script>
  <?}?>

            </td>
        </tr>
     <? if ($act=="upd" && $id!=""){ ?>
    <tr>
           <td colspan="2" class="td_koyu"><a href="#"><span onclick="javascript:popup('phone.php?act=new&CONTACT_ID=<?=$id?>&SITE_ID=<?=$SITE_ID?>','contacts',500,500)">Telefon Ekle</span></a></td>
        </tr>
      <tr>
           <td colspan="2">
              <table cellspacing="0" cellpddding="0" border="0">
                       <?$sql_str = "SELECT PHONES.PHONE_ID,PHONE_TYPES.TYPE_NAME,PHONES.COUNTRY_CODE,
                                      PHONES.CITY_CODE,PHONES.PHONE_NUMBER,PHONES.EXTENSION 
                                      FROM PHONES LEFT JOIN PHONE_TYPES
                                      ON PHONES.PHONE_TYPE_ID = PHONE_TYPES.PHONE_TYPE_ID
                                      WHERE CONTACT_ID = '$id'" ; 
                          if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                            print_error($error_msg);
                            exit;
                          }
                      ?>
                 <tr CLASS="bgc2">                                
                      <td class="td1_koyu" width="5%">&nbsp;</td>
                      <td class="td1_koyu" width="15%">Türü</td>
                      <td class="td1_koyu" width="15%">Ülke</td>
                      <td class="td1_koyu" width="15%">Şehir</td>
                      <td class="td1_koyu" width="15%">Tel</td>
                      <td class="td1_koyu" width="15%">Dahili</td>
                      <td colspan=2 class="td1_koyu" width="15%">İşlemler</td>
                 </tr>
                 <?while ($row = mysqli_fetch_object($result)){?>   
                 <tr>
                    <td width="5%" class="td1">>></td> </td>                 
                    <td width="15%" class="td1"><?=$row->TYPE_NAME; ?></td>
                    <td width="15%" class="td1"><?=$row->COUNTRY_CODE?></td>
                    <td width="15%" class="td1"><?=$row->CITY_CODE?></td>
                    <td width="15%" class="td1"><?=$row->PHONE_NUMBER?></td>
                    <td width="15%" class="td1"><?=$row->EXTENSION?></td>                              
                    <td width="15%"><a href="#" onclick="javascript:popup('phone.php?act=upd&CONTACT_ID=<?=$id?>&id=<?=$row->PHONE_ID ?>','contacts',500,500)">Güncelle</a></td>
                    <td width="15%"><a href="#" onclick="javascript:return del_record('<?=$row->PHONE_ID?>')">Sil</a></td>
                 </tr>
                 <?}?>
              </table>
            </td>                      
          </tr>
          <?}?>
     </form>
  </table><br>
  <table align="right" width="100%" border="0">
    <tr>
      <td colspan="4" width="70%"></td>
      <td align="right"><a href="contacts_db.php?act=del&id=<?=$id?>&SITE_ID=<?=$SITE_ID?>"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="contact_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="contacts.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
    </tr>
  </table>
   <script language="javascript" src="/scripts/form_validate.js"></script>
   <script language="javascript">
        form_fields[0] = Array ("SITE_ID", " Fihrist Kaydının Bağlı Olduğu Siteyi Seçiniz.",  TYP_DROPDOWN);
        form_fields[1] = Array ("USER_ID", " Kaydeden Kişiyi Seçiniz.",  TYP_DROPDOWN);
        form_fields[2] = Array ("NAME", "Adı alanını Giriniz.",  TYP_NOT_NULL);
      form_fields[3] = Array ("SURNAME", "Soyadı Alanını Giriniz.",  TYP_NOT_NULL );
        <?if($act=='upd'){?>  
            FillSecondCombo('USER_ID', 'ADI', '08SITE_ID='+'<?=$SITE_ID?>' , '<?=$USER_ID?>' , 'USER_ID' , '<?=$USER_ID?>');
      if (document.contact.IS_COMPANY[0].checked){
        change_company(1);
        }else{
        change_company(2);    
      }
        <?}else{?>
      FillSecondCombo('USER_ID', 'ADI', '08SITE_ID='+ document.all('SITE_ID').value , '<?=$USER_ID?>' ,'USER_ID' , document.all('USER_ID').value);
        <?}?>

        function del_record(REC){
         if (confirm("Bu kaydı silmek istediğinizden emin misiniz?")) {
            popup('phone_db.php?act=del&id='+REC,'contacts',500,500);
         }
         else 
         return false;
      }   
   </script> 
<? table_footer();
   page_footer(0);
?>
</body>
</html>

