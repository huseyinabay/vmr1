  <html>  
<head>  
<title>ShotDev.Com Tutorial</title>  
</head>  
<body>  
<?  
	require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/site.cnf');
	include("mail_send.php");
	
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
	
  
  //$conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
  
  ///////////////////////////////////////////////////////////////////////////////
  


$objConnect = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE") or die(mysql_error());  


//$objDB = mysqli_select_db("MCRYSTALINFONE"); 

	
	 $kriter = "";
	 $ACCESS_CODE = "";
	 $forceMainTable = "";
	 $order ="";
	 $D_ID = "";
	 $alert = "";
     $max_acc_dur="43200";
     $usr_crt = "";
	 

	$SITE_ID = "1";
	
	// Tarih secimi : f- dun , i - gecen hafta , g- gecen ay
	
	$MY_DATE = "f";
	$t0 = strftime("%Y-%m-%d", mktime(0,0,0,date("m"),date("d")-1,date("y")));
	
	//$MY_DATE = "g";	
	//$t0 = day_of_last_month("first");
    //$t1 = day_of_last_month("last");

	//$MY_DATE = "i";
    //$t0 = day_of_last_week(1);
    //$t1 = day_of_last_week(7);

	//echo $t0;exit;

    $kriter = $cdb->field_query($kriter,   "SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
    $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.  
	$kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  ">",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
    $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.

    //Zaman kriterleri ve tablo ismi seçimi başlangıç
    add_time_crt();//Zaman kriteri
	$link  ="";

     if($forceMainTable)
       $CDR_MAIN_DATA = "CDR_MAIN_DATA";
     else
       $CDR_MAIN_DATA = getTableName($t0,$t1);
      
     if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";  

    //Zaman kriterleri ve tablo ismi seçimi bitiş
	
    $header="Çağrıların Giden Ve Gelen Aramlara Göre Dağılımı";

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
	
	
	$usr_crta = str_replace("CDR_MAIN_DATA", "CDR_MAIN_ALL", "$usr_crt");
	   $strSQL  = "SELECT LTRIM(ORIG_DN) AS ORIG_DN, 
					COUNT(IF(CALL_TYPE=1,1,NULL)) AS OUT_COUNT,
					$OUT_ABANDON 
					SUM(CASE WHEN CALL_TYPE=1 THEN DURATION END) AS OUT_DUR, 
					SUM(CASE WHEN CALL_TYPE=1 THEN DURATION END)/COUNT(IF(CALL_TYPE=1,1,NULL)) AS OUT_AVRG,
					COUNT(IF(CALL_TYPE=2,2,NULL)) AS IN_COUNT,
					$IN_ABANDON 
					SUM(CASE WHEN CALL_TYPE=2 THEN DURATION END) AS IN_DUR,
					SUM(CASE WHEN CALL_TYPE=2 THEN DURATION END)/COUNT(IF(CALL_TYPE=1,1,NULL)) AS IN_AVRG, 
					COUNT(CALL_TYPE) AS TOTAL_COUNT,   
					SUM(DURATION) AS TOTAL_DUR,    
					SUM(DURATION)/COUNT(SITE_ID) AS TOTAL_AVRG   
					FROM CDR_MAIN_ALL
                 ";


      if ($kriter != "")
            $strSQL .= " WHERE ".$kriter." ".$usr_crta;
     $strSQL .= " GROUP BY ORIG_DN";
	 
	 //echo $strSQL;exit;
 
 
 //////////////////////////////////////////////////
 
  
 
//$strSQL = "SELECT * FROM customer";  
$objQuery = mysqli_query($objConnect,$strSQL);  


if($objQuery)  
{  
//*** Get Document Path ***//  
$strPath = realpath(basename(getenv($_SERVER["SCRIPT_NAME"])))."/usr/local/wwwroot/multi.crystalinfo.net/root/crons/XlsReports"; // C:/AppServ/www/myphp  
  
//*** Excel Document Root ***//  
$strFileName = "all_recs_daily.xls";  
  
//*** Connect to Excel.Application ***//  
$xlApp = new COM("Excel.Application");  
$xlBook = $xlApp->Workbooks->Add();  
  
//*** Create Sheet 1 ***//  
$xlBook->Worksheets(1)->Name = "My Customer";  
$xlBook->Worksheets(1)->Select;  
  
 // echo $xlBook;exit;
//*** Width & Height (A1:A1) ***//  
$xlApp->ActiveSheet->Range("A1:A1")->ColumnWidth = 10.0;  
$xlApp->ActiveSheet->Range("B1:B1")->ColumnWidth = 13.0;  
$xlApp->ActiveSheet->Range("C1:C1")->ColumnWidth = 23.0;  
$xlApp->ActiveSheet->Range("D1:D1")->ColumnWidth = 12.0;  
$xlApp->ActiveSheet->Range("E1:E1")->ColumnWidth = 13.0;  
$xlApp->ActiveSheet->Range("F1:F1")->ColumnWidth = 12.0;  
  
//*** Report Title ***//  
$xlApp->ActiveSheet->Range("A1:F1")->BORDERS->Weight = 1;  
$xlApp->ActiveSheet->Range("A1:F1")->MergeCells = True;  
$xlApp->ActiveSheet->Range("A1:F1")->Font->Bold = True;  
$xlApp->ActiveSheet->Range("A1:F1")->Font->Size = 20;  
$xlApp->ActiveSheet->Range("A1:F1")->HorizontalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(1,1)->Value = "Dahili Ozet Raporu";  
  
//*** Header ***//  
$xlApp->ActiveSheet->Cells(3,1)->Value = "DAHILI";  
$xlApp->ActiveSheet->Cells(3,1)->Font->Bold = True;  
$xlApp->ActiveSheet->Cells(3,1)->VerticalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,1)->HorizontalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,1)->BORDERS->Weight = 1;  
  
