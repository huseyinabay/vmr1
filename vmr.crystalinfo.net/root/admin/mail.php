<?
      require_once "/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/functions.php";
	  
	  use PHPMailer\PHPMailer\PHPMailer;
	  use PHPMailer\PHPMailer\Exception;

require '/usr/local/wwwroot/multi.crystalinfo.net/root/PHPMailer/src/Exception.php';
require '/usr/local/wwwroot/multi.crystalinfo.net/root/PHPMailer/src/PHPMailer.php';
require '/usr/local/wwwroot/multi.crystalinfo.net/root//PHPMailer/src/SMTP.php';

      
      $cUtility = new Utility();
      $cdb = new db_layer();
      $conn = $cdb->getConnection();
      $Mail = new PHPMailer();
      require_valid_login();
?>

<?php
      cc_page_meta(0);
      page_header();
     echo "<br>";
      table_header("Mail ", "500");
?>

<FORM NAME="mail" action="mail_db.php" method="post">
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>From</td>
    <td><input type="text" class="input1" NAME="FROM" value="info@crystal.net" size="20"></td>
  </tr>
  <tr>
    <td>From Name</td>
    <td><input type="text" NAME="FROMNAME"  class="input1" value ="CrystalInfo.net den"  size="20"></td>
  </tr>
  <tr>
    <td>Sender</td>
    <td><input type="text" NAME="SENDER" class="input1" value="postmaster@vodasoft.com.tr" size="20"></td>
  </tr>
  <tr>
    <td>AddCustomHeader</td>
    <td><input type="text" NAME="ADDCUSTOMHEADER" class="input1" value ="Errors-To: <postmaster@vodasoft.com.tr>" size="20"></td>
  </tr>
  <tr>
    <td>To</td>
    <td><input type="text" NAME="TO" class="input1" value="" size="20"></td>
  </tr>
  <tr>
    <td>Subject</td>
    <td><input type="text" NAME="SUBJECT" class="input1" size="20"></td>
  </tr>
  <tr>
    <td>Body</td>
    <td><textarea rows="7" NAME="BODY" class="textarea1"  cols="40"></textarea></td>
  </tr>
  <tr>
    <td></td>
    <td><input type="submit" value="Gönder"></td>
  </tr>
  
</table>
</FORM>

<?table_footer();
  echo "<br>";
  page_footer("");
?>


