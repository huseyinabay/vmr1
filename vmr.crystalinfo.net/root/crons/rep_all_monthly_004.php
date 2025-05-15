<?php
	require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/functions.php');
	//date_default_timezone_set('Europe/Istanbul');
	
register_globals();

	// SITE SCIMI
	$SITE_ID = "4";
	$ttype = "AYLIK_OZET";
	
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
	$company = substr($company,2);
	//echo $company;exit;
	
	// Tarih secimi : f- dun , i - gecen hafta , g- gecen ay
	$t0 = "";
	$t1 = "";
	
	$MY_DATE = "g";	
	$t0 = day_of_last_month("first");
    $t1 = day_of_last_month("last");

	$st0 =  strtotime($t0);
	$st0 = date('dmY', $st0);
	
	$st1 =  strtotime($t1);
	$st1 = date('dmY', $st1);

	//echo $st0;exit;

/*******EDIT LINES 3-8*******/
	$kriter = "";
    $max_acc_dur="43200";
	 
	//$to = "hmutlu@netser.com.tr"; 
	$to = "IZMIR_SANTRAL_RAPOR@renault.com.tr;hmutlu@netser.com.tr;aabay@mniletisim.com.tr;mbudak@mniletisim.com.tr;okurt@netser.com.tr;herdinc@netser.com.tr";
	$subject   = "$ttype Rapor, $company Şube , Rapor Tarihi : $st0 - $st1";

	$body = "Çağrıların Giden Ve Gelen Aramalara Göre Aylık Özet Raporu.<br>
			 Şube Adı: $company <br>
			 Rapor Tarihi: $st0 - $st1";
	
	$path = "/usr/local/wwwroot/multi.crystalinfo.net/root/crons/XlsReports/"; 
	$file_name = $company."_".$ttype."_".$st0."-".$st1.".xls";
	
	//echo $file_name;exit;

	/*******YOU DO NOT NEED TO EDIT ANYTHING BELOW THIS LINE*******/    
