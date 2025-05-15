<?php
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();

 // Eventually the message must be sent to end users. The targer user can be selected to the list 
 // or a group may be selected
 // If a user is selected then send it  
  if ($act=="del"){
      for ($i=1;$i <= $msg_cnt;$i++){
         $varname = "del_msg_".$i; // name of the check box. True if it is checked
       $mapvar  = "map_del_msg_".$i;
     
      if (($$varname == 1)) {
           $sql_str = sprintf("DELETE FROM MESSAGES WHERE SIRA_NO = %s",$$mapvar);
           if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
              print_error($error_msg);
              exit;
           } 
       }
     } // end for
  }else{
    if (isset($USER_ID) and ($USER_ID !="")){
         foreach($USER_ID as $id){
             $query = "INSERT INTO  MESSAGES ".
                  "(INS_USER_ID, TRG_USER_ID, KONU, DETAY, INSERT_DATE, INSERT_TIME) ".
                  "VALUES(".$_SESSION["user_id"].",".$id.",\""."[Kişiye]  ".$frm_konu."\",\"".$frm_detay."\",\"".my_date()."\",\"".my_time()."\")";
        if (!($cdb->execute_sql($query,$result,$error_msg))){
               echo "Mesaj Kaydı esnasında bir problem oluştu. Hata:".$error_msg;
             exit;
             }
         }
     }
  }

 header("Location:"."mesaj_src.php"); 
?>
