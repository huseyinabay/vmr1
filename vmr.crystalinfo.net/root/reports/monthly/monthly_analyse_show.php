<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     session_cache_limiter('nocache');     
     require_valid_login();
    //Hak Kontrol
    if (right_get("SITE_ADMIN")){
        //Site admin hakk varsa hereyi g�ebilir.  
      if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
      $site_crt="SITE_ID =".$SITE_ID;
      $dept_crt = " WHERE 1=1";
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
    // Admin vaye ALL_REPORT hakk varsa kendi sitesindeki hereyi g�ebilir.
      $SITE_ID = $_SESSION['site_id'];
      $site_crt="SITE_ID =".$SITE_ID;
      $dept_crt = " WHERE 1=1";
    }elseif(got_dept_right($_SESSION["user_id"])==1){
    //Bir departmann raporunu g�ebiliyorsa kendi sitesindekileri girebilir.
      $SITE_ID = $_SESSION['site_id'];
      $site_crt="SITE_ID =".$SITE_ID;
      $dept_id_string = get_dept_list($_SESSION["user_id"]);
      $dept_crt = " WHERE DEPT_ID IN($dept_id_string)";
    }else{
            print_error("Bu sayfay G�me Hakknz Yok!!!");
      exit;
    } 
  //Hak kontrol sonu  
   $kriter = $dept_crt." AND ".$site_crt;
   $start = $cUtility->myMicrotime();
//    echo "$report_type xx $analysis_type"; 
?>
   <table border=0 width="95%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" width="100%" class="rep_header" align="left">
          <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD>
                 <a href="http://www.crystalinfo.net" target="_blank">
                   <img border="0" SRC="<?=IMAGE_ROOT?>logo2.gif">
                 </a>
              </TD>
              <TD width="50%" align=center CLASS="header">
                  <?echo $company?><BR><B>
                  <?="AYLIK ANALIZLER"?></B><br>
                  <?=$header?>
              </TD>
              <TD width="25%" align="left">
                 <img SRC="<?=IMAGE_ROOT?>company.gif">
              </TD>
           </TR>
         </TABLE>
    </td>
  </tr>
  <tr>
    <td width="100%" class="rep_header" align="left">
        <table border=0 width="100%" cellspacing=0 cellpadding=0>
          <tr> 
            <td width="50%" class="rep_header" align="left">
                 <table width="50%" cellspacing="0" cellpadding="0"
                        border="0">
                    <tr <?if(0/*$record==""*/) echo "style=\"display:none;\""?>>
                        
                    </tr>
                    <tr>
                       <td class="rep_header" align="left" nowrap width="40%"
                           colspan="2">
                       </td>
                    </tr>    
                </table>    
              </td><td></td>
          </tr>
          <tr>
            <td></td>
          </tr>
          <tr>
              <td width="50%" class="rep_header" align="left">
                 Tarih (<?=" $month1/$year1"?>
                 <?if($report_type =="time_int"){?>
                 <?echo " - "."$month2/$year2 ";}?>
                 )
              </td>
              <td width="50%" align="rigth">
                <table border=0 cellspacing=0 cellpadding=0 align="right"> 
                 <tr>
                   <td>
                      <img src="<?=IMAGE_ROOT?>report/top02.gif" border=0>
                   </td>
                   <td>
                     <a href="javascript:history.back(1);">
                      <img src="<?=IMAGE_ROOT?>report/geri.gif"
                           border=0 title="Geri">
                     </a>
                   </td>
                   <td>
                     <a href="javascript:history.forward(1);">
                      <img src="<?=IMAGE_ROOT?>report/ileri.gif" border=0
                           title="�Geri">
                     </a>
                   </td>
                   <td>
                     <a href="javascript:document.all('sort_me').submit();">
                      <img src="<?=IMAGE_ROOT?>report/yenile.gif" border=0
                           title="Yenile">
                     </a>
                   </td>
                   <td>
                     <a href="javascript:window.print();">
                      <img src="<?=IMAGE_ROOT?>report/print.gif" border=0
                           title="Yazdr"> 
                     </a>
                   </td>
                   <td>
                      <img src="<?=IMAGE_ROOT?>report/top01.gif" border=0>
                   </td>
                 </tr>  
              </table>
          </td>
         </tr>
       </table>
       <table width="100%" border=1 cellspacing=2 cellpadding=2 align="right">
 <?

