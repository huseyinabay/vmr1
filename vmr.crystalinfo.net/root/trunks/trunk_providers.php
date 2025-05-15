<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();

   //Site Admin veya Admin Hakkı yoksa bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Burayı Görme Hakkınız Yok");
    exit;
   }

?><body bgcolor="F7FBFF"><?   
   // YUKARI >>
   if ($upbtn && $id && $oldpriority && $priority)
   {
      if($oldpriority>1){
        $newpriority = $oldpriority-1;
        $strSQL = " SELECT TRUNK_PROVIDER_ID, PRIORITY FROM TRUNK_PROVIDER WHERE TRUNK_ID=".$id." 
                    AND PRIORITY IN(".$newpriority.", ".$oldpriority.") ORDER BY PRIORITY  ";
        if (!($cdb->execute_sql($strSQL, $result1, $error_msg))){
          print_error($error_msg);
          exit;
        }
        $j = 0;
        if (mysqli_num_rows($result1)>0){
          while($row1 = mysqli_fetch_object($result1)){
              if($row1->PRIORITY == $oldpriority){
                $oldprid = $row1->TRUNK_PROVIDER_ID;
              }else{
                $newprid = $row1->TRUNK_PROVIDER_ID;
              }
          }
          mysql_query("UPDATE TRUNK_PROVIDER SET PRIORITY = ".$newpriority." WHERE TRUNK_PROVIDER_ID = ".$oldprid);
          mysql_query("UPDATE TRUNK_PROVIDER SET PRIORITY = ".$oldpriority." WHERE TRUNK_PROVIDER_ID = ".$newprid);
        }
      }
   }
   // AŞAĞI >>
   if ($downbtn && $id && $oldpriority && $priority)
   {
      if($oldpriority < $priority){
        $newpriority = $oldpriority+1;
        $strSQL = " SELECT TRUNK_PROVIDER_ID, PRIORITY FROM TRUNK_PROVIDER WHERE TRUNK_ID=".$id." 
                    AND PRIORITY IN(".$newpriority.", ".$oldpriority.") ORDER BY PRIORITY  ";
        if (!($cdb->execute_sql($strSQL, $result1, $error_msg))){
          print_error($error_msg);
          exit;
        }
        $j = 0;
        if (mysqli_num_rows($result1)>0){
          while($row1 = mysqli_fetch_object($result1)){
              if($row1->PRIORITY == $oldpriority){
                $oldprid = $row1->TRUNK_PROVIDER_ID;
              }else{
                $newprid = $row1->TRUNK_PROVIDER_ID;
              }
          }
          mysql_query("UPDATE TRUNK_PROVIDER SET PRIORITY = ".$newpriority." WHERE TRUNK_PROVIDER_ID = ".$oldprid);
          mysql_query("UPDATE TRUNK_PROVIDER SET PRIORITY = ".$oldpriority." WHERE TRUNK_PROVIDER_ID = ".$newprid);
        }
      }
   }
   // EKLEME >>
   if ($ekle && $id && $eklenen && $priority)
   {
      mysql_query("INSERT INTO TRUNK_PROVIDER (TRUNK_ID, PROVIDER_TARIFF_ID, PRIORITY) VALUES ('$id', '$eklenen', '$priority')");
   }
   // CIKARMA <<
   if ($cikar && $id && $cikarilan)
   {
      mysql_query("DELETE FROM TRUNK_PROVIDER WHERE PROVIDER_TARIFF_ID = $cikarilan AND TRUNK_ID = $id");
      $strSQL = " SELECT TRUNK_PROVIDER_ID FROM TRUNK_PROVIDER WHERE TRUNK_ID=".$id." ORDER BY PRIORITY  ";
      if (!($cdb->execute_sql($strSQL, $result1, $error_msg))){
        print_error($error_msg);
        exit;
      }
      $j = 0;
      if (mysqli_num_rows($result1)>0){
        while($row1 = mysqli_fetch_object($result1)){
            $j = $j+1;
            mysql_query("UPDATE TRUNK_PROVIDER SET PRIORITY = ".$j." WHERE TRUNK_PROVIDER_ID = ".$row1->TRUNK_PROVIDER_ID);
        }
      }
   }
   
  $cell_header_left = "<font face=Verdana size=1>Eklenebilecek Tarifeler</font>";
  $cell_header_right = "<font face=Verdana size=1>Eklenmiş Tarifeler</font>";
  cc_page_meta(0);
?>

<script language="javascript">
  function check_transfer(obj){
    if (obj.selectedIndex == -1) { return false; }
    return true;
  }
</script>

