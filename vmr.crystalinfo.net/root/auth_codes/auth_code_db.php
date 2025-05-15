<?  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
    $cDB = new db_layer();
    require_valid_login();

    $args = Array();
    //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
     }

      $sql_str = "SELECT * FROM AUTH_CODES WHERE AUTH_CODE = '$AUTH_CODE' AND SITE_ID = '$SITE_ID'" ; 
      $my_state = exist_status($sql_str,$act,'AUTH_CODE_ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu Authorization kaydı daha önce bir başkası için yapılmış. İkinci Defa Yapamazsınız.");
            exit;
      }      

      $args[] = array("AUTH_CODE",      $AUTH_CODE,        cFldWQuote);
      $args[] = array("SITE_ID",        $SITE_ID,          cFldWoQuote);
      $args[] = array("AUTH_CODE_DESC", $AUTH_CODE_DESC,   cFldWQuote);
      
      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("AUTH_CODES", $args);
		$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
      }
   
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("AUTH_CODE_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("AUTH_CODES", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }

////////////////////////////////////////DELETE AUTH_CODE/////////////////////////////////////
      if ($act =="del" && $id!="" && is_numeric($id))  { 
            $sql_str = "DELETE FROM AUTH_CODES WHERE AUTH_CODE_ID = '$id' AND SITE_ID = ".$SITE_ID;
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
          header("Location:auth_code_src.php");
        exit;
    }
//////////////////////////////////////////////////////////////////////////////////////////
    
      header("Location:auth_code.php?act=upd&saved=1&id=".$id);
?>
