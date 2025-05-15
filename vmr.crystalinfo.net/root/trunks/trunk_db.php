<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";

      $cDB = new db_layer();
      require_valid_login();

     //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
     }


      $sql_str = "SELECT * FROM TRUNKS WHERE MEMBER_NO = '$MEMBER_NO' AND SITE_ID = '$SITE_ID'" ; 
      $my_state = exist_status($sql_str,$act,'TRUNK_ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu Sitede bu isimle bir hat kaydı var. İkinci Defa Yapamazsınız.");
            exit;
      }
    
    $cDT = new datetime_operations();
      $args = Array();

      $args[] = array("SITE_ID",         $SITE_ID,        cFldWoQuote);
      $args[] = array("MEMBER_NO",          $MEMBER_NO,        cFldWQuote);
      $args[] = array("TRUNK_NAME",        $TRUNK_NAME,       cFldWQuote);
      $args[] = array("OPT_TYPE_ID",      $OptTypeid,  cFldWoQuote);
      $args[] = array("PHONE_NUMBER",       $PHONE_NUMBER,      cFldWQuote);
      $args[] = array("TRUNK_TYPE",        $TRUNK_TYPE,       cFldWoQuote);
      $args[] = array("TRUNK_DESC",         $TRUNK_DESC,       cFldWQuote);
      $args[] = array("TRUNK_IO_TYPE",      $TRUNK_IO_TYPE,     cFldWoQuote);

      
      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("TRUNKS", $args);
		$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
      }
   
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("TRUNK_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("TRUNKS", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
    
////////////////////////////////////////DELETE CONTACT/////////////////////////////////////
      if ($act =="del" && $id!="" && is_numeric($id))  { 
            $sql_str = "DELETE FROM TRUNKS WHERE TRUNK_ID = '$id' AND SITE_ID = ".$SITE_ID;
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
          header("Location:trunk_src.php");
        exit;
    }
//////////////////////////////////////////////////////////////////////////////////////////
    
      header("Location:trunk.php?act=upd&saved=1&id=".$id);
?>
