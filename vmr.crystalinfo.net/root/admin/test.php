
   <?   
   
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();
     session_cache_limiter('nocache');     
     $conn = $cdb->getConnection();
   
   
    $link = mysqli_connect('localhost', 'crinfo', 'SiGmA*19', 'MCRYSTALINFONE') or die('Could not connect');
   if (mysqli_connect_errno())
	{
	echo "Failed to connect to MYSQL: " . mysqli_connect_error();
	}
   mysqli_select_db($link, 'MCRYSTALINFONE') or die('Could not select database');
   
    $haberler =  "SELECT SIRA_NO, BASLIK FROM NEWS ORDER BY INSERT_DATE DESC LIMIT 10 ";
   	   //echo $sqlQuery;
           mysqli_query($link, $haberler);
   
   
   //$haberler="SELECT SIRA_NO, BASLIK FROM NEWS ORDER BY INSERT_DATE DESC LIMIT 10 ";

    if (!($cdb->execute_sql($haberler, $rs_haberler, $error_msg))){
        print_error($error_msg);
                exit;
}



   while($row = mysqli_fetch_array($rs_haberler))
   echo "{$row['BASLIK']}<br>------------------------ ";



?>