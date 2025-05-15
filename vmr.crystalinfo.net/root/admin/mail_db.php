<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	  require_once("../crons/doc_root.cnf");
	  
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'].'/PHPMailer/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT'].'/PHPMailer/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/PHPMailer/src/SMTP.php';

      header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    
      clearstatcache ();
	  
	  $cUtility = new Utility();
      $cdb = new db_layer();
      $conn = $cdb->getConnection();
	  
	  $Mail = new PHPMailer();
	  $Mail->IsSMTP();
	  $Mail->SMTPDebug = 0;  // 1, 2 , 3
	  $Mail->setLanguage('tr', '/usr/local/wwwroot/multi.crystalinfo.net/root/PHPMailer/language');
	  	  //if (extension_loaded('openssl')) { print 'openssl extension loaded.';} // open ssl testi icin
	  
  $Mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true ]]; // Exchange icin eklendi
	  
  $Mail->Mailer = SMTP_PROTOCOL;
  $Mail->SMTPAuth = SMTP_AUTH;
  $Mail->SMTPSecure = SMTP_SECURE;  //tls , ssl
  $Mail->Host = SMTP_SERVER;
  $Mail->Port = SMTP_PORT;  //587 // 465
  $Mail->Username = SMTP_USER; 
  $Mail->Password = SMTP_PWD;
  $Mail->SetFrom(SMTP_FROM, SMTP_FROMNAME); // Mail attigimizda yazacak isim	 
  $Mail->AddCustomHeader("Errors-To: <".SMTP_PMASTER.">");
  $Mail->IsHTML(SMTP_HTML);
  $Mail->ClearAttachments ();
  $Mail->AddAttachment($DOCUMENT_ROOT."$page", "report.html");
  $Mail->ClearAllRecipients();
  $Mail->AddAddress($email);
  $Mail->Subject = "Telefon Görüşme Raporu";
  $Mail->Body = "Telefon Görüşme Raporu";
  $Mail->CharSet ="utf-8";

  
      if (!$Mail->Send()){
          print_error("Mail gönderilemedi, Hata:". $Mail->ErrorInfo);
          exit;
      }
      print_error("Mail başarıyla \" $email \" adresine gönderilmiştir");
	  

?>
