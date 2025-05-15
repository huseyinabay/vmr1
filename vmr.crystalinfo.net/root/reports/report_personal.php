<?
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
 require_valid_login();
 
 $hours=array("00:00","01:00","02:00","03:00","04:00","05:00","06:00","07:00",
               "08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00",
               "16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00");
 $day_week=array("Monday" => "Pzt" , "Tuesday" => "Sl" , "Wednesday" => "Çrş" , 
 "Thursday" => "Prş" , "Friday" => "C" , "Saturday" => "Cts", "Sunday" => "Pz");
 
     cc_page_meta();
     page_header();
     echo "<br>";


     $cUtility = new Utility();
     $cdb = new db_layer();

    if(!isset($day))
        $day=0;
    $start_day=$day-5;
    $end_day=$day+1;
    $SQL_T="   
        SELECT
        DATE_ADD(NOW(), INTERVAL ".$end_day." DAY) AS EN_MON,
        DATE_ADD(NOW(), INTERVAL ".$start_day." DAY) AS ST_MON
    ;";
    if (!$cdb->execute_sql($SQL_T, $result_t, $error)){
      echo "Error..".$error;   
      exit;
    }
    $row_t=mysqli_fetch_object($result_t);


    for($m=0;$m<=4;$m++){
        $the_day=$day+$m-4;
        $SQL_x="   
            SELECT
            DAYOFMONTH(DATE_ADD(NOW(), INTERVAL ".$the_day." DAY)) AS THE_DAY,
            MONTH(DATE_ADD(NOW(), INTERVAL ".$the_day." DAY)) AS THE_MON,
            DAYNAME(DATE_ADD(NOW(), INTERVAL ".$the_day." DAY)) AS WEEK_DAY,
            YEAR(DATE_ADD(NOW(), INTERVAL ".$the_day." DAY)) AS THE_YEAR
        ;";
        if (!$cdb->execute_sql($SQL_x, $result_x, $error)){
          echo "Error..".$error;   
          exit;
        }
        $row_x=mysqli_fetch_object($result_x);
        $months[$m]=$row_x->THE_MON;
        $days[$m]=$row_x->THE_DAY;
        $weekdays[$m]=$row_x->WEEK_DAY;
        $years[$m]=$row_x->THE_YEAR;
    }

        $SQL_1 = "
        SELECT A.EXT_NO AS EXT1,
        B.EXT_NO AS EXT2, C.EXT_NO AS EXT3, AUTH_CODES.AUTH_CODE,USER_ID
        FROM USERS
        LEFT JOIN EXTENTIONS A ON A.EXT_ID = USERS.EXT_ID1
        LEFT JOIN EXTENTIONS B ON B.EXT_ID = USERS.EXT_ID2
        LEFT JOIN EXTENTIONS C ON C.EXT_ID = USERS.EXT_ID3
        LEFT JOIN AUTH_CODES ON AUTH_CODES.AUTH_CODE_ID = USERS.AUTH_CODE_ID
        WHERE USER_ID = '" .$_SESSION['user_id'].
        "';";                 // 187 İLE DEĞİŞTİRMEYİ UNUTMA...
     
    if (!$cdb->execute_sql($SQL_1,$result_1,$error)){
      echo "Error..".$error;   
      exit;
    }
    $row_1=mysqli_fetch_object($result_1);
    $origs="99999"; //this EXTENSION assigned to anybod and no call occured
    if($row_1->EXT1!=""){
        $origs=$row_1->EXT1;
    }

    if($row_1->EXT2!=""){
        if($origs!=" "){
            $origs=$origs.", ".$row_1->EXT2;
        }else{
            $origs=$row_1->EXT2;
        }
    }

    if($row_1->EXT3!=""){
        if($origs!=" "){
            $origs=$origs.", ".$row_1->EXT3;
        }else{
            $origs=$row_1->EXT3;
        }
    }
    if($row_1->AUTH_CODE!=""){
        $auth_c="OR AUTH_ID=".$row_1->AUTH_CODE;
    }
    

   $SQL="
        SELECT COUNT(*) AS CNT, DURATION, TIME_STAMP_DAY, TIME_STAMP_HOUR, TIME_STAMP
        FROM CDR_MAIN_DATA WHERE SITE_ID = ".$_SESSION['site_id']." AND (ORIG_DN IN (".$origs.") ".$auth_c.")
        AND (TIME_STAMP < DATE_ADD(NOW(), INTERVAL ".$end_day." DAY) AND TIME_STAMP > DATE_ADD(NOW(), INTERVAL ".$start_day." DAY))
        GROUP BY TIME_STAMP_DAY, TIME_STAMP_HOUR
    ;";
    if (!$cdb->execute_sql($SQL,$result,$error)){
      echo "Error..".$error;   
      exit;
    }
        
    while($row = mysqli_fetch_object($result)){
        $val[$row->TIME_STAMP_HOUR][$row->TIME_STAMP_DAY][0]=calculate_all_time($row->DURATION);
        $val[$row->TIME_STAMP_HOUR][$row->TIME_STAMP_DAY][1]=$row->CNT;
    }
