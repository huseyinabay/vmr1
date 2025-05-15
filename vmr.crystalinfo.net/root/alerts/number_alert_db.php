﻿<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
    $cDB = new db_layer();
    require_valid_login();
  
  //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
  if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
     print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
  }
  //Ülke kodu alanı mutlaka dolu olmalı
  if ($CountryCode==''){
    $CountryCode = get_country_code($SITE_ID);
  }

  //Bir uyarı silinecekse onun kriterleri ve gönderilecek E-mail adresleri de silinmeli.
  if($act == "del" && $id !="" && is_numeric($id)){
     $sql_str = "DELETE FROM ALERT_DEFS WHERE ALERT_DEF_ID = $id"; 
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
     $sql_str = "DELETE FROM ALERT_CRT WHERE ALERT_DEF_ID = $id"; 
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
     $sql_str = "DELETE FROM ALERT_TO_EMAIL WHERE ALERT_DEF_ID = $id"; 
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
         header("Location:number_alert_src.php");
    exit;
  }else{
    //Uyarı eklendiğinde veya değiştirildiğinde mutlaka yapıldığı andan sonraki
    //kayıtlar için işlemeli. Geçmiş kayıtlarla ilgilenmemeli.
    $sql_str = "SELECT MAX(CDR_ID) AS LAST_ID FROM CDR_MAIN_DATA WHERE SITE_ID = $SITE_ID"; 
    if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
      print_error($error_msg);
      exit;
    }
    if (mysql_num_rows($result)>0){
      $row = mysqli_fetch_object($result);
      $LAST_PROC_ID = $row->LAST_ID;
    }

    // act=upd ise kayıtların bulunmasında sorun olabileceği için mevcut kayıtlar siliniyor
    if($act == "upd" && $id !="" && is_numeric($id)){
      $sql_str = "DELETE FROM ALERT_CRT WHERE ALERT_DEF_ID = $id"; 
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
      }
      //Kayıt güncellenmesi yapılıyor.
      $args = Array();
      $args[] = array("ALERT_DEF_ID",      $id,         	cReqWoQuote);
      $args[] = array("ALERT_DEF_NAME",    $ALERT_DEF_NAME, cFldWQuote);
      $args[] = array("LAST_PROC_ID",      $LAST_PROC_ID,   cFldWQuote);
      $args[] = array("SITE_ID",           $SITE_ID,        cFldWQuote);
      $sql_str =  $cDB->UpdateString("ALERT_DEFS", $args);
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
      }
    }
    //Yeni kayıtsa o kayıt ile ilgili bilgiler girilmeli.
     if ($act=='new' || $act==''){ 
      $args = Array();
      $args[] = array("ALERT_ID",           '1',           	  cFldWoQuote);
      $args[] = array("ALERT_DEF_NAME",     $ALERT_DEF_NAME,  cFldWQuote);
      $args[] = array("LAST_PROC_ID",       $LAST_PROC_ID,    cFldWQuote);
      $args[] = array("SITE_ID",            $SITE_ID,         cFldWQuote);

                  $sql_str =  $cDB->InsertString("ALERT_DEFS", $args);
		$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
    }
    //act yeni veya update olması önemli değil. İlgili kriterler eklenmeli.
    $args2 = Array();
    $args2[] = array("ALERT_DEF_ID",    $id,        cFldWoQuote);
    $args2[] = array("FIELD_NAME",      'SITE_ID',  cFldWQuote);
    $args2[] = array("OPERATOR",        '=',        cFldWQuote);
    $args2[] = array("VALUE",           $SITE_ID,   cFldWoQuote);
    $sql_str =  $cDB->InsertString("ALERT_CRT", $args2);
    if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
      print_error($error_msg);
      exit;
    }
    unset($args2);

    $args2 = Array();
    $args2[] = array("ALERT_DEF_ID",   $id,             cFldWoQuote);
    $args2[] = array("FIELD_NAME",     'CountryCode',   cFldWQuote);
    $args2[] = array("OPERATOR",       '=',             cFldWQuote);
    $args2[] = array("VALUE",          $CountryCode,    cFldWQuote);
    $sql_str =  $cDB->InsertString("ALERT_CRT", $args2);
    if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
           exit;
      }
    unset($args2);

    $args2 = Array();
    $args2[] = array("ALERT_DEF_ID",    $id,           cFldWoQuote);
    $args2[] = array("FIELD_NAME",      'LocalCode',   cFldWQuote);
    $args2[] = array("OPERATOR",        '=',           cFldWQuote);
    $args2[] = array("VALUE",           $LocalCode,    cFldWQuote);
    $sql_str =  $cDB->InsertString("ALERT_CRT", $args2);
    if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
      print_error($error_msg);
      exit;
    }
    unset($args2);

    $args2 = Array();
    $args2[] = array("ALERT_DEF_ID",     $id,             cFldWoQuote);
    $args2[] = array("FIELD_NAME",       'PURE_NUMBER',   cFldWQuote);
    $args2[] = array("OPERATOR",         '=',             cFldWQuote);
    $args2[] = array("VALUE",            $PURE_NUMBER,    cFldWQuote);
    $sql_str =  $cDB->InsertString("ALERT_CRT", $args2);
    if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
      print_error($error_msg);
      exit;
    }
  }
  header("Location:number_alert.php?act=upd&saved=1&id=".$id);
?>
