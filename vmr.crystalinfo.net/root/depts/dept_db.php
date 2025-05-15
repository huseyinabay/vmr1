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
      $sql_str = "SELECT * FROM DEPTS WHERE DEPT_NAME = '$DEPT_NAME' AND SITE_ID = '$SITE_ID'" ; 
      $my_state = exist_status($sql_str,$act,'DEPT_ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu isimle bir departman kaydı yapılmış. İkinci Defa Yapamazsınız.");
            exit;
      }

    
////////////////////////////////////////DELETE DEPT/////////////////////////////////////
// 
      if ($act =="del" && $id!="" && is_numeric($id))  { 
            $sql_str = "DELETE FROM DEPTS WHERE DEPT_ID = '$id' AND SITE_ID = ".$SITE_ID;
      if (!($cDB->execute_sql($sql_str, $result, $error_msg))){
                  print_error($error_msg);
                  exit;
            }
      header("Location:dept_src.php");
    exit;
    }
//////////////////////////////////////////////////////////////////////////////////////////

      $args[] = array("SITE_ID",          $SITE_ID,          cFldWoQuote);
      $args[] = array("DEPT_NAME",          $DEPT_NAME,         cFldWQuote);
      $args[] = array("DEPT_RSP_EMAIL",     $DEPT_RSP_EMAIL,    cFldWQuote);
      
	       if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("DEPTS", $args);
			$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
      }
	  
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("DEPT_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("DEPTS", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
      header("Location:dept.php?act=upd&saved=1&id=".$id);
?>
