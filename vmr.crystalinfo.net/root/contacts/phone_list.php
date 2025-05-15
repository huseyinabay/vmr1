<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();

   if (right_get("SITE_ADMIN")){
     if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
   }else{
     $SITE_ID = $_SESSION['site_id'];
   }

   if ($country_code=='')
     $country_code = get_country_code($SITE_ID);

   if ($type=="ulke"){
     $LocationTypeid="3";
	 $fld_name = "CountryCode";
	 $crt="";
   }
   if ($type=="sehir"){
     $LocationTypeid="1";
	 $fld_name = "LocalCode";
	 $crt = "AND CountryCode = '$country_code'";
   }
   $sql_str = "SELECT $fld_name AS Code, LocationName FROM TLocation WHERE LocationTypeid = ".$LocationTypeid." ".$crt." ORDER BY LocationName"; 
     if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
?>
<html>
<body>
<?
   cc_page_meta();
   echo "<center>";
   table_header("Kod Listesi","50%");
   echo "</center>";?>
   <table align="center" cellspacing="0" cellpddding="0" border="0" width="100%">
      <?while ($row = mysqli_fetch_object($result)){?>   
      <tr>
         <td width="15%" class="td1_koyu"><a href="#" onclick="send_value('<?=$row->Code ?>')"> <?=$row->Code; ?></a></td>
         <td width="85%" class="td1"><?=$row->LocationName?></td>
      </tr>
      <?}?>
   </table>
 <?table_footer();?>  
<script>
   function send_value(value){
       var cont = '<?=$LocationTypeid?>';
       if (cont==3){
            window.opener.document.all('CountryCode').value = value;
			window.opener.document.all('country_code').value = value;
       }else if (cont==1){       
            window.opener.document.all('LocalCode').value = value;
       }
       window.close();
   } 
</script>

</body>
</html>   