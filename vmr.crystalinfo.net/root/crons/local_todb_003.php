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
$local_root = "/home/firuzkoy/";  //BUFFER IN DOSYALARI KOYDUĞU YER

$list=Array(); 
$list=read_dir($local_root);
$i=0;
while($list[$i]) { 
          $file_name =  $list[$i]; $i++;
          echo $file_name ."\n <br>";
          if (file_exists($local_root.$file_name)) {
                    // Connecting, selecting database
                    $link = mysqli_connect('localhost', 'crinfo', 'SiGmA*19') or die('Could not connect: ' . mysql_error());
                    //SELECT DB
                    mysqli_select_db('MCRYSTALINFONE') or die('Could not select database');
                    //OPEN BUFFER FILE
                    $handle = fopen($local_root.$file_name, "r");
                    // BU DOSYAYI NORMAL OKUNAN BİR DOSYA GİBİ SİSTEME KALDET
                    $handle2= fopen("/usr/local/sigma/crystal/data/".date("m")."_".date("Y")."/3raw_".date("m")."_".date("Y").".dat","a+");
                    if ($handle) {
                              while (!feof($handle)) {
                                        $buffer = fgets($handle, 256); //DOSYA OKUNUYOR
                                        fwrite($handle2, $buffer);     //DOSYA DİĞER DOSYAYA YAZILIYOR.
                                        if(strlen($buffer)>2){
                                                  //DB YE İNSET EDİLİYOR.
                                                  $sqlQuery =  "\n INSERT INTO RAW_DATA(DATA, DATE, SOURCE, SITE_ID,ERROR_CODE) VALUES ('".$buffer."',CURDATE(),'buffer',3, '0')"; 
                                                  mysqli_query($link, $sqlQuery);
                                        }
                              }
                              fclose($handle);
                    } 
          }
          unlink ($local_root.$file_name); // ESK] DOSYA S]STEMDEN S]L]N]YOR.
} //while of dir
fclose($handle2);


function read_dir($dir) {
     global $local_root;
     if ($handle = opendir($dir)) {
          /* This is the correct way to loop over the directory. */ 
          while (false !== ($file = readdir($handle))) {
               
               if ($file != "." && $file != "..")  {
                  if(substr($file,0, 8)=="Record.2"){ // uygun dosya 
                    $array[] = $file;
                  }else{ //yanlış dosyaları sil
                    unlink ($local_root.$file);   
                  }                
               }
          }
          closedir($handle);
     }
     return $array;
}

?>