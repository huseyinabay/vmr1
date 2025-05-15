<?

    $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
    include("mail_send.php");
	
    if(!$conn) exit;
 
 error_reporting(0);
 /*
       ini_set('display_errors', 'On');
	   error_reporting(E_ALL);
*/	   

function get_ext_name2($ext_no,$SITE_ID){
      global $conn;
	  $sql_str_F1 = "SELECT EXT_NO,  SUBSTRING(DESCRIPTION,1,50) AS DESCR FROM EXTENTIONS WHERE EXT_NO = '$ext_no' AND SITE_ID = '$SITE_ID'";
	  $result_F1 = mysqli_query($conn,$sql_str_F1);
      $row_F1 = mysqli_fetch_object($result_F1);
       return ($row_F1->DESCR);
 } 
 

function get_call_type($LocationTypeid){
      global $conn;
    $sql_str_F2="SELECT LocationType FROM TLocationType WHERE LocationTypeid= '$LocationTypeid'"; 
      $result_F2 = mysqli_query($conn,$sql_str_F2);
      $row_F2 = mysqli_fetch_object($result_F2);
        return($row_F2->LocationType);
}

function get_tel_place($Locationid){
      global $conn;
    $sql_str_F3="SELECT LocationName FROM TLocation WHERE Locationid= '$Locationid'"; 
    $result_F3 = mysqli_query($conn,$sql_str_F3);
      $row_F3 = mysqli_fetch_object($result_F3);
        return($row_F3->LocationName);
}


function get_ext_mail($ext_No,$site_id){
      global $conn;
    $sql_str_F4="SELECT EMAIL FROM EXTENTIONS WHERE SITE_ID= '$site_id' AND EXT_NO ='".$ext_No."'";
    $result_F4 = mysqli_query($conn,$sql_str_F4);
      $row_F4 = mysqli_fetch_object($result_F4);
        if ($row_F4->EMAIL<>'')
      return($row_F4->EMAIL);
    else
      return "";
}
 
 
function int_format($my_val){
    if ($my_val <= '9')
      return '0'.$my_val;
    else 
      return $my_val;  
  }


function calculate_all_time($time){
        $hr = floor($time/3600);
        $rest = $time - $hr*3600;
        $min = floor($rest/60);
        $sn = $rest - $min*60;
        $sn = number_format($sn,0,'','');
        $my_time = int_format($hr).":".int_format($min).":".int_format($sn);
        return $my_time;
  }
  
  
