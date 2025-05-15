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

   //Kullanıcının site id si bulunmalı
   $sql_str = "SELECT SITE_ID FROM USERS WHERE USER_ID = $id";
   if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
  }
  if (mysqli_num_rows($result)>0){
    $row = mysqli_fetch_object($result);
    $site_crt = " AND SITE_ID=".$row->SITE_ID;
    $my_site_id = $row->SITE_ID;
  }else{
     print_error("Lütfen kullanıcının bağlı olduğu Site'yi seçiniz.");
       exit;    
  }
   
?><body bgcolor="F7FBFF"><?   
   
   // EKLEME >>
   if ($ekle && $id && $eklenen)
   {//Site Admin arkadaş eğer bir kullanıcıyı önce bir siteye ekler ona haklar verir daha sonrada onun sitesini
    //değiştirise bu durumda Site Id olmazsa her iki sitede hakkı olan departmanları görür. Oysa
  //sadece kendi sitesindeki deprtmanları görmeli. Oluşan tutarsızlığı sistemi etkilemesi önleniyor.
      mysqli_query($conn, "INSERT INTO DEPT_REP_RIGHTS (USER_ID, DEPT_ID,SITE_ID) VALUES ('$id', '$eklenen','$my_site_id')");
   }
   // CIKARMA <<
   if ($cikar && $id && $cikarilan)
   {
      mysqli_query($conn, "DELETE FROM DEPT_REP_RIGHTS WHERE DEPT_ID = '$cikarilan' AND USER_ID = '$id' AND SITE_ID='$my_site_id'");
   }
   
  $cell_header_left = "<font face=Verdana size=1>Raporunu Alabileceği Departmanlar</font>";
  $cell_header_right = "<font face=Verdana size=1>Raporunu Aldığı Departmanlar</font>";
  cc_page_meta(0);
?>

<script language="javascript">
  function check_transfer(obj){
    if (obj.selectedIndex == -1) { return false; }
    return true;
  }
</script>

<form name="user_transfer" method="post" action="dept_rep_rights.php?act=<? echo $_GET['act'] ?>">
<input type="hidden" name="id" value="<?=$id?>">
<table width=100% height=100% class="formbgx" bgcolor="F7FBFF" border="0">
    <tr><td width=260 class=generic><?=$cell_header_left?></td></tr>
    <tr>
        <td width=260 valign=top>
            <select name=eklenen size="6" style="width:270px" class="select1">
                 <?
                 $strSQL = "SELECT DEPT_ID FROM DEPT_REP_RIGHTS WHERE USER_ID = $id";
                 $cdb->execute_sql($strSQL, $arg_result, $arg_error_msg);
                 $query = "";
                 while ($row = mysqli_fetch_object($arg_result))
                 {
                  $query .= '\''.$row->DEPT_ID.'\',';
                 }
                 if ($query) { $query = "WHERE DEPT_ID NOT IN (".substr($query,0,-1).")"; }
                 else { $query = "WHERE 1=1"; }
                 $strSQL = "SELECT DEPTS.DEPT_ID, DEPTS.DEPT_NAME FROM DEPTS $query $site_crt ORDER BY DEPT_NAME";
                 echo $cUtility->FillComboValuesWSQL($conn, $strSQL, false, "");
                  ?>
            </select>
        </td>
    </tr>        
    <tr>
        <td align="center" valign=top>
        <input id=ekle type="submit" name="ekle" value="Ekle" onclick="javascript:return check_transfer(document.all('eklenen'));">&nbsp;&nbsp;
        <input id=cikar type="submit" name="cikar" value="Çıkar" onclick="javascript:return check_transfer(document.all('cikarilan'));">
        </td>    
    </tr>
    <tr><td width=260><?=$cell_header_right?></td></tr>
    <tr>
        <td width=260 valign=top>
            <select name=cikarilan size="6" style="width:270px" class="select1">
                 <?
                 $strSQL = " SELECT DISTINCT DEPTS.DEPT_ID, DEPTS.DEPT_NAME FROM DEPTS ". 
                           " LEFT JOIN DEPT_REP_RIGHTS ". 
                           " ON DEPTS.DEPT_ID = DEPT_REP_RIGHTS.DEPT_ID ".
                           " WHERE DEPT_REP_RIGHTS.USER_ID = $id ORDER BY DEPT_ID";
                 echo    $cUtility->FillComboValuesWSQL($conn, $strSQL, false, "");
                 ?>
            </select>
        </td>
    </tr>
</table>
</form>


