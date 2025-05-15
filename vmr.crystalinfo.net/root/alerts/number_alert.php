<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();

     //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
     }

     $site_crt="";
     if ($act=="upd" && $id!=="" && is_numeric($id)){
       if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
           $site_crt = " AND ALERT_DEFS.SITE_ID = ".$_SESSION['site_id'];
       }
       $sql_str = "SELECT ALERT_DEF_NAME FROM ALERT_DEFS WHERE ALERT_DEFS.ALERT_ID = 1
             AND ALERT_DEFS.ALERT_DEF_ID = $id ".$site_crt;
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
    if (mysqli_num_rows($result)>0){
          $row = mysqli_fetch_object($result);
       $ALERT_DEF_NAME = $row->ALERT_DEF_NAME;
    }else{
        print_error("Belirtilen Kayıt Bulunamadı");
          exit;
    }

     $sql_str = "SELECT * FROM ALERT_CRT WHERE ALERT_DEF_ID = $id ";
   if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
   while ($row = mysqli_fetch_object($result)){
    switch ($row->FIELD_NAME){  
      case 'CountryCode':
        $CountryCode = $row->VALUE;
        break;
      case 'LocalCode':
        $LocalCode = $row->VALUE;
        break;
      case 'PURE_NUMBER':
        $PURE_NUMBER = $row->VALUE;
        break;
      case 'SITE_ID':
        $SITE_ID = $row->VALUE;
        break;
      default:
     }
   }
   $sql_str = "SELECT * FROM ALERT_TO_EMAIL WHERE ALERT_DEF_ID = $id ";
     if (!($cdb->execute_sql($sql_str,$result1,$error_msg))){
        print_error($error_msg);
        exit;
     }
}
     if($act<>'upd')
       $SITE_ID = $_SESSION['site_id'];

  cc_page_meta();
     echo "<center>";
     page_header();
     echo "<center>";
     echo "<br>";
     table_header("Numara Uyarıları","75%");
