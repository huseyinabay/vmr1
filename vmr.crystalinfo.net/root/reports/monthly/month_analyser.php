<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer(); 
   $conn = $cdb->getConnection();
  
   $YEAR  = date("Y",time(NULL));
   $MONTH = date("n", time(NULL))-1;
   if($MONTH == 0){$MONTH = 12; $YEAR = $YEAR - 1; }
   
   $str_sql = "SELECT SITE_ID FROM SITES";
   $cdb->execute_sql($str_sql, $result, $err_msg);
 
   while($rowSite = mysqli_fetch_array($result)){
     $SITE_ID = $rowSite['SITE_ID']; 
     $sql_str1="SELECT SITE_NAME, MAX_ACCE_DURATION FROM SITES WHERE SITE_ID=".$SITE_ID;
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

    $reportType = "Çağrı Tipi";
    $sqlCallStr = "";
    $sqlCallStr = "SELECT CDR_MAIN_DATA.LocationTypeid AS TYPE, SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                      FROM CDR_MAIN_DATA
                      WHERE SITE_ID = $SITE_ID AND ERR_CODE = '0' AND CALL_TYPE = '1' AND DURATION < '$max_acc_dur' 
					                 AND CDR_MAIN_DATA.ORIG_DN <> '' AND 
									 DATE_FORMAT(MY_DATE,'%Y') = '$YEAR' AND
                                     DATE_FORMAT(MY_DATE,'%m') = '$MONTH'
                      GROUP BY LocationTypeid";
    //echo $sqlCallStr."<br><br>";
    $cdb->execute_sql($sqlCallStr, $resultCall, $err_msg);
    if (mysqli_num_rows($resultCall) > 0) {
      while($rowCall = mysqli_fetch_object($resultCall)){
	    if ($rowCall->TYPE > 3)
          $data[4] += $rowCall->PRICE;
        else
          $data[$rowCall->TYPE] = $rowCall->PRICE;
      }
	  $sqlCallInsert = "";
      $sqlCallInsert = "INSERT INTO MONTHLY_ANALYSE VALUES ('$SITE_ID','$reportType','$YEAR','$MONTH','' , '',
	                            '$data[0]', '$data[1]', '$data[2]','$data[3]','$data[4]')";
      $cdb->execute_sql($sqlCallInsert, $resultCallInsert, $err_msg);
    }
    unset($data);

    $reportType = "";
    $reportType = "department";
    $sqlDeptExtStr = "";
    $sqlDeptExtStr = "SELECT LocationTypeid AS TYPE, ORIG_DN, SUM(CDR_MAIN_DATA.PRICE) AS TOTAL 
                      FROM CDR_MAIN_DATA
                      WHERE CDR_MAIN_DATA.SITE_ID = '$SITE_ID' AND ERR_CODE = '0' AND CALL_TYPE = '1' 
					        AND DURATION < '$max_acc_dur' AND CDR_MAIN_DATA.ORIG_DN <> '' 
							AND DATE_FORMAT(MY_DATE,'%Y') = '$YEAR' AND DATE_FORMAT(MY_DATE,'%c') = '$MONTH'
                      GROUP BY ORIG_DN, TYPE";
     //echo $sqlDeptExtStr."<br><br>";
     $reportType = "";
     $reportType = "dahili";
     $cdb->execute_sql($sqlDeptExtStr, $resultDeptExt, $err_msg);
     if (mysqli_num_rows($resultDeptExt) > 0) {
       while($rowExt = mysqli_fetch_object($resultDeptExt)){  
         $my_dept = get_orig_dept_id($rowExt->ORIG_DN, $SITE_ID);
         if ($my_dept=="") $my_dept = '-2';
         if($rowExt->ORIG_DN != ""){
           if($rowExt->TYPE == 0){
             $dataExt[$my_dept][$rowExt->ORIG_DN][0] += $rowExt->TOTAL;
			 $dataDepart[$my_dept][0]      += $rowExt->TOTAL;
           }else if($rowExt->TYPE == 1){
             $dataExt[$my_dept][$rowExt->ORIG_DN][1] += $rowExt->TOTAL;
		     $dataDepart[$my_dept][1]      += $rowExt->TOTAL;
           }else if($rowExt->TYPE == 2){
             $dataExt[$my_dept][$rowExt->ORIG_DN][2] += $rowExt->TOTAL;
			 $dataDepart[$my_dept][2]      += $rowExt->TOTAL;
           }else if($rowExt->TYPE == 3){
             $dataExt[$my_dept][$rowExt->ORIG_DN][3] += $rowExt->TOTAL;
		     $dataDepart[$my_dept][3]      += $rowExt->TOTAL;
           }else{
             $dataExt[$my_dept][$rowExt->ORIG_DN][4] += $rowExt->TOTAL;
		     $dataDepart[$my_dept][4]      += $rowExt->TOTAL;
           }
         }else{
           if($rowExt->TYPE == 0){
             $dataExt['-2']['-2'][0]    += $rowExt->TOTAL;
		     $dataDepart['-2'][0] += $rowExt->TOTAL;
           }else if($rowExt->TYPE == 1){
             $dataExt['-2']['-2'][1]    += $rowExt->TOTAL;
			 $dataDepart['-2'][1] += $rowExt->TOTAL;
           }else if($rowExt->TYPE == 2){
             $dataExt['-2']['-2'][2]    += $rowExt->TOTAL;
			 $dataDepart['-2'][2] += $rowExt->TOTAL;
           }else if($rowExt->TYPE == 3){
             $dataExt['-2']['-2'][3]    += $rowExt->TOTAL;
			 $dataDepart['-2'][3] += $rowExt->TOTAL;
           }else{
             $dataExt['-2']['-2'][4]    += $rowExt->TOTAL;
		     $dataDepart['-2'][4] += $rowExt->TOTAL;
           }
         }
       }
       foreach ($dataExt as $key2 => $value2) {
         foreach($value2 as $keynew => $valuenew){
           $sqlExtInsert = "";
           $sqlExtInsert = "INSERT INTO MONTHLY_ANALYSE VALUES ('$SITE_ID','$reportType','$YEAR','$MONTH','$keynew','$key2', 
		                          '".$value2[$keynew][0]."','".$value2[$keynew][1]."','".$value2[$keynew][2]."','".$value2[$keynew][3]."','".$value2[$keynew][4]."')";
           //echo $sqlExtInsert."<br><br>";
           //$cdb->execute_sql($sqlExtInsert, $resultExtInsert, $err_msg);
         }
       }
       unset($dataExt);
       $reportType = "department";
       foreach($dataDepart as $key=>$value) {
         $sqlDeptInsert = "";
         $sqlDeptInsert = "INSERT INTO MONTHLY_ANALYSE VALUES ('$SITE_ID','$reportType','$YEAR','$MONTH','','$key',
                                       '".$dataDepart[$key][0]."','".$dataDepart[$key][1]."','".$dataDepart[$key][2]."','".$dataDepart[$key][3]."','".$dataDepart[$key][4]."')";
         //echo $sqlDeptInsert."<br><br>";
         //$cdb->execute_sql($sqlDeptInsert, $resultDeptInsert,$err_msg);
        }
        unset($dataDepart);
     }

     $sqlTrunkStr = "";
     $sqlTrunkStr = "SELECT TER_TRUNK_MEMBER,TO_PROVIDER_ID,".
                        "  SUM(CDR_MAIN_DATA.PRICE) AS TOTAL".
                        "  FROM CDR_MAIN_DATA ".
                        "  WHERE CDR_MAIN_DATA.SITE_ID = '$SITE_ID' AND".
                        "        ERR_CODE = '0' AND CALL_TYPE = '1' AND".
                        "        DURATION < '$max_acc_dur' AND ".
                        "        CDR_MAIN_DATA.ORIG_DN <> '' AND ".
                        "        TIME_STAMP_YEAR = '$YEAR' AND".
                        "        TIME_STAMP_MONTH = '$MONTH' ".
                        "  GROUP BY TER_TRUNK_MEMBER, TO_PROVIDER_ID";
     $cdb->execute_sql($sqlTrunkStr, $resultTrunk, $err_msg);
     //echo $sqlTrunkStr."<br>";
     if (mysqli_num_rows($resultTrunk) > 0) {
       while ($rowTrunk = mysqli_fetch_object($resultTrunk)){
          $trunk[$rowTrunk->TER_TRUNK_MEMBER][$rowTrunk->TO_PROVIDER_ID] += $rowTrunk->TOTAL;
       }
       $reportType = "";
       $reportType = "trunks";
       foreach ($trunk as $key=>$value){
         foreach ($value as $keyx=>$valuex){
           $sqlTrunkInsert = "";
           $sqlTrunkInsert = "INSERT INTO TRUNK_ANALYSE VALUES('$SITE_ID','$key','$YEAR','$MONTH','$keyx','".$trunk[$key][$keyx]."')";
           //echo $sqlTrunkInsert."<br><br>";
           //$cdb->execute_sql($sqlTrunkInsert, $resultTrunkInsert, $err_msg);
         }
       }
       unset($trunk);
     }
     
	 $sqlStr = "SELECT ORIG_DN, CALL_TYPE, DURATION ".
               " FROM CDR_MAIN_DATA ". 
               " WHERE CDR_MAIN_DATA.SITE_ID = '$SITE_ID' AND ".
               " ERR_CODE = '0' AND ".
               " DURATION < '$max_acc_dur' AND ".
               " CDR_MAIN_DATA.ORIG_DN <> '' AND ".
               "       TIME_STAMP_YEAR = '$YEAR' AND ".
               "       TIME_STAMP_MONTH = '$MONTH' ";
