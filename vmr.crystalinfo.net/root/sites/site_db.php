<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
      $cDB = new db_layer();
      require_valid_login();
      $args = Array();

     //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
  check_right("SITE_ADMIN");

      $sql_str = "SELECT * FROM SITES WHERE SITE_NAME = '$SITE_NAME'" ; 
      $my_state = exist_status($sql_str,$act,'SITE_ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu isimle bir site kaydı yapılmış. İkinci Defa Yapamazsınız.");
            exit;
      }  
  
      $args[] = array("SITE_NAME",             $SITE_NAME,       cFldWQuote);
      $args[] = array("SITE_IP",              $SITE_IP,            cFldWQuote);
      $args[] = array("SITE_CODE",             $SITE_CODE,       cFldWQuote);
      $args[] = array("SITE_LOCAL_CODE",      $SITE_LOCAL_CODE,    cFldWQuote);
      $args[] = array("SWITCH_LOG_TYPE_ID",     $SWITCH_LOG_TYPE_ID,  cFldWoQuote);
      $args[] = array("ADMIN_EMAIL",         $ADMIN_EMAIL,      cFldWQuote);
      $args[] = array("ACT_PERIOD",         $ACT_PERIOD,      cFldWQuote);
      $args[] = array("MONTHLY_MAILING_DAY",     $MONTHLY_MAILING_DAY,  cFldWQuote);
      $args[] = array("PRICE_FACTOR",         $PRICE_FACTOR,      cFldWQuote);
      $args[] = array("MONTHLY_MAILING_DEPT_DAY",   $MONTHLY_MAILING_DEPT_DAY,cFldWQuote);
      $args[] = array("MAX_ACCE_DURATION",       $MAX_ACCE_DURATION,    cFldWQuote);
    
      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("SITES", $args);
		$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
      }
   
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("SITE_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("SITES", $args);
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
      header("Location:site.php?act=upd&saved=1&id=".$id);
?>
