<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
      $cDB = new db_layer();
      require_valid_login();
      $args = Array();

   //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
   }
    
      $sql_str = "SELECT * FROM ACCESS_CODES WHERE ACCESS_CODE = '$ACCESS_CODE' AND SITE_ID = '$SITE_ID'" ; 
      $my_state = exist_status($sql_str,$act,'ACCESS_CODE_ID',$id);
      if ($my_state==1 || $my_state==2){//1 yeni kayıt ve mevcut 2 update ve mevcut
            print_error("Bu Erişim Kodu kaydı daha önce yapılmış. İkinci Defa Yapamazsınız.");
            exit;
      }      

      $args[] = array("ACCESS_CODE",     $ACCESS_CODE,     cFldWQuote);
      $args[] = array("SITE_ID",          $SITE_ID,          cFldWoQuote);
      $args[] = array("ACCESS_CODE_DESC",  $ACCESS_CODE_DESC,  cFldWQuote);
      
      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("ACCESS_CODES", $args);
		$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
      }

      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("ACCESS_CODE_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("ACCESS_CODES", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
    
////////////////////////////////////////DELETE ACCESS_CODE/////////////////////////////////////
      if ($act =="del" && $id!="" && is_numeric($id))  { 
            $sql_str = "DELETE FROM ACCESS_CODES WHERE ACCESS_CODE_ID = '$id' AND SITE_ID = ".$SITE_ID;
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
          header("Location:access_code_src.php");
        exit;
    }
//////////////////////////////////////////////////////////////////////////////////////////
    
      header("Location:access_code.php?act=upd&saved=1&id=".$id);
?>
