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
       $sql_str = "SELECT ALERT_DEF_NAME FROM ALERT_DEFS WHERE ALERT_DEFS.ALERT_ID = 2
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
      case 'DURATION':
        $DURATION = $row->VALUE;
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
     table_header("Süre Uyarıları","80%");
?>
<script>function submit_form() {
    if(check_form(document.duration_alert)){
        document.all('SITE_ID').disabled=false;  
        document.duration_alert.submit();
    }
}
</script>
  <center>
  <table width="65%" cellpadding="0" cellspacing="0" align="center">
      <form name="duration_alert" method="post" onsubmit="return check_form(this);" action="duration_alert_db.php?act=<? echo  $_GET['act'] ?>">
        <input type="hidden" name="id" value="<?=$id?>">
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
            <td colspan="3" width="80%"><input size="40" class="input1" maxlenght="250" name="ALERT_DEF_NAME" VALUE="<?=$ALERT_DEF_NAME;?>">  </td> 
        </tr>
     <tr class="form">
          <td width="20%" class="td1_koyu">Süre</td>
           <td colspan="3" width="70%"><input size="5" class="input1" maxlenght="5" name="DURATION" VALUE="<?=$DURATION/60;?>">  </td> 
       </tr>
    <tr height="40">
      <td></td>
      <td colspan="3"><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()">
	    <?if($saved == "1"){?>
  <span style="color:red" id="spanKaydedildi">Kaydedildi!</span>
  <script>setTimeout(function(){ document.getElementById('spanKaydedildi').style.display = 'none'; }, 3000);</script>
  <?}?>
	  </td>
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
                    echo "<td width=\"10%\"><a href=\"javascript:popup('add_email.php?act=upd&id=".$row1->AEM_ID."&ALERT_DEF_ID=$id','duration_alert',500,200)\"><img border=\"0\" src=\"".IMAGE_ROOT."guncelle1.gif\"></a></td>";
                    echo "<td width=\"5%\"><a href=\"javascript:if(confirm('Dikkat  E-mail silinecek'))popup('add_email.php?act=del&id=".$row1->AEM_ID."','duration_alert',500,200)\"><img border=\"0\" src=\"".IMAGE_ROOT."email_sil.gif\"></a></td>";
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
          <td colspan="2"class="td1"><br>Yukarıda girilmiş olan süre(dk) aşıldığında belirtilen kişilere
      e-posta gönderilecektir. Süre için dakika baz alınmıştır.</td>
        </tr>
  </table><br>
  <table align="right" width="100%" border="0">
    <tr>
      <td colspan="4" width="70%"></td>
      <td align="right"><a href="duration_alert_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="duration_alert.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
         <?if ($id){?>
      <td align="right"><a href="javascript:if(confirm('Dikkat \n Kayıt Silinecek')) this.location.href='duration_alert_db.php?act=del&id=<?=$id?>'"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a></td>
         <?}?>

    </tr>
  </table>


      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("SITE_ID", "Site alanını seçmeniz gerekli.", TYP_DROPDOWN);
            form_fields[1] = Array ("ALERT_DEF_NAME", "Uyarı adını girmeniz gerekli.", TYP_NOT_NULL);
            form_fields[2] = Array ("DURATION", "Konuşma süresini rakam olarak girmeniz gerekli.", TYP_NOT_NULL + TYP_DIGIT);

      function open_email(my_id){
        if (document.all('id').value==''){
          alert('Lütfen Önce Kaydedip Sonra Mail Ekleyiniz')
        }else{
          popup('add_email.php?ALERT_DEF_ID=' + my_id,'',500,200);
        }
      }
    </script>
    
<?table_footer();
page_footer(0);?>  
