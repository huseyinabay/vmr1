<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";

      $cDB = new db_layer();
      require_valid_login();

      if ($IS_GLOBAL == "1" || $IS_GLOBAL == "on"){
            $IS_GLOBAL ="1";
       }else{ 
         $IS_GLOBAL = "0";           
      }   
      
      if ($IS_COMPANY == "1" || $IS_COMPANY == "on"){
            $IS_COMPANY ="1";
       }else{ 
         $IS_COMPANY = "0";           
      }   

      $cDT = new datetime_operations();
      $args = Array();
      $args[] = array("USER_ID",       $USER_ID,            cFldWoQuote);
      $args[] = array("SITE_ID",       $SITE_ID,            cFldWoQuote);
      $args[] = array("NAME",          $NAME,               cFldWQuote);
      $args[] = array("SURNAME",       $SURNAME,            cFldWQuote);
      $args[] = array("COMPANY",       $COMPANY,            cFldWQuote);
      $args[] = array("POSITION",      $POSITION,           cFldWQuote);
      $args[] = array("PERSONAL_EMAIL",$PERSONAL_EMAIL,     cFldWQuote);
      $args[] = array("COMPANY_EMAIL", $COMPANY_EMAIL,      cFldWQuote);
      $args[] = array("WEB_SITE",      $WEB_SITE,           cFldWQuote);
      $args[] = array("ADDRESS",       $ADDRESS,            cFldWQuote);
      $args[] = array("IS_GLOBAL",     $IS_GLOBAL,          cFldWQuote);
      $args[] = array("IS_COMPANY",   $IS_COMPANY,          cFldWQuote);
    
	
		       if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("CONTACTS", $args);
			$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
      }
	  

      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("CONTACT_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("CONTACTS", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
    
////////////////////////////////////////DELETE CONTACT/////////////////////////////////////

      if ($act =="del" && $id!="" && is_numeric($id))  { 
            $sql_str = "DELETE FROM CONTACTS WHERE CONTACT_ID = '$id' AND SITE_ID = ".$SITE_ID;
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $sql_str = "DELETE FROM PHONES WHERE CONTACT_ID = '$id' AND SITE_ID = ".$SITE_ID;
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
      header("Location:contact_src.php");
    exit;
    }
//////////////////////////////////////////////////////////////////////////////////////////

      header("Location:contacts.php?act=upd&saved=1&id=".$id);
?>