//    echo "<table><tr>SITE: $SITE_ID</tr></table>";
  if ($show_type == "price") {
   if ($report_type == "time_int") // Zaman Araliginda
   {
      if ($analysis_type == "dept"){
        $sqlStr = "SELECT "."DEPT_NAME,TIME_STAMP_MONTH,SEHIR_ICI_TUTAR,".
                  " SEHIR_ARASI_TUTAR,".
                "GSM_TUTAR, ULUSLAR_ARASI_TUTAR, DIGER_TUTAR ".
                "FROM MONTHLY_ANALYSE JOIN DEPTS ON DEPTS.DEPT_ID = ".
                " MONTHLY_ANALYSE.DEPT_ID ".
                " WHERE TYPE = 'department' AND".
                " MONTHLY_ANALYSE.SITE_ID = '$SITE_ID'";
        $sqlStr .= " AND TIME_STAMP_YEAR >= $year1 ".
                " AND TIME_STAMP_MONTH >= $month1".
                " AND TIME_STAMP_YEAR <=$year2 AND".
                " TIME_STAMP_MONTH <= $month2".
                " ORDER BY DEPT_NAME ";

//         echo $sqlStr;
        $cdb->execute_sql($sqlStr, $result, $err_msg);
//         echo "<table border=1 cellspacing=1 cellpadding=0>";
        echo "<tr bgcolor=#E4E4E4 align='right'>";
        echo "<td> DEPARTMAN </td>"."<td> AY </td>". 
             "<td> SEHIR ICI TUTAR </td>".
             "<td> SEHIRLER ARASI TUTAR </td>"."<td> GSM TUTAR </td>".
             "<td> ULUSLARARASI TUTAR </td>"."<td> DIGER TUTAR </td>";
        echo "</tr>"; 
        $round = 7; // asagidaki dongudeki limit degiskeni
      } 
      else if ($analysis_type == "ext"){
        $sqlStr = "SELECT DESCRIPTION,DAHILI_ID,TIME_STAMP_MONTH,".
                  "SEHIR_ICI_TUTAR,"."SEHIR_ARASI_TUTAR,".
                "GSM_TUTAR, ULUSLAR_ARASI_TUTAR,DIGER_TUTAR ".
                "FROM MONTHLY_ANALYSE JOIN EXTENTIONS ON EXTENTIONS.EXT_NO = ".
                " MONTHLY_ANALYSE.DAHILI_ID ".
                " WHERE MONTHLY_ANALYSE.SITE_ID = '$SITE_ID' ";
        $sqlStr .= " AND TIME_STAMP_YEAR >= $year1 ".
                " AND TIME_STAMP_MONTH >= $month1".
                " AND TIME_STAMP_YEAR <=$year2 AND".
                " TIME_STAMP_MONTH <= $month2".
                " ORDER BY DAHILI_ID ";
//         echo $sqlStr;
        $cdb->execute_sql($sqlStr, $result, $err_msg);
//         echo "<table border=1 cellspacing=1 cellpadding=0>";
        echo "<tr bgcolor=#E4E4E4 align='right'>";
        echo "<td> SAHIS </td>"."<td> DAHILI </td>"."<td> AY </td>".
             "<td> SEHIR ICI TUTAR </td>".
             "<td> SEHIRLER ARASI TUTAR </td>"."<td> GSM TUTAR </td>".
             "<td> ULUSLARARASI TUTAR </td>"."<td> DIGER TUTAR </td>";
        echo "</tr>";
        $round = 8;
     }
     else if ($analysis_type == "trunk"){
       $sqlStr = " SELECT TRUNK_NAME,TRUNK_MEMBER,SUM(PRICE)".
                 " FROM TRUNK_ANALYSE ".
                 " JOIN TRUNKS ON TRUNK_ANALYSE.TRUNK_MEMBER = ".
                 " TRUNKS.MEMBER_NO ".
                 " WHERE TRUNK_ANALYSE.SITE_ID = '$SITE_ID' ";
       $sqlStr .= " AND TIME_STAMP_YEAR >= $year1 ".
                  " AND TIME_STAMP_MONTH >= $month1".
                  " AND TIME_STAMP_YEAR <=$year2 AND".
                  " TIME_STAMP_MONTH <= $month2".
                  " GROUP BY TRUNK_ANALYSE.TRUNK_MEMBER";
       
//        echo $sqlStr;
       $cdb->execute_sql($sqlStr, $result, $err_msg);
//        echo "<table border=1 cellspacing=1 cellpadding=0>";
       echo "<tr bgcolor=#E4E4E4 align='right'>";
       echo "<td> HAT </td>"."<td> HAT NO </td>"."<td> UCRET </td>";
       echo "</tr>";
       $round = 3;
     }
     else if ($analysis_type == "general"){
       $sqlStr = "SELECT TIME_STAMP_MONTH,SEHIR_ICI_TUTAR,".
                 "SEHIR_ARASI_TUTAR,".
                 "GSM_TUTAR, ULUSLAR_ARASI_TUTAR, DIGER_TUTAR ".
                 "FROM MONTHLY_ANALYSE".
                 " WHERE TYPE = 'call type' AND".
                 " MONTHLY_ANALYSE.SITE_ID = '$SITE_ID'";
       $sqlStr .= " AND TIME_STAMP_YEAR >= $year1 ".
                  " AND TIME_STAMP_MONTH >= $month1".
                  " AND TIME_STAMP_YEAR <=$year2 AND".
                  " TIME_STAMP_MONTH <= $month2";
//        echo $sqlStr;
       $cdb->execute_sql($sqlStr, $result, $err_msg);
//        echo "<table border=1>";
       echo "<tr bgcolor=#E4E4E4 align='right'>";
       echo "<td> AY </td>"."<td> SEHIR ICI TUTAR </td>".
            "<td> SEHIRLER ARASI TUTAR </td>"."<td> GSM TUTAR </td>".
            "<td> ULUSLARARASI TUTAR </td>"."<td> DIGER TUTAR </td>";
       echo "</tr>";
       $round = 6; 
   }
  }
  else if ($report_type == "month"){ //Belli bir Donem (Aylik)
     
     if ($analysis_type == "dept"){
      $sqlStr = "SELECT "."DEPT_NAME,SEHIR_ICI_TUTAR, SEHIR_ARASI_TUTAR,".
                "GSM_TUTAR, ULUSLAR_ARASI_TUTAR, DIGER_TUTAR ".
                "FROM MONTHLY_ANALYSE JOIN DEPTS ON DEPTS.DEPT_ID = ".
                " MONTHLY_ANALYSE.DEPT_ID ".
                " WHERE TYPE = 'department' AND".
                " MONTHLY_ANALYSE.SITE_ID = '$SITE_ID'";
      $sqlStr = $sqlStr." AND TIME_STAMP_YEAR = '$year1' ".
                " AND TIME_STAMP_MONTH = '$month1'";
      if ($DEPT_ID != -1){
          $dept_crt = " MONTHLY_ANALYSE.DEPT_ID = '$DEPT_ID' ";
          $sqlStr = $sqlStr." AND ".$dept_crt;
      }
//       echo $sqlStr;
      $cdb->execute_sql($sqlStr, $result, $err_msg);
//       echo "<table border=1 cellspacing=1 cellpadding=0>";
      echo "<tr bgcolor=#E4E4E4 align='right'>";
      echo "<td> DEPARTMAN </td>"."<td> SEHIR ICI TUTAR </td>".
           "<td> SEHIRLER ARASI TUTAR </td>"."<td> GSM TUTAR </td>".
           "<td> ULUSLARARASI TUTAR </td>"."<td> DIGER TUTAR </td>";
      echo "</tr>";
      $round = 6;
   }
   else if ($analysis_type == "ext"){
      $sqlStr = "SELECT DESCRIPTION,DAHILI_ID,SEHIR_ICI_TUTAR,".
                "SEHIR_ARASI_TUTAR,".
                "GSM_TUTAR, ULUSLAR_ARASI_TUTAR, DIGER_TUTAR ".
                "FROM MONTHLY_ANALYSE JOIN EXTENTIONS ON EXTENTIONS.EXT_NO = ".
                " MONTHLY_ANALYSE.DAHILI_ID ".
                " WHERE MONTHLY_ANALYSE.SITE_ID = '$SITE_ID' ";
      $sqlStr .= " AND TIME_STAMP_YEAR = '$year1' ".
                " AND TIME_STAMP_MONTH = '$month1'".
                " ORDER BY DAHILI_ID ";
      if ($my_dahili){
         $ext_crt = " AND DAHILI_ID IN ($my_dahili) ";
         $sqlStr .= $ext_crt;
      }
//       echo $sqlStr;
      $cdb->execute_sql($sqlStr, $result, $err_msg);
//       echo "<table border=1 cellspacing=1 cellpadding=0>";
      echo "<tr bgcolor=#E4E4E4 align='right'>";
      echo "<td> SAHIS </td>"."<td> DAHILI </td>"."<td> SEHIR ICI TUTAR </td>".
           "<td> SEHIRLER ARASI TUTAR </td>"."<td> GSM TUTAR </td>".
           "<td> ULUSLARARASI TUTAR </td>"."<td> DIGER TUTAR </td>";
      echo "</tr>";
      $round = 7;
   }
   else if ($analysis_type == "trunk"){
       $sqlStr = " SELECT TRUNK_NAME,TRUNK_MEMBER,SUM(PRICE)".
                 " FROM TRUNK_ANALYSE ".
                 " JOIN TRUNKS ON TRUNK_ANALYSE.TRUNK_MEMBER = ".
                 " TRUNKS.MEMBER_NO ".
                 " WHERE TRUNK_ANALYSE.SITE_ID = '$SITE_ID' ";
       $sqlStr .= " AND TIME_STAMP_YEAR = '$year1' ".
                 " AND TIME_STAMP_MONTH = '$month1'";
       if ($hat != -1) {
           $sqlStr .= " AND TRUNKS.TRUNK_ID = '$hat' ";
       }
       $sqlStr .= " GROUP BY TRUNK_ANALYSE.TRUNK_MEMBER"; 
//        echo $sqlStr;
       $cdb->execute_sql($sqlStr, $result, $err_msg);
//        echo "<table border=1 cellspacing=1 cellpadding=0>";
       echo "<tr bgcolor=#E4E4E4 align='right'>";
       echo "<td> HAT </td>"."<td> HAT NO </td>"."<td> UCRET </td>";
       echo "</tr>";
       $round = 3;
   }
   else if ($analysis_type == "general"){
       $sqlStr = "SELECT SITE_NAME, SEHIR_ICI_TUTAR,".
                 "SEHIR_ARASI_TUTAR,".
                 "GSM_TUTAR, ULUSLAR_ARASI_TUTAR, DIGER_TUTAR ".
                 "FROM MONTHLY_ANALYSE ".
                 " JOIN SITES ON SITES.SITE_ID = MONTHLY_ANALYSE.SITE_ID ".
                 " WHERE TYPE = 'call type' AND".
                 " MONTHLY_ANALYSE.SITE_ID = '$SITE_ID'";
       $sqlStr .= " AND TIME_STAMP_YEAR = '$year1' ".
                  " AND TIME_STAMP_MONTH = '$month1'";
//        echo $sqlStr;
       $cdb->execute_sql($sqlStr, $result, $err_msg);
//        echo "<table border=1 cellspacing=1 cellpadding=0>";
       echo "<tr bgcolor=#E4E4E4 align='right'>";
       echo "<td> SITE </td>"."<td> SEHIR ICI TUTAR </td>".
            "<td> SEHIRLER ARASI TUTAR </td>"."<td> GSM TUTAR </td>".
            "<td> ULUSLARARASI TUTAR </td>"."<td> DIGER TUTAR </td>";
       echo "</tr>";
       $round = 6; 
   }
  }
  
  if (mysqli_num_rows($result) > 0){
    while ($row = mysqli_fetch_array($result)){
        $bground = "#FFFFFF";
        if ($i%2) 
           $bground="#E4E4E4";
        echo "<tr bgcolor = $bground >";
        for ($j = 0;$j < $round; $j++){
             echo "<td align=right>";
             if (is_numeric($row[$j]) && $j >= $round-5 && 
                  $analysis_type != "trunk"){
                setlocale(LC_MONETARY, 'tr_TR');
                echo money_format('%.0n', $row[$j]);
                $sumAll += $row[$j];
                if ($j == $round-5) //Sehir ici
                  $sumCity += $row[$j];
                if ($j == $round-4) //Sehirler arasi
                  $sumIntCity += $row[$j];
                if ($j == $round-3) // Gsm
                  $sumGsm += $row[$j];
                if ($j == $round-2) // Uluslararasi
                  $sumIntNat += $row[$j];
             }else{
                echo $row[$j];
             }
             echo "</td>";
        }
        echo "</tr>";
        $i++;
    }

    echo "</table></table>";
    if ($analysis_type != "trunk"){
    echo "<table align=right><tr><td></td></tr>";
    echo "<tr><td align=right class=td1_koyu width='30%'>";
    echo "Sehir Ici:</td><td align=left class=td1_koyu width='70%'>";
    echo " &nbsp &nbsp &nbsp ".money_format('%.0n', $sumCity)."</td></tr>";

    echo "<tr><td align=right class=td1_koyu width='45%'>";
    echo "Sehirler Arasi:</td><td align=left class=td1_koyu width='55%'>";
    echo " &nbsp &nbsp &nbsp ".money_format('%.0n', $sumIntCity)."</td></tr>";

    echo "<tr><td align=right class=td1_koyu width='45%'>";
    echo "GSM :</td><td align=left class=td1_koyu width='55%'>";
    echo " &nbsp &nbsp &nbsp ".money_format('%.0n', $sumGsm)."</td></tr>";

    echo "<tr><td align=right class=td1_koyu width='45%'>";
    echo "Uluslararasi:</td><td align=left class=td1_koyu width='55%'>";
    echo " &nbsp &nbsp &nbsp ".money_format('%.0n', $sumIntNat)."</td></tr>";

    echo "<tr><td align=right class=td1_koyu width='45%'>";
    echo "Toplam : </td><td align=left class=td1_koyu width='55%'>";
    echo " &nbsp &nbsp &nbsp ".money_format('%.0n', $sumAll)."</td></tr>";

    echo "</table>";
    }
  }
 }else if ($show_type == "dur_count"){
    if ($analysis_type == "ext"){
             $sqlStr = "SELECT DAHILI_ID, ".
                         " CALL_TYPE, DURATION, CALL_COUNT".
                         " FROM MONTHLY_DUR_COUNT ".
                         " WHERE REP_TYPE = 'dahili' AND".
                         " MONTHLY_DUR_COUNT.SITE_ID = '$SITE_ID'";
             if ($report_type == "time_int"){
                $sqlStr .= " AND TIME_STAMP_YEAR >= $year1 ".
                           " AND TIME_STAMP_MONTH >= $month1".
                           " AND TIME_STAMP_YEAR <=$year2 AND".
                           " TIME_STAMP_MONTH <= $month2";
                           
             }else{
                $sqlStr .= " AND TIME_STAMP_YEAR = '$year1' ".
                           " AND TIME_STAMP_MONTH = '$month1'";
                if ($my_dahili){
                    $ext_crt = " AND DAHILI_ID IN ($my_dahili) ";
                    $sqlStr .= $ext_crt;
                }
            }
            //echo $sqlStr;
           $cdb->execute_sql($sqlStr, $result, $err_msg);
           $content = "DAHILI_ID";
           $content_show = "Dahili";
    }else if ($analysis_type == "dept"){
             $sqlStr = "SELECT DEPT_NAME, ".
                         " CALL_TYPE, DURATION, CALL_COUNT".
                         " FROM MONTHLY_DUR_COUNT JOIN DEPTS".
                         " ON DEPTS.DEPT_ID = ".
                         " MONTHLY_DUR_COUNT.DEPT_ID ".
                         " WHERE REP_TYPE = 'department' AND".
                         " MONTHLY_DUR_COUNT.SITE_ID = '$SITE_ID'";
             if ($report_type == "time_int"){
                $sqlStr .= " AND TIME_STAMP_YEAR >= $year1 ".
                           " AND TIME_STAMP_MONTH >= $month1".
                           " AND TIME_STAMP_YEAR <=$year2 AND".
                           " TIME_STAMP_MONTH <= $month2";
                           
             }else{
                $sqlStr .= " AND TIME_STAMP_YEAR = '$year1' ".
                           " AND TIME_STAMP_MONTH = '$month1'";
                if ($DEPT_ID != -1){
                    $dept_crt = " MONTHLY_DUR_COUNT.DEPT_ID = '$DEPT_ID' ";
                    $sqlStr = $sqlStr." AND ".$dept_crt;
                }
            }
            $sqlStr .= " ORDER BY DEPT_NAME ";
//             echo $sqlStr;
            $cdb->execute_sql($sqlStr, $result, $err_msg);
            $content = "DEPT_NAME";
            $content_show = "Departman";
           
    }else if ($analysis_type == "trunk"){
                         $sqlStr = "SELECT TRUNKS.TRUNK_NAME, ".
                         " CALL_TYPE, DURATION, CALL_COUNT".
                         " FROM MONTHLY_DUR_COUNT JOIN TRUNKS ON ".
                         " MONTHLY_DUR_COUNT.TRUNK_MEMBER =".
                         " TRUNKS.MEMBER_NO ".
                         " WHERE REP_TYPE = 'TRUNK' AND ".
                         " MONTHLY_DUR_COUNT.SITE_ID = '$SITE_ID'";
             if ($report_type == "time_int"){
                $sqlStr .= " AND TIME_STAMP_YEAR >= $year1 ".
                           " AND TIME_STAMP_MONTH >= $month1".
                           " AND TIME_STAMP_YEAR <=$year2 AND".
                           " TIME_STAMP_MONTH <= $month2";
                           
             }else{
                $sqlStr .= " AND TIME_STAMP_YEAR = '$year1' ".
                           " AND TIME_STAMP_MONTH = '$month1'";
                if ($hat != -1) {
                   $sqlStr .= " AND TRUNKS.TRUNK_ID = '$hat' ";
                }
             }
            $cdb->execute_sql($sqlStr, $result, $err_msg);
            $content = "TRUNK_NAME";
            $content_show = "Hat";
    }else if ($analysis_type == "general"){
                         $sqlStr = "SELECT SITE_NAME, ".
                         " CALL_TYPE, DURATION, CALL_COUNT".
                         " FROM MONTHLY_DUR_COUNT ".
                         " JOIN SITES ON MONTHLY_DUR_COUNT.SITE_ID = ".
                         " SITES.SITE_ID ".
                         " WHERE REP_TYPE = 'general' AND ".
                         " MONTHLY_DUR_COUNT.SITE_ID = '$SITE_ID'";
             if ($report_type == "time_int"){
                $sqlStr .= " AND TIME_STAMP_YEAR >= $year1 ".
                           " AND TIME_STAMP_MONTH >= $month1".
                           " AND TIME_STAMP_YEAR <=$year2 AND".
                           " TIME_STAMP_MONTH <= $month2";
                           
             }else{
                $sqlStr .= " AND TIME_STAMP_YEAR = '$year1' ".
                           " AND TIME_STAMP_MONTH = '$month1'";
             }
             
            $cdb->execute_sql($sqlStr, $result, $err_msg);
            $content = "SITE_NAME";
            $content_show = "SITE";
    }
           echo "<tr bgcolor=#E4E4E4 cellspacing=2".
                " cellpadding=2 align='center'>";
           echo "<td>$content_show</td>";
           echo "<td><table width='100%' border=0><tr".
                " width='100%'><b>SURE</b></tr>".
                "<tr align='center'>".
                "<td>Gelen</td>".
                "<td>Giden</td>".
                "<td>Dahili</td>".
                "</tr></table></td>".
                "<td><table width='100%' border=0><tr><b>ADET</b></tr>".
                "<tr align='center'>".
                "<td>Gelen</td>".
                "<td>Giden</td>".
                "<td>Dahili</td>".
                "</tr>";
          echo "</table></td>";
    if (mysqli_num_rows($result) > 0){
       while ($row = mysqli_fetch_array($result))
       {          
        $dur_count_dept[$row[$content]][$row['CALL_TYPE']][0]
            = $row['DURATION'];
        $dur_count_dept[$row[$content]][$row['CALL_TYPE']][1]
            = $row['CALL_COUNT'];
       }
       
       foreach ($dur_count_dept as $key=>$value){
          $bground="#FFFFFF";
          if ($ii % 2) $bground = "#E4E4E4";
          echo "<tr bgcolor=$bground cellspacing=2".
               " cellpadding=2 align='center'>";
          echo "<td>$key</td>";
          $htmlDurStr = "";
          $htmlCountStr = "";
          for ($i = 0; $i <=2; $i++){
             if (!$value[$i][0])
                  $value[$i][0] = 0;
             if (!$value[$i][1])
                  $value[$i][1] = 0;
             $htmlDurStr .= "<td  width='15%' align=center>".
                           $value[$i][0]."</td>";
             $htmlCountStr .= "<td width='15%' align=center>".
                           $value[$i][1]."</td>";
          }
          echo "<td><table width='100%'><tr bgcolor=$bground>\n".
               $htmlDurStr."</tr></td></table>\n";
          echo "<td><table width='100%'><tr bgcolor=$bground>\n".
               $htmlCountStr."</tr></td></table>\n";
          echo "</tr>\n";
          $ii++;
       }
       echo "</table></td></tr>\n";
       echo "</table>\n";
   }
}
?>