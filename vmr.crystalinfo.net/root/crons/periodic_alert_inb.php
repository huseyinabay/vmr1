<?php
  require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/mail/PHPMailerAutoload.php');
  $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
  if(!$conn) exit;
	  include("periodic_inb.php");
	  include("mail_send.php");
      if(!$conn) exit;

//////STEPS -- 
/*
          1- Get daily alerts with definitions
          2- format an sql with the values
          3- Get the data to sent to the e-Mails
          4- if there is data to be  sent get e-mail     
          5- Send e-Mails and update ALERT_DEFS to LAST_PROC_ID 
*/      
      
      $sql_str = "SELECT * FROM ALERT_DEFS
                  INNER JOIN ALERTS ON ALERTS.ALERT_ID = ALERT_DEFS.ALERT_ID
                  WHERE ALERTS.ALERT_ID = 6 " ;
                   
      if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
            print_error($error_msg);
            exit;
      }
	  

	  
	  
	  
	  
      while($row = mysqli_fetch_object($result)){
            $ALERT_ID = $row->ALERT_ID;
			$ALERT_NAME = $row->ALERT_NAME;
            $ALERT_DEF_ID = $row->ALERT_DEF_ID;
            $SITE_ID = $row->SITE_ID;
            $LAST_PROC_ID = $row->LAST_PROC_ID;
            $DATA = "";
			
		
		$sql_dn = "SELECT * FROM ALERT_CRT
                  WHERE ALERT_DEF_ID = $ALERT_DEF_ID AND FIELD_NAME='ORIG_DN' ";
                   
      if (!($cdb->execute_sql($sql_dn,$rs_dn,$error_msg))){
            print_error($error_msg);
            exit;
      }
	  
			//$ORIG_DN = $rs_dn->ORIG_DN;
			
			
		
			
            //////////////Get the mails to be sent to 
            $sql2 = "SELECT ALERT_TO_EMAIL.* FROM ALERT_DEFS
                     INNER JOIN ALERT_TO_EMAIL ON ALERT_DEFS.ALERT_DEF_ID = ALERT_TO_EMAIL.ALERT_DEF_ID
                     WHERE ALERT_TO_EMAIL.ALERT_DEF_ID = '$ALERT_DEF_ID '
                        " ;
            if (!($cdb->execute_sql($sql2, $rs2, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
			//echo $ALERT_ID;
			//echo $ALERT_DEF_ID;
			//echo $ALERT_NAME;
			
			$d=0;
			if(mysqli_num_rows($rs_dn) >= 1){
				while($row_dn = mysqli_fetch_object($rs_dn)){
		    $ORIG_DN = $row_dn->VALUE;

            $DATA = periodic_inb($SITE_ID,$ORIG_DN,'day');
			$d++;
				}
			}

		
            $k=0;
            if(mysqli_num_rows($rs2) >= 1){
            while($row2 = mysqli_fetch_object($rs2)){
 //           echo "$row2->MAIL \n";
                  mail_send($row2->MAIL,$row->ALERT_NAME,$DATA);
                   $k++;
            } //MAİL LOOP*/
       }
   }
?>
