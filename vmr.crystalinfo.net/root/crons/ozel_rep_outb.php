<?php
	require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/functions.php');
	//date_default_timezone_set('Europe/Istanbul');

register_globals();

	// SITE SCIMI
	$SITE_ID = "4";
	$ttype = "GIDEN";
	
	    $sql_site="SELECT SITE_NAME FROM SITES WHERE SITE_ID = $SITE_ID"; 
    if (!($cdb->execute_sql($sql_site,$result,$error_msg))){
        print_error($error_msg);
        exit;
    }
    if (mysqli_num_rows($result)>0){
    $row = mysqli_fetch_object($result);
        $company = $row->SITE_NAME;
    }else{
    print_error("Site paramatreleri bulunamadı.");
    exit;
  }
	$t0 = "";
	$t1 = "";
	
	$t0 = '20230101';
	$t1 = '20230328';
	
	$st0 =  strtotime($t0);
	$st0 = date('dmY', $st0);
	
	$st1 =  strtotime($t1);
	$st1 = date('dmY', $st1);
	
	//echo $st0;exit;

/*******EDIT LINES 3-8*******/
	$kriter = "";
    $max_acc_dur="43200";
	 
	$to = "hmutlu@netser.com.tr"; 
	//$to = "ozkan.guclu@renault.com.tr;hmutlu@netser.com.tr;aabay@mniletisim.com.tr;mbudak@mniletisim.com.tr;okurt@netser.com.tr;herdinc@netser.com.tr";
	$subject   = "$ttype DETAY Raporu, $company Şube, Rapor Tarihi: $st0 - $st1";

	$body = "Giden Çağrı Detay Raporu. <br>
			 Şube Adı: $company <br>
			 Rapor Tarihi: $st0 - $st1 <br>";
	
	$path = "/usr/local/wwwroot/multi.crystalinfo.net/root/crons/XlsReports/"; 
	$file_name = $company."_".$ttype."_".$st0."-".$st1.".xls";
		
	//echo $file_name;exit;

	/*******YOU DO NOT NEED TO EDIT ANYTHING BELOW THIS LINE*******/    
