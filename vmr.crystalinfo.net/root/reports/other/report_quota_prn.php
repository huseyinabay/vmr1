<? require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer(); 
   $conn = $cdb->getConnection();
   require_valid_login();

  if (right_get("SITE_ADMIN")){
    //Site admin hakkı varsa herşeyi görebilir.  
    //Site id gelmemişse kişinin bulunduğu site raporu alınır.
    if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
  }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
    // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
    $SITE_ID = $_SESSION['site_id'];
  }else{
    print_error("Bu sayfayı Görme Hakkınız Yok!!!");
    exit;
  }

    $sql_str1="SELECT SITE_NAME, MAX_ACCE_DURATION FROM SITES WHERE SITE_ID = ".$SITE_ID; 
    if (!($cdb->execute_sql($sql_str1,$result1,$error_msg))){
        print_error($error_msg);
        exit;
    }
    if (mysqli_num_rows($result1)>0){
    $row1 = mysqli_fetch_object($result1);
        $company = $row1->SITE_NAME;
        $max_acc_dur =  ($row1->MAX_ACCE_DURATION)*60;
    }else{
    print_error("Site paramatreleri bulunamadı.");
    exit;
  }
  
  $local_country_code = get_country_code($SITE_ID);
    
  function show_me($fld1,$selected_fld){
    if ($selected_fld=='PRICE'){
      $MyRetVal = write_price($fld1);
    }else{
      $MyRetVal = calculate_all_time($fld1);
    }
    return $MyRetVal;
  }

  function show_diff($fld1,$fld2,$selected_fld){
    $MyRetVal = $fld1 - $fld2;
    if ($MyRetVal > 0){
      if ($selected_fld=='PRICE'){
        $MyRetVal = write_price($MyRetVal);
      }else{
        $MyRetVal = calculate_all_time($MyRetVal);
      }
    }else{
      $MyRetVal = 0;
    }
    return $MyRetVal;
  }
 ?>
 <form name="sort_me" method="post" action="">
         <input type="hidden" name="SITE_ID" value="<?=$SITE_ID?>">
         <input type="hidden" name="MY_DATE" value="<?=$MY_DATE?>">
         <input type="hidden" name="t0" value="<?=$t0?>">         
       <input type="hidden" name="t1" value="<?=$t1?>">         
    <input type="hidden" name="type" value="<?=$type?>">
  </form>
 
 <?

  cc_page_meta();
  echo "<center>";

   $report_type="Kota Raporu";

   if ($act == "src") {
     $kriter = "";
     
     //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
     $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.SITE_ID"    ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
     $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ERR_CODE"    ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
     $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.CALL_TYPE"   ,  "=",  "1"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
     $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.DURATION"    ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
     $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ORIG_DN"    ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.
     //Tarih Kontrolü burada başlıyor.

       switch($MY_DATE){
       case 'c':
        $t0 = date("Y-m-01");
        $t1 = date("Y-m-d");
        $kriter .= $cdb->field_query($kriter, "DATE_FORMAT(MY_DATE,'%Y-%m-%d')"     ,">=",  "'$t0'");
        $kriter .= $cdb->field_query($kriter, "DATE_FORMAT(MY_DATE,'%Y-%m-%d')"     ,"<=",  "'$t1'");
        break;
      case 'g' :
        $t0 = day_of_last_month("first");
        $t1 = day_of_last_month("last");
        $kriter .= $cdb->field_query($kriter, "DATE_FORMAT(MY_DATE,'%Y-%m-%d')"     ,">=",  "'$t0'");
        $kriter .= $cdb->field_query($kriter, "DATE_FORMAT(MY_DATE,'%Y-%m-%d')"     ,"<=",  "'$t1'");
        break;
        default:  
    }
    //**Bunlar birlikte olmalı ve bu sırada olmalı.
    $CDR_MAIN_DATA = getTableName($t0,$t1);
    if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";
    //**	

    if($report_quota=="") $report_quota = 'quota_incity';
      
    switch ($report_quota){
      case 'quota_incity' :
        $crt_mtr_code_type = " AND LocationTypeid= '0'";
        $QUOTA_FIELD = 'INCITY_LIMIT';
        $selected_fld = 'DURATION';
        $header = "Şehiriçi Konuşma Kotasını Aşanlar";
        break;
      case 'quota_intercity' :
        $crt_mtr_code_type = " AND LocationTypeid= '1'";
        $QUOTA_FIELD = 'INTERCITY_LIMIT';
        $selected_fld = 'DURATION';
        $header = "Şehirlerarası Konuşma Kotasını Aşanlar";
        break;
      case 'quota_gsm' :
        $crt_mtr_code_type = " AND LocationTypeid= '2'";
        $QUOTA_FIELD = 'GSM_LIMIT';
        $selected_fld = 'DURATION';
        $header = "GSM Kotasını Konuşma Aşanlar";
        break;
      case 'quota_intern' :
        $crt_mtr_code_type = " AND LocationTypeid= '3'";
        $QUOTA_FIELD = 'INTERNATIONAL_LIMIT';
        $selected_fld = 'DURATION';
        $header = "Uluslararası Konuşma Kotasını Aşanlar";
        break;
      case 'quota_price' :
        $crt_mtr_code_type = "";
        $QUOTA_FIELD = 'PRICE_LIMIT';
        $selected_fld = 'PRICE';
        $header = "Ücret Kotasını Aşanlar";
        break;
      default:
        echo "Hatalı Durum Oluştu";
    }
    
if($type=="") $type= 'ext';
     switch ($type){
     case 'ext':
      $field1_name="Dahili";$width1="25%";
      //Direk Dahililere Yapılan Atama
      $sql_str = "SELECT QUOTAS.QUOTA_NAME,QUOTAS.$QUOTA_FIELD,EXTENTIONS.EXT_NO,
              EXTENTIONS.DESCRIPTION,QUOTA_ASSIGNS.OBJ_ID
            FROM QUOTA_ASSIGNS
              INNER JOIN QUOTAS ON QUOTA_ASSIGNS.QUOTA_ID = QUOTAS.QUOTA_ID
              INNER JOIN EXTENTIONS ON QUOTA_ASSIGNS.OBJ_ID = EXTENTIONS.EXT_ID
              WHERE QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID = 1 AND QUOTAS.SITE_ID =".$SITE_ID;
      if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
              print_error($error_msg);
                exit;
            }
      $i=0;
      if (mysqli_num_rows($result)>0){
        while($row = mysqli_fetch_array($result)){
          if($row["$QUOTA_FIELD"] > 0){
            $arr_user_name[$i] = $row["QUOTA_NAME"];
            $arr_user_quota[$i] = $row["$QUOTA_FIELD"];
            $arr_user_who[$i] = $row["EXT_NO"];
            $arr_user_id[$i] = $row["OBJ_ID"];
            $arr_user_desc[$i] = $row["DESCRIPTION"];
            $i++;
          }
        }
      }

      //Bir departmanın üyelerine yapılan atama
      $sql_str = "SELECT QUOTAS.QUOTA_NAME,QUOTAS.$QUOTA_FIELD,QUOTA_ASSIGNS.OBJ_ID
            FROM QUOTA_ASSIGNS
              INNER JOIN QUOTAS ON QUOTA_ASSIGNS.QUOTA_ID = QUOTAS.QUOTA_ID
              INNER JOIN DEPTS ON QUOTA_ASSIGNS.OBJ_ID = DEPTS.DEPT_ID
              WHERE QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID = 3 AND QUOTAS.SITE_ID =".$SITE_ID;
      if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
              print_error($error_msg);
                exit;
            }
      $i=0;
      if (mysqli_num_rows($result)>0){
        while($row = mysqli_fetch_array($result)){
          if($row["$QUOTA_FIELD"] > 0){
            $arr_dept_name[$i] = $row["QUOTA_NAME"];
            $arr_dept_quota[$i] = $row["$QUOTA_FIELD"];
            $arr_dept_id[$i] = $row["OBJ_ID"];
            $i++;
          }
        }
      }
      $k = sizeof($arr_dept_id);
      $j = 0;
      //Yapılan Atamalar departmanın herbir üyesi olan dahililere dağıtılsın.
      if ($k > 0) {
        for($i=0;$i<$k;$i++){
          $sql_str = "SELECT EXT_ID,EXT_NO,DESCRIPTION FROM EXTENTIONS WHERE DEPT_ID = ".$arr_dept_id[$i]." AND SITE_ID = ".$SITE_ID;
          if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                    exit;
                }
          if (mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
              $arr_dept_user_name[$j] = $arr_dept_name[$i];
              $arr_dept_user_quota[$j] = $arr_dept_quota[$i];
              $arr_dept_user_who[$j] = $row["EXT_NO"];
              $arr_dept_user_id[$j] = $row["EXT_ID"];
              $arr_dept_user_desc[$j] = $row["DESCRIPTION"];
              $j++;
            }
          }
        }
      }

      if (sizeof($arr_user_id)>0 && sizeof($arr_dept_user_id)>0){
        $ext_and_dept = array_intersect($arr_user_id, $arr_dept_user_id);
        $ext_dif_dept = array_diff($arr_user_id, $arr_dept_user_id);
        $dept_dif_ext = array_diff($arr_dept_user_id,$arr_user_id);
      }else if(sizeof($arr_user_id)<=0){
        $ext_and_dept = $arr_dept_user_id;
      }else if(sizeof($arr_dept_user_id<=0)){
        $ext_and_dept = $arr_user_id;
      }
      
      $m = 1; // Asıl oluşacak olan array id'si
      $k=0;
      while ($k < sizeof($ext_and_dept)){
            $t = key($ext_and_dept);
        $arr_name[$m]   = $arr_user_name[$t];
        $arr_quota[$m]   = $arr_user_quota[$t];
        $arr_who[$m]  = $arr_user_who[$t];
        $arr_id[$m]   = $arr_user_id[$t];
        $arr_desc[$m]   = $arr_user_desc[$t];
        next($ext_and_dept);
        $k++;
        $m++;
      }
      $k=0;
      while ($k < sizeof($ext_dif_dept)){
            $t = key($ext_dif_dept);
        $arr_name[$m]   = $arr_user_name[$t];
        $arr_quota[$m]   = $arr_user_quota[$t];
        $arr_who[$m]  = $arr_user_who[$t];
        $arr_id[$m]   = $arr_user_id[$t];
        $arr_desc[$m]   = $arr_user_desc[$t];
        next($ext_dif_dept);
        $k++;
        $m++;
      }
      $k=0;
      while ($k < sizeof($dept_dif_ext)){
            $t = key($dept_dif_ext);
        $arr_name[$m]   = $arr_dept_user_name[$t];
        $arr_quota[$m]   = $arr_dept_user_quota[$t];
        $arr_who[$m]  = $arr_dept_user_who[$t];
        $arr_id[$m]   = $arr_dept_user_id[$t];
        $arr_desc[$m]   = $arr_dept_user_desc[$t];
        next($dept_dif_ext);
        $k++;
        $m++;
      }

      $k = sizeof($arr_name);
      $t = 0; //Yeni oluşacak array için gerekli key.
        if ($k > 0){
          for ($i=1;$i <= $k;$i++){
            $sql_str = "SELECT SUM(DURATION) AS DURATION, SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                        FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                        WHERE ".$kriter." AND ORIG_DN = ".$arr_who[$i]." ".$crt_mtr_code_type;

          $sql_str .= " GROUP BY CDR_MAIN_DATA.ORIG_DN" ;
              //echo $sql_str;exit;
           if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                    exit;
                 }
            $row = mysqli_fetch_object($result);
            if(mysqli_num_rows($result) > 0){
             $t++;
            if ($row->$selected_fld > $arr_quota[$i]){
              $arr_last_name[$t] = $arr_name[$i];
              $arr_last_quota[$t] = $arr_quota[$i];
              $arr_last_fld[$t] = $row->$selected_fld;
              $arr_last_who[$t] = $arr_who[$i];
              $arr_last_desc[$t] = $arr_desc[$i];
             }
          }
        }
      }  
      break;
     case 'auth':
      $field1_name="Auth. Kodu";$width1="25%";      
      $sql_str = "SELECT QUOTAS.QUOTA_NAME,QUOTAS.$QUOTA_FIELD,AUTH_CODES.AUTH_CODE,QUOTA_ASSIGNS.OBJ_ID,
                    AUTH_CODES.AUTH_CODE_DESC
                  FROM QUOTA_ASSIGNS
                    INNER JOIN QUOTAS ON QUOTA_ASSIGNS.QUOTA_ID = QUOTAS.QUOTA_ID
                    INNER JOIN AUTH_CODES ON QUOTA_ASSIGNS.OBJ_ID = AUTH_CODES.AUTH_CODE_ID
                  WHERE QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID = 4 AND QUOTAS.SITE_ID = ".$SITE_ID;
      if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
              print_error($error_msg);
                exit;
            }
      $i=1;
      if (mysqli_num_rows($result)>0){
        while($row=mysqli_fetch_array($result)){
          if($row["$QUOTA_FIELD"] > 0){
            $arr_name[$i]   = $row["QUOTA_NAME"];
            $arr_quota[$i]   = $row["$QUOTA_FIELD"];
            $arr_who[$i]  = $row["AUTH_CODE"];
            $arr_id[$i]   = $row["OBJ_ID"];
            $arr_desc[$i]   = $row["AUTH_CODE_DESC"];
            $i++;
          }
        }
      }
      $k = sizeof($arr_name);
      $t = 0;
        if ($k > 0){
          for ($i=1;$i <= $k;$i++){
            $sql_str = "SELECT SUM(DURATION) AS DURATION, SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                  FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                  WHERE ".$kriter." AND CDR_MAIN_DATA.AUTH_ID = ".$arr_who[$i]." ".$crt_mtr_code_type;

           $sql_str .= " GROUP BY AUTH_CODE" ; 
           //echo $sql_str;exit;
           if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                    exit;
                }
            $row = mysqli_fetch_object($result);
            if(mysqli_num_rows($result) > 0){
             $t++;
            if ($row->$selected_fld > $arr_quota[$i]){
              $arr_last_name[$t] = $arr_name[$i];
              $arr_last_quota[$t] = $arr_quota[$i];
              $arr_last_fld[$t] = $row->$selected_fld;
              $arr_last_who[$t] = $arr_who[$i];
              $arr_last_desc[$t] = $arr_desc[$i];
             }
          }
        }
      }    
      break;   
     case 'dept':
      $field1_name="Departman";$width1="25%";
      $sql_str = "SELECT QUOTAS.QUOTA_NAME,QUOTAS.$QUOTA_FIELD,DEPTS.DEPT_NAME,QUOTA_ASSIGNS.OBJ_ID
                  FROM QUOTA_ASSIGNS
                    INNER JOIN QUOTAS ON QUOTA_ASSIGNS.QUOTA_ID = QUOTAS.QUOTA_ID
                    INNER JOIN DEPTS ON QUOTA_ASSIGNS.OBJ_ID = DEPTS.DEPT_ID
                  WHERE QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID = 2 AND QUOTAS.SITE_ID = ".$SITE_ID;

      if (!($cdb->execute_sql($sql_str, $rs, $error_msg))){
              print_error($error_msg);
                exit;
            }
      $i=1;

      while($row = mysqli_fetch_array($rs)){
        if($row["$QUOTA_FIELD"] > 0){
          $arr_name[$i]   = $row["QUOTA_NAME"];
          $arr_id[$i]   = $row["OBJ_ID"];
          $arr_quota[$i]   = $row["$QUOTA_FIELD"];
          $arr_who[$i]   = $row["DEPT_NAME"];
          $arr_desc[$i]   ='';
          $i++;
        }
      }
    
      $k = sizeof($arr_name);
      $t = 0;
        if ($k > 0){
          for ($i=1;$i <= $k;$i++){
            $sql_str = "SELECT SUM(DURATION) AS DURATION, EXTENTIONS.DEPT_ID,SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                        FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                          LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
                          LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                        WHERE ".$kriter." AND EXTENTIONS.DEPT_ID = '".$arr_id[$i]."' ".$crt_mtr_code_type;

           $sql_str .= " GROUP BY DEPT_ID" ; 
           //echo $sql_str;exit;
          if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                    exit;
                }
           $row = mysqli_fetch_object($result);
           if(mysqli_num_rows($result) > 0){
            $t++;
            if ($row->$selected_fld > $arr_quota[$i]){
              $arr_last_name[$t] = $arr_name[$i];
              $arr_last_quota[$t] = $arr_quota[$i];
              $arr_last_fld[$t] = $row->$selected_fld;
              $arr_last_who[$t] = $arr_who[$i];
              $arr_last_desc[$t] = $arr_desc[$i];
             }
           }
        } 
      }  
      break;
    default:
      echo "Hatalı Durum Oluştu--";
      die;
      break;   
   }
