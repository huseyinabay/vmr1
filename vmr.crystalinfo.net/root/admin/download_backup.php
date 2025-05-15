<?   
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  $cUtility = new Utility();
  $cdb = new db_layer();
  $capp = new  application();
  $cdate = new datetime_operations();
  //  require_valid_login();
  Header("Content-Type: application/x-octet-stream");
  // you can put the header file-length here but I never use it!
  Header( "Content-Disposition: attachment; filename=$prm.gz");
  $strdir  = "/usr/local/sigma/backup/crystalinfo_data.$prm.tar.gz";
  $fp = fopen($strdir,"r");

   while($buff = fread($fp,1000000)){
         echo $buff;
   }
   fclose($fp);
?>
<a HREF="self.close();">Kapat</a>
<?die;?>
<!--
<html>
<head>
<body onload="beginDownload()">
Download işlemi başlamazsa <a href="<? echo "/temp/BM_".$_SESSION['username']."_".$id.".csv" ?>">burayı tıklayınız</a>.<p>
Pencereyi kapatmak için <a href="javascript:window.close();">burayı tıklayınız</a>.
-->
<script language="JavaScript" type="text/javascript">
function beginDownload() {
     newlocation = "<? echo "/temp/BM_".$_SESSION['username']."_".$id.".csv" ?>";
  // Bizarre logic because JavaScript won't behave
  idl = 1;
  if (location.search) { // for NN2
    idl = -location.search.indexOf("idl=n");
  }
  if (idl > 0) {
    window.location = newlocation;
  }
  return 1;
}
</script>
<a HREF="self.close();">Kapat</a>
</body>
</html>


