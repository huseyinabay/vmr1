<?php
  	require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/mail/PHPMailerAutoload.php');
    
    $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
    include("mail_send.php");
    include("periodic_sum.php");
	
    if(!$conn) exit;
 
 error_reporting(0);
 /*
       ini_set('display_errors', 'On');
	   error_reporting(E_ALL);
  */ 
      $sql_str_1 = "SELECT * FROM ALERT_DEFS
                  INNER JOIN ALERTS ON ALERTS.ALERT_ID = ALERT_DEFS.ALERT_ID
                  WHERE ALERTS.ALERT_ID = 7 OR ALERTS.ALERT_ID = 8 OR ALERTS.ALERT_ID = 13" ;
                   
      $result_1 = mysqli_query($conn,$sql_str_1);
      while($row = mysqli_fetch_object($result_1)){
            $ALERT_ID = $row->ALERT_ID;
            $ALERT_DEF_ID = $row->ALERT_DEF_ID;
            $SITE_ID = $row->SITE_ID;
            $LAST_PROC_ID = $row->LAST_PROC_ID;
            $DATA = "";
            //////////////Get the mails to be sent to 
            $sql_str_2 = "SELECT ALERT_TO_EMAIL.* FROM ALERT_DEFS
                     INNER JOIN ALERT_TO_EMAIL ON ALERT_DEFS.ALERT_DEF_ID = ALERT_TO_EMAIL.ALERT_DEF_ID
                     WHERE ALERT_TO_EMAIL.ALERT_DEF_ID = '$ALERT_DEF_ID '
                        " ;
            $result_2 = mysqli_query($conn,$sql_str_2);

            //echo $ALERT_ID;exit;
            if ($ALERT_ID == 8){
            if(date("d")==1 || $force==1){//Ayın ilk günü ise çalışsın. Çünkü aylık mail. Force ise hangi gün olursa olsun üret.   // halil
                    $DATA = periodic_summary($SITE_ID,'month');
                }  // halil
            }elseif($ALERT_ID == 13){
                if(strftime("%u")==1  || $force==1){//Haftanın ilk günü ise çalışsın. Çünkü haftalık mail.Force ise hangi gün olursa olsun üret.
                    $DATA = periodic_summary($SITE_ID,'week');
                }
            }elseif($ALERT_ID == 7){//Günün özeti.
                $DATA = periodic_summary($SITE_ID,'day');
            }
            $k=0;
            if(mysqli_num_rows($result_2) >= 1){
            while($row2 = mysqli_fetch_object($result_2)){
//            echo "$row2->MAIL \n";
                  mail_send($row2->MAIL,$row->ALERT_NAME,$DATA);
                   $k++;
            } //MAİL LOOP*/
       }
   }
?>
