<?  require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/functions.php");
    $cDB = new db_layer();
    require_valid_login();

    $args = Array();
    //Site Admin veya Admin Hakk� yokda bu tan�m� yapamamal�
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya eri�im hakk�n�z yok!");
    exit;
     }
  if ($act =="" || $act =="new" )  { 
      $sql_str = "SELECT * FROM ACCOUNT_EMAIL WHERE ACCOUNT_NO = '$ACCOUNT_NO' " ; 
      $my_state = exist_status($sql_str,$act,'id',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu hesap kayd� daha �nce  yap�lm��. �kinci Defa Yapamazs�n�z.");
            exit;
      }      
}
      $args[] = array("ACCOUNT_NO",     $ACCOUNT_NO,       cFldWQuote);
      $args[] = array("EMAIL",        $EMAIL,          cFldWQuote);
      
      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("ACCOUNT_EMAIL", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $id = mysql_insert_id();
      }
   
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("Id",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("ACCOUNT_EMAIL", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }

////////////////////////////////////////DELETE AUTH_CODE/////////////////////////////////////
      if ($act =="del" && $id!="" && is_numeric($id))  { 
            $sql_str = "DELETE FROM ACCOUNT_EMAIL WHERE Id = '$id' ";
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
          header("Location:auth_mail_src.php");
        exit;
    }
//////////////////////////////////////////////////////////////////////////////////////////
    
      header("Location:auth_mail.php?act=upd&id=".$id);
?>
