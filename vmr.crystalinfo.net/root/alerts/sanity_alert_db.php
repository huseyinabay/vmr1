<?
        require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
        $cDB = new db_layer();
        require_valid_login();
       check_right("ADMIN");

        if ($ALERT_DEF_ID!=="" && is_numeric($ALERT_DEF_ID)){
      $args = Array();
      $args[] = array("ALERT_DEF_ID",  $ALERT_DEF_ID,  cReqWoQuote);
      $args[] = array("FREQUENCY",    $FREQUENCY,     cFldWoQuote);

        $sql_str =  $cDB->UpdateString("ALERT_DEFS", $args);
          if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
            print_error($error_msg);
             exit;
         }
    }
      header("Location:sanity_alert.php?id=".$ALERT_DEF_ID);
?>