?>
   <div id="day_div_" style="display:none;left:580;top:300;position:absolute">
    <?=$row->TIME_STAMP_DAY." Gün ve".$row->TIME_STAMP_HOUR?> Saat te herhangi bir çağrı yapmamışsınız.
  </div>

     <script language=javascript>
       function show_hide(layer_id)
       {
           if(document.all[layer_id].style.display=='none'){
               document.all[layer_id].style.display='';
           }else{
               document.all[layer_id].style.display='none';
           }
       }
       function popup(popup_url, popup_name, winWidth, winHeight)
        {
            scrL = (screen.width - winWidth) / 2;
            scrT = ((screen.height - winHeight) / 2);
            yeni = window.open(popup_url, popup_name, "width="+winWidth+",height="+winHeight+",status=yes,resizable=yes,scrollbars=yes,top="+scrT+",left="+scrL);
            //yeni.focus();
        }
   </script>
   <?table_header("Arama Sayısı","50%")?>
   <center>
               <form action="report_personal.php" name="time_sel">
                   1 Gün Geri
                   <a href="report_personal.php?day=<?=$day-1?>">
                   <img src="/images/geri.gif" border="0"></a>
                   <a href="report_personal.php?day=<?=$day+1?>">
                   <img src="/images/ileri.gif" border=0></a>
                   1 Gün İleri
               </form>
   </center>
   <table cellpadding="0" cellspacing="0" border="1" width="100%"  >
            <td width="10%"></td>
            <?
            for($k=0;$k<=4;$k++){
                  echo "<td width=15% align=center class=\"td1_koyu\" >".$days[$k]."/".$months[$k]."</td>";
                  //echo " ".$day_week[$weekdays[$k]]."</td>\n";
            }
            ?>
       </tr>
       <tr>
            <td width="10%"></td>
            <?
            for($k=0;$k<=4;$k++){
                  //echo "<td width=15% align=center class=\"td1_koyu\" >".$days[$k]."/".$months[$k];
                  echo "<td align=center> ".$day_week[$weekdays[$k]]."</td>\n";
           }
            ?>
       </tr>
   </table>
   <div id="early" style="display:none">
         <table cellpadding="0" cellspacing="0" border="0" >
            <?for($i=0;$i<=5;$i++){?>
                <tr class=<?=bgc($i)?> width="10%">
                     <td class="td1_koyu" width="10%"><?=$hours[$i]?></td>
                     <?for($j=0;$j<=4;$j++){ ?>
                         <td align=center class="td1_koyu" width=15%>
                                <span onclick="javascript:popup('day_details.php?recs=<?=$val[$i][$days[$j]][0];?>&gun=<?=$days[$j]."&saat=".$i."&ordn=".$origs."&authc=".$auth_c."&e_day=".$end_day."&s_day=".$start_day;?>','details',300,300)" style="cursor:hand">
                                    <?        
                                    if($val[$i][$days[$j]][0]!=""){
                                        //echo $val[$i][$days[$j]][0];
                                        echo $val[$i][$days[$j]][1];
                                    }else{
                                        echo "-";
                                    }?>
                                </span>
                       </td> 
                    <?}?>
                  </tr>
             <?}?>
          </table>
       </div>
   <a href="javascript:show_hide('early')">Bu saatten öncekiler</a><br>
       <div id=middle_of_day>
           <table cellpadding="0" cellspacing="0" border="1" >
                  <?for($i=6;$i<=19;$i++){ ?>
                        <tr class=<?=bgc($i)?>>
                              <td class="td1_koyu" width="10%"><?=$hours[$i]?></td>
                                    <?for($j=0;$j<=4;$j++){ ?>
                                            <td align=center class="td1_koyu" width=15%>
                                                <span onclick="javascript:popup('day_details.php?recs=<?=$val[$i][$days[$j]][0];?>&gun=<?=$days[$j]."&saat=".$i."&ordn=".$origs."&authc=".$auth_c."&e_day=".$end_day."&s_day=".$start_day;?>','details',300,300)" style="cursor:hand">
                                                     <?        
                                                     if($val[$i][$days[$j]][0]!=""){
                                                         //echo $val[$i][$days[$j]][0];
                                                         echo $val[$i][$days[$j]][1];
                                                     }else{
                                                         echo "-";
                                                     }
                                                     ?>
                                             </span>
                                          </td> 
                                   <?}?>
                            </td> 
                      </tr>
                   <?}?>
             </table>
       </div>
   <a href="javascript:show_hide('late')">Bu saatten sonrakiler</a>
       <div id="late" style="display:none">
           <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <?for($i=20;$i<=23;$i++){?>
                   <tr class=<?=bgc($i)?>>
                      <td class="td1_koyu" width="10%"><?=$hours[$i]?></td>
                                <? for($j=0;$j<=4;$j++){ ?>
                                     <td align=center class="td1_koyu" width=15%>
                                <span onclick="javascript:popup('day_details.php?recs=<?=$val[$i][$days[$j]][0];?>&gun=<?=$days[$j]."&saat=".$i."&ordn=".$origs."&authc=".$auth_c."&e_day=".$end_day."&s_day=".$start_day;?>','details',300,300)" style="cursor:hand">
                                      <?        
                                          if($val[$i][$days[$j]][0]!=""){
                                             //echo $val[$i][$days[$j]][0];
                                              echo $val[$i][$days[$j]][1];
                                          }else{
                                              echo "-";
                                          }
                                      ?>      
                                 </span>
                                     </td> 
                               <?}?>     
                         </td> 
                    </tr>
                 <?}?>
             </table>
       </div>
     <?table_footer();?>
  <?   page_footer(0);?>
   </body>
   </html>
