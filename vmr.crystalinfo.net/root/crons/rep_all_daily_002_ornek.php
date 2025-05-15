<?php
	//require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/site.cnf');
	require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/functions.php');
	//include("mail_send.php");

	//error_reporting(E_ALL);
	//ini_set('display_errors', 'On');

register_globals();

	// SITE SCIMI
	$SITE_ID = "2";
	$ttype = "GUNLUK_OZET";
	
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
	
	
	$MY_DATE = "f";
	$t0 = strftime("%Y-%m-%d", mktime(0,0,0,date("m"),date("d")-1,date("y")));
	
	$st0 =  strtotime($t0);
	$st0 = date('dmY', $st0);

	//echo $st0;exit;

/*******EDIT LINES 3-8*******/

	$kriter = "";
	$forceMainTable = "";
	
    $max_acc_dur="43200";
    $usr_crt = "";
	 
	$to = "hmutlu@mniletisim.com.tr"; 
	//$to = "hmutlu@mniletisim.com.tr;aabay@mniletisim.com.tr;mbudak@mniletisim.com.tr;okurt@netser.com.tr;herdinc@netser.com.tr";
	$subject   = "$ttype Rapor, $company Bölge , Rapor Tarihi : $t0";

	$body = "Çağrıların Giden Ve Gelen Aramalara Göre Günlük Özet Raporu.\r\n
			, Bolge Adı: $company \r\n
			, Rapor Tarihi: $st0";

	
	$path = "/usr/local/wwwroot/multi.crystalinfo.net/root/crons/XlsReports/"; 
	$file_name = $company."_".$ttype."_".$st0.".xls";
		
	//echo $file_name;exit;

	/*******YOU DO NOT NEED TO EDIT ANYTHING BELOW THIS LINE*******/    
