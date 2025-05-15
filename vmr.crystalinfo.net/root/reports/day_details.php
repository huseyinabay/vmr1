<?
    require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/page_functions.php";

    require_valid_login();
    $cUtility = new Utility();
    $cdb = new db_layer();
    cc_page_meta();
    echo "<body bgcolor=#FFFFFF>";
    echo "<center>";
    if($recs==""){
        echo "<br>Kayıtlı Çağrı yok...";
        echo "<p><a href=\"javascript:self.close()\">Pencereyi Kapat</a></p>";
        exit;
    }
     $SQL="SELECT  DURATION_HOUR, DURATION_MN, DURATION_SN, TIME_STAMP_DAY, TIME_STAMP_HOUR, TIME_STAMP,
             LocalCode, PURE_NUMBER, PRICE, COUNTER
           FROM CDR_MAIN_DATA WHERE (ORIG_DN IN (".$ordn.") ".$authc.")
           AND (TIME_STAMP < DATE_ADD(NOW(), INTERVAL ".$e_day." DAY) AND TIME_STAMP > DATE_ADD(NOW(), INTERVAL ".$s_day." DAY))
           AND TIME_STAMP_DAY='$gun' AND TIME_STAMP_HOUR = '$saat'
           ;";
     if (!$cdb->execute_sql($SQL,$result_d,$error)){
       echo print_error($error);
       exit;
     }
     while($row_d = mysqli_fetch_object($result_d)){
     ?>
       <TABLE>
         <TR CLASS="bgc1">
           <TD COLSPAN="2">Arama Detayı</TD>
         </TR>
         <TR>
           <TD CLASS="header">Numara</TD>
           <TD><?=$row_d->LocalCode . "  ".$row_d->PURE_NUMBER ?></TD>
         </TR>
         <TR>
           <TD CLASS="header">Kontör</TD>
           <TD><?=$row_d->COUNTER?></TD>
         </TR>
         <TR>
           <TD CLASS="header">Fiyat</TD>
           <TD><?=write_price($row_d->PRICE)?></TD>
           </TR>
         <TR>
           <TD CLASS="header">Süre</TD>
           <TD><?=$row_d->DURATION_HOUR.":".$row_d->DURATION_MN.":".$row_d->DURATION_SN?></TD>
         </TR>
         <TR>
           <TD CLASS="header">Zaman</TD>
           <TD NOWRAP><?=$row_d->TIME_STAMP?></TD>
         </TR>
       </TABLE>
      <?
     }
    ?>
    <a href="javascript:self.close()">Pencereyi Kapat</a>
    </center>
   </body>
