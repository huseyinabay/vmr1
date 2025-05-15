<?
   require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/functions.php");
   if (!defined("IMAGE_ROOT")){ // Note that it should be quoted
      define("IMAGE_ROOT", "/images/");
   }  
   $cdb = new db_layer();

   function write_site_files(){
     global $cdb, $DOCUMENT_ROOT;
     $SQL_SITE = "SELECT SITE_ID, SITE_NAME FROM SITES WHERE SITE_ID>1";
     if(!$cdb->execute_sql($SQL_SITE, $result2, $errmsg)){
       print_error($errmsg);
       exit;
     }
     echo "<center><br><br>Bölgelere Göre En Son Gelen Data Dosyalari ve Tarihleri
            <table width='90%' border=1>
            <tr><td>Site Adi</td><td>Dosya Adi</td><td>Tarih</td></tr>";
     while($row=mysql_fetch_object($result2)){
       $SITEID = $row->SITE_ID;
       $SITENAME = $row->SITE_NAME;
       if($SITEID<10){
         $boxName = "box00".$SITEID.".txt";
       }else if($SITEID<100){
         $boxName = "box0".$SITEID.".txt";
       }else{
         $boxName = "box".$SITEID.".txt";
       } 
       @$fd = fopen($DOCUMENT_ROOT."/crons/sites/".$boxName, "r");
       @$buffer = fgets($fd, 4096);
       $nameArr = split(";", $buffer);
       echo "<tr><td NOWRAP>".$SITENAME."</td><td NOWRAP>".$nameArr[0]."</td><td NOWRAP>".date("d-m-Y H:i:s", $nameArr[1])."</td></tr>\n";
       @fclose($fd); 
     }
    echo "</table></center>";
   }

 ?>
<?cc_page_meta(0);
page_header();
echo "<br>";
table_header("Müþteri Özel Bölümü","80%");?>
<center>
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
  <tr>
    <td align="center" valign="middle"></td>
  </tr>
  <!--Link Baþlangýç-->
  <tr>
    <td align="left" valign="middle"><li><a href="report_outb.php">Raporlar</a></td>
  </tr>
  <!--Link Bitiþ-->
  <tr>
    <td align="left" valign="middle"><li><a href="extentions_src.php">Dahili Ve Hesap Arama</a></td>
  </tr>
  <tr>
    <td align="left" valign="middle"><li><a href="auth_code_src.php">Þifre Ve Hesap Arama</a></td>
  </tr>
  <tr>
    <td align="left" valign="middle"><li><a href="auth_mail_src.php">Þifre Ve Mail Arama</a></td>
  </tr>
  <tr>
    <td align="left" valign="middle"><br><br><br><li><a href="weekly_mail.php">Þifre Mailing (Test Ýçin) </a></td>
  </tr>
  <tr>
    <td align="left" valign="middle"><li><a href="weekly_pers_auth_mail.php">Kiþisel Þifre Mailing (Test Ýçin) </a></td>
  </tr>
</table>
</center>
<?table_footer();
page_footer(0);?>

