<?php
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $DIR_NAME ="/usr/local/sigma/crystal/data/";
   $cdb = new db_layer();
   if($act == "" || $act == "flsel"){
     $d = @dir($DIR_NAME);
     if(!$d){
       $access = "no";
     }
     $fp = fopen ($DIR_NAME."raw_06_2004.dat", "r"); 
      if(!$fp){  
        echo "Error";
        exit;
      }
      $fn = fopen ("/usr/local/sigma/crystal/data/temp_".$flname, "w+"); 
      if(!$fn){  
        echo "Error";
        exit;
      }
     while (!feof ($fp)){
          $buffer = fgets($fp, 5000);
          if(strlen($buffer) > 0){
              $str_arr = explode("\t", $buffer);
              $text = $str_arr[0];
              $text = str_replace("'","\'",$text);
              $sql = "INSERT INTO RAW_DATA(SITE_ID,DATA, DATE, SOURCE, DONE)
                        VALUES(1,'".$text."','".$str_arr[1]."','TEXT',0)";
              if (!($cdb->execute_sql($sql, $result, $error_msg))){
                    echo $error_msg."<br>";
                    fwrite($fn, implode("\t", $str_arr));
              }
          }  
      } 
      fclose ($fp);
      fclose ($fn);

//      $fp = fopen ($DIR_NAME.$flname, "w+"); 
//      $fn = fopen ("/usr/local/sigma/crystal/temp_".$flname, "r"); 
//      if(!$fp || !$fn){  
//        echo "Error";
//        exit;
//       }
//     while (!feof ($fn)){
//          $buffer = fgets($fn, 5000);
//          if(strlen($buffer) > 0){
//                fwrite($fp, $buffer);
//          }
//      }
//     fclose ($fp);
//     fclose ($fn);
//      unlink("/usr/local/sigma/acd/temp_".$flname); 
      print_error("İşlem Başarıyla Gerçekleştirildi..<br>");
      }
?>
