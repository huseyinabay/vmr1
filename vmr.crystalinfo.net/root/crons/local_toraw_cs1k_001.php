<?php

$local_root = "/usr/local/sigma/crystal/data/".date("m")."_".date("Y")."/";
$file_name = "raw_".date("m")."_".date("Y")."_discnn.dat";

if (file_exists($local_root.$file_name)) {
// Connecting, selecting database
   $link = mysqli_connect('localhost', 'crinfo', 'SiGmA*19', 'MCRYSTALINFONE') or die('Could not connect');
   if (mysqli_connect_errno())
	{
	echo "Failed to connect to MYSQL: " . mysqli_connect_error();
	}
   mysqli_select_db($link, 'MCRYSTALINFONE') or die('Could not select database');

$handle = fopen($local_root.$file_name, "a+");
@mkdir("/usr/local/sigma/crystal/data/".date("m")."_".date("Y")."/");

// $handle = fopen($local_root.$file_name, "a+");
$handle2= fopen("/usr/local/sigma/crystal/data/".date("m")."_".date("Y")."/2raw_".date("m")."_".date("Y").".dat","a+");


if ($handle) {
    while (!feof($handle)) {
    
        $buffer = fgets($handle, 256);


//$buffer = str_replace (chr(10),"", $buffer);
//$buffer = str_replace (chr(13),"", $buffer);
$buffer = str_replace (chr(0),"", $buffer);
	
//        echo $buffer;
//    if(substr($buffer, 14,1) == chr(32) && substr($buffer, 1,1) != chr(32)){


//    $buffer =  substr_replace($buffer,"",14,1);
//    for ($ii = 0; $ii <= strlen($buffer); $ii++) {
//    if(substr($buffer, $ii,1)==chr(13) || substr($buffer, $ii,1)==chr(10)){
//        $buffer =  substr_replace($buffer,"",$ii,1);
//	echo "asdasd";
//    }
//     }
//         }	    

	fwrite($handle2, $buffer);    
        if(strlen($buffer)>2){
           $sqlQuery =  "\n INSERT INTO RAW_DATA(DATA, DATE, SOURCE, SITE_ID,ERROR_CODE) VALUES ('".$buffer."',CURDATE(),'cs1000',1, '0')";
   	   //echo $sqlQuery;
           mysqli_query($link, $sqlQuery);
	}
   }
 
 } 
 unlink ($local_root.$file_name);
 fclose($handle);

}

?>