$xlApp->ActiveSheet->Cells(3,2)->Value = "OUT_COUNT";  
$xlApp->ActiveSheet->Cells(3,2)->Font->Bold = True;  
$xlApp->ActiveSheet->Cells(3,2)->VerticalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,2)->HorizontalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,2)->BORDERS->Weight = 1;  
  
$xlApp->ActiveSheet->Cells(3,3)->Value = "OUT_ABANDON";  
$xlApp->ActiveSheet->Cells(3,3)->Font->Bold = True;  
$xlApp->ActiveSheet->Cells(3,3)->VerticalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,3)->HorizontalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,3)->BORDERS->Weight = 1;  
  
$xlApp->ActiveSheet->Cells(3,4)->Value = "OUT_DUR";  
$xlApp->ActiveSheet->Cells(3,4)->Font->Bold = True;  
$xlApp->ActiveSheet->Cells(3,4)->VerticalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,4)->HorizontalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,4)->BORDERS->Weight = 1;  
  
$xlApp->ActiveSheet->Cells(3,5)->Value = "OUT_AVRG";  
$xlApp->ActiveSheet->Cells(3,5)->Font->Bold = True;  
$xlApp->ActiveSheet->Cells(3,5)->VerticalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,5)->HorizontalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,5)->BORDERS->Weight = 1;  
  
$xlApp->ActiveSheet->Cells(3,6)->Value = "IN_COUNT";  
$xlApp->ActiveSheet->Cells(3,6)->Font->Bold = True;  
$xlApp->ActiveSheet->Cells(3,6)->VerticalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,6)->HorizontalAlignment = -4108;  
$xlApp->ActiveSheet->Cells(3,6)->BORDERS->Weight = 1;  
  
//***********//  
  
$intRows = 4;  
while($objResult = mysqli_fetch_array($objQuery))  
{  
  
//*** Detail ***//  
$xlApp->ActiveSheet->Cells($intRows,1)->Value = $objResult["ORIG_DN"];  
$xlApp->ActiveSheet->Cells($intRows,1)->BORDERS->Weight = 1;  
$xlApp->ActiveSheet->Cells($intRows,1)->HorizontalAlignment = -4108;  
  
$xlApp->ActiveSheet->Cells($intRows,2)->Value = $objResult["OUT_COUNT"];  
$xlApp->ActiveSheet->Cells($intRows,2)->BORDERS->Weight = 1;  
  
$xlApp->ActiveSheet->Cells($intRows,3)->Value = $objResult["OUT_ABANDON"];  
$xlApp->ActiveSheet->Cells($intRows,3)->BORDERS->Weight = 1;  
  
$xlApp->ActiveSheet->Cells($intRows,4)->Value = $objResult["OUT_DUR"];  
$xlApp->ActiveSheet->Cells($intRows,4)->HorizontalAlignment = -4108;  
$xlApp->ActiveSheet->Cells($intRows,4)->BORDERS->Weight = 1;  
  
$xlApp->ActiveSheet->Cells($intRows,5)->Value = $objResult["OUT_AVRG"];  
$xlApp->ActiveSheet->Cells($intRows,5)->BORDERS->Weight = 1;  
$xlApp->ActiveSheet->Cells($intRows,5)->NumberFormat = "$#,##0.00";  
  
$xlApp->ActiveSheet->Cells($intRows,6)->Value = $objResult["IN_COUNT"];  
$xlApp->ActiveSheet->Cells($intRows,6)->BORDERS->Weight = 1;  

$intRows++;  
}  
  
@unlink($strFileName); //*** Delete old files ***//  
  
$xlBook->SaveAs($strPath."/".$strFileName); //*** Save to Path ***//  
 

 
//*** Close & Quit ***//  
$xlApp->Application->Quit();  
$xlApp = null;  
$xlBook = null;  
$xlSheet1 = null;  
  
  
  
}  
  
mysqli_close($objConnect);  
  
//*************** Send Email ***************//  
  
$strTo = "hmutlu@mniletisim.com.tr";  
$strSubject = "Excel Report";  
$strMessage = "Download all_recs_daily.xls for Excel Report";  
  
//*** Uniqid Session ***//  
$strSid = md5(uniqid(time()));  
 

		$strHeader = "";
		$strHeader .= "From: Mr.Weerachai Nukitram<webmaster@shotdev.com>\nReply-To: webmaster@shotdev.com\n";
		$strHeader .= "Cc: Mr.Surachai Sirisart<surachai@shotdev.com>";
		$strHeader .= "Bcc: webmaster@shotdev.com";
	
