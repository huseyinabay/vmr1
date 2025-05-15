<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";

      $cDB = new db_layer();
      require_valid_login();

     //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
     }

      $sql_str = "SELECT * FROM UNREP_EXTS WHERE UNREP_EXT_NO = '$UNREP_EXT_NO' AND SITE_ID = '$SITE_ID'" ; 
      $my_state = exist_status($sql_str,$act,'UNREP_EXT_ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu dahili kaydı daha önce yapılmış. İkinci Defa Yapamazsınız.");
            exit;
      }

    $cDT = new datetime_operations();
      $args = Array();

      $args[] = array("SITE_ID",          $SITE_ID,      cFldWoQuote);
      $args[] = array("UNREP_EXT_NO",       $UNREP_EXT_NO,  cFldWQuote);
      
      if ($act =="" || $act =="new" )  {
            $sql_str =  $cDB->InsertString("UNREP_EXTS", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $id = mysqli_insert_id($cDB->getConnection());
      }
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("UNREP_EXT_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("UNREP_EXTS", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
      if($act == "del" && $id !="" && is_numeric($id)){
          $sql_str = "DELETE FROM UNREP_EXTS WHERE UNREP_EXT_ID = ".$id;
          if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                      print_error($error_msg);
                      exit;
                }
         header("Location:unrep_exts_src.php");
         exit;
      }

      header("Location:unrep_exts.php?act=upd&id=".$id);
?>
