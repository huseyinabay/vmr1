<?php


   //ini_set('display_errors', 'On');
   //error_reporting(E_ALL);

//  Error Codes , halil aşagidaki gibi olmali
define("ZERO_ROW_COUNT", 101 );
define("FILE_NOT_EXIST", 102);
define ("DO_NOT_CACHE", 103);
define("INVALID_DIRNAME",  104);
define ("INVALID_FILENAME", 105);
//$my_time0 = $t0;
//$my_time1 = $t1;

$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
      
function mk_report_fname($prefix, &$tm){ 
  // returns formatted filename for reports
  // filename structure: reportType_DATE1_DATE2.html  
  // sample: auth_2004-06-21_2004-06-24.html
  global $MY_DATE;
  global $SITE_ID;
  global $my_time0;
  global $my_time1;

  $time0 = $my_time0;
  $time1 = $my_time1;

 

  //time0 baslama tarihi; time1 bitis tarihi
  switch($MY_DATE){
    case 'b': //Bu Gun
      $time0 = date("Y-m-d"); // 2004-06-24
      break;
 
    case 'c': // Bu ay 
      $time0 = date("Y-m-01");
      $time1 = date("Y-m-d");
      break;
 
   case 'f':   //Dun +
      $time0=strftime("%Y-%m-%d",mktime(0,0,0,date("m"),date("d")-1,date("y")));
      break;
   case 'g' : //Gecen Ay +
      $time0 = day_of_last_month("first");
      $time1 = day_of_last_month("last");
      break;
    case 'h': // Bu Hafta
      $time0 = day_of_this_week(1);
      $time1 = date("Y-m-d");
      break;
   
    case 'i': // Gecen Hafta +
      $time0 = day_of_last_week(1);
      $time1 = day_of_last_week(7);
      break;
    case 'e': // Secilmis tarih
  
      if ($time0){
//         echo "xxx".$time0."xxx<BR>";
        $time0 = convert_date_time($time0,'start');
//         echo "xxx".$time0."xxx";
        if(substr($time0,-8)=='00:00:00'){
            $time0 = substr($time0,0,10);
         }

      }else{
         return NULL;
       }
      if ($time1){
         $time1 = convert_date_time($time1,'end');
         if(substr($time1,-8)=='23:59:59'){
            $time1 = substr($time1,0,10);
         }

      }
      
      break;
   default:
         return NULL; //kaydedilmemesi gereken rapor icin NULL doner
      
  }

 if (empty($time0)){
     return NULL;
  }
    
	
	$last=""; // halil
	
  if ($last > 0){
     $time0 = strftime("%Y-%m-%d", mktime(0,0,0, date("m"), date("day") - last +1, date("y")));
     $time1 = date("Y-m-d");
  }

  if ($time1){
     $format = "%d_".$prefix."_%s_%s.html";
     $fName = sprintf($format,$SITE_ID,$time0,$time1);
  }else{
     $format = "%d_".$prefix."_%s.html";
     $fName = sprintf($format, $SITE_ID, $time0);
  }
  $tm = $time0;
  return $fName;
}

function mk_report_dirname($tm)
{
   // dirname baslangic tarihi t0 a gore olusturuluyor:  2004_06/ gibi
   return substr($tm, 0, 7); 
}


function save_report_as($maindir, $dirName, $fName){
    //saves output buffer as an html page named $fName
   global $DOCUMENT_ROOT;

 //  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
   if(substr($DOCUMENT_ROOT,strlen($DOCUMENT_ROOT)-1, 1)!='/')
     // $DOCUMENT_ROOT .= "/";

   if (is_null($dirName) == FALSE) {
   if (file_exists($maindir) == FALSE) // main dir: ozet,top
      mkdir($maindir, 0750);
   if (file_exists("$maindir/$dirName/")== FALSE)//subdir:2004_06
      mkdir("$maindir/$dirName/", 0750);
   
   $path = "$maindir/$dirName/$fName";
   }else {
     $path = "$maindir/$fName";
   }
   

   $handle = fopen($path,"wb");
   if ($handle == FALSE){
      echo $path."acilamadi<br>" ;
      return FALSE;
   }
   fwrite($handle, ob_get_contents());
   fclose($handle);
//    echo "<br>$path<br>";
   
   return TRUE;
}

function redirect($maindir, $dirName, $fName){
   // redirects to the presaved report.
   global $DOCUMENT_ROOT;
   if(substr($DOCUMENT_ROOT,strlen($DOCUMENT_ROOT)-1, 1)!='/')
      //$DOCUMENT_ROOT .= "/";

   $path = "$maindir/$dirName/$fName";
   if (file_exists($DOCUMENT_ROOT.$path) == FALSE) {
//       echo "<br>$DOCUMENT_ROOT$path bulunamadi<br>";
      return FALSE;
   }else{
      header("Location: http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT']."/$path");
      exit;
   }
}

function return_full_path($maindir){// for mailing purposes only
      global $type;
      global $DOCUMENT_ROOT;
      global $withCache;
      
      $fName =  mk_report_fname($type, $tm);         
      $dirname = mk_report_dirname($tm);
      $fpath = "$maindir/$dirname/$fName";
      if ((strcmp($withCache, "DBwriteCache") == 0) or          
          (strcmp($withCache,"callCache") == 0) )
          return $fpath;
      else if(strcmp($withCache, "DBnotCache") == 0)
          return "/temp/report.html";

}

function call_cache($maindir) { // returns cache_status
 global $type;
 global $withCache;
 global $DEPT_ID; 
 global $record;
 global $ORIG_DN;

//   kriterler uygun sekilde set edilmisse caching iptal
  if (empty($record) == FALSE || $DEPT_ID != -1 || empty($ORIG_DN) == FALSE
      || empty($last) == FALSE)
  {
     return DO_NOT_CACHE;
  }

   if (strcmp($withCache,"DBnotCache") == 0){
       return DO_NOT_CACHE;
   }
   
   if (($fName =  mk_report_fname($type, $tm)) == NULL)
	   
  // echo $fName; die;
   { 
       return INVALID_FILENAME;
   }
   if (($dirname = mk_report_dirname($tm)) == NULL)
       return INVALID_DIRNAME; 
   
   if (strcmp($withCache, "DBwriteCache") == 0)
       return FILE_NOT_EXIST;
   
   if (redirect($maindir, $dirname, $fName) == FALSE){
         return FILE_NOT_EXIST;
   }  
   return DO_NOT_CACHE;
}

function make_cache($maindir, $cache_status, $row_count) {
   global $type;
   if ($row_count == 0)
       return ZERO_ROW_COUNT; /* $row_count == 0 */
   
   else if ($cache_status == INVALID_DIRNAME ||
            $cache_status == INVALID_FILENAME) 
   {
       return $cache_status; 
   }
   
   if ($cache_status == DO_NOT_CACHE){
//       echo "do not cache<br>";
      save_report_as("temp","","report.html");
   }
   
   else if($cache_status == FILE_NOT_EXIST) {
//       echo "file not exists<br>";
     $fName =  mk_report_fname($type, $tm);
     $dirname = mk_report_dirname($tm);
     save_report_as($maindir,$dirname,$fName);
   }
   return $cache_status;
}

?>