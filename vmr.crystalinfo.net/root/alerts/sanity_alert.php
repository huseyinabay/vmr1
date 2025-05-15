<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();
     $conn = $cdb->getConnection();
     check_right("ADMIN");
   
     $sql_str = "SELECT ALERT_DEF_NAME,ALERT_DEF_ID,FREQUENCY FROM ALERT_DEFS WHERE ALERT_ID = 10 ";
  if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }

   if (!mysqli_num_rows($result) > 0){
    $sql_str = "INSERT INTO ALERT_DEFS (ALERT_ID, 
            ALERT_DEF_NAME, FREQUENCY) 
          VALUES(10, 'Sanity Raporu', 1)";
     if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
   ?>
     <script>window.location.reload();</script>
   <?
   }

      $row = mysqli_fetch_object($result);
   $ALERT_DEF_NAME = $row->ALERT_DEF_NAME;
   $ALERT_DEF_ID = $row->ALERT_DEF_ID;
   $FREQUENCY =  $row->FREQUENCY; 

   $sql_str = "SELECT * FROM ALERT_TO_EMAIL WHERE ALERT_DEF_ID = $ALERT_DEF_ID";
     if (!($cdb->execute_sql($sql_str,$result1,$error_msg))){
        print_error($error_msg);
        exit;
     }

  cc_page_meta();
     echo "<center>";
     page_header();
     echo "<center>";
     echo "<br>";
     table_header("Sanity Raporu","80%");
?>
<script>function submit_form() {
    document.sanity_alert.submit();
}
</script>
  <center>
  <table width="65%" cellpadding="0" cellspacing="0" align="center">
    <form name="sanity_alert" method="post" onsubmit="return check_form(this);" action="sanity_alert_db.php">
    <input type="hidden" name="ALERT_DEF_ID" value="<?=$ALERT_DEF_ID?>">
    <tr class="form">
      <td width="30%" class="td1_koyu">Uyarı Adı</td>
      <td colspan="3" width="80%"><? echo $ALERT_DEF_NAME;?></td> 
    </tr>
    <tr class="form">
      <td width="30%" class="td1_koyu">Uyarı Peryodu</td>
      <td colspan="3" width="80%">
        <select name="FREQUENCY" style="width:120">
          <option value="1" <?if ($FREQUENCY==1){echo selected;}?>>Günlük</option>
          <option value="2" <?if ($FREQUENCY==2){echo selected;}?>>Haftalık</option>
          <option value="3" <?if ($FREQUENCY==3){echo selected;}?>>Aylık</option>                              
        </select>
      </td> 
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
          if (mysqli_num_rows($result1)>0){
            while ($row1 = mysqli_fetch_object($result1)){
            echo " <tr class=\"".bgc2($i)."\">";
                    echo "<td width=\"60%\">".$row1->MAIL."</td>";
                    echo "<td width=\"10%\"><a href=\"javascript:popup('add_email.php?act=upd&id=".$row1->AEM_ID."&ALERT_DEF_ID=$ALERT_DEF_ID','sanity_alert',500,200)\"><img border=\"0\" src=\"".IMAGE_ROOT."guncelle1.gif\"></a></td>";
                    echo "<td width=\"5%\"><a href=\"javascript:if(confirm('Dikkat  E-mail silinecek'))popup('add_email.php?act=del&id=".$row1->AEM_ID."','sanity_alert',500,200)\"><img border=\"0\" src=\"".IMAGE_ROOT."email_sil.gif\"></a></td>";
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
      <td align="center"><img border="0" src="<?=IMAGE_ROOT?>eposta_ekle.gif" style="cursor:hand;" onclick="javascript:open_email('<?=$ALERT_DEF_ID?>')"></td>
    </tr>
      <tr class="form">
          <td colspan="2" class="td1"><br>CrystalInfo sisteminin kurulu olduğu PC ve çalışması ile ilgili rapor,
      belirtilen kişilere e-posta olarak gönderilecektir.</td>
        </tr>
  </table>

      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
      function open_email(my_id){
          popup('add_email.php?ALERT_DEF_ID=' + my_id,'',500,200);
      }
    </script>
<?table_footer();
page_footer(0);?>  
