<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	  include $_SERVER['DOCUMENT_ROOT']."/root/crons/mail_send.php";
      //include(dirname($DOCUMENT_ROOT)."/root/crons/mail_send.php");
      $cUtility = new Utility();
      $cdb = new db_layer();
      $conn = $cdb->getConnection();
      $Mail = new phpmailer();
//////STEPS --
/*
          1- Get Continuoous alerts with definitions
          2- format an sql with the values
          3- Get the data to sent to the e-Mails
          4- if there is data to be  sent get e-mail     
          5- Send e-Mails and update ALERT_DEFS to LAST_PROC_ID 
*/
      $sql_str = "SELECT * FROM ALERTS
                  INNER JOIN ALERT_DEFS ON ALERTS.ALERT_ID = ALERT_DEFS.ALERT_ID
                  WHERE IS_CONTINUOUS > 0 ORDER BY IS_CONTINUOUS
                  " ; 
      if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
            print_error($error_msg);
            exit;
      }
      while($row = mysqli_fetch_object($result)){
            $ALERT_DEF_ID = $row->ALERT_DEF_ID;
            $ALERT_NAME = $row->ALERT_NAME;
            $ALERT_DEF_NAME = $row->ALERT_DEF_NAME;
            $LAST_PROC_ID = $row->LAST_PROC_ID;
            $SITE_ID = $row->SITE_ID;
            $is_continuous = 3;//$row->IS_CONTINUOUS;
////////////////////CONCAT THE CRITERS/////////////////////
            $sql2 = "SELECT ALERT_TO_EMAIL.* FROM ALERT_DEFS
                     INNER JOIN ALERT_TO_EMAIL ON ALERT_DEFS.ALERT_DEF_ID = ALERT_TO_EMAIL.ALERT_DEF_ID
                     WHERE ALERT_TO_EMAIL.ALERT_DEF_ID = '$ALERT_DEF_ID '
                        " ; 
      if (!($cdb->execute_sql($sql2, $rs2, $error_msg))){
              print_error($error_msg);
              exit;
           }

//////////////Get the mails to be sent to 
            //echo $sql2;exit;

      if ($is_continuous == 1){
            $sql_A = "SELECT * FROM ALERT_CRT
                          WHERE ALERT_CRT.ALERT_DEF_ID = '$row->ALERT_DEF_ID'
                        " ; 
        if (!($cdb->execute_sql($sql_A, $rs_A, $error_msg))){
                    print_error($error_msg);
                    exit;
            }
              $kriter = "";
              while($row_A = mysqli_fetch_object($rs_A)){
                    $kriter .=" AND ". $row_A->FIELD_NAME .$row_A->OPERATOR ."'". $row_A->VALUE ."'";
              }
/////////////////////////////////////////////////////////////////
              $sql1 = "SELECT * FROM CDR_MAIN_DATA 
                        WHERE CDR_ID > '$row->LAST_PROC_ID' $kriter
                        " ;
              //echo $sql1;exit;
        if (!($cdb->execute_sql($sql1, $rs1, $error_msg))){
                    print_error($error_msg);
                    exit;
                }

        if(get_site_prm('MAIL_SENT_TO_EXT', $SITE_ID)==1){
          $send_ext = 1;
        }
//////////////package the data to be sent
          if (mysqli_num_rows($rs1)>'0'){
          while($row1 = mysqli_fetch_object($rs1)){
            $DATA="";
            $DATA = "Uyarılarda belirtilen kriterlere uyan aşağıdaki çağrı yapılmıştır.<br>";
            $DATA .= "Uyarı Türü = ".$ALERT_NAME."<br>";
            $DATA .= "Uyarı Adı = ".$ALERT_DEF_NAME."<br>";
            $DATA .= "Çağrı Türü = ".get_call_type($row1->LocationType)."<br>";
            $DATA .= "Aranan Yer = ".get_tel_place($row1->Locationid)."<br>";
            $DATA .= "Aranan Tel = ".$row1->LocalCode." ".$row1->PURE_NUMBER."<br>";
            $DATA .= "Kontör Miktarı = ".$row1->COUNTER."<br>";
            $DATA .= "Süre = ".calculate_all_time($row1->DURATION)."<br>";
            $DATA .= "Tutar = ".number_format($row1->PRICE,0,'','.')."<br>";
            $LAST_PROC_ID = $row1->CDR_ID;
            if ($send_ext==1){
              if (get_ext_mail($row1->ORIG_DN,$SITE_ID))
                $extra_mail = get_ext_mail($row1->ORIG_DN,$SITE_ID);
            }
            $str = $DATA;
            if ($str!=""){
			  if(mysqli_num_rows($rs2)>'0'){
                while($row2 = mysqli_fetch_object($rs2)){
                  mail_send($row2->MAIL,"Uyarılarda belirtilen kriterlere uyan aşağıdaki çağrı yapılmıştır.",$str);
                  $ALERT_DEF_ID = $row2->ALERT_DEF_ID;
                }
                mysql_data_seek($rs2,0);//MAİL LOOP
              }
              if($extra_mail){//Kendisine de gitsin
                 mail_send($extra_mail,"Aşağıdaki kriterlere uyan bir çağrı yaptınız ve bu konuda uyarılmanız istendi.",$str);
              }//Ekstra mail
            }//str dolu
          }//Uygun kayıtlar dolanıyor
        }//Uygun kayır var mı?
      }elseif($is_continuous==3){
        $ALERT_DEF_ID = $row->ALERT_DEF_ID;
        $ALERT_NAME = $row->ALERT_NAME;
        $ALERT_DEF_NAME = $row->ALERT_DEF_NAME;
        $sql_A = "SELECT * FROM ALERT_CRT
                  WHERE ALERT_CRT.ALERT_DEF_ID = '$row->ALERT_DEF_ID'
                  ORDER BY ALERT_CRT_ID ASC
                      " ;
          if (!($cdb->execute_sql($sql_A, $rs_A, $error_msg))){
                      print_error($error_msg);
                      exit;
              }
                $kriter = " SITE_ID = ".$SITE_ID;
                while($row_A = mysqli_fetch_object($rs_A)){
            if ($row_A->FIELD_NAME=='DESCRIPTION'){
              $switch_desc = $row_A->VALUE;
            }elseif ($row_A->FIELD_NAME=='SWITCH_CODE'){
              $kriter .=" AND DATA LIKE '%".$row_A->VALUE ."%'";
            }
                }
          //echo $kriter;exit;
  /////////////////////////////////////////////////////////////////
                $sql1 = "SELECT * FROM RAW_DATA 
                          WHERE ".$kriter." AND ID > '$row->LAST_PROC_ID'
                          " ;
                //echo $sql1;exit;
          if (!($cdb->execute_sql($sql1, $rs1, $error_msg))){
                      print_error($error_msg);
                      exit;
                  }
          
          if(mysqli_num_rows($rs1)>'0'){
            while($row1 = mysqli_fetch_object($rs1)){
              $DATA="";
              $DATA = "Santral Uyarılarında belirtilen kriterlere uyan aşağıdaki kayıt gelmiştir.<br>";
              $DATA .= "Uyarı Türü = ".$ALERT_NAME."<br>";
              $DATA .= "Uyarı Adı = ".$ALERT_DEF_NAME."<br>";
              $DATA .= "Açıklama = ".$switch_desc."<br>";
              $LAST_PROC_ID = $row1->CDR_ID;
              //////////////package the data to be sent
              if(mysqli_num_rows($rs2)>'0'){
                while($row2 = mysqli_fetch_object($rs2)){
                  $str = $DATA;
				  $ALERT_DEF_ID = $row2->ALERT_DEF_ID;
                  mail_send($row2->MAIL,"Uyarılarda belirtilen kriterlere uyan aşağıdaki çağrı yapılmıştır.",$str);
                }mysqli_data_seek($rs2,0);//MAİL LOOP
              }
            }
          }
        }
    $sql1 = " UPDATE ALERT_DEFS SET LAST_PROC_ID = '$LAST_PROC_ID' WHERE ALERT_DEF_ID = '$ALERT_DEF_ID'" ;
      if (!($cdb->execute_sql($sql1, $rs1, $error_msg))){
            print_error($error_msg);
            exit;
      }
}
?>