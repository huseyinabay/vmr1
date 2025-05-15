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
      $sql_str = "SELECT * FROM SANTRAL WHERE NAME = '$SWITCH_NAME'" ; 
      $my_state = exist_status($sql_str,$act,'ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu isimle bir Santral kaydı yapılmış. İkinci Defa Yapamazsınız.");
            exit;
      }

      $args[] = array("NAME",          $SWITCH_NAME,         cFldWQuote);
      $args[] = array("ROWS",     $ROWS,    cFldWQuote);
      
      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("SANTRAL", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $id = mysqli_insert_id($cDB->getConnection());
      }

      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("SANTRAL", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
      header("Location:switch.php?act=upd&id=".$id);
?>
