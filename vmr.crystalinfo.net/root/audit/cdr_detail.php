<? require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();
     $conn = $cdb->getConnection();
     $start = $cUtility->myMicrotime();

  if ($semi_id){
    $crt = " SEMI_FINISHED_ID = '".$semi_id."'";
  }else{
    $crt = " CDR_ID = '".$id."'";
  }
  $sql_str =  "SELECT CDR_ID, REC_TYPE, REC_NO, ORIG_TRUNK_MEMBER, ORIG_DN, TER_DN, TER_TRUNK_MEMBER,
                 TIME_STAMP_YEAR, TIME_STAMP_MONTH, TIME_STAMP_DAY, TIME_STAMP_HOUR, TIME_STAMP_MN, TIME_STAMP_SN,
                 DURATION_HOUR, DURATION_MN, DURATION_SN, DIGITS, CALL_TYPE, SEMI_FINISHED_ID,
				 AUTH_ID, ACCESS_CODE, CountryCode, LocalCode, PURE_NUMBER, COUNTER,PRICE,
                 ERR_CODE, TIME_STAMP, CONF_NO, S_MAIN_ID 
        FROM CDR_MAIN_DATA WHERE ".$crt;
         if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                    print_error($error_msg);
                    exit;
         }
  //Orada bulamadı ise diğer tabloda olabilir.
  if(!mysqli_num_rows($result)>0){
    $sql_str =  "SELECT CDR_ID, REC_TYPE, REC_NO, ORIG_TRUNK_MEMBER, ORIG_DN, TER_DN, TER_TRUNK_MEMBER,
                   TIME_STAMP_YEAR, TIME_STAMP_MONTH, TIME_STAMP_DAY, TIME_STAMP_HOUR, TIME_STAMP_MN, TIME_STAMP_SN,
                   DURATION_HOUR, DURATION_MN, DURATION_SN, DIGITS, CALL_TYPE, SEMI_FINISHED_ID, AUTH_ID,
				   ACCESS_CODE, CountryCode, LocalCode, PURE_NUMBER, COUNTER,PRICE,
                   ERR_CODE, TIME_STAMP, CONF_NO, S_MAIN_ID 
                 FROM CDR_MAIN_INB WHERE ".$crt;
     if (!($cdb->execute_sql($sql_str,$result1,$error_msg))){
                    print_error($error_msg);
                    exit;
         }
     if(mysqli_num_rows($result1)>0){
      $row = mysqli_fetch_object($result1);
     }else{
      echo("Belirtilen Kayıt Sistemde Bulunamadı");
      exit;
     }
  }else{
         $row = mysqli_fetch_object($result);
  }

  cc_page_meta();
  echo "<center>";
  echo "<BR><BR>";   
  table_header("Log Detayı",'95%');
?>
      <table border="1">
        <tr>
          <td>Kayıt No</td>  
          <td>=</td>  
          <td><?=$row->CDR_ID?></td>
        </tr>
        <?if ($row->S_MAIN_ID<>'0' AND  $row->S_MAIN_ID<>''){?>
        <tr>
          <td>Ana Kayıt</td>  
          <td>=</td>  
          <td><a href="#"><span onclick="javascript:popup('cdr_detail.php?id=<?=$row->S_MAIN_ID?>','cdr_detail',600,300)"><?=$row->S_MAIN_ID?></span></a></td>
        </tr>
        <?}?>
        <tr>
          <td>Kayıt Tipi</td>  
          <td>=</td>  
          <td><?=$row->REC_TYPE?></td>
        </tr>
        <tr>
          <td>Santral No</td>  
          <td>=</td>  
          <td><?=$row->REC_NO?></td>
        </tr>
        <tr>
          <td>Arayan</td>  
          <td>=</td>  
          <td><?=$row->ORIG_DN?></td>
        </tr>  
        <tr>
          <td>Çıkış Hattı</td>  
          <td>=</td>  
          <td><?=$row->TER_TRUNK_MEMBER?></td>
        </tr>
        <tr>
          <td>Tarih Saat</td>  
          <td>=</td>  
          <td><?=$row->TIME_STAMP?></td>
        </tr>
        <tr>
          <td>Süre Saat</td>  
          <td>=</td>  
          <td><?=$row->DURATION_HOUR?></td>
        </tr>
        <tr>
          <td>Süre Dk.</td>  
          <td>=</td>  
          <td><?=$row->DURATION_MN?></td>
        </tr>
        <tr>
          <td>Süre Sn.</td>  
          <td>=</td>  
          <td><?=$row->DURATION_SN?></td>
        </tr>
      </table>
    </td>
    <td>
      <table border="1">
        <tr>
          <td>Numara</td>  
          <td>=</td>  
          <td><?=$row->DIGITS?></td>
        </tr>
        <tr>
          <td>Erişim Kodu</td>  
          <td>=</td>  
          <td><?=$row->ACCESS_CODE?></td>
        </tr>
        <tr>
          <td>Ülke Kodu</td>  
          <td>=</td>  
          <td><?=$row->CountryCode?></td>
        </tr>        
        <tr>
          <td>Şehir Kodu</td>  
          <td>=</td>  
          <td><?=$row->LocalCode?></td>
        </tr>
        <tr>
          <td>Tel No</td>  
          <td>=</td>  
          <td><?=$row->PURE_NUMBER?></td>
        </tr>
        <tr>
          <td>Kontör</td>  
          <td>=</td>  
          <td><?=$row->COUNTER?></td>
        </tr>
        <tr>
          <td>Ücret</td>  
          <td>=</td>  
          <td><?=$row->PRICE?></td>
        </tr>
        <tr>
          <td>Auth. Kodu</td>  
          <td>=</td>  
          <td><?=$row->AUTH_ID?></td>
        </tr>
        <tr>
          <td>Konf. No</td>  
          <td>=</td>  
          <td><?=$row->CONF_NO?></td>
        </tr>
        <tr>
          <td>Hata Kodu</td>  
          <td>=</td>  
          <td><?=$row->ERR_CODE?></td>
        </tr>

      </table>
      </td>
     </tr><tr><td colspan=2> 
<?
    $sql_str =  "SELECT * FROM SEMI_ARCHIEVE WHERE ID= $row->SEMI_FINISHED_ID";
     if (!($cdb->execute_sql($sql_str,$result1,$error_msg))){
       print_error($error_msg);
       exit;
     }
     $rowRawData = mysqli_fetch_object($result1);
?>
      <table border=1 width="100%">
         <tr>
          <td>Ana Kayıt</td>  
          <td>=</td>  
          <td>
          <textarea  cols="100" rows="3" wrap="no"><?=$rowRawData->LINE1."\n".$rowRawData->LINE2."\n".$rowRawData->LINE3?>          
          </textarea>
          </td>
        </tr>
      </table>
<?table_footer();?>
