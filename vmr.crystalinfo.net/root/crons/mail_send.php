<?
	require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/site.cnf');

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

require '/usr/local/wwwroot/multi.crystalinfo.net/root/PHPMailer/src/Exception.php';
require '/usr/local/wwwroot/multi.crystalinfo.net/root/PHPMailer/src/PHPMailer.php';
require '/usr/local/wwwroot/multi.crystalinfo.net/root//PHPMailer/src/SMTP.php';

      clearstatcache ();

	   function mail_send($mail,$subject,$DATA,$add_img=1){
	  
	  $Mail = new PHPMailer();
	  $Mail->IsSMTP();
	  $Mail->SMTPDebug = 0;  // 1, 2 , 3
	  $Mail->setLanguage('tr', '/usr/local/wwwroot/multi.crystalinfo.net/root/PHPMailer/language');
	  $Mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true ]]; // Exchange icin eklendi
	  
	  //if (extension_loaded('openssl')) { print 'openssl extension loaded.';} // open ssl testi icin
	  
  $Mail->Mailer = SMTP_PROTOCOL;
  $Mail->SMTPAuth = SMTP_AUTH;
  $Mail->SMTPSecure = SMTP_SECURE;  //tls , smtp
  $Mail->Host = SMTP_SERVER;
  $Mail->Port = SMTP_PORT;  //587 // 465
  $Mail->Username = SMTP_USER; 
  $Mail->Password = SMTP_PWD;
  $Mail->SetFrom(SMTP_FROM, SMTP_FROMNAME); // Mail attigimizda yazacak isim	 
  $Mail->AddCustomHeader("Errors-To: <".SMTP_PMASTER.">");
  $Mail->IsHTML(SMTP_HTML);
  $Mail->ClearAttachments ();
  $Mail->ClearAllRecipients();
  $Mail->CharSet ="utf-8";
  
   if ($add_img==1){
       $Mail->AddEmbeddedImage("/usr/local/wwwroot/multi.crystalinfo.net/root/images/company.gif", "my-attach", "logo.gif");
	   $Mail->AddEmbeddedImage("/usr/local/wwwroot/multi.crystalinfo.net/root/images/logo1.gif", "my-crystal", "crystal.gif");
     }
	  
	 if ($DATA!=""){
        $Mail->AddAddress($mail);
        $Mail->Subject = $subject;
        $Mail->Body = $DATA;

        if (!$Mail->Send()){
            echo $Mail->ErrorInfo. "<br>". $mail ." Adresine Atamadım. \n";
        }else{
           echo $mail ." Adresine Atıldı. \n<BR>";
        }
        
     }
	 
	 
 }     
  
?>