<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();
?>
<html>
<head>
<title>Mesajlar</title>
<meta http-equiv="Content-Type" content="text/html; charset=">
<link rel="stylesheet" href="/crystal.css" type="text/css">
</head>

<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="20" marginwidth="0" marginheight="0">
<? if (isset($frm_sira_no)) {

    $user_id = $_SESSION["user_id"];
  

    $sql_str = "SELECT SIRA_NO,KONU,DETAY,A2.NAME FROM_ADI, A2.SURNAME FROM_SOYADI,  A1.NAME TO_ADI, A1.SURNAME TO_SOYADI, INSERT_DATE, INSERT_TIME, READ_IT ".
               "FROM (MESSAGES LEFT JOIN USERS A1 ON MESSAGES.TRG_USER_ID = A1.USER_ID) ".
               "LEFT JOIN USERS A2 ON MESSAGES.INS_USER_ID = A2.USER_ID ".
               "WHERE MESSAGES.TRG_USER_ID = ".$user_id." AND ". 
               "MESSAGES.SIRA_NO = ".$frm_sira_no.";";

  //// If any records found
  if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
      echo "Belirtilen kaydın detayları bulunamadı...FRM SIRA NO: ".$frm_sira_no;
      exit;
  }
  
  $row = mysqli_fetch_object($result);  
  table_header("Mesaj Detayları","");
  
?>
<div align="center">
<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="100%"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr class="header1"> 
          <td width="0%" height="20"><b>KİMDEN</b></td>
          <td width="0%" height="20"><b>KİME</b></td>
          <td width="0%" height="20"><b>TARİH-SAAT</b></td>
        </tr>
         <tr> 
                <td height="20"><? echo $row->FROM_ADI." ".$row->FROM_SOYADI;?></td>
                <td height="20"><? echo ($row->TO_ADI=="" ? "Departmana" :($row->TO_ADI." ".$row->TO_SOYADI));?></td>
                <td height="20"><? echo db2normal($row->INSERT_DATE)." - ".$row->INSERT_TIME;?></td>
        </tr>                
        <tr> <td class="header1" colspan="6" height="20"><b>KONU:</b><br><? echo $row->KONU?></td></tr>
        <tr> <td colspan="6" class="form_text" height="20"><? echo nl2br($row->DETAY)?></td></tr>
  </table>
          </td>
        </tr>
      </table>

<?} //// if result set is not empty...
 ?>
<p align="center"><a href="javascript:close();"><img src="<?=IMAGE_ROOT?>kapat.gif" border="0"></a></p>

<?  table_footer()?>

</body>

</html>
