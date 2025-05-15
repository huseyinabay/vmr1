<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";

      $cDB = new db_layer();
      require_valid_login();

     //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
     }
    
    $INCITY_LIMIT     = $INCITY_HOUR*3600 + $INCITY_MINUTE*60; 
      $INTERCITY_LIMIT    = $INTERCITY_HOUR*3600 + $INTERCITY_MINUTE*60; 
    $GSM_LIMIT       = $GSM_HOUR*3600 + $GSM_MINUTE*60; 
      $INTERNATIONAL_LIMIT   = $INTERNATIONAL_HOUR*3600 + $INTERNATIONAL_MINUTE*60;

      $cDT = new datetime_operations();
      $args = Array();
      $args[] = array("SITE_ID",        $SITE_ID,       cFldWQuote);
      $args[] = array("QUOTA_NAME",        $QUOTA_NAME,     cFldWQuote);
      $args[] = array("INCITY_LIMIT",     $INCITY_LIMIT,       cFldWoQuote);
      $args[] = array("INTERCITY_LIMIT",  $INTERCITY_LIMIT,   cFldWoQuote);
      $args[] = array("GSM_LIMIT",      $GSM_LIMIT,          cFldWoQuote);
      $args[] = array("INTERNATIONAL_LIMIT",$INTERNATIONAL_LIMIT,cFldWoQuote);
      $args[] = array("PRICE_LIMIT",    $PRICE_LIMIT,    cFldWoQuote);

      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("QUOTAS", $args);
		$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
      }
   
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("QUOTA_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("QUOTAS", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
    
////////////////////////////////////////DELETE CONTACT/////////////////////////////////////
      if ($act =="del" && $id!="" && is_numeric($id))  { 
      $sql_str = "DELETE FROM QUOTAS WHERE QUOTA_ID = '$id' AND SITE_ID = ".$SITE_ID;
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $sql_str = "DELETE FROM QUOTA_ASSIGNS WHERE QUOTA_ID = '$id'";
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
          header("Location:quota_src.php");
        exit;
    }
//////////////////////////////////////////////////////////////////////////////////////////
    
      header("Location:quota.php?act=upd&saved=1&id=".$id);
?>