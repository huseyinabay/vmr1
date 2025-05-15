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
	  
	  
	  
	  
	   $sql_str1 = "SELECT * FROM EXTENTIONS WHERE TN = '$TN' AND SITE_ID = '$SITE_ID' AND TN!=''" ; 
      $my_state = exist_status($sql_str1,$act,'EXT_ID',$id);
      if ($my_state==1 || $my_state==2 ){
            print_error("Bu Sitede bu TN kaydý var. Ýkinci Defa Yapamazsýnýz.");
            exit;
      }

		$sql_str2 = "SELECT * FROM EXTENTIONS WHERE PER_NO = '$PER_NO' AND SITE_ID = '$SITE_ID' AND PER_NO!=''" ; 
      $my_state = exist_status($sql_str2,$act,'EXT_ID',$id);
      if ($my_state==1 || $my_state==2){
            print_error("Bu Sitede bu PER NO kaydý var. Ýkinci Defa Yapamazsýnýz.");
            exit;
      }



      $cDT = new datetime_operations();
      $args = Array();
      if ($RESIDE_IN_EXTEN==''){$RESIDE_IN_EXTEN=0;}

      $args[] = array("EXT_NO",             $EXT_NO,         cFldWQuote);
      $args[] = array("SITE_ID",            $SITE_ID,        cFldWoQuote);
	  $args[] = array("ACCOUNT_NO",           $ACCOUNT_NO,       cFldWQuote);
      $args[] = array("EMAIL",              $EMAIL,          cFldWQuote);
      $args[] = array("DEPT_ID",            $DEPT_ID,        cFldWQuote);
      $args[] = array("DESCRIPTION",        $DESCRIPTION,    cFldWQuote);
      $args[] = array("RESIDE_IN_EXTEN",    $RESIDE_IN_EXTEN,cFldWoQuote);
	  $args[] = array("TN",   			    "'".$TN."'",  cFldWoQuote);
      $args[] = array("YER",   	  	  	  "'".$YER."'",    cFldWoQuote);
      $args[] = array("PER_NO",              $PER_NO,        cFldWQuote);

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