//create MySQL connection  

    $kriter = $cdb->field_query($kriter,   "SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
    $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.  
	$kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  ">",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
    $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.

    add_time_crt();//Zaman kriteri
    
    $sql_str_del="TRUNCATE TABLE `MCRYSTALINFONE`.`CDR_MAIN_ALL`";
    if (!($cdb->execute_sql($sql_str_del,$R1,$error_msg))){
        print_error($error_msg);
        exit;
    }
	
	//echo $sql_str_del;exit;
	
	
	    $sql_str_out=  "INSERT INTO CDR_MAIN_ALL 
						SELECT SITE_ID,REC_TYPE,ORIG_DN,TIME_STAMP_YEAR,TIME_STAMP_MONTH,TIME_STAMP_DAY,TIME_STAMP_HOUR,TIME_STAMP_MN,TIME_STAMP_SN,DURATION,DIGITS,
						CLID,CALL_TYPE,ACCESS_CODE,CountryCode,LocalCode,PURE_NUMBER,PRICE,ERR_CODE,TIME_STAMP,MY_DATE
						FROM CDR_MAIN_DATA "; 
		$sql_str_out .= " WHERE ".$kriter;
 // echo $sql_str_out;exit;
    if (!($cdb->execute_sql($sql_str_out,$R2,$error_msg))){
        print_error($error_msg);
        exit;
    }
	
		$sql_str_inb ="INSERT INTO CDR_MAIN_ALL 
						SELECT SITE_ID,REC_TYPE,ORIG_DN,TIME_STAMP_YEAR,TIME_STAMP_MONTH,TIME_STAMP_DAY,TIME_STAMP_HOUR,TIME_STAMP_MN,TIME_STAMP_SN,DURATION,DIGITS,
						CLID,CALL_TYPE,ACCESS_CODE,CountryCode,LocalCode,PURE_NUMBER,PRICE,ERR_CODE,TIME_STAMP,MY_DATE
						FROM CDR_MAIN_INB";  
		$sql_str_inb .= " WHERE ".$kriter;
		
    if (!($cdb->execute_sql($sql_str_inb,$R3,$error_msg))){
        print_error($error_msg);
        exit;
    }

	//echo $sql_str_inb;exit;

	//echo $switch_id;
	//$REC_TYPE = "OU";
	
	//$IN_ABANDON = "COUNT(IF(DURATION='0' AND CALL_TYPE=2,2,NULL )) AS IN_ABANDON,";
	//$OUT_ABANDON = "COUNT(IF(DURATION='0' AND CALL_TYPE=1,1,NULL )) AS OUT_ABANDON,";

	//ipecs UCM
	//$IN_ABANDON = "COUNT(IF(REC_TYPE='R' AND CALL_TYPE=2,2,NULL )) AS IN_ABANDON,"; 
	//$OUT_ABANDON = "COUNT(IF(REC_TYPE='OU' AND CALL_TYPE=1,1,NULL )) AS OUT_ABANDON,";
	
	//eMG
	$IN_ABANDON = "COUNT(IF(REC_TYPE='R' OR REC_TYPE='G' AND CALL_TYPE=2,2,NULL )) AS IN_ABANDON,";
	$OUT_ABANDON = "COUNT(IF(REC_TYPE='O' AND DURATION='0' AND CALL_TYPE=1,1,NULL )) AS OUT_ABANDON,";
	

			   $strSQL  = "SELECT LTRIM(ORIG_DN) AS ORIG_DN,
					EXTENTIONS.DESCRIPTION AS ACIKLAMA,
					DEPTS.DEPT_NAME AS DEPARTMAN,
					COUNT(IF(CALL_TYPE=1,1,NULL))-COUNT(IF(REC_TYPE='O' AND DURATION='0' AND CALL_TYPE=1,1,NULL )) AS OUT_COUNT,
					SUM(CASE WHEN CALL_TYPE=1 THEN DURATION END) AS OUT_DUR, 
					SUM(CASE WHEN CALL_TYPE=1 THEN DURATION END)/COUNT(IF(CALL_TYPE=1,1,NULL)) AS OUT_AVRG,
					COUNT(IF(CALL_TYPE=2,2,NULL)) AS IN_COUNT,
					$IN_ABANDON 
					SUM(CASE WHEN CALL_TYPE=2 THEN DURATION END) AS IN_DUR,
					SUM(CASE WHEN CALL_TYPE=2 THEN DURATION END)/COUNT(IF(CALL_TYPE=1,1,NULL)) AS IN_AVRG, 
					COUNT(CALL_TYPE)-COUNT(IF(REC_TYPE='O' AND DURATION='0' AND CALL_TYPE=1,1,NULL )) AS TOTAL_COUNT,  
					SUM(DURATION) AS TOTAL_DUR,    
					SUM(DURATION)/COUNT(CDR_MAIN_ALL.SITE_ID) AS TOTAL_AVRG   
					FROM CDR_MAIN_ALL
					LEFT JOIN EXTENTIONS ON CDR_MAIN_ALL.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_ALL.SITE_ID = EXTENTIONS.SITE_ID
					LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                 ";

      if ($kriter != "")
            $strSQL .= " WHERE CDR_MAIN_ALL.".$kriter;
     $strSQL .= " GROUP BY ORIG_DN ORDER BY DEPTS.DEPT_NAME, ORIG_DN";
	 
	//echo $strSQL;exit;


$Connect = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE") or die("Couldn't connect to MySQL:<br>" . mysqli_error() . "<br>" . mysqli_errno());
  
//execute query 
$result = mysqli_query($Connect,$strSQL) or die("Couldn't execute query:<br>" . mysqli_error(). "<br>" . mysqli_errno()); 


/*******Start of Formatting for Excel*******/ 
$handle = fopen($path.$file_name, "w");

$excel='<table border="1">';
$excel.='<tr><th bgcolor="wheat">Dahili No</th><th bgcolor="wheat">Aciklama</th><th bgcolor="wheat">Departman</th>
<th>Giden Adet</th><th>Giden Sure</th><th>Giden Ort Sure</th>
<th bgcolor="lightblue">Gelen Adet</th><th bgcolor="lightblue">Gelen Cevapsiz</th><th bgcolor="lightblue">Gelen Sure</th><th bgcolor="lightblue">Gelen Ort Sure</th>
<th>Toplam Cagri Adet</th><th>Toplam Cagri Suresi</th><th>Toplam Cagri Ort</th></tr>';

	 $gtot_out_count   = "";
	 $gtot_out_dur     = "";
	 $gtot_out_avrg    = "";
	 
	 $gtot_in_count    = "";
	 $gtot_in_abandon  = "";
	 $gtot_in_dur      = "";
	 $gtot_in_avrg     = "";
	 
	 $gtot_tot_count   = "";
	 $gtot_tot_dur     = "";
	 $gtot_tot_avrg    = "";

$counter= 0;
while ($row = mysqli_fetch_assoc($result)){

	if ($row['TOTAL_COUNT'] > "0") {
	
	$excel.='<tr><td bgcolor="wheat">'; 
	$excel.=$row['ORIG_DN']; 
	$excel.='</td><td bgcolor="wheat">'; 
	$excel.=$row['ACIKLAMA']; 
	$excel.='</td><td bgcolor="wheat">'; 
	$excel.=$row['DEPARTMAN'];
	
	if ($row['OUT_DUR'] > "0") { $oAvrg= $row['OUT_DUR'] / $row['OUT_COUNT'];} else {$oAvrg="0";}
	
	$excel.='</td><td>'; 
	$excel.=$row['OUT_COUNT']; 
	$excel.='</td><td>'; 

	$out_dur  = $row['OUT_DUR']; 
	$H = floor($out_dur / 3600);
	$i = floor(($out_dur / 60) % 60);
	$s = $out_dur % 60;
	$out_dur  = sprintf("%02d:%02d:%02d", $H, $i, $s);
	$excel.= $out_dur ;
	
	$excel.='</td><td>'; 
	$H = floor($oAvrg / 3600);
	$i = floor(($oAvrg / 60) % 60);
	$s = $oAvrg % 60;
	$oAvrg  = sprintf("%02d:%02d:%02d", $H, $i, $s);
	$excel.= $oAvrg;
	
	$excel.='</td><td bgcolor="lightblue">'; 
	$excel.=$row['IN_COUNT']; 
	$excel.='</td><td bgcolor="lightblue">'; 
	$excel.=$row['IN_ABANDON'];
	$excel.='</td><td bgcolor="lightblue">'; 
	
	$in_dur  = $row['IN_DUR']; 
	$H = floor($in_dur / 3600);
	$i = floor(($in_dur / 60) % 60);
	$s = $in_dur % 60;
	$in_dur  = sprintf("%02d:%02d:%02d", $H, $i, $s);
	$excel.= $in_dur ;
	
	$excel.='</td><td bgcolor="lightblue">'; 
	
	if ($row['IN_DUR'] > "0") { $iAvrg= $row['IN_DUR'] / $row['IN_COUNT'];} else {$iAvrg="0";}
	$H = floor($iAvrg / 3600); 
	$i = floor(($iAvrg / 60) % 60);
	$s = $iAvrg % 60;
	$iAvrg  = sprintf("%02d:%02d:%02d", $H, $i, $s);
	$excel.= $iAvrg;
	
	$excel.='</td><td>'; 
	$excel.=$row['TOTAL_COUNT']; 
	$excel.='</td><td>'; 
	
	
	$tot_dur  = $row['TOTAL_DUR']; 
	$H = floor($tot_dur / 3600);
	$i = floor(($tot_dur / 60) % 60);
	$s = $tot_dur % 60;
	$tot_dur  = sprintf("%02d:%02d:%02d", $H, $i, $s);
	$excel.= $tot_dur ;
	
	$excel.='</td><td>'; 
	
	if ($row['TOTAL_DUR'] > "0") { $tAvrg= $row['TOTAL_DUR'] / $row['TOTAL_COUNT'];} else {$tAvrg="0";}
	$H = floor($tAvrg / 3600); 
	$i = floor(($tAvrg / 60) % 60); 
	$s = $tAvrg % 60;
	$tAvrg  = sprintf("%02d:%02d:%02d", $H, $i, $s);
	$excel.= $tAvrg;
	$excel.='</td></tr>';
	
	 $gtot_out_count   = ((int)$gtot_out_count) + ((int)$row['OUT_COUNT']);
	 $gtot_out_dur     = ((int)$gtot_out_dur) + ((int)$row['OUT_DUR']);
	 //$gtot_out_avrg    = ((int)$gtot_out_avrg) + ((int)$row['OUT_AVRG']);
	 
	 $gtot_in_count    = ((int)$gtot_in_count) + ((int)$row['IN_COUNT']);
	 $gtot_in_abandon  = ((int)$gtot_in_abandon) + ((int)$row['IN_ABANDON']);
	 $gtot_in_dur      = ((int)$gtot_in_dur) + ((int)$row['IN_DUR']);
	 //$gtot_in_avrg     = ((int)$gtot_in_avrg) + ((int)$row['IN_AVRG']);
	 
	 $gtot_tot_count   = ((int)$gtot_tot_count) + ((int)$row['TOTAL_COUNT']);
	 $gtot_tot_dur     = ((int)$gtot_tot_dur) +((int)$row['TOTAL_DUR']);
	 //$gtot_tot_avrg    = ((int)$gtot_tot_avrg) + ((int)$row['TOTAL_AVRG']);
	 
	 
//echo 'Row '.$counter;
$counter ++;

	}
}


//////////////////// ALT TOPLAMLAR /////////////////////////////

//echo $gtot_tot_dur;exit;

$gtot_out_avrg  =  $gtot_out_dur / $gtot_out_count;
$gtot_in_avrg   =  $gtot_in_dur / $gtot_in_count;
$gtot_tot_avrg  =  $gtot_tot_dur / $gtot_tot_count;


$H = floor($gtot_out_dur / 3600);
$i = floor(($gtot_out_dur / 60) % 60);
$s = $gtot_out_dur % 60;
$gtot_out_dur  = sprintf("%02d:%02d:%02d", $H, $i, $s);

$H = floor($gtot_in_dur / 3600);
$i = floor(($gtot_in_dur / 60) % 60);
$s = $gtot_in_dur % 60;
$gtot_in_dur  = sprintf("%02d:%02d:%02d", $H, $i, $s);

$H = floor($gtot_tot_dur / 3600);
$i = floor(($gtot_tot_dur / 60) % 60);
$s = $gtot_tot_dur % 60;
$gtot_tot_dur  = sprintf("%02d:%02d:%02d", $H, $i, $s);

$H = floor($gtot_out_avrg/ 3600);
$i = floor(($gtot_out_avrg/ 60) % 60);
$s = $gtot_out_avrg % 60;
$gtot_out_avrg  = sprintf("%02d:%02d:%02d", $H, $i, $s);

$H = floor($gtot_in_avrg/ 3600);
$i = floor(($gtot_in_avrg / 60) % 60);
$s = $gtot_in_avrg % 60;
$gtot_in_avrg  = sprintf("%02d:%02d:%02d", $H, $i, $s);

$H = floor($gtot_tot_avrg / 3600);
$i = floor(($gtot_tot_avrg / 60) % 60);
$s = $gtot_tot_avrg % 60;
$gtot_tot_avrg  = sprintf("%02d:%02d:%02d", $H, $i, $s);




//echo $gtot_tot_dur;exit;

/*
$oparsed = date_parse($gtot_out_dur);
$oseconds = $oparsed['hour'] * 3600 + $oparsed['minute'] * 60 + $oparsed['second'];
$gtot_out_avrg    =  $oseconds / $gtot_out_count;
$gtot_out_avrg = date("H:i:s", $gtot_out_avrg);	

$iparsed = date_parse($gtot_in_dur);
$iseconds = $iparsed['hour'] * 3600 + $iparsed['minute'] * 60 + $iparsed['second'];
$gtot_in_avrg     =  $iseconds / $gtot_in_count;
$gtot_in_avrg  = date("H:i:s", $gtot_in_avrg);

$tparsed = date_parse($gtot_tot_dur);
$tseconds = $tparsed['hour'] * 3600 + $tparsed['minute'] * 60 + $tparsed['second'];
$gtot_tot_avrg  =  $tseconds / $gtot_tot_count;
$gtot_tot_avrg  = date("H:i:s", $gtot_tot_avrg);
*/

/*
echo $seconds; echo "/";echo $gtot_tot_count; echo "=";echo $gtot_tot_avrg;

echo "son";echo date("H:i:s",$gtot_tot_avrg);

exit;
*/
	$excel.='<tr><td>'; 
	$excel.="";
	$excel.='</td><td>'; 
	$excel.="";
	$excel.='</td><td><strong>'; 
	$excel.="GENEL TOPLAMLAR";	
	$excel.='</strong></td><td><strong>'; 
	$excel.="$gtot_out_count"; 
	$excel.='</strong></td><td><strong>'; 
	$excel.="$gtot_out_dur";
	$excel.='</strong></td><td><strong>'; 
	$excel.="$gtot_out_avrg";
	$excel.='</strong></td><td bgcolor="lightblue"><strong>'; 
	$excel.="$gtot_in_count";
	$excel.='</strong></td><td  bgcolor="lightblue"><strong>'; 
	$excel.="$gtot_in_abandon";
	$excel.='</strong></td><td bgcolor="lightblue"><strong>'; 
	$excel.="$gtot_in_dur";
	$excel.='</strong></td><td bgcolor="lightblue"><strong>'; 
	$excel.="$gtot_in_avrg";
	$excel.='</strong></td><td><strong>'; 
	$excel.="$gtot_tot_count";
	$excel.='</strong></td><td><strong>'; 
	$excel.="$gtot_tot_dur";
	$excel.='</strong></td><td><strong>'; 
	$excel.="$gtot_tot_avrg";
	$excel.='</td></tr>';


$excel.='</table>';


//echo $excel;exit;

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