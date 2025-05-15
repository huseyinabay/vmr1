<? require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
     print_error("Bu sayfaya erişim hakkınız yok!");
     exit;
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
</head>
<body>

<div id="bekle" name="bekle" style="position:absolute;left:;top:;">
<table align="center" border="0" cellspacing=0 cellpadding="0">
  <tr height="25">
    <td>UMTH Analizi Yapılıyor....</td>
  </tr>

</table>   
</div>
<script>
   wid = screen.width-180;
   hei = screen.height-30;
   document.all("bekle").style.left=(wid/2);
   document.all("bekle").style.top=(hei/2);
   document.all("bekle").style.display='';
</script>
<script>
  setTimeout("window.location.href='umth.php?SITE_ID=<?=$SITE_ID?>'",'3000');
</script>
</body>
</html>
