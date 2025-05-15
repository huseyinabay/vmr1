<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/class.phpmailer.php";
      
      $cUtility = new Utility();
      $cdb = new db_layer();
      $conn = $cdb->getConnection();
      $Mail = new phpmailer();
/*
          1- Get Continuaous alerts with definations
          2- format an sql with the values
          3- Get the data to sent to the e-Mails
          4- if there is data to be  sent get e-mail     
          5- Send e-Mails and update ALERT_DEFS to LAST_PROC_ID 
*/      
      $sql_str = "SELECT * FROM ALERTS
                  INNER JOIN ALERT_DEFS ON ALERTS.ALERT_ID = ALERT_DEFS.ALERT_ID
                  WHERE ALERTS.ALERT_ID=15
                  " ; 
      if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
            print_error($error_msg);
            exit;
      }
      while($row = mysqli_fetch_object($result)){
            $ALERT_DEF_ID = $row->ALERT_DEF_ID;
            $LAST_PROC_ID = $row->LAST_PROC_ID;
////////////////////CANCAT THE CRITERS/////////////////////

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
            $sql1 = "SELECT CDR_ID, EXTENTIONS.EMAIL, TIME_STAMP, CLID, TER_DN FROM CDR_MAIN_DATA 
                        INNER JOIN EXTENTIONS ON EXTENTIONS.EXT_NO = CDR_MAIN_DATA.TER_DN            
                        WHERE CDR_ID >= '$row->LAST_PROC_ID' $kriter
                        " ; 
            if (!($cdb->execute_sql($sql1, $rs1, $error_msg))){
                  print_error($error_msg);
                  exit;
            }

//////////////package the data to be sent
            while($row1 = mysqli_fetch_object($rs1)){
                  $DATA .= "$row1->CLID numaralı telefon $row1->TIME_STAMP  tarihinde sizi aradı, ulaşamadı.";
                  $LAST_PROC_ID = $row1->CDR_ID;

                  if($row1->CLID && $row1->EMAIL){
                        $Mail->From       ="info@crystal.net";
                        $Mail->FromName   ="CrystalInfo.net'den";
                        $Mail->Sender     ="postmaster@vodasoft.com.tr";
                        $Mail->AddCustomHeader("Errors-To: <postmaster@vodasoft.com.tr>");
                        $Mail->CharSet    = "iso-8859-9";
                        $Mail->IsHTML(true);
                        $Mail->ClearAllRecipients();                  
                        $str = $DATA;   
                        if ($str!=""){
                              $row1->EMAIL = "info@vodasoft.com.tr"
;
                              $Mail->AddAddress($row1->EMAIL);
                              $Mail->Subject = $row->ALERT_NAME;
                              $Mail->Body = $str;
                              $Mail->IsSendmail();
                              if (!$Mail->Send()){
                                    $message=$Mail->ErrorInfo;
                                    echo $message. "<br>". $row->EMAIL ." Adresine Atamadım.";
                                    exit;
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