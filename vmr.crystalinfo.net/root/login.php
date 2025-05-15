<?php
   //require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/functions.php");
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";	
   //  require_once "/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/functions.php";	

   $cUtility = new Utility();
   $cdb = new db_layer();
 

   ini_set('display_errors', 'On');
   error_reporting(E_ALL);
  
   if (isset($_SESSION["user_id"]))  // Eğer login durumda ise logout olsun
      logout(0);  // First logout...
  login();
  right_set($_SESSION["user_id"]);
  
  header("Location:main.php");
?>
