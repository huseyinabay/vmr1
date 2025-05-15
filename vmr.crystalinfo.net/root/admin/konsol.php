<?  //INCLUDES
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   require_valid_login();
    
   $cUtility = new Utility();
   $cdb = new db_layer();
   
  // $SITE_ID = $_SESSION['site_id'];
   //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Burayı Görme Hakkınız Yok");
    exit;
   }
   $conn = $cdb->getConnection();
   if (!right_get("SITE_ADMIN"))
       $SITE_ID = $_SESSION['site_id'];
   if (right_get("SITE_ADMIN") && ($SITE_ID=="" || $SITE_ID=="-1"))
       $SITE_ID = $_SESSION['site_id'];

   $page = $_SERVER['PHP_SELF'];
$sec = "17";   // auto refresh page
   
?>
<link rel="stylesheet" href="/crystal.css" TYPE="text/css">
<?
   cc_page_meta();
   echo "<center>";
   page_header();
?>


<html>
    <head>
    <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
    </head>
    <body>


<table border="0" width="600" height="400" ALIGN="left" >
  <form name="site" action="konsol.php" method="post">
  <tr class="form">
        <td class="td1_koyu" width="40%" align="right">Site Adı</td>
             <td>
                 <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="Fillothercombos(this.value)">
                     <?
                         $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ORDER BY SITE_NAME";
                         echo $cUtility->FillComboValuesWSQL($conn, $strSQL, false,  $SITE_ID);
                     ?>
                 </select>
             </td>
    </tr>
  </form>
  <tr>
      <td valign="bottom" align="center" colspan="2">Ham Data Konsol (Veriler her 15 saniyede bir güncellenir)</td>
      <input type="hidden" name="ID" value="">
    </tr>
  <tr>

	<td align="center" colspan="3" height="350" valign="bottom" width="2586" style="background-color: #000000; color: #008000; font-weight: bold"> 
          
<?
     session_cache_limiter('nocache');     
   
			$processed="SELECT ID, SITE_ID, REPLACE(DATA,'&',' ') AS DATA FROM RAW_ARCHIEVE WHERE SITE_ID = '$SITE_ID' ORDER BY ID DESC LIMIT 0, 15";		  
  
    if (!($cdb->execute_sql($processed, $rs_processed, $error_msg))){
        print_error($error_msg);
                exit;
}
   echo "<marquee align='middle' scrollamount='1' height='300' width='2386%' direction='down' scrolldelay='0'><font color='#00FF00'>";      
   while($row = mysqli_fetch_array($rs_processed))
   echo "{$row['SITE_ID']}-{$row['DATA']}<br>";
   echo "</font></marquee>";

?>
            </td>
		
		
  </tr>
</table>
    </body>
</html>
<?page_footer(0);?>