?>
<br><br>
<table width="85%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" class="rep_header" align="center">
          <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD><a href="http://www.crystalinfo.net" target="_blank"><img border="0" SRC="<?=IMAGE_ROOT?>logo2.gif" ></a></TD>
              <TD width="50%" align=center CLASS="header"><?echo $company;?><BR> KOTA RAPORU<br><?=$header?></TD>
              <TD width="25%" align=right><img SRC="<?=IMAGE_ROOT?>company.gif"></TD>
            </TR>
          </TABLE>
      </td>
  </tr>
    <tr>
     <td>
     <table width="100%" cellspacing=0 cellpadding=0>
    <tr>
      <td width="50%" class="rep_header" align="left">
         <?if($t0!=""){?>
          Tarih (<?=date("d/m/Y",strtotime($t0))?>
       <?if($t1!=""){?>
         <?echo (" - ".date("d/m/Y",strtotime($t1)));}?>
    )
       <?}?>
       </td><td width="50%" align="right"> 
      <table cellspacing=0 cellpadding=0>
        <tr>
          <td><img src="<?=IMAGE_ROOT?>report/top02.gif" border=0></td>
          <td><a href="javascript:history.back(1);"><img src="<?=IMAGE_ROOT?>report/geri.gif" border=0 title="Geri"></a></td>
          <td><a href="javascript:history.forward(1);"><img src="<?=IMAGE_ROOT?>report/ileri.gif" border=0 title="İleri"></a></td>
          <td><a href="javascript:document.all('sort_me').submit();"><img src="<?=IMAGE_ROOT?>report/yenile.gif" border=0 title="Yenile"></a></td>
          <td><a href="javascript:window.print();"><img src="<?=IMAGE_ROOT?>report/print.gif" border=0 title="Yazdır"></a></td>
          <td><img src="<?=IMAGE_ROOT?>report/top01.gif" border=0></td>
        </tr>
      </table>
      </td></tr></table>
      </td>
  </tr>
  <tr>
    <td>
      <table width="100%" border="0" bgcolor="#C7C7C7" cellspacing="1" cellpadding="0">
          <tr>
              <td class="rep_table_header" width="<?=$width1;?>"><?echo $field1_name;?></td>
          <td class="rep_table_header" width="15%">Kota Adı</td>
          <td class="rep_table_header" width="15%">Kota Miktarı</td>
          <td class="rep_table_header" width="15%">Konuşulan Miktar</td>
          <td class="rep_table_header" width="15%">Aşılan Miktar</td>
          <td class="rep_table_header" width="15%">Aşım Yüzdesi</td>
          </tr>
        <tr>
          <td colspan="6" bgcolor="#000000" height="1"></td>
        </tr>
      <? 
        $k=0;
        if (sizeof($arr_last_name) > 0){
             while($k < sizeof($arr_last_name)){
               $i = key($arr_last_name);
               $bg_color = "E4E4E4";   
               if($i%2) $bg_color ="FFFFFF";
               echo " <tr  BGCOLOR=$bg_color>";
               echo " <td class=\"rep_td\">&nbsp;<b>".$arr_last_who[$i]."  ".$arr_last_desc[$i]."</b></td>";
               echo " <td class=\"rep_td\">".$arr_last_name[$i]."</td>";
               echo " <td class=\"rep_td\">".show_me($arr_last_quota[$i],$selected_fld)."</td>";
               echo " <td class=\"rep_td\">".show_me($arr_last_fld[$i],$selected_fld)."</td>";
               echo " <td class=\"rep_td\">".show_diff($arr_last_fld[$i],$arr_last_quota[$i],$selected_fld)."</td>";
               echo " <td class=\"rep_td\">% ".percent($arr_last_fld[$i],$arr_last_quota[$i])."</td>";
               next($arr_last_name);
               $k++;
             }
         }
      ?>
      </table>
    </td>
  </tr>
  <tr height="20">
    <td></td>
  </tr>
<?
}
?>      
</table>  
