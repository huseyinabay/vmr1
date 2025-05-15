<?php
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
        //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
        check_right("SITE_ADMIN");
   if(!$month && !$year){
     $month = date("m");
     $year = date("Y");
   }else{
	 if ($type=="next"){
	   $month = $month + 1;
	   if ($month == 13){
	     $month = "1";
		 $year= $year + 1;
	   }
	 }else if($type=="back"){
	   $month = $month - 1;
	   if ($month == 0){
	     $month = "12";
		 $year= $year - 1;
	   }
	 }
   }
   if ($month < 10 && substr($month,0,1)<>'0'){
	 $month = "0".$month;
   }
   if($DIR_NAME==""){
     $DIR_NAME ="/usr/local/sigma/crystal/data/".$month."_".$year."/";
   }
   $cdb = new db_layer();
   $cUtility = new Utility();
   require_valid_login();
   if($act == "" || $act == "flsel"){
     $d = dir($DIR_NAME);
     if(!$d){
       $access = "no";
     }
   cc_page_meta(0);
   page_header();
   
   
   	//  ini_set('display_errors', 'On');
    //  error_reporting(E_ALL);
	   //error_reporting(E_ALL ^ E_NOTICE);
   
   
?>
        <br><br>
        <table border=1 width="550" cellspacing=0>
          <tr>
		  <td><b>
            Crystalinfo santral verilerini okurken veritabanına erişemediği zamanlarda,<br>
            aldığı verileri "raw_ay_yıl_discnn.dat" gibi bir dosya adı ile kaydeder.<br> 
            Bu verilerin sisteme aktarılması gereklidir. <br><br>
            Yüklenebilecek Dosyalar kısmında buna uygun dosya isimleri varsa lütfen üzerine<br>
            tıklayarak veritabanına aktarılmasını sağlayınız.<br>
            <br>
            <p style="color:red;">
        	Uyarı : Herhangi bir problem olmadan bu işlem yapılırsa sistemde mükerrer kayıtlar <br>
	    oluşmasına sebebiyet verebilir. İşlemin dikkatli ve kontrollü bir şekilde yapılmasaı gerekir.
	    </p>
          </td>
          </tr>
        </table><br>
<?
       table_header("Yüklenebilecek Dosyalar...","50%"); 
?>
      <center>
      <table border=1 width="80%" cellspacing=0>
      <tr><td><a href = "text_to_db.php?month=<?=$month?>&year=<?=$year?>&type=back">Önceki Ay</a></td><td><a href = "text_to_db.php?month=<?=$month?>&year=<?=$year?>&type=next">Sonraki Ay</a></td></tr>
      <tr><td colspan="2" class="header">Site Id: 
                  <select name="SITE_ID" class="select1" style="width:200" >
                     <?
                         $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ORDER BY SITE_NAME";
                         echo $cUtility->FillComboValuesWSQL($conn, $strSQL, false,  $SITE_ID);
                     ?>
            </select>
            </td></tr>
      <tr><td colspan="2" class="header">Kontrol Edilen Ay : <?=$month."-".$year?></td></tr>
	  <tr><td>Dosya Adı</td><td>Boyutu</td></tr>
<?


   if($access!="no"){

    while ($entry = $d->read()) {
         $stt = explode(".", $entry);
//         if(1==1 || strchr($entry, "discnn") && substr($stt[0], 0, 3) == "raw" && $stt[1]=="dat"){
         if(1==1){
             $file_size = (number_format((filesize($DIR_NAME.$entry)/1024), 2))." KB";
             echo " <tr><td>\n<a href=\"text_to_db.php?act=upload&DIR_NAME=".$DIR_NAME."&flname=".$entry."\">";
             echo $entry;
             echo "</a></td>\n";
             echo "<td>".$file_size."</td>\n";
         }
     }
     $d->close();
   } ?>
     </tr>
     </table>
      <center>
     <?
     table_footer(0);
     page_footer("");
    }elseif($act == "upload" && !empty($flname)){
//      $month = "08";
//      $year = "2003";


////////////////////////////////////////7
   if($SITE_ID=="")   $SITE_ID=1; ///// SİTE NO
////////////////////////////////////////

      $fp = fopen ($DIR_NAME.$flname, "r"); 
      if(!$fp){  
        echo "Error";
        exit;
      }
      $fn = fopen ("/usr/local/sigma/crystal/temp_".$flname, "w+"); 
      if(!$fn){  
        echo "Error";
        exit;
      }

	  $mysayac=1;
     while (!feof ($fp)){
          $buffer = fgets($fp, 5000);
          if(strlen($buffer) > 0){
              $str_arr = explode("\t", $buffer);
			  $ltime = time();
			  //echo $ltime; die;
              $text = $str_arr[0];
              $text = str_replace("'","\'",$text);
			  $text = preg_replace('/[^^a-zA-Z0-9:_(),.\/ ]/','',$text);   
			  
              // MERIDIAN ICIN RAW_DATA kullanilacak // ustteki satir da iptal edilecek & karakteri icin
             // $sql = "INSERT INTO RAW_DATA(DATA, DATE, SOURCE, DONE, ERROR_CODE, SITE_ID)VALUES('".$text."','".$ltime."','TEXT',0,0, $SITE_ID)";
          $sql =  "\n INSERT INTO SEMI_FINISHED(LINE1_ID, LINE1, DATE_TIME, YEAR, MONTH, DAY, SITE_ID, DONE) VALUES (".$mysayac.", '".$text."',CURDATE(), YEAR(CURDATE()),MONTH(CURDATE()), DAYOFMONTH(CURDATE()), $SITE_ID, '0')"; 
                $mysayac=$mysayac+1;
			 if (!($cdb->execute_sql($sql, $result, $error_msg))){
                    echo $error_msg."<br>";
                    fwrite($fn, implode("\t", $str_arr));
              }
          }  
      } 
      fclose ($fp);
      fclose ($fn);

      $fp = fopen ($DIR_NAME.$flname, "w+"); 
      $fn = fopen ("/usr/local/sigma/crystal/temp_".$flname, "r"); 
      if(!$fp || !$fn){  
        echo "Error";
        exit;
      }
     while (!feof ($fn)){
          $buffer = fgets($fn, 5000);
          if(strlen($buffer) > 0){
                fwrite($fp, $buffer);
          }
     }
      fclose ($fp);
      fclose ($fn);
      unlink("/usr/local/sigma/crystal/temp_".$flname); 
      print_error("İşlem Başarıyla Gerçekleştirildi..<br>");
      }
?>