//create MySQL connection  

    $kriter = $cdb->field_query($kriter,   "SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
    $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.  
	$kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  ">",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
    $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.

    //Zaman kriterleri ve tablo ismi seçimi başlangıç
    add_time_crt();//Zaman kriteri

     if($forceMainTable)
       $CDR_MAIN_DATA = "CDR_MAIN_DATA";
     else
       $CDR_MAIN_DATA = getTableName($t0,$t1);
      
     if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";  

    //Zaman kriterleri ve tablo ismi seçimi bitiş
	
    
    $sql_str_del="TRUNCATE TABLE `MCRYSTALINFONE`.`CDR_MAIN_ALL`";
    if (!($cdb->execute_sql($sql_str_del,$R1,$error_msg))){
        print_error($error_msg);
        exit;
    }
	
	//echo $sql_str_del;exit;
	
	$usr_crtd = str_replace("CDR_MAIN_DATA", "$CDR_MAIN_DATA", "$usr_crt");
	
	    $sql_str_out=  "INSERT INTO CDR_MAIN_ALL 
						SELECT SITE_ID,REC_TYPE,ORIG_DN,TIME_STAMP_YEAR,TIME_STAMP_MONTH,TIME_STAMP_DAY,TIME_STAMP_HOUR,TIME_STAMP_MN,TIME_STAMP_SN,DURATION,DIGITS,
						CLID,CALL_TYPE,ACCESS_CODE,CountryCode,LocalCode,PURE_NUMBER,PRICE,ERR_CODE,TIME_STAMP,MY_DATE
						FROM $CDR_MAIN_DATA "; 
		$sql_str_out .= " WHERE ".$kriter." ".$usr_crtd;
 // echo $sql_str_out;exit;
    if (!($cdb->execute_sql($sql_str_out,$R2,$error_msg))){
        print_error($error_msg);
        exit;
    }
	
	$usr_crti = str_replace("CDR_MAIN_DATA", "CDR_MAIN_INB", "$usr_crt");
	
	     $sql_str_inb ="INSERT INTO CDR_MAIN_ALL 
						SELECT SITE_ID,REC_TYPE,ORIG_DN,TIME_STAMP_YEAR,TIME_STAMP_MONTH,TIME_STAMP_DAY,TIME_STAMP_HOUR,TIME_STAMP_MN,TIME_STAMP_SN,DURATION,DIGITS,
						CLID,CALL_TYPE,ACCESS_CODE,CountryCode,LocalCode,PURE_NUMBER,PRICE,ERR_CODE,TIME_STAMP,MY_DATE
						FROM CDR_MAIN_INB";  
		$sql_str_inb .= " WHERE ".$kriter." ".$usr_crti;
		
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
	
	
	//zaman formati mysql den
	/*
	   $strSQL  = "SELECT LTRIM(ORIG_DN) AS ORIG_DN,
					EXTENTIONS.DESCRIPTION AS ACIKLAMA,
					DEPTS.DEPT_NAME AS DEPARTMAN,
					COUNT(IF(CALL_TYPE=1,1,NULL)) AS OUT_COUNT,
					$OUT_ABANDON 
					IFNULL(SEC_TO_TIME(SUM(CASE WHEN CALL_TYPE=1 THEN DURATION END)), '00:00:00') AS OUT_DUR, 
					TIME_FORMAT(IFNULL(SEC_TO_TIME(SUM(CASE WHEN CALL_TYPE=1 THEN DURATION END)/COUNT(IF(CALL_TYPE=1,1,NULL))), '00:00:00'), '%H:%i:%s') AS OUT_AVRG,
					COUNT(IF(CALL_TYPE=2,2,NULL)) AS IN_COUNT,
					$IN_ABANDON 
					IFNULL(SEC_TO_TIME(SUM(CASE WHEN CALL_TYPE=2 THEN DURATION END)), '00:00:00') AS IN_DUR,
					TIME_FORMAT(IFNULL(SEC_TO_TIME(SUM(CASE WHEN CALL_TYPE=2 THEN DURATION END)/COUNT(IF(CALL_TYPE=1,1,NULL))), '00:00:00'), '%H:%i:%s') AS IN_AVRG, 
					COUNT(CALL_TYPE) AS TOTAL_COUNT,   
					IFNULL(SEC_TO_TIME(SUM(DURATION)), '00:00:00') AS TOTAL_DUR,    
					TIME_FORMAT(IFNULL(SEC_TO_TIME(SUM(DURATION)/COUNT(CDR_MAIN_ALL.SITE_ID)), '00:00:00'), '%H:%i:%s') AS TOTAL_AVRG   
					FROM CDR_MAIN_ALL
					LEFT JOIN EXTENTIONS ON CDR_MAIN_ALL.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_ALL.SITE_ID = EXTENTIONS.SITE_ID
					LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                 ";

*/

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

//header info for browser



// header

//header("Content-Type: application/xls"); 
//header("Content-Type: application/vnd.ms-excel");       
//header("Content-Disposition: attachment; filename=$file_name");  
//header("Pragma: no-cache"); 
//header("Expires: 0");


/*******Start of Formatting for Excel*******/ 
$handle = fopen($path.$file_name, "w");

$excel='<table border="1">';
$excel.='<tr><th bgcolor="wheat">Dahili No</th><th bgcolor="wheat">Aciklama</th><th bgcolor="wheat">Departman</th>
<th>Giden Adet</th><th>Giden Sure</th><th>Giden Ort Sure</th>
<th bgcolor="lightblue">Gelen Adet</th><th bgcolor="lightblue">Gelen Cevapsiz</th><th bgcolor="lightblue">Gelen Sure</th><th bgcolor="lightblue">Gelen Ort Sure</th>
<th>Toplam Cagri Adet</th><th>Toplam Cagri Suresi</th><th>Toplam Cagri Ort</th></tr>';

//echo '<table border="1">';
//make the column headers what you want in whatever order you want
//echo '<tr><th>Dahili No</th><th>Aciklama</th><th>Departman</th><th>Giden Çağrı Adet</th><th>Giden Cevapsiz</th></tr>';
//loop the query data to the table in same order as the headers
$counter= 0;
while ($row = mysqli_fetch_assoc($result)){
    //echo "<tr><td>".$row['ORIG_DN']."</td><td>".$row['OUT_COUNT']."</td><td>".$row['OUT_ABANDON']."</td></tr>";
	$excel.='<tr><td bgcolor="wheat">'; 
	$excel.=$row['ORIG_DN']; 
	$excel.='</td><td bgcolor="wheat">'; 
	$excel.=$row['ACIKLAMA']; 
	$excel.='</td><td bgcolor="wheat">'; 
	$excel.=$row['DEPARTMAN'];
	
	$excel.='</td><td>'; $excel.=$row['OUT_COUNT']; $excel.='</td><td>'; $excel.=date("H:i:s",$row['OUT_DUR']); $excel.='</td><td>'; $excel.=date("H:i:s",$row['OUT_AVRG']); 
	
	$excel.='</td><td bgcolor="lightblue">'; 
	$excel.=$row['IN_COUNT']; 
	$excel.='</td><td bgcolor="lightblue">'; 
	$excel.=$row['IN_ABANDON'];
	$excel.='</td><td bgcolor="lightblue">'; 
	$excel.=date("H:i:s",$row['IN_DUR']); 
	$excel.='</td><td bgcolor="lightblue">'; 
	$excel.=date("H:i:s",$row['IN_AVRG']);
	
	$excel.='</td><td>'; 
	$excel.=$row['TOTAL_COUNT']; 
	$excel.='</td><td>'; 
	$excel.=date("H:i:s",
	$row['TOTAL_DUR']); 
	$excel.='</td><td>'; 
	$excel.=date("H:i:s",
	$row['TOTAL_AVRG']); 
	$excel.='</td></tr>';
	
	
	 $gtot_out_count   = $gtot_out_count + $row['OUT_COUNT'];
	 $gtot_out_dur     = $gtot_out_dur + $row['OUT_DUR'];
	 $gtot_out_avrg    = $gtot_out_avrg + $row['OUT_AVRG'];
	 
	 $gtot_in_count    = $gtot_in_count + $row['IN_COUNT'];
	 $gtot_in_abandon  = $gtot_in_abandon + $row['IN_ABANDON'];
	 $gtot_in_dur      = $gtot_in_dur + $row['IN_DUR'];
	 $gtot_in_avrg     = $gtot_in_avrg + $row['IN_AVRG'];
	 
	 $gtot_tot_count   = $gtot_tot_count + $row['TOTAL_COUNT'];
	 $gtot_tot_dur     = $gtot_tot_dur + $row['TOTAL_DUR'];
	 $gtot_tot_avrg    = $gtot_tot_avrg + $row['TOTAL_AVRG'];
	 
	 
//echo 'Row '.$counter;
$counter ++;

}

//echo '</table>';

//////////////////// ALT TOPLAMLAR /////////////////////////////
$gtot_out_dur  = date("H:i:s", $gtot_out_dur);
$gtot_out_avrg = date("H:i:s", $gtot_out_avrg);
$gtot_in_dur   = date("H:i:s", $gtot_in_dur);
$gtot_in_avrg  = date("H:i:s", $gtot_in_avrg);
$gtot_tot_dur  = date("H:i:s", $gtot_tot_dur);
$gtot_tot_avrg  = date("H:i:s", $gtot_tot_avrg);

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