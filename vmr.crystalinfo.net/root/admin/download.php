<?
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  $cUtility = new Utility();
  $cdb = new db_layer();
  $conn = $cdb->getConnection();
  require_valid_login();
  check_right("SITE_ADMIN");
  cc_page_meta(0);
  page_header();
  echo "<br>";
  table_header("Backup Dosyalarını İndir", "500");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Untitled</title>
</head>
<body>
<?
      
if (file_exists("/usr/local/sigma/backup/crystalinfo_data.daily.tar.gz")) {
?>
--> <a href="download_backup.php?prm=daily" target=_new>Günlük Backup</a><BR>
<?}
if (file_exists("/usr/local/sigma/backup/crystalinfo_data.weekly.tar.gz")) {
?>
--> <a href="download_backup.php?prm=weekly" target=_new>Haftalık Backup</a><BR>
<?}
if (file_exists("/usr/local/sigma/backup/crystalinfo_data.monthly.tar.gz")) {
?>
--> <a href="download_backup.php?prm=monthly" target=_new>Aylık Backup</a>
<?}?>
</body>
</html>
<BR><BR><BR>

Not : Backuplar günlük, haftalık ve aylık olarak bütün database kayıtlarını içerecek şekilde alınmaktadır. 

<?table_footer();
  echo "<br>";
  page_footer("");
?>



