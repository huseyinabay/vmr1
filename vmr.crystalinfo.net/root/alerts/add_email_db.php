<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
      $cDB = new db_layer();
      require_valid_login();

      $sql_str = "SELECT * FROM ALERT_TO_EMAIL WHERE MAIL = '$EMAIL' AND ALERT_DEF_ID = '$ALERT_DEF_ID'" ;
      $my_state = exist_status($sql_str,$act,'AEM_ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu Uyarı İçin Girdiğiniz E-Mail kaydı daha önce yapılmış. İkinci Defa Yapamazsınız.");
            exit;
      }      
      $args = Array();
      $args[] = array("ALERT_DEF_ID",       $ALERT_DEF_ID,         cFldWQuote);
      $args[] = array("MAIL",            $EMAIL,        cFldWQuote);
      
      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("ALERT_TO_EMAIL", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
   
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("AEM_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("ALERT_TO_EMAIL", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
    if ($act =="del" ){ 
       $sql_str = "DELETE FROM ALERT_TO_EMAIL WHERE AEM_ID = $id"; 
    if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
  }   
?>
<script>
  window.opener.location.reload();
  window.close();
</script>
