<?
    require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/mail/PHPMailerAutoload.php');
    
    $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
    include("mail_send.php");
	
    if(!$conn) exit;
 /*
 error_reporting(0);
 */
       ini_set('display_errors', 'On');
	   error_reporting(E_ALL);
	  
function get_system_prm($MyVal){
    global $conn;
    $sql_str_f1="SELECT VALUE FROM SYSTEM_PRM WHERE NAME= '$MyVal'";
    $result_f1 = mysqli_query($conn,$sql_str_f1);
    $row_f1 = mysqli_fetch_object($result_f1);
    return($row_f1->VALUE);
}	  

  //GET THE REQUIRED SYSTEM PARAMETERS MAXIMUM RECORD COUNT AND MAX REPORTABLE DAYS
  $MAX_REC_COUNT= get_system_prm('MAX_RECORD_COUNT');
  $MAX_RECORD_DAYS= get_system_prm('MAX_RECORD_DAYS');

  // DELETE THE ARCHIEVED ITEMS THAT ARE MORE THAN MAX_RECORD_COUNT
  $sql_str_1 = "SELECT COUNT(ID) AS RAW_ADET FROM RAW_ARCHIEVE";
   $result_1 = mysqli_query($conn,$sql_str_1);
      $row_1 = mysqli_fetch_object($result_1);
  

  if ($row_1->RAW_ADET > $MAX_REC_COUNT){
	$DEL_ID = 0;  
    $sql_str_2 = "SELECT ID FROM RAW_ARCHIEVE ORDER BY ID DESC LIMIT $MAX_REC_COUNT, 1";
     $result_2 = mysqli_query($conn,$sql_str_2);
        $row_2 = mysqli_fetch_object($result_2);
    $DEL_ID = $row_2->ID;
	
    $qry = "DELETE FROM RAW_ARCHIEVE WHERE ID <= '$DEL_ID' AND DONE <> '0'";
    if (!($cdb->execute_sql($qry, $resultb, $error_msg))){
      echo $error_msg;
      exit;
    }
  }

  //ARCHIEVE THE SEMI_FINISHED TABLE AND DELETE THE ARCHIEVED ITEMS
  $sql_str_3 = "SELECT COUNT(ID) AS SEMI_ADET FROM SEMI_ARCHIEVE";
   $result_3 = mysqli_query($conn,$sql_str_3);
      $row_3 = mysqli_fetch_object($result_3);

  if ($row_3->SEMI_ADET > $MAX_REC_COUNT){
    $DEL_ID = 0;
    $sql_str_4 = "SELECT ID FROM SEMI_ARCHIEVE ORDER BY ID DESC LIMIT $MAX_REC_COUNT, 1";
     $result_4 = mysqli_query($conn,$sql_str_4);
        $row_4 = mysqli_fetch_object($result_4);
    $DEL_ID = $row_4->ID;

    $sql_str_5 = "DELETE FROM SEMI_ARCHIEVE WHERE ID <= '$DEL_ID' AND DONE <> '0'";
     $result_5 = mysqli_query($conn,$sql_str_5);
        $row_5 = mysqli_fetch_object($result_5);
  }

  //ARCHIEVE THE CDR_MAIN_DATA TABLE AND DELETE THE ARCHIEVED ITEMS
  $DEL_ID = 0;
  $sql_str_6 = "SELECT CDR_ID FROM CDR_MAIN_DATA 
          WHERE MY_DATE = DATE_SUB(NOW(),INTERVAL $MAX_RECORD_DAYS DAY) 
          ORDER BY CDR_ID ASC LIMIT 0,1;";
  $result_6 = mysqli_query($conn,$sql_str_6);
     //$row_6 = mysqli_fetch_object($result_6);
		
  if (mysqli_num_rows($result_6) > 0){
    $row_6 = mysqli_fetch_object($result_6);
    $DEL_ID = $row_6->CDR_ID;
  }else{
    $DEL_ID = "";
  }
  if ($DEL_ID){
    $sql_str_7 = "INSERT INTO CDR_ARCHIEVE SELECT * FROM CDR_MAIN_DATA WHERE CDR_ID <= '$DEL_ID'";
     $result_7 = mysqli_query($conn,$sql_str_7);
        $row_7 = mysqli_fetch_object($result_7);
	
    $sql_str_8 = "DELETE FROM CDR_MAIN_DATA WHERE CDR_ID <= '$DEL_ID'";
     $result_8 = mysqli_query($conn,$sql_str_8);
        $row_8 = mysqli_fetch_object($result_8);
  }

  //ARCHIEVE THE CDR_MAIN_INB TABLE AND DELETE THE ARCHIEVED ITEMS
  $DEL_ID = 0;
  $sql_str_9 = "SELECT CDR_ID FROM CDR_MAIN_INB 
          WHERE MY_DATE=DATE_SUB(NOW(),INTERVAL $MAX_RECORD_DAYS DAY) 
          ORDER BY CDR_ID LIMIT 0,1;";
   $result_9 = mysqli_query($conn,$sql_str_9);
      //$row_9 = mysqli_fetch_object($result_9);
		
  if (mysqli_num_rows($result_9) > 0){
    $row_9 = mysqli_fetch_object($result_9);
    $DEL_ID = $row_9->CDR_ID;
  }else{
    $DEL_ID = "";
  }
  if ($DEL_ID){
    $sql_str_10 = "INSERT INTO CDR_MAIN_INB_ARCH SELECT * FROM CDR_MAIN_INB WHERE CDR_ID <= '$DEL_ID'";
    $result_10 = mysqli_query($conn,$sql_str_10);
        $row_10 = mysqli_fetch_object($result_10);
		
    $sql_str_11 = "DELETE FROM CDR_MAIN_INB WHERE CDR_ID <= '$DEL_ID'";
     $result_11 = mysqli_query($conn,$sql_str_11);
        $row_11 = mysqli_fetch_object($result_11);
  }
?>

