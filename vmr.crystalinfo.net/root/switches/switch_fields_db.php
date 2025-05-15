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

////////////////////////////////////////DELETE FIELD/////////////////////////////////////
// 
      if ($act =="del" && $FIELD_ID!="" && is_numeric($FIELD_ID))  { 
            $sql_str = "DELETE FROM SANT_ALANLARI WHERE LOG_TYPE_ID = '$log_id' AND FIELD_ID=$FIELD_ID";
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
      header("Location:switch_fields.php?sw_id=".$sw_id."&log_id=".$log_id);
    exit;
    }
//////////////////////////////////////////////////////////////////////////////////////////


      $sql_str = "SELECT * FROM SANT_ALANLARI WHERE LOG_TYPE_ID = '$log_id' AND FIELD_ID=$FIELD_ID" ; 
      $my_state = exist_status($sql_str,$act,'ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu alana kayıt yapılmış. İkinci Defa Yapamazsınız.");
            exit;
      }
      $sql_str = "SELECT IS_NUMBER FROM SANT_ALAN_TANIMLARI WHERE ID = $FIELD_ID" ; 
       if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
    if (mysqli_num_rows($result)>0){
         $rw = mysqli_fetch_object($result);
        }else{
            print_error("Yanlış Alan Adı.");
            exit;
        }
        $IS_NUMBER = $rw->IS_NUMBER;
      $args[] = array("FIELD_ID",          $FIELD_ID,         cFldWQuote);
      $args[] = array("ROW_NUM",     $ROW_NUM,    cFldWQuote);
      $args[] = array("START",     $START,    cFldWQuote);
      $args[] = array("LENGTH",     $LENGTH,    cFldWQuote);
      $args[] = array("LOG_TYPE_ID",     $log_id,    cFldWQuote);
      $args[] = array("SANTRAL_ID",     $sw_id,    cFldWQuote);
      $args[] = array("IS_NUMBER",     $IS_NUMBER,    cFldWQuote);
      
      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("SANT_ALANLARI", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $id = mysqli_insert_id($cDB->getConnection());
      }

      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("SANT_ALANLARI", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
      header("Location:switch_fields.php?act=upd&id=".$id."&sw_id=".$sw_id."&log_id=".$log_id);
?>
