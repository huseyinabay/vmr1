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
      if(!is_numeric($log_id) && !is_numeric($sw_id)){
          print_error("Hatalı İşlem Oluştu!");
     exit;
      }
      $args[] = array("NAME",          $CRT_NAME,         cFldWQuote);
      $args[] = array("ROW_NUM",     $ROW_NUM,    cFldWQuote);
      $args[] = array("START",     $START,    cFldWQuote);
      $args[] = array("LENGTH",     $LENGTH,    cFldWQuote);
      $args[] = array("SWITCH_LOG_ID",     $log_id,    cFldWQuote);
      $args[] = array("SWITCH_ID",     $sw_id,    cFldWQuote);
      $args[] = array("CRT_CHAR",     $CRT_CHAR,    cFldWQuote);
      
      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("SANT_SATIR_KRT", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $id = mysqli_insert_id($cDB->getConnection());
      }

      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("SANT_SATIR_KRT", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
      header("Location:switch_crt.php?act=upd&id=".$id."&sw_id=".$sw_id."&log_id=".$log_id);
?>