?>
<script>function submit_form() {
    var phone;
   if(check_form(document.contact)){
        phone = document.number_alert.PURE_NUMBER.value;
        document.all('SITE_ID').disabled=false;
    if(phone.length < 3){
            alert("Telefon Numarası Uzunluğu En az 3 rakamlı olmalı..");
        }else{
            document.number_alert.submit();
        }
   }
}
</script>  
  <center>
  <table width="65%" cellpadding="0" cellspacing="0" border="0" align="center">
       <form name="number_alert" method="post" onsubmit="return check_form(this);" action="number_alert_db.php?act=<? echo  $_GET['act'] ?>">
       <input type="hidden" name="id" value="<?=$id?>">
	   <input type="hidden" name = "country_code" value="<?if ($CountryCode ==''){echo get_country_code($SITE_ID);}else{echo $CountryCode;}?>">
       <tr class="form">
            <td class="td1_koyu">Site Adı</td>
            <td>
                <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")){echo disabled;}?>>
                <?
                    $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                    echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $SITE_ID);
                ?>
                </select>
            </td>
        </tr>
      <tr class="form">
          <td width="25%" class="td1_koyu">Uyarı Adı</td>
          <td width="75%"><input size="40" class="input1" maxlenght="250" name="ALERT_DEF_NAME" VALUE="<?=$ALERT_DEF_NAME;?>">  </td> 
      </tr>
      <tr>
         <td width="25%" class="td1_koyu">Ülke Kodu</td>
          <td><input size="5" class="input1" name="CountryCode" VALUE="<?if ($CountryCode==""){echo "90";}else{echo $CountryCode ;}?>" onblur="set_country();">
               <a href="#"><span onclick="javascript:popup('/contacts/phone_list.php?type=ulke','phone',500,500)">Bak</span></a>
          </td> 
       </tr>
       <tr class="form">
            <td width="25%" class="td1_koyu" NOWRAP>Şehir Kodu</td>
            <td><input size="5" class="input1" name="LocalCode" VALUE="<?=$LocalCode ;?>">
              <a href="#"><span onclick="javascript:popup('/contacts/phone_list.php?type=sehir&country_code='+ document.all('country_code').value,'phone',500,500)">Bak</span></a>
            </td> 
      </tr>
       <tr class="form">
          <td width="25%" class="td1_koyu">Telefon</td>
           <td><input size="8" class="input1" maxlenght="20" name="PURE_NUMBER" VALUE="<?=$PURE_NUMBER ;?>">  </td> 
       </tr>
    <tr height="40">
      <td></td>
      <td colspan="3"><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()"></td>
	    <?if($saved == "1"){?>
  <span style="color:red" id="spanKaydedildi">Kaydedildi!</span>
  <script>setTimeout(function(){ document.getElementById('spanKaydedildi').style.display = 'none'; }, 3000);</script>
  <?}?>
       </tr>
  </table><br><br><br>
  <table width="65%" cellpadding="0" cellspacing="0" border="0" align="center">
    <tr>
      <td width="25%"></td>  
      <td width="75%">
        <?table_in_table_header("Gideceği E-Postalar","100%")?>
        <table>      
          <?$i;
          if ($act=='upd'){
            while ($row1 = mysqli_fetch_object($result1)){
            echo " <tr class=\"".bgc2($i)."\">";
                    echo "<td width=\"60%\">".$row1->MAIL."</td>";
                    echo "<td width=\"10%\"><a href=\"javascript:popup('add_email.php?act=upd&id=".$row1->AEM_ID."&ALERT_DEF_ID=$id','number_alert',500,200)\"><img border=\"0\" src=\"".IMAGE_ROOT."guncelle1.gif\"></a></td>";
                    echo "<td width=\"5%\"><a href=\"javascript:if(confirm('Dikkat  E-mail silinecek'))popup('add_email.php?act=del&id=".$row1->AEM_ID."','number_alert',500,200);\"><img border=\"0\" src=\"".IMAGE_ROOT."email_sil.gif\"></a></td>";
            echo "</tr>";
            $i++;
            }
          }?>
               </table>
        <?table_in_table_footer()?>    
           </form>
         </td>
    </tr>
    <tr>
      <td></td>
      <td align="center"><img border="0" src="<?=IMAGE_ROOT?>eposta_ekle.gif" style="cursor:hand;" onclick="javascript:open_email('<?=$id?>')"></td>
    </tr>
       <tr class="form">
           <td colspan="2"class="td1"><br>Yukarıda girilmiş olan numara arandığında belirtilen kişilere
      e-posta gönderilecektir. Ülke kodu her zaman dolu olmalıdır. Eğer belirtilen numara
      şehir içi ise lütfen şehir kodu alanını boş bırakınız.</td>
         </tr>
  </table><br>
  <table align="right" width="100%" border="0">
    <tr>
      <td colspan="4" width="70%"></td>
      <td align="right"><a href="number_alert_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="number_alert.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
         <?if ($id){?>
      <td align="right"><a href="javascript:if(confirm('Dikkat \n Kayıt Silinecek')) this.location.href='number_alert_db.php?act=del&id=<?=$id?>'"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a></td>
         <?}?>
    </tr>
  </table>
   <script language="javascript" src="/scripts/form_validate.js"></script>
     <script language="javascript">
      form_fields[0] = Array ("SITE_ID", "Site alanını seçmeniz gerekli.", TYP_DROPDOWN);
      form_fields[1] = Array ("ALERT_DEF_NAME", "Uyarı adını girmeniz gerekli.", TYP_NOT_NULL);
    function open_email(my_id){
      if (document.all('id').value==''){
        alert('Lütfen Önce Kaydedip Sonra Mail Ekleyiniz')
      }else{
        popup('add_email.php?ALERT_DEF_ID=' + my_id,'',500,200);
      }
    }
	function set_country(){
	  document.all('country_code').value = document.all('CountryCode').value;
	}
    </script>
<?table_footer();
page_footer(0);?>  
