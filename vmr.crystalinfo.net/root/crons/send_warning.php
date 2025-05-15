<?
  include "doc_root.cnf";
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/class.phpmailer.php";
  function mail_send($mail,$subject,$DATA,$add_img=1){
     $Mail = new phpmailer();
     $Mail->From       = "Vodasoft";
     $Mail->FromName   = "Vodasoft";
     $Mail->Sender     = "admin@vodasoft.com.tr";
     $Mail->AddCustomHeader("Errors-To: <okoc@vodasoft.com.tr>");
     $Mail->CharSet    = "iso-8859-9";
     $Mail->IsHTML(True);
     $Mail->ClearAttachments ();
	 $Mail->ClearAllRecipients();
	 if ($DATA!=""){
        $Mail->AddAddress($mail);
        $Mail->Subject = $subject;
        $Mail->Body = $DATA;
        $Mail->IsSendmail();
        if (!$Mail->Send()){
            echo $Mail->ErrorInfo. "<br>". $mail ." Adresine Atamadım. \n";
        }else{
           echo $mail ." Adresine Atıldı. <BR>\n";
        }
        
     }
  }

  $WARNING = "Lütfen gün içerisinde yapmış oldugunuz:<br><br>
 
Müşteri görüşmelerini KONTAK olarak(Gelen mailler içerisindeki önemli olanları da attach ederek)<br>
Ziyaretler, toplantıları ZİYARET olarak<br>
Çıkan fırsatları LEAD olarak<br>
Yerinde servis, telefonda servis hizmetlerini SERVİS olarak<br>
 
Belli bir müşteri ile görüşme dışında, içerde yapmış oldugunuz çalışmaları da PROJE'lerin altına giriniz.<br>
 
Gerçekleştirip girmediğiniz çalışmalar bilgi kaybına, bilgi kaybı verimsizliğe ve gelir kaybına sebep olmaktadır. Lütfen işletmemizin ENDİREK GELİR KAYBI'na engel olunuz.
<br><br>";
  mail_send("vodasoft@vodasoft.com.tr", " ----------------------------ÇOK ÖNEMLİ HATIRLATMA-------------------------------------", $WARNING);
?>