function write_price($price){
    $price = number_format($price,2, ',', '.');
    return $price;
  }		   
	
  function get_site_prm($MyVal,$site_id){
      global $conn;
    $sql_str="SELECT $MyVal FROM SITES WHERE SITE_ID= '$site_id'";
    $result = mysqli_query($conn,$sql_str);
    $row=mysqli_fetch_object($result);
        return($row->$MyVal);
}
	
 
  $sql_str_1 = "SELECT * FROM ALERTS
              INNER JOIN ALERT_DEFS ON ALERTS.ALERT_ID = ALERT_DEFS.ALERT_ID
              WHERE IS_CONTINUOUS > 0 ORDER BY IS_CONTINUOUS";
			 
	//echo $sql_str_1;exit;		 
  $result_1 = mysqli_query($conn,$sql_str_1);

  while($row1 = mysqli_fetch_object($result_1)){
    $ALERT_DEF_ID = $row1->ALERT_DEF_ID;
    $ALERT_NAME = $row1->ALERT_NAME;
    $ALERT_DEF_NAME = $row1->ALERT_DEF_NAME;
    $LAST_PROC_ID = $row1->LAST_PROC_ID;
    $SITE_ID = $row1->SITE_ID;
    $is_continuous = $row1->IS_CONTINUOUS;
	
	//echo $row1->ALERT_DEF_ID;exit;	
    ////////////////////CONCAT THE CRITERS/////////////////////
    $sql_str_2 = "SELECT ALERT_TO_EMAIL.* FROM ALERT_DEFS
             INNER JOIN ALERT_TO_EMAIL ON ALERT_DEFS.ALERT_DEF_ID = ALERT_TO_EMAIL.ALERT_DEF_ID
             WHERE ALERT_TO_EMAIL.ALERT_DEF_ID = '$ALERT_DEF_ID '
            " ; 
    $result_2 = mysqli_query($conn,$sql_str_2);

    if ($is_continuous == 1){
      $sql_str_A = "SELECT * FROM ALERT_CRT
                WHERE ALERT_CRT.ALERT_DEF_ID = '$row1->ALERT_DEF_ID'" ; 
				
      $result_A = mysqli_query($conn,$sql_str_A);
      $kriter = "";
	  while($row_A = mysqli_fetch_object($result_A)){
        $kriter .=" AND ". $row_A->FIELD_NAME .$row_A->OPERATOR ."'". $row_A->VALUE ."'";
      }
      /////////////////////////////////////////////////////////////////
      $sql_str_3 = "SELECT CDR_ID, ORIG_DN, LocationTypeid, Locationid, CountryCode,LocalCode,PURE_NUMBER,COUNTER, DURATION, PRICE, DATE_FORMAT(TIME_STAMP,'%d.%m.%Y %H:%i:%s') AS MY_TIME
	           FROM CDR_MAIN_DATA 
               WHERE CDR_ID > '$row1->LAST_PROC_ID' AND ERR_CODE='0' $kriter" ;
      $result_3 = mysqli_query($conn,$sql_str_3);

      if(get_site_prm('MAIL_SENT_TO_EXT', $SITE_ID)==1){
        $send_ext = 1;
      }
      //////////////package the data to be sent
      if (mysqli_num_rows($result_3)>'0'){
        while($row1 = mysqli_fetch_object($result_3)){
          $DATA="<table border=\"0\" width=\"500\">
                   <tr>
                     <td><a href=\"http://www.crystalinfo.net\" target=\"_blank\"><img border=0 SRC=\"cid:my-crystal\" ></a></td>
                     <td width=\"50%\" align=center CLASS=\"header\">&nbsp;</td>
                     <td width=\"25%\" align=right><img SRC=\"cid:my-attach\"></td>
                   </tr>
                 </table>";
          $DATA .= "<table width=\"500\"><tr><td colspan=2 bgcolor=\"#88ACD5\"><b>
                      Uyarılarda belirtilen kriterlere uyan aşağıdaki çağrı yapılmıştır.</b></td></tr>";
          $DATA .= "<tr><td bgcolor=\"#B3CAE3\" width=\"75\">Uyarı Türü</td><td bgcolor=\"#E6EEF7\">".$ALERT_NAME."</td></tr>";
          $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Uyarı Adı</td><td bgcolor=\"#E6EEF7\">".$ALERT_DEF_NAME."</td></tr>";
          $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Dahili</td><td bgcolor=\"#E6EEF7\">".$row1->ORIG_DN." - ".get_ext_name2($row1->ORIG_DN,$SITE_ID)."</td></tr>";
          $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Çağrı Türü</td><td bgcolor=\"#E6EEF7\">".get_call_type($row1->LocationTypeid)."</td></tr>";
          $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Aranan Numara</td><td bgcolor=\"#E6EEF7\">".$row1->CountryCode." ".$row1->LocalCode." ".$row1->PURE_NUMBER."</td></tr>";
          $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Aranan Lokasyon</td><td bgcolor=\"#E6EEF7\">".get_tel_place($row1->Locationid)."</td></tr>";
          $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Aranan Saat</td><td bgcolor=\"#E6EEF7\">".$row1->MY_TIME."</td></tr>";
          $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Kontör Miktarı</td><td bgcolor = \"#E6EEF7\">".$row1->COUNTER."</td></tr>";
          $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Süre</td><td bgcolor = \"#E6EEF7\">".calculate_all_time($row1->DURATION)."</td></tr>";
          $DATA .= "<tr><td bgcolor=\"#B3CAE3\">Tutar</td><td bgcolor = \"#E6EEF7\">".write_price($row1->PRICE)."</td></tr>";
          $DATA .= "</table>";
          $LAST_PROC_ID = $row1->CDR_ID;
          $extra_mail = "";
          if ($send_ext==1){
          if (get_ext_mail($row1->ORIG_DN,$SITE_ID))
            $extra_mail = get_ext_mail($row1->ORIG_DN,$SITE_ID);
          }
          //echo $str = $DATA;exit;
          if ($str!=""){
            if(mysqli_num_rows($result_2)>'0'){
              while($row2 = mysqli_fetch_object($result_2)){
                mail_send($row2->MAIL,"Uyarılarda belirtilen kriterlere uyan aşağıdaki çağrı yapılmıştır.",$DATA);
              }
              mysqli_data_seek($result_2,0);//MAİL LOOP
            }
            if($extra_mail){//Kendisine de gitsin
              mail_send($extra_mail,"Sistem yöneticinizin belirttiği aşağıdaki kriterlere uyan bir çağrı yaptınız.",$DATA);
            }//Ekstra mail
          }//str dolu
        }//Uygun kayıtlar dolanıyor
      }//Uygun kayır var mı?
    }elseif($is_continuous==3){
      $ALERT_DEF_ID = $row->ALERT_DEF_ID;
      $ALERT_NAME = $row->ALERT_NAME;
      $ALERT_DEF_NAME = $row->ALERT_DEF_NAME;
      $sql_str_B = "SELECT * FROM ALERT_CRT
                WHERE ALERT_CRT.ALERT_DEF_ID = '$row->ALERT_DEF_ID'
                ORDER BY ALERT_CRT_ID ASC ";
      $result_B = mysqli_query($conn,$sql_str_B);
      $kriter = " SITE_ID = ".$SITE_ID;
      while($row_B = mysqli_fetch_object($result_B)){
        if ($row_B->FIELD_NAME=='DESCRIPTION'){
          $switch_desc = $row_B->VALUE;
        }elseif ($row_B->FIELD_NAME=='SWITCH_CODE'){
          $kriter .=" AND DATA LIKE '%".$row_B->VALUE ."%'";
        }
      }
      //echo $kriter;exit;
      /////////////////////////////////////////////////////////////////
      $sql_str_4 = "SELECT * FROM RAW_ARCHIEVE 
               WHERE ".$kriter." AND ID > '$row->LAST_PROC_ID'" ;
      //echo $sql_str_4;exit;
      $result_4 = mysqli_query($conn,$sql_str_4);
      if(mysqli_num_rows($result_4)>'0'){
        while($row4 = mysqli_fetch_object($result_4)){
          $DATA="";
          $DATA = "Santral Uyarılarında belirtilen kriterlere uyan aşağıdaki kayıt gelmiştir.<br>";
          $DATA .= "Uyarı Türü = ".$ALERT_NAME."<br>";
          $DATA .= "Uyarı Adı = ".$ALERT_DEF_NAME."<br>";
          $DATA .= "Açıklama = ".$switch_desc."<br>";
          $LAST_PROC_ID = $row4->CDR_ID;
          //////////////package the data to be sent
          if(mysqli_num_rows($result_2)>'0'){
            while($row2 = mysqli_fetch_object($result_2)){
              mail_send($row2->MAIL,"Santralden belirtilen kriterlere uygun aşağıdaki kayıt gelmiştir.",$DATA);
            }
            mysqli_data_seek($result_2,0);//MAİL LOOP
          }
        }
      }
    }
    $sql_str_4 = " UPDATE ALERT_DEFS SET LAST_PROC_ID = '$LAST_PROC_ID' WHERE ALERT_DEF_ID = '$ALERT_DEF_ID'" ; 
    $result_4 = mysqli_query($conn,$sql_str_4);
  }
?>