//   echo $sqlStr;
     $cdb->execute_sql($sqlStr, $resultDurCount, $err_msg);
     
     while ($rowCallDur = mysqli_fetch_object($resultDurCount)){
        $dur_count_orig[$rowCallDur->ORIG_DN][$rowCallDur->CALL_TYPE][0]+=
                                               $rowCallDur->DURATION;
        $dur_count_orig[$rowCallDur->ORIG_DN][$rowCallDur->CALL_TYPE][1]+= 1;
        
        $my_dept = get_orig_dept_id($rowCallDur->ORIG_DN, $SITE_ID);
        if ($my_dept == "")
            $my_dept = 0;
//         echo $my_dept."xx";
        $dur_count_dept[$my_dept][$rowCallDur->CALL_TYPE][0] +=
                                               $rowCallDur->DURATION;

        $dur_count_dept[$my_dept][$rowCallDur->CALL_TYPE][1] += 1;

        $dur_count_sum[$rowCallDur->CALL_TYPE][0] += $rowCallDur->DURATION;
        $dur_count_sum[$rowCallDur->CALL_TYPE][1] += 1;
     }
     if (mysqli_num_rows($resultDurCount) > 0) {
        foreach($dur_count_sum as $keysum=>$valuesum)
          $sqlDurCountInsert = "";
          $sqlDurCountInsert = "INSERT INTO MONTHLY_DUR_COUNT".
                                 " VALUES (".
                                 " '$SITE_ID' ,".
                                 " 'general',".
                                 " '$MONTH' , ".
                                 " '$YEAR',  ".
                                 " '',".
                                 " '',".
                                 " '' ,".
                                 " '$keysum' ,".
                                 " '".$valuesum[0]."' ,".
                                 " '".$valuesum[1]."'".             
                                 " )";
        echo $sqlDurCountInsert."<br><br>";
      $cdb->execute_sql($sqlDurCountInsert,$resultDurCountInsert,$err_msg);
       
       foreach ($dur_count_orig as $key=>$value){
         foreach ($value as $keynew=>$value2){
          $sqlDurCountInsert = "";
          $sqlDurCountInsert = "INSERT INTO MONTHLY_DUR_COUNT".
                                 " VALUES (".
                                 " '$SITE_ID' ,".
                                 " 'dahili',".
                                 " '$MONTH' , ".
                                 " '$YEAR',  ".
                                 " '',".
                                 " '$key',".
                                 " '' ,".
                                 " '$keynew' ,".
                                 " '".$value2[0]."' ,".
                                 " '".$value2[1]."'".             
                                 " )";
//         echo $sqlDurCountInsert."<br><br>";
         $cdb->execute_sql($sqlDurCountInsert, $resultDurCountInsert,$err_msg);
         }
       }
        foreach ($dur_count_dept as $key3=>$value3){
          foreach ($value3 as $keynew4=>$value4){
          $sqlDurCountInsert = "";
          $sqlDurCountInsert = "INSERT INTO MONTHLY_DUR_COUNT".
                                 " VALUES (".
                                 " '$SITE_ID' ,".
                                 " 'department' ,".
                                 " '$MONTH' , ".
                                 " '$YEAR',  ".
                                 " '',".
                                 " '',".
                                 " '$key3' ,".
                                 " '$keynew4',".
                                 " '".$value4[0]."' ,".
                                 " '".$value4[1]."'".             
                                 " )";
//         echo $sqlDurCountInsert."<br><br>";
          $cdb->execute_sql($sqlDurCountInsert,$resultDurCountInsert,$err_msg);
         }
       }
       unset($dur_count_dept);
       unset($dur_count_orig);
     }

     $sqlDurCountStr = "";
     $sqlDurCountStr = "SELECT TER_TRUNK_MEMBER, CALL_TYPE, DURATION ".
                       "  FROM CDR_MAIN_DATA ".
                       "  WHERE CDR_MAIN_DATA.SITE_ID = '$SITE_ID' AND".
                       "        ERR_CODE = '0' AND ".
                       "        DURATION < '$max_acc_dur' AND ".
                       "        CDR_MAIN_DATA.ORIG_DN <> '' AND ".
                       "        TIME_STAMP_YEAR = '$YEAR' AND ".
                       "       TIME_STAMP_MONTH = '$MONTH' ";
     $cdb->execute_sql($sqlDurCountStr , $resultDurCount, $err_msg);
