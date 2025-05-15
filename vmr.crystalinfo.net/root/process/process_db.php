<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
      $cDB = new db_layer();
      require_valid_login();
     //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
       print_error("Bu sayfaya erişim hakkınız yok!");
       exit;
     }
     $cDT = new datetime_operations();
     $args = Array();
     $PROCESS_DATE = normal2db($PROCESS_DATE,'/');
	 
     $args[] = array("SUBJECT",           $SUBJECT,         cFldWQuote);
     $args[] = array("DETAIL",            $DETAIL,          cFldWQuote);
     $args[] = array("PROCESS_DATE",      $PROCESS_DATE,    cFldWQuote);

     if ($act =="" || $act =="new" )  { 
       $sql_str =  $cDB->InsertString("PROCESS", $args);
       if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
         print_error($error_msg);
         exit;
       }
       $id = mysqli_insert_id($cDB->getConnection());
     }

     if($act == "upd" && $id !="" && is_numeric($id)){
       $args[] = array("PROCESS_ID",$id, cReqWoQuote);
       $sql_str =  $cDB->UpdateString("PROCESS", $args);
       if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
         print_error($error_msg);
         exit;
       }
     }

      header("Location:process.php?act=upd&id=".$id);

?>