$strHeader .= "MIME-Version: 1.0\n";  
$strHeader .= "Content-Type: multipart/mixed; boundary=\"".$strSid."\"\n\n";  
$strHeader .= "This is a multi-part message in MIME format.\n";  
  
$strHeader .= "--".$strSid."\n";  
$strHeader .= "Content-type: text/html; charset=windows-874\n"; // or UTF-8 //  
$strHeader .= "Content-Transfer-Encoding: 7bit\n\n";  
$strHeader .= $strMessage."\n\n";  
  
$strContent1 = chunk_split(base64_encode(file_get_contents("MyXls/MyExcel.xls")));  
$strHeader .= "--".$strSid."\n";  
$strHeader .= "Content-Type: application/octet-stream; name=\"MyExcel.xls\"\n";  
$strHeader .= "Content-Transfer-Encoding: base64\n";  
$strHeader .= "Content-Disposition: attachment; filename=\"MyExcel.xls\"\n\n";  
$strHeader .= $strContent1."\n\n";  
  
$flgSend = @mail($strTo,$strSubject,null,$strHeader); // @ = No Show Error //  
if($flgSend)  
{  
echo "Excel Generated & Email Sending.";  
}  
else  
{  
echo "Cannot send mail.";  
}  
  
  

  
  
  /////////////////////////////////////////////////////////////////
  
  
  
  
  /*
  
  
	   
  $sql_str = "SELECT * FROM ALERTS
              INNER JOIN ALERT_DEFS ON ALERTS.ALERT_ID = ALERT_DEFS.ALERT_ID
              WHERE ALERTS.ALERT_ID=16 AND ALERT_DEFS.SITE_ID=$SITE_ID"  ; 
  $result = mysqli_query($conn,$sql_str);
//print_r ($result); die;
//ECHO $sql_str; die;	

  while($row = mysqli_fetch_object($result)){
    $ALERT_DEF_ID = $row->ALERT_DEF_ID;
    $LAST_PROC_ID = $row->LAST_PROC_ID;

    $sql1 = "SELECT CDR_MAIN_INB.SITE_ID, EXTENTIONS.SITE_ID, CDR_ID, EXTENTIONS.EMAIL, TIME_STAMP, CHG_INFO, CLID, TER_DN, EXTENTIONS.DESCRIPTION AS DES, E.DESCRIPTION as EDES 
             FROM CDR_MAIN_INB
             LEFT JOIN EXTENTIONS ON EXTENTIONS.EXT_NO = CDR_MAIN_INB.TER_DN
             LEFT JOIN EXTENTIONS as E ON E.EXT_NO = CDR_MAIN_INB.CLID AND EXTENTIONS.SITE_ID = CDR_MAIN_INB.SITE_ID            
             WHERE ERR_CODE=0 AND CALL_TYPE=2 AND CLID<>'' AND CDR_ID > '$row->LAST_PROC_ID' AND CDR_MAIN_INB.SITE_ID=$SITE_ID AND EXTENTIONS.SITE_ID=$SITE_ID AND DURATION='0'
             LIMIT 10" ; 
//ECHO $sql1; die;			 
		$rs1 = mysqli_query($conn,$sql1);

    ////////////package the data to be sent
    while($row1 = mysqli_fetch_object($rs1)){
      $DATA ="";
      $row1->CLID = str_replace("X","",$row1->CLID);
	  $row1->CHG_INFO = str_replace("RING","",$row1->CHG_INFO);
      $DATA = "<b> $row1->CLID - $row1->EDES</b> numaralı telefon <b> $row1->TIME_STAMP </b>  tarihinde sizi( Dahili :<b> $row1->TER_DN   $row1->DES</b>) aradı, ulaşamadı. çalma süresi - $row1->CHG_INFO <BR><BR><BR>";
      $DATA .= "You( Extention :<b> $row1->TER_DN  $row1->DES</b>) called by <b>$row1->CLID </b> at <b> $row1->TIME_STAMP </b> but the number couldn't reach. Ring time - $row1->CHG_INFO <BR> ";
      $sbjct = "Cyrstallinfo- $row1->CLID - $row1->EDES numaralı telefon $row1->TIME_STAMP tarihinde sizi( Dahili : $row1->TER_DN $row1->DES) aradı, ulaşamadı";
      $LAST_PROC_ID = $row1->CDR_ID;
	  $mail = $row1->EMAIL;	
		

      if($row1->CLID && $row1->EMAIL && $DATA){
        mail_send($row1->EMAIL,$sbjct,$DATA);
    	}
    }
  }
  $sql2 = "UPDATE ALERT_DEFS SET LAST_PROC_ID = '$LAST_PROC_ID' WHERE ALERT_DEF_ID = '$ALERT_DEF_ID' AND SITE_ID=$SITE_ID"; 
 //ECHO $sql2; die;
  $rs2 = mysqli_query($conn,$sql2);
 
*/

 ?>  
</body>  
</html>