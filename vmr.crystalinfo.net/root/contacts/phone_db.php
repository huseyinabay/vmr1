<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
      $cDB = new db_layer();
      require_valid_login();
    if($SITE_ID==0){
      $SITE_ID=$_SESSION['site_id'];
    }
    $sql_str = "SELECT SITE_ID FROM CONTACTS WHERE CONTACT_ID = '$CONTACT_ID'";
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
     }
     if (mysqli_num_rows($result)>0){
       $row = mysqli_fetch_object($result);
       $site_id = $row->SITE_ID;
     }
      $sql_str = "SELECT * FROM PHONES INNER JOIN CONTACTS ON PHONES.CONTACT_ID = CONTACTS.CONTACT_ID
          WHERE PHONES.SITE_ID = '$site_id' AND PHONES.COUNTRY_CODE= '$CountryCode'
          AND PHONES.CITY_CODE='$LocalCode' AND PHONES.PHONE_NUMBER='$PHONE_NUMBER'";
      $my_state = exist_status($sql_str,$act,'PHONE_ID',$id);
      if ($my_state==1 || $my_state==2){
            echo("Bu telefon bulunduğunuz site için daha önce kayıt edilmiş. 
                  İkinci defa kaydedemezsiniz. Eğer bu telefonu bu kayıt için eklemek 
                  istiyorsanız diğer kayıttan siliniz.");
            exit;
      }      
      

    $CITY_CODE = ltrim($LocalCode, "0");
    
    $cDT = new datetime_operations();
      $args = Array();
      $args[] = array("SITE_ID",         $site_id,          cFldWoQuote);
      $args[] = array("PHONE_TYPE_ID",   $PHONE_TYPE_ID,      cFldWoQuote);
      $args[] = array("COUNTRY_CODE",    $CountryCode,       cFldWQuote);
      $args[] = array("CITY_CODE",       $LocalCode,          cFldWQuote);
      $args[] = array("PHONE_NUMBER",    $PHONE_NUMBER,       cFldWQuote);
      $args[] = array("EXTENSION",       $EXTENSION,          cFldWQuote);
      $args[] = array("DESCRIPTION",     $DESCRIPTION,        cFldWQuote);

      if ($act =="" || $act =="new" )  { 
          $args[] = array("CONTACT_ID",    $CONTACT_ID,         cFldWQuote);
          $sql_str =  $cDB->InsertString("PHONES", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $id = mysqli_insert_id($cDB->getConnection());
      }
   
      if($act == "upd" && $id !="" && is_numeric($id)){
            $args[] = array("PHONE_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("PHONES", $args);
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }

      }

      //Telefon kaydının silinmesi için Contacts.php 'den buraya del ile geliniyor.
      if($act == "del" && $id !="" && is_numeric($id)){
      $sql_str = "DELETE FROM PHONES WHERE PHONE_ID = ".$id;
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            } 
      }     
?>
<script language="Javascript">
   window.opener.location.reload();
   window.close();
</script>