<form name="trunk_provider" method="post" action="trunk_providers.php?act=<? echo $_GET['act'] ?>">
<input type="hidden" name="id" value="<?=$id?>">
<table>
<tr><td>
<table width=100% height=100% class="formbgx" bgcolor="F7FBFF">
<tr><td width=180 class=generic><?=$cell_header_left?></td>
<td width=50>&nbsp;</td>
<td width=180><?=$cell_header_right?></td>
</tr>
<tr>
<td width=180 valign=top align=center>
<select name=eklenen size="6" style="width=200" class="select1">
         <?
         $strSQL = "SELECT PROVIDER_TARIFF_ID FROM TRUNK_PROVIDER WHERE TRUNK_ID = $id";
         $cdb->execute_sql($strSQL, $arg_result, $arg_error_msg);
         $query = "";
         while ($row = mysqli_fetch_object($arg_result))
         {
          $query .= '\''.$row->PROVIDER_TARIFF_ID.'\',';
         }
         if ($query) { $query = "WHERE ProviderTariffid NOT IN (".substr($query,0,-1).")"; }
         else { $query = "WHERE 1=1"; }
         $strSQL = "SELECT ProviderTariffid, TariffName FROM TProviderTariff $query";
         echo    $cUtility->FillComboValuesWSQL($conn, $strSQL, false, "");
          ?>
</select>
</td>
<td width=50 valign=top align=center>
<input id=ekle type="submit" name="ekle" value=">>" onclick="javascript:return check_transfer(document.all('eklenen'));"><p>
<input id=cikar type="submit" name="cikar" value="<<" onclick="javascript:return check_radio(document.all('cikarilan'));">
</td>
<td width=200 valign=top align=left>
<!--<select name=cikarilan size="6" style="width=200" class="select1">-->
         <?
         $strSQL = " SELECT DISTINCT ProviderTariffid, TariffName, PRIORITY FROM TProviderTariff ". 
                   " LEFT JOIN TRUNK_PROVIDER ". 
                   " ON TProviderTariff.ProviderTariffid = TRUNK_PROVIDER.PROVIDER_TARIFF_ID ".
                   " WHERE TRUNK_PROVIDER.TRUNK_ID = $id ORDER BY TRUNK_PROVIDER.PRIORITY";
//         echo    $cUtility->FillComboValuesWSQL($conn, $strSQL, false, "");
           if (!($cdb->execute_sql($strSQL,$result,$error_msg))){
                print_error($error_msg);
                exit;
             }
          $k = 0;
          if (mysqli_num_rows($result)>0){
            table_in_table_header(" ","100%");
               while($row = mysqli_fetch_object($result)){
               ?><tr>
                 <td class="bg_acik_f_koyu" width="20%"><input type="radio" name = "cikarilan" value="<?=$row->ProviderTariffid?>"> <?=$row->PRIORITY?></td>
                 <td class="bg_koyu_f_acik"  width="80%"><?=$row->TariffName?> </td>
               
               </tr><?
               $k = $row->PRIORITY;
               }
            table_in_table_footer();
          }
          $priority = $k+1;
         ?>
<input type="hidden" name="priority" value="<?=$priority?>">
<input type="hidden" name="oldpriority" value="">
</td>
</tr>
</table>
</td>
<td>
<table>
<tr height="12"><td></td></tr>
 <tr>
  <td>
    <input type="submit" name="upbtn"  value="Yukarı" onclick="javascript:return selectedpriority(document.all('cikarilan'), 'up');">
  </td>
 </tr>
 <tr>
  <td>
    <input type="submit" name="downbtn" value="Aşağı " onclick="javascript:return selectedpriority(document.all('cikarilan'), 'down');">
  </td>
 </tr>
</table>
</td>
</tr></table>
</form>

<script language="javascript">
  function check_radio(obj){
    var cnt;
    var i;
    var goout;
    if(document.all('priority').value>2){
      cnt = document.all('priority').value;
      for(i=0;i<=(cnt-2);i++){
       if(obj[i].checked){
         return true;
       }
      }
      return false;
    }else if(document.all('priority').value>1){
      if(obj.checked){return true;}
      return false;
    }else{
      return false;
    }  
  }
function selectedpriority(obj, btndir){
    var cnt;
    var i;
    var goout;
    var startval
    var endval;
    if(document.all('priority').value>2){
      cnt = document.all('priority').value;
      if(btndir == 'up'){ 
        startval = 1;
        endval = cnt-2;
      }else{
        startval = 0;
        endval = cnt-3;
      }
      for(i=startval;i<=endval;i++){
       if(obj[i].checked){
         document.all('oldpriority').value = (i+1); 
         return true;
       }
      }
    }
    return false;

}
</script>