//create MySQL connection  

    $kriter = $cdb->field_query($kriter,   "SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
    $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.  
	$kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "1"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
    $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.
	$kriter .= $cdb->field_query($kriter,   "MY_DATE"     ,  ">=",  "$t0"); //Baslangic tarigi
	$kriter .= $cdb->field_query($kriter,   "MY_DATE"     ,  "<=",  "$t1"); //Bitis tarihi

	$Connect = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE") or die("Couldn't connect to MySQL:<br>" . mysqli_error() . "<br>" . mysqli_errno());
	
	    $strSQL ="SELECT CDR_MAIN_DATA.SITE_ID,REC_TYPE,ORIG_DN,TIME_STAMP_YEAR,TIME_STAMP_MONTH,TIME_STAMP_DAY,
					TIME_STAMP_HOUR,TIME_STAMP_MN,TIME_STAMP_SN,DURATION,DIGITS,TER_TRUNK_MEMBER,
					CALL_TYPE,ACCESS_CODE,CountryCode,LocalCode,PURE_NUMBER,PRICE,ERR_CODE,TIME_STAMP,MY_DATE,
					EXTENTIONS.DESCRIPTION AS ACIKLAMA,
					DEPTS.DEPT_NAME AS DEPARTMAN
					FROM CDR_MAIN_DATA
					LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
					LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
					";  
		$strSQL .= " WHERE CDR_MAIN_DATA.".$kriter;
		$strSQL .= " ORDER BY TIME_STAMP";
	
  
  //echo $strSQL;exit;
  
//execute query 
$result = mysqli_query($Connect,$strSQL) or die("Couldn't execute query:<br>" . mysqli_error(). "<br>" . mysqli_errno()); 

//header info for browser


/*******Start of Formatting for Excel*******/ 
$handle = fopen($path.$file_name, "w");

$excel='<table border="1">';
$excel.='<tr><th>Dahili No</th><th>Aciklama</th><th>Departman</th>
<th>Hat No</th><th>Tip</th><th>Aranan Numara</th><th>Tarih</th><th>Saat</th><th>Süre</th><th>Ucret</th></tr>';

$counter= 0;
$counterT= 0;
$tot_duration ="";

while ($row = mysqli_fetch_assoc($result)){
    //echo "<tr><td>".$row['ORIG_DN']."</td><td>".$row['OUT_COUNT']."</td><td>".$row['OUT_ABANDON']."</td></tr>";
	if ($row['REC_TYPE'] == "T") {	$excel.='</td><td bgcolor="lightblue">'; }	else {$excel.='</td><td>'; }
	$excel.=$row['ORIG_DN']; 
	if ($row['REC_TYPE'] == "T") {	$excel.='</td><td bgcolor="lightblue">'; }	else {$excel.='</td><td>'; }
	$excel.=$row['ACIKLAMA']; 
	if ($row['REC_TYPE'] == "T") {	$excel.='</td><td bgcolor="lightblue">'; }	else {$excel.='</td><td>'; }
	$excel.=$row['DEPARTMAN'];
	if ($row['REC_TYPE'] == "T") {	$excel.='</td><td bgcolor="lightblue">'; }	else {$excel.='</td><td>'; }
	$excel.=$row['TER_TRUNK_MEMBER']; 
	if ($row['REC_TYPE'] == "T") {	$excel.='</td><td bgcolor="lightblue">'; }	else {$excel.='</td><td>'; }
	$excel.=$row['REC_TYPE']; 
	if ($row['REC_TYPE'] == "T") {	$excel.='</td><td bgcolor="lightblue">'; }	else {$excel.='</td><td>'; }
	$excel.= substr($row['DIGITS'],1);
	if ($row['REC_TYPE'] == "T") {	$excel.='</td><td bgcolor="lightblue">'; }	else {$excel.='</td><td>'; }
	$excel.=date("d-m-Y",strtotime($row['MY_DATE'])); 
	if ($row['REC_TYPE'] == "T") {	$excel.='</td><td bgcolor="lightblue">'; }	else {$excel.='</td><td>'; }
	$excel.=$row['TIME_STAMP_HOUR'].":".$row['TIME_STAMP_MN'].":".$row['TIME_STAMP_MN']; 
	if ($row['REC_TYPE'] == "T") {	$excel.='</td><td bgcolor="lightblue">'; }	else {$excel.='</td><td>'; }
	
	$duration = $row['DURATION']; 
	$H = floor($duration / 3600);
	$i = floor(($duration / 60) % 60);
	$s = $duration % 60;
	$duration  = sprintf("%02d:%02d:%02d", $H, $i, $s);
	$excel.= $duration;
	
	if ($row['REC_TYPE'] == "T") {	$excel.='</td><td bgcolor="lightblue">'; }	else {$excel.='</td><td>'; }
	$excel.=$row['PRICE']; 
	
	$excel.='</td></tr>';
	
	if ($row['REC_TYPE'] == "T") {$counterT ++;}
	$tot_duration = ((int)$tot_duration) + ((int)$row['DURATION']);

//echo 'Row '.$counter;
$counter ++;

}

	$H = floor($tot_duration  / 3600);
	$i = floor(($tot_duration / 60) % 60);
	$s = $tot_duration  % 60;
	$tot_duration  = sprintf("%02d:%02d:%02d", $H, $i, $s);

$body .= "Transfer Çağrı Sayısı: $counterT Adet <br>";
$body .= "Toplam Çağrı Sayısı: $counter Adet <br>";
$body .= "Toplam Görüşme Süresi: $tot_duration <br>";
$body .= "<br> Not: Çağrı Tipi: O ve G Cevapsız Çağrı, T Transfer Edilmiş <br>";


$excel.='</table>';


 fwrite($handle, $excel); 
 fclose($handle);
	
	/*************************** E-MAILING *********************************/
	
	require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/site.cnf');
	require '/usr/local/wwwroot/multi.crystalinfo.net/root/PHPMailer/src/Exception.php';
	require '/usr/local/wwwroot/multi.crystalinfo.net/root/PHPMailer/src/PHPMailer.php';
	require '/usr/local/wwwroot/multi.crystalinfo.net/root//PHPMailer/src/SMTP.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

      clearstatcache ();
//$test = "Buradayim";

	$Mail = new PHPMailer();
	$Mail->IsSMTP();
	$Mail->SMTPDebug = 0;  // 1, 2 , 3
	$Mail->setLanguage('tr', '/usr/local/wwwroot/multi.crystalinfo.net/root/PHPMailer/language');
	$Mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true ]]; // Exchange icin eklendi
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

	$Mail->AddAttachment( $path.$file_name );
	$Mail->AddAddress( $to );
	$Mail->Subject   = $subject;
	$Mail->Body      = $body;


	$to = explode( ';', implode( ';', (array) $to ) );
	foreach ( $to as $to_recipient ) {
    $Mail->AddAddress( trim( $to_recipient ) );
	}


        if (!$Mail->Send()){
            echo $Mail->ErrorInfo. "<br>". $to ." Adresine Atamadım. \n";
        }else{
           //echo $mail ." Adresine Atıldı. \n<BR>";
		   foreach ( $to as $to_recipient ) {
		   echo $to_recipient ." Adresine Atıldı. \n";
		   }
        }
        
?>