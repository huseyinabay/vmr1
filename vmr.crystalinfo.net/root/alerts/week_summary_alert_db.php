<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  $cDB = new db_layer();
   require_valid_login();
  //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
       print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
  }

      $sql_str = "SELECT * FROM ALERT_DEFS WHERE ALERT_ID = 13 AND SITE_ID = '$SITE_ID'" ; 
      $my_state = exist_status($sql_str,$act,'ALERT_DEF_ID',$id);
      if ($my_state==1 || $my_state==2){//1 yeni kayıt ve mevcut 2 update ve mevcut
      print_error("Bu site için Haftalık Rapor kaydı sistemde mevcuttur.");
            exit;
      }      

  if($act == "del" && $id !="" && is_numeric($id)){
     $sql_str = "DELETE FROM ALERT_DEFS WHERE ALERT_DEF_ID = $id"; 
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
     $sql_str = "DELETE FROM ALERT_TO_EMAIL WHERE ALERT_DEF_ID = $id"; 
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
      header("Location:week_summary_alert_src.php");
    exit;
  }else{
    if($act == "upd" && $id !="" && is_numeric($id)){
      //Kayıt güncellenmesi yapılıyor.
            $args = Array();
      $args[] = array("ALERT_DEF_ID",      $id,         cReqWoQuote);
          $args[] = array("SITE_ID",          $SITE_ID,     cFldWQuote);
            $sql_str =  $cDB->UpdateString("ALERT_DEFS", $args);
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
    //Yeni kayıtsa o kayıt ile ilgili bilgiler girilmeli.
     if ($act=='new' || $act==''){ 
         $args = Array();
      $args[] = array("ALERT_ID",           '13',             cFldWoQuote);
      $args[] = array("ALERT_DEF_NAME",        'Haftanın Konuşma Özeti',   cFldWQuote);
          $args[] = array("SITE_ID",          $SITE_ID,         cFldWQuote);
      $args[] = array("FREQUENCY",        '2',             cFldWoQuote);
                        
		$sql_str =  $cDB->InsertString("ALERT_DEFS", $args);
		$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
    }
  }
    header("Location:week_summary_alert.php?act=upd&saved=1&id=".$id);
?>
