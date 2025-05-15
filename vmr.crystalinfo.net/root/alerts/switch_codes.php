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
       $sql_str = "SELECT ALERT_DEF_NAME FROM ALERT_DEFS WHERE ALERT_DEFS.ALERT_ID = 14
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
      case 'SWITCH_CODE_NAME':
        $SWITCH_CODE_NAME = $row->VALUE;
        break;
      case 'DESCRIPTION':
        $DESCRIPTION = $row->VALUE;
        break;
      case 'SWITCH_CODE':
        $SWITCH_CODE = $row->VALUE;
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
   page_header();
   echo "<center>";
   echo "<br>";
   table_header("Santral Uyarıları","70%");
?>
<script>
function submit_form() {
  document.all('SITE_ID').disabled=false;
    document.swithces.submit();
}
</script>
<center>
 <form name="swithces" action="switch_codes_db.php?act=<?=$act?>" method="post">
    <input type="hidden" name="id" value="<?=$id?>">
  <table width="65%" cellpadding="0" cellspacing="0" border="0">
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
    <tr>
        <td class="td1_koyu" width="30%">Adı</td>
        <td width="70%">
            <input type="text" name="ALERT_DEF_NAME" class="input1" value="<?=$ALERT_DEF_NAME?>">
        </td>
    </tr>
    <tr>
        <td class="td1_koyu" width="30%">Kod </td>
        <td width="70%">
            <input type="text" name="SWITCH_CODE" class="input1" value="<?=$SWITCH_CODE?>">
            
        </td>
    </tr>
    <tr>
        <td colspan=2> 
        <STRONG>Kod :</STRONG> Santralde hata ve benzeri durumların ifade eden, santralin gönderdiği bilgilerdir.<BR>
        <STRONG>Örnek :</STRONG> ERR 4331<BR>
        Kod bölümüne ERR yazdığınızda, ERR ile başlayan tüm datalar belirtilen maile bildirilecektir.
        </td>
    </tr>
    <tr>
        <td class="td1_koyu" width="30%"> Tanımı </td>
        <td width="70%">
         <textarea cols=40 rows=7 class=textarea1 name="DESCRIPTION"><?=$DESCRIPTION?></textarea>
        </td>
    </tr>
    <tr>
    <td colspan="2" align=center>
        <img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()">
		  <?if($saved == "1"){?>
  <span style="color:red" id="spanKaydedildi">Kaydedildi!</span>
  <script>setTimeout(function(){ document.getElementById('spanKaydedildi').style.display = 'none'; }, 3000);</script>
  <?}?>
    </td>
    </tr>
   </table>
  <table width="65%" cellpadding="0" cellspacing="0" border="0" align="center">
    <tr>
      <td width="75%">
        <?table_in_table_header("Gideceği E-Postalar","100%")?>
        <table>      
          <?$i;
          if ($act=='upd'){
            while ($row1 = mysqli_fetch_object($result1)){
            echo " <tr class=\"".bgc2($i)."\">";
                    echo "<td width=\"60%\">".$row1->MAIL."</td>";
                    echo "<td width=\"10%\"><a href=\"javascript:popup('add_email.php?act=upd&id=".$row1->AEM_ID."&ALERT_DEF_ID=$id','swithces',500,200)\"><img border=\"0\" src=\"".IMAGE_ROOT."guncelle1.gif\"></a></td>";
                    echo "<td width=\"5%\"><a href=\"javascript:if(confirm('Dikkat  E-mail silinecek'))popup('add_email.php?act=del&id=".$row1->AEM_ID."','swithces',500,200);\"><img border=\"0\" src=\"".IMAGE_ROOT."email_sil.gif\"></a></td>";
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
      <?if ($id){?>
      <td align="center"><img border="0" src="<?=IMAGE_ROOT?>eposta_ekle.gif" style="cursor:hand;" onclick="javascript:open_email('<?=$id?>')"></td>
         <?}?>
    </tr>
      <tr class="form">
          <td colspan="2"class="td1"><br>Yukarıda belirtilmiş olan kodlar santralden gönderildiğinde belirtilen kişilere
      e-posta gönderilecektir.</td>
        </tr>
  </table><br>
  <table align="right" width="100%" border="0">
    <tr>
      <td colspan="4" width="70%"></td>
      <td align="right"><a href="switch_codes_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
         <td align="right"><a href="switch_codes.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
      <?if ($id){?>
         <td align="right"><a href="javascript:if(confirm('Dikkat \n Kayıt Silinecek')) this.location.href='switch_codes_db.php?act=del&id=<?=$id?>'"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a></td>
         <?}?>

    </tr>
  </table>


   <script language="javascript" src="/scripts/form_validate.js"></script>
     <script language="javascript">
      form_fields[0] = Array ("SITE_ID", "Site alanını seçmeniz gerekli.", TYP_DROPDOWN);
      form_fields[1] = Array ("SWITCH_CODE", "Santralden gelecek datayı girmeniz gerekli.", TYP_NOT_NULL);
      form_fields[2] = Array ("SWITCH_CODE_NAME", "Uyarı adını girmeniz gerekli.", TYP_NOT_NULL);
    function open_email(my_id){
      if (document.all('id').value==''){
        alert('Lütfen Önce Kaydedip Sonra Mail Ekleyiniz')
      }else{
        popup('add_email.php?ALERT_DEF_ID=' + my_id,'',500,200);
      }
    }
    </script>
</form>
</center>
<?
  table_footer();
   page_footer(0);
?>