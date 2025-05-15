<?php
   require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/mail/PHPMailerAutoload.php');
     
   $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
   include("mail_send.php");
   
  if(!$conn) exit;
 
 error_reporting(0);
/* 
    	   ini_set('display_errors', 'On');
	   error_reporting(E_ALL);
   */
  function get_err_def($ERR_CODE){
    $sql_str1 = "SELECT * FROM ERR_CODES WHERE ERR_CODE = ".$ERR_CODE;
    $result_1 = mysqli_query($conn,$sql_str1);
    if (mysqli_num_rows($result_1)>0){
      $row = mysqli_fetch_object($result_1);
      return $row1->HEADER;
    }else{
      return "";
    }
  }

  $sql_str2 = "SELECT * FROM ALERT_DEFS WHERE ALERT_ID = 10 ";
  $result_2 = mysqli_query($conn,$sql_str2);

  if (mysqli_num_rows($result_2)>0){
    $row2=mysqli_fetch_object($result_2);
    $ALERT_DEF_ID = $row2->ALERT_DEF_ID;
    $ALERT_DEF_NAME = $row2->ALERT_DEF_NAME;
    $frequency = $row2->FREQUENCY;
    $ALERT_DEF_NAME = $row2->ALERT_DEF_NAME;

    //////////////Get the mails to be sent to 
    $sql_str3 = "SELECT ALERT_TO_EMAIL.* 
					FROM ALERT_DEFS
					INNER JOIN ALERT_TO_EMAIL ON ALERT_DEFS.ALERT_DEF_ID = ALERT_TO_EMAIL.ALERT_DEF_ID
					WHERE ALERT_TO_EMAIL.ALERT_DEF_ID = '$ALERT_DEF_ID '" ;
			 
	$result_3 = mysqli_query($conn,$sql_str3);		 

    if ($frequency==3 && date("d")==1){//Aylık isteniyor ve ayın başı
      //Ok sorun yok. Rapor üretilecek.Bu durumda nceki ay alınmalı.
	  $date_crt = " DATE_FORMAT(MY_DATE,'%m') = MONTH(NOW())-1";
	  $Name = "Önceki ay";
    }else if($frequency==2 && date("w")==1){//Haftalık rapor isteniyor ve haftanın ilk günü
      //Ok sorun yok. Rapor üretilecek.İçinde bulunulan ay yapılabilir.
 	  $date_crt = " DATE_FORMAT(MY_DATE,'%m') = MONTH(NOW())";
	  $Name = "Bu ay";
    }else if($frequency==1){//Günlük rapor isteniyorsa başka kontrole gerek yok.
      //Ok sorun yok. Rapor üretilecek.İçinde bulunulan ay yapılabilir.
	  $date_crt = " DATE_FORMAT(MY_DATE,'%m') = MONTH(NOW())";
	  $Name = "Bu ay";
    }else if($force==1){//Herhangi bir anda zorla rapor isteniyor.
      //Ok sorun yok. Rapor üretilecek.
    }else{
      exit;
    }
    //Ok ise devam edip raporu hazırlayalım.
    $DATA="<table border=\"0\" width=\"800\">
             <tr>
               <td><a href=\"http://www.crystalinfo.com.tr\" target=\"_blank\"><img border=0 SRC=\"cid:my-crystal\" ></a></td>
               <td width=\"100%\" align=center CLASS=\"header\">CrystalInfo Sistem Bilgi Raporu</td>
               <td width=\"25%\" align=right><img SRC=\"cid:my-attach\"></td>
             </tr>
           </table>";
    $DATA .= "<table width=\"799\"><tr><td colspan=2 bgcolor=\"#88ACD5\"><b>Sistem Özet Bilgileri</b></td></tr>";
    //////////////////////DISK STATUS//////////////////////////////
    exec((" df /| awk '{ print $5 }'"), $aa) ;
    $dolu = str_replace("%", "", $aa[1]);
    $bos = 100 -$dolu;
    //////////////////////END OF DISK STATUS//////////////////////////////
    $DATA .= "<tr><td bgcolor=\"#B3CAE3\" width=\"200\">Disk Durumu</td><td bgcolor=\"#E6EEF7\"><b>%".$dolu."</b> Dolu - <b>%".$bos."</b> Boş</td></tr>";
    //////////////////////DB SIZE//////////////////////////////      
	$sql_str6 = "SELECT SUM(DATA_LENGTH + INDEX_LENGTH) /1024 /1024 AS MB FROM information_schema.TABLES WHERE table_schema='MCRYSTALINFONE'" ; 
            $result_6 = mysqli_query($conn,$sql_str6);
            $row6 = mysqli_fetch_object($result_6);
	
	    //////////////////////END OF DB SIZE//////////////////////////////           
    $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Database Boyutu</td><td bgcolor=\"#E6EEF7\">".$row6->MB.' '.'MB'."</td></tr>";
    //////////////////////LAST RECORD//////////////////////////////
    $sql_str = "SELECT * FROM SEMI_ARCHIEVE ORDER BY ID DESC LIMIT 1" ; 
    $result = mysqli_query($conn,$sql_str);
	
    $row = mysqli_fetch_object($result);
    $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Son İşlenen Kayıt</td><td bgcolor=\"#E6EEF7\">".$row->LINE1."</td></tr>";
    $DATA .= "<tr><td colspan=2 bgcolor=\"#88ACD5\"><b>".$Name." Giden Çağrıların Kayıt Durumları</b></td></tr>";
    //////////////////////THIS MONTHS RECORD STATUS//////////////////////////////
    $sql_str4 = " SELECT COUNT(*) AS CNT, ERR_CODE, ERR_CODES.HEADER FROM CDR_MAIN_DATA
                  INNER JOIN ERR_CODES ON ERR_CODES.ID = CDR_MAIN_DATA.ERR_CODE
                  GROUP BY ERR_CODE" ;
				  
    $result_4 = mysqli_query($conn,$sql_str4);	
    while($row4 = mysqli_fetch_object($result_4)){
    $DATA .= "<tr><td bgcolor=\"#B3CAE3\">".$row4->HEADER."</td><td bgcolor=\"#E6EEF7\">".$row4->CNT."</td></tr>";
    }
    $DATA .= "<tr><td colspan=2 bgcolor=\"#88ACD5\"><b>".$Name." Gelen Çağrıların Kayıt Durumları</b></td></tr>";
    //////////////////////THIS MONTHS RECORD STATUS//////////////////////////////
    $sql_str5 = "SELECT COUNT(*) AS CNT, ERR_CODE, ERR_CODES.HEADER FROM CDR_MAIN_INB
                 INNER JOIN ERR_CODES ON ERR_CODES.ID = CDR_MAIN_INB.ERR_CODE
					GROUP BY ERR_CODE" ;
				 
    $result_5 = mysqli_query($conn,$sql_str5);	
    while($row5 = mysqli_fetch_object($result_5)){
    $DATA .= "<tr><td bgcolor=\"#B3CAE3\">".$row5->HEADER."</td><td bgcolor=\"#E6EEF7\">".$row5->CNT."</td></tr>";
    }
    //////////////////////END OF THIS MONTHS RECORD STATUS//////////////////////////////           
    $DATA .= "</table>";
    //Mailler gönderiliyor
    if(mysqli_num_rows($result_3)>'0'){
      while($row3 = mysqli_fetch_object($result_3)){
        mail_send($row3->MAIL,"CrystalInfo Sistemi Özet Raporu.",$DATA);
      }
    }
  }
?>
 
