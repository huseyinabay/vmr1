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
      $site_crt = " AND ALERT_DEFS.SITE_ID = ".$SESSION['site_id'];
    }
       $sql_str = "SELECT * FROM ALERT_DEFS WHERE ALERT_DEFS.ALERT_ID = 7
             AND ALERT_DEFS.ALERT_DEF_ID = $id ".$site_crt;
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
      print_error($error_msg);
      exit;
    }
    if (mysqli_num_rows($result)>0){
      $row = mysqli_fetch_object($result);
      $ALERT_DEF_NAME = $row->ALERT_DEF_NAME;
       $ALERT_DEF_ID = $row->ALERT_DEF_ID;
      $SITE_ID = $row->SITE_ID;
    }else{
      print_error("Belirtilen Kayıt Bulunamadı");
      exit;
    }
     $sql_str = "SELECT * FROM ALERT_TO_EMAIL WHERE ALERT_DEF_ID = '$ALERT_DEF_ID'";
    if (!($cdb->execute_sql($sql_str,$result1,$error_msg))){
          print_error($error_msg);
          exit;
       }
  }else{
    $SITE_ID = $SESSION['site_id'];
    $ALERT_DEF_NAME = 'Günün Konuşma özeti';
  }

  cc_page_meta();
     echo "<center>";
     page_header();
     echo "<center>";
     echo "<br>";
     table_header("Gün Özeti","80%");
?>
<script>
    function submit_form() {
        if(check_form(document.day_summary_alert)){
            document.all('SITE_ID').disabled=false;      
            document.day_summary_alert.submit();
        }
    }
</script>
  <center>
  <table width="50%" cellpadding="0" cellspacing="0">
    <form name="day_summary_alert" method="post" onsubmit="return check_form(this);" action="day_summary_alert_db.php?act=<? echo  $_GET['act'] ?>">
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
      <tr>
      <td width="30%" class="td1_koyu">Uyarı Adı</td>
      <td width="70%"><? echo $ALERT_DEF_NAME;?></td> 
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
  </form>
  <table width="50%" cellpadding="0" cellspacing="0" border="0" align="center">
    <tr>
      <td width="60%">
        <?table_in_table_header("Gideceği E-Postalar","100%")?>
        <table>      
          <?$i;
          if ($act=='upd'){          
              if(mysql_num_rows($result1)>0){
                while ($row1 = mysqli_fetch_object($result1)){
                  echo " <tr class=\"".bgc2($i)."\">";
                          echo "<td width=\"60%\">".$row1->MAIL."</td>";
                          echo "<td width=\"10%\"><a href=\"javascript:popup('add_email.php?act=upd&id=".$row1->AEM_ID."&ALERT_DEF_ID=$ALERT_DEF_ID','day_summary_alert',500,200)\"><img border=\"0\" src=\"".IMAGE_ROOT."guncelle1.gif\"></a></td>";
                          echo "<td width=\"5%\"><a href=\"javascript:if(confirm('Dikkat  E-mail silinecek'))popup('add_email.php?act=del&id=".$row1->AEM_ID."','day_summary_alert',500,200)\"><img border=\"0\" src=\"".IMAGE_ROOT."email_sil.gif\"></a></td>";
                  echo "</tr>";
                  $i++;
                }
              }
          }?>
              </table>
        <?table_in_table_footer()?>    
            </form>
          </td>
    </tr>
    <tr>
      <td width="75%" align="center"><img border="0" src="<?=IMAGE_ROOT?>eposta_ekle.gif" style="cursor:hand;" onclick="javascript:open_email('<?=$ALERT_DEF_ID?>')"></td>
    </tr>
      <tr class="form">
          <td colspan="2"class="td1"><br>Günün Konuşma Özeti belirtilen e-posta adresine
      e-posta olarak gönderilecektir.</td>
        </tr>
  </table>
  <table align="right" width="100%" border="0">
    <tr>
      <td colspan="4" width="70%"></td>
      <td align="right"><a href="day_summary_alert_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="day_summary_alert.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
         <?if ($id){?>
      <td align="right"><a href="javascript:if(confirm(' Dikkat \n Kayıt Silinecek')) this.location.href='day_summary_alert_db.php?act=del&id=<?=$id?>'"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a></td>
         <?}?>

    </tr>
  </table>
  

      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("SITE_ID", "Site alanını girmeniz gerekli.", TYP_DROPDOWN);

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
