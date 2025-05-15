<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
      $cDB = new db_layer();
      require_valid_login();

      $args = Array();
    
      //Site Admin yokda bu tanımı yapamamalı
       if (!right_get("SITE_ADMIN")){
          print_error("Bu sayfaya erişim hakkınız yok!");
     exit;
       }
/*      $sql_str = "SELECT * FROM SANTRAL WHERE NAME = '$SWITCH_NAME'" ; 
      $my_state = exist_status($sql_str,$act,'DEPT_ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu isimle bir Santral kaydı yapılmış. İkinci Defa Yapamazsınız.");
            exit;
      }
*/
      $args[] = array("NAME",          $LOG_NAME,         cFldWQuote);
      $args[] = array("ROW_NUM",     $ROW_NUM,    cFldWQuote);
      $args[] = array("START",     $START,    cFldWQuote);
      $args[] = array("LENGTH",     $LENGTH,    cFldWQuote);
      $args[] = array("VALUE",     $VALUE,    cFldWQuote);
      $args[] = array("START1",     $START1,    cFldWQuote);
      $args[] = array("LENGTH1",     $LENGTH1,    cFldWQuote);
      $args[] = array("VALUE1",     $VALUE1,    cFldWQuote);
      $args[] = array("CALL_TYPE",     $CALL_TYPE,    cFldWQuote);
      $args[] = array("SANTRAL_ID",     $sw_id,    cFldWQuote);
      
      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("SANTRAL_LOG_TIPLERI", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $id = mysqli_insert_id($cDB->getConnection());
      }

      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("SANTRAL_LOG_TIPLERI", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
      header("Location:log_types.php?act=upd&id=".$id."&sw_id=".$sw_id);
?>
