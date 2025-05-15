<?
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cDB = new db_layer();
    require_valid_login();
  $cDT = new datetime_operations();

  if($act == "del" && $id !="" && is_numeric($id)){
      //İlk önce ilgili alanlar boşaltılmalı ki aynı kayıtta fazla 
      $sql_str = "DELETE FROM QUOTA_ASSIGNS WHERE QUOTA_ASSIGN_ID = '$id'" ;
    if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
           print_error($error_msg);
           exit;
     }
     if ($from =="main"){?>
       <script>
      //window.reload();
        if(window.parent.frames.length > 0)
          window.parent.location.href='quota_assign.php?act=upd&q_id='+<?=$qu_id?>;
        else
          window.location.href = 'quota_report.php?id=<?=$qu_id?>';
      </script>
    <?}
    exit;
  }
  
  if ($act =="" || $act =="new" )  { 
       $sql_str = "SELECT QUOTA_ASSIGNS.*, QUOTAS.SITE_ID FROM QUOTA_ASSIGNS 
          INNER JOIN QUOTAS ON QUOTA_ASSIGNS.QUOTA_ID = QUOTAS.QUOTA_ID
          WHERE QUOTA_ASSIGN_TYPE_ID = $QUOTA_ASSIGN_TYPE_ID AND QUOTAS.SITE_ID = $SITE_ID" ; 
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
           print_error($error_msg);
           exit;
        }
       $i;
         while($row = mysqli_fetch_object($result)){
           $i++;
           switch($QUOTA_ASSIGN_TYPE_ID){
            case 1:
              if ($row->OBJ_ID == $EXT_ID ){
                echo "Bu Dahili İçin Daha Önce Kota Tanımlaması Yapmışsınız.
                    Bu Atamayı Güncellemek İstiyorsanız Güncelle'yi Tıklayınız";
                echo "<a href=\"quota_assign.php?act=upd&id=".$row->QUOTA_ASSIGN_ID."\">Güncelle</a>";
                exit;
              }
              break;
            case 2:
              if ($row->OBJ_ID == $DEPT_ID) {
                echo "Bu Departman İçin Daha Önce Kota Tanımlaması Yapmışsınız.<br>
                    Bu Atamayı Güncellemek İstiyorsanız Güncelle'yi Tıklayınız.<br>";
                echo "<a href=\"quota_assign.php?act=upd&id=".$row->QUOTA_ASSIGN_ID."\">Güncelle</a>";
                exit;
              }  
              break;
            case 3:
              if ($row->OBJ_ID == $DEPT_ID){
                echo "Bu Departman İçin Daha Önce Kota Tanımlaması Yapmışsınız.
                  Bu Atamayı Güncellemek İstiyorsanız Güncelle'yi Tıklayınız";
                echo "<a href=\"quota_assign.php?act=upd&id=".$row->QUOTA_ASSIGN_ID."\">Güncelle</a>";
                exit;
              }  
              break;
           case 4:
              if ($row->OBJ_ID == $AUTH_CODE_ID ){
                echo "Bu Auth. Kodu İçin Daha Önce Kota Tanımlaması Yapmışsınız.
                  Bu Atamayı Güncellemek İstiyorsanız Güncelle'yi Tıklayınız";
                echo "<a href=\"quota_assign.php?act=upd&id=".$row->QUOTA_ASSIGN_ID."\">Güncelle</a>";
                exit;
              }
              break;
            default:
          }
        }
       }
      switch ($QUOTA_ASSIGN_TYPE_ID) {
      case 1:
        $FIELD_VALUE= $EXT_ID;
        break;
      case 2:
        $FIELD_VALUE= $DEPT_ID;
        break;
      case 3:
        $FIELD_VALUE= $DEPT_ID;
        break;
      case 4:
        $FIELD_VALUE= $AUTH_CODE_ID;      
    }
    
    $args = Array();
    $args[] = array("QUOTA_ID",        $QUOTA_ID,        cFldWoQuote);
        $args[] = array("QUOTA_ASSIGN_TYPE_ID",  $QUOTA_ASSIGN_TYPE_ID,  cFldWoQuote);
    $args[] = array("OBJ_ID",        $FIELD_VALUE,      cFldWoQuote);
    $args[] = array("DESCRIPTION",      $DESCRIPTION,      cFldWQuote);

      if ($act =="" || $act =="new" )  { 
            $sql_str =  $cDB->InsertString("QUOTA_ASSIGNS", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $id = mysqli_insert_id($cDB->getConnection());
      }

      if($act == "upd" && $id !="" && is_numeric($id)){
          $args[] = array("QUOTA_ASSIGN_ID",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("QUOTA_ASSIGNS", $args);
            if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      }
      header("Location:quota_assign.php?act=upd&id=".$id);
?>