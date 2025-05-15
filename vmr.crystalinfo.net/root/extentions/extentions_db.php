<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
      $cDB = new db_layer();
      require_valid_login();

     //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
     }

      $sql_str = "SELECT * FROM EXTENTIONS WHERE EXT_NO = '$EXT_NO' AND SITE_ID = '$SITE_ID'" ; 
      $my_state = exist_status($sql_str,$act,'EXT_ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu Sitede bu isimle bir dahili kaydı var. İkinci Defa Yapamazsınız.");
            exit;
      }

      $cDT = new datetime_operations();
      $args = Array();
      if ($RESIDE_IN_EXTEN==''){$RESIDE_IN_EXTEN=0;}

      $args[] = array("EXT_NO",             $EXT_NO,         cFldWQuote);
      $args[] = array("SITE_ID",            $SITE_ID,        cFldWoQuote);
      $args[] = array("EMAIL",              $EMAIL,          cFldWQuote);
      $args[] = array("DEPT_ID",            $DEPT_ID,        cFldWQuote);
      $args[] = array("DESCRIPTION",        $DESCRIPTION,    cFldWQuote);
      $args[] = array("RESIDE_IN_EXTEN",    $RESIDE_IN_EXTEN,cFldWoQuote);

      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("EXTENTIONS", $args);
			$id = $cDB->execute_insert($sql_str,$result,$error_msg);
            if (!$id){
                  print_error($error_msg);
                  exit;
            }
      }
   
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("EXT_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("EXTENTIONS", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }

    if($act == "del" && $id !="" && is_numeric($id)){
          $sql_str = "DELETE FROM EXTENTIONS WHERE EXT_ID = ".$id." AND SITE_ID =".$SITE_ID;
          if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                      print_error($error_msg);
                      exit;
                }
         header("Location:extentions_src.php");
         exit;
      }

      header("Location:extentions.php?act=upd&saved=1&id=".$id);

?>
