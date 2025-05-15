<?
/*
Dikkat edilecek hususlar;
1- burada geçen root ve directory lerin sistemde olması gerekli.
2- DB adı, şifresi ve user name düzeltilmeli
3- DB name düzeltilmeli
4- bu klasörlerin yazma hakları kontrol edilmeli.
5- RAW_DATA ile atılan bir dosyanın içeriğikarşılaştırılmalı, 
doğru bir aktarım yapılıyor mu diye.

*/

$local_root = "/usr/local/sigma/crystal/avaya/";  //BUFFER IN DOSYALARI KOYDUĞU YER


$list=Array(); 
$list=read_dir($local_root);
$i=0;

while(isset($list[$i])) { 
          $file_name =  $list[$i]; $i++;
          echo $file_name ."\n <br>";
          if (file_exists($local_root.$file_name)) {
                    // Connecting, selecting database
                    $link = mysqli_connect('localhost', 'crinfo', 'SiGmA*19') or die('Could not connect: ' . mysql_error());
                    //SELECT DB
                    mysqli_select_db($link,"MCRYSTALINFONE") or die('Could not select database');
                    //OPEN BUFFER FILE
                 $handle = fopen($local_root.$file_name, "a+");
                 @mkdir("/usr/local/sigma/crystal/data/".date("m")."_".date("Y")."/");
                    // BU DOSYAYI NORMAL OKUNAN BİR DOSYA GİBİ SİSTEME KALDET
                    $handle2= fopen("/usr/local/sigma/crystal/data/".date("m")."_".date("Y")."/1raw_".date("m")."_".date("Y").".dat","a+");
                    if ($handle) {
                     $mysayac=1;
                              while (!feof($handle)) {
                                        $buffer = fgets($handle, 256); //DOSYA OKUNUYOR
					     $buffer = str_replace (chr(0),"", $buffer);                                        
//$buffer = str_replace ("/11","hhh", $buffer);
					     fwrite($handle2, $buffer);     //DOSYA DİĞER DOSYAYA YAZILIYOR.
                                        if(strlen($buffer)>2){
                                                  //DB YE İNSET EDİLİYOR.
                                                  $sqlQuery =  "\n INSERT INTO SEMI_FINISHED(LINE1_ID, LINE1, DATE_TIME, YEAR, MONTH, DAY, SITE_ID, DONE) VALUES (".$mysayac.", '".$buffer."',CURDATE(), YEAR(CURDATE()),MONTH(CURDATE()), DAYOFMONTH(CURDATE()), 1, '0')"; 
                                                  $mysayac=$mysayac+1;
                                                  mysqli_query($link, $sqlQuery);
                                        }
                              }
                              fclose($handle);
                    }
          }
          unlink ($local_root.$file_name); // ESKİ DOSYA SİSTEMDEN SİLİNİYOR.
} //while of dir
	if (isset($handle2)){
fclose($handle2);
	}

function read_dir($dir) {
     if ($handle = opendir($dir)) {
        /* This is the correct way to loop over the directory. */
          while (false !== ($file = readdir($handle))) {
               //if ($file != "." && $file != "..")  $array[] = $file;
              if(substr($file,0, 9)=="avayadata") $array[] = $file; // Halil  - ana dosyayı silmemesi icin yapıldı 
          }

       closedir($handle);
     }
	 if (isset($array)){
     return $array;
	 }
}

?>