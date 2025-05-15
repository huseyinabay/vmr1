<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	 
	 //ini_set('display_errors', 'On');
     //error_reporting(E_ALL);
	 
      $cDB = new db_layer();
      require_valid_login();

      $cDT = new datetime_operations();
      $args = Array();

      //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
       if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
          print_error("Burayı Görme Hakkınız Yok");
     exit;
       }

      $sql_str = "SELECT * FROM USERS WHERE USERNAME = '$USERNAME'" ; 
	  
      $my_state = exist_status($sql_str,$act,'USER_ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu kullanıcı kaydı daha önce yapılmış. İkinci Defa Yapamazsınız.");
            exit;
      }

////////////////////////////////////////DELETE USER/////////////////////////////////////
// bir kullanıcı silinince nelerin silinmesi gerektiği belirlenmeli.
      if ($act =="del" && $id!="" && is_numeric($id))  { 
            $sql_str = "DELETE FROM USERS WHERE USER_ID = '$id' AND SITE_ID = ".$SITE_ID;
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
      header("Location:user_src.php");
    exit;
    }
//////////////////////////////////////////////////////////////////////////////////////////

      $args[] = array("SITE_ID",        $SITE_ID,       cFldWoQuote);
      $args[] = array("USERNAME",       $USERNAME,       cFldWQuote);
//      $args[] = array("PASSWORD",       $PASSWORD,       cFldWQuote);
      $args[] = array("NAME",           $NAME,           cFldWQuote);
      $args[] = array("SURNAME",        $SURNAME,        cFldWQuote);
      $args[] = array("POSITION",       $POSITION,       cFldWQuote);
      $args[] = array("EMAIL",          $EMAIL,          cFldWQuote);
      $args[] = array("LAST_UPDATER",   $LAST_UPDATER,   cFldWQuote);
      $args[] = array("LAST_UPDATE",    $LAST_UPDATE,    cFldWQuote);
      $args[] = array("DISABLED",       $DISABLED,       cFldWQuote);
      $args[] = array("LAST_TOUCH",     $LAST_TOUCH,     cFldWQuote);
      $args[] = array("EXT_ID1",        $EXT_ID1,       cFldWQuote);
      $args[] = array("EXT_ID2",        $EXT_ID2,       cFldWQuote);
      $args[] = array("EXT_ID3",        $EXT_ID3,       cFldWQuote);
      $args[] = array("DEPT_ID",        $DEPT_ID,        cFldWQuote);
      $args[] = array("AUTH_CODE_ID",   $AUTH_CODE_ID,  cFldWoQuote);
      $args[] = array("GSM",            $GSM,            cFldWQuote);
      $args[] = array("HOME_TEL",       $HOME_TEL,       cFldWQuote);
      $args[] = array("NOTE",           $NOTE,           cFldWQuote);

      
	 
	  
      if ($act =="" || $act =="new" )  { 
                        $sql_str =  $cDB->InsertString("USERS", $args);
		$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
      }
   
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("USER_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("USERS", $args);
//echo $sql_str;die;
			
			
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }

      }

      header("Location:user.php?act=upd&saved=1&id=".$id);

?>
