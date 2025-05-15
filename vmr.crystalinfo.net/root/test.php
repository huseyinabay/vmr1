<?
 require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/site.cnf";
 require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/db.php";
 
 
  global $_SESSION;
    //        global $_SERVER['REMOTE_ADDR'];
    //        global $_SERVER['HTTP_HOST'];
            
            if ($_SESSION["mobil"]=="E"){
                    define('IMAGE_ROOT','C:/Mondial/images/');
                    define('IMAGES','C:/Mondial/images/');
            }else{
                    define('IMAGE_ROOT','/images/');
                    define('IMAGES','/images/');
            }
            
            $adi        = $_SESSION["adi"];
            $soyadi     = $_SESSION["soyadi"];
            $user_name  = $_SESSION["username"];
            $date       = my_date();     
            $time       = my_time();
            $page       = GETENV(SCRIPT_NAME) ;    
            $SqlStr     = ("INSERT INTO PAGE_TRACK (SES_ID, USER_NAME, DATE, TIME, DOMAIN, PAGE, IP ) VALUES (\"".$_SESSION["last_id"]."\",\"".$user_name."\",\"".$date."\",\"".$time."\",'".$_SERVER['HTTP_HOST']."',\"".$page."\",\"".$_SERVER['REMOTE_ADDR']."\")");
            @mysqli_query($conn, $SqlStr);
 
 /*
 $DBN = (DB_NAME);
 echo  $DBN;
  */
  
  
?>