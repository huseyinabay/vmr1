<?php
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  $DIR_NAME ="/usr/local/sigma/crystal/data/";
  $cdb = new db_layer();
  require_valid_login();
  if($act == "" || $act == "flsel"){
    $d = @dir($DIR_NAME);
    if(!$d){
      print_error("Dosya Erişim Hatası");
      exit ;
    }
  cc_page_meta(0);
  page_header();
?>
<?
       table_header("Yüklenebilecek Dosyalar...","50%"); 
?>
      <center>
      <table border=1 width="80%" cellspacing=0>
       <tr><td>Dosya Adı</td><td>Boyutu</td></tr>
<?
     while ($entry = $d->read()) {
         $stt = explode(".", $entry);
         if($stt[1]=="bdf"){
             $file_size = (number_format((filesize($DIR_NAME.$entry)/1024), 2))." KB";
             $fp = fopen ($DIR_NAME.$entry, "r"); 
             if(!$fp){  
                 echo "Error";
                 exit;
             }
             while (!feof ($fp)){
                 $buffer = fgets($fp, 5000);
                 if(strlen($buffer) > 0){
                     $str_arr = explode("\t", $buffer);
                     $text = $str_arr[0];
                     $text = str_replace("'","\'",$text);
                     $sql = "INSERT INTO RAW_DATA(DATA, DATE, SOURCE, DONE, ERROR_CODE)
                             VALUES('".$text."','".time()."','TEXT',0,0)";
                      if (!($cdb->execute_sql($sql, $result, $error_msg))){
                          echo $error_msg."<br>";
                     }
                }  
            }
            fclose ($fp);
            if(file_exists($DIR_NAME.$entry))
            unlink($DIR_NAME.$entry); 
         }
     }
     exit;
   }  
     ?>