//      echo $sqlDurCountStr;

     while ($rowCallDur = mysqli_fetch_object($resultDurCount)){
        $dur_count_trunk[$rowCallDur->TER_TRUNK_MEMBER]
                        [$rowCallDur->CALL_TYPE][0]+= $rowCallDur->DURATION;
        $dur_count_trunk[$rowCallDur->TER_TRUNK_MEMBER]
                        [$rowCallDur->CALL_TYPE][1]+= 1;
     }
     if (mysqli_num_rows($resultDurCount) > 0) {
       foreach ($dur_count_trunk as $key5=>$value5){
         foreach ($value5 as $keynew6=>$value6){
           $sqlDurCountInsert = "";
           $sqlDurCountInsert = "INSERT INTO MONTHLY_DUR_COUNT".
                                 " VALUES (".
                                 " '$SITE_ID' ,".
                                 " 'trunk' ,".
                                 " '$MONTH' , ".
                                 " '$YEAR',  ".
                                 " '$key5',".
                                 " '' ,".
                                 " '' ,".
                                 " '$keynew6',".
                                 " '".$value6[0]."' ,".
                                 " '".$value6[1]."'".             
                                 " )";
//         echo $sqlDurCountInsert."<br><br>";
          $cdb->execute_sql($sqlDurCountInsert,$resultDurCountInsert,$err_msg);
        }
      }
    }
     
     
  }
?>