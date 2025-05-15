<?php
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();

 $error_occured = FALSE;
 $error_msg     = "";


 $query = "INSERT INTO  NEWS ".
          "( USER_ID, BASLIK, DETAY, INSERT_DATE, INSERT_TIME,LEVEL) ".
          "VALUES(".$_SESSION["user_id"].",\"".$frm_baslik."\",\"".$frm_detay."\",\"".my_date()."\",\"".my_time()."\",".$frm_level.")";

 if (!($cdb->execute_sql($query,$result,$error_msg))){
   print_error(sprintf("Internal Error %s",$error_msg));
   exit();
 }
  header("Location:"."news_list.php");

?>
 
