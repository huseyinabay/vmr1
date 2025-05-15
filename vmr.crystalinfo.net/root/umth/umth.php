<?
 require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
 $cUtility = new Utility();
 $cdb = new db_layer();
 session_cache_limiter('nocache');
 require_valid_login();
 cc_page_meta(0);
 //Hak Kontrol
   //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   
   
   
   
 if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
   print_error("Burayı Görme Hakkınız Yok");
   exit;
 }
 if (!right_get("SITE_ADMIN"))
   $SITE_ID = $_SESSION['site_id'];
 if (right_get("SITE_ADMIN") && ($SITE_ID=="" || $SITE_ID=="-1"))
   $SITE_ID = $_SESSION['site_id'];

 //Hak kontrol sonu  

 $max_acc_dur = 60 * get_site_prm("MAX_ACCE_DURATION", $SITE_ID);
 $local_country_code = get_site_prm("LOCAL_COUNTRY_CODE", $SITE_ID);
 
 
 //date_default_timezone_set('Europe/Istanbul');
 //setlocale(LC_TIME,"turkish");
 setlocale(LC_ALL, 'tr_TR');
 //setlocale(LC_CTYPE, 'tr_TR');
 
  //$gecen = utf8_encode($gecen);
 
 $gecen = iconv("ISO-8859-9","UTF-8",strftime("%B", mktime(0,0,0,date("m")-1,1,date("y"))));
 $onceki = iconv("ISO-8859-9","UTF-8",strftime("%B", mktime(0,0,0,date("m")-2,1,date("y"))));
 $dahaonceki = iconv("ISO-8859-9","UTF-8",strftime("%B", mktime(0,0,0,date("m")-3,1,date("y"))));
 
 
 
 $LTIME = strftime("%Y-%m", mktime(0,0,0,date("m")-1,1,date("y")));
 $PTIME = strftime("%Y-%m", mktime(0,0,0,date("m")-2,1,date("y")));
 $BPTIME = strftime("%Y-%m", mktime(0,0,0,date("m")-3,1,date("y")));

 //echo $LTIME;
 
 //Bgcolro renkleri
 $bgc1 = "#CEDBE3";
 $bgc2 = "#FFFFFF";
 
 function getCodeName($Locationid){
   global $cdb;
   $sqlStr = "SELECT LocationName FROM TLocation WHERE Locationid =".$Locationid;
   $cdb->execute_sql ($sqlStr, $result, $err_msg);
   $row = mysqli_fetch_object($result);
   return $row->LocationName;
 }

 function getGSMProvider($Locationid){
   global $cdb;
   $sqlStr = "SELECT TTelProvider.TelProvider FROM TLocation 
              LEFT JOIN TTelProvider On TLocation.TelProviderid = TTelProvider.TelProviderid
              WHERE Locationid =".$Locationid;
   $cdb->execute_sql ($sqlStr, $result, $err_msg);
   $row = mysqli_fetch_object($result);
   return $row->TelProvider;
 }

 function get_table_name($MyPrm){
   $MyStart = $MyPrm."-01";
   $MyEnd   = $MyPrm."-30";
   $MAIN_DATA_TABLE = getTableName($MyStart,$MyEnd);
   if(!checkTable($MAIN_DATA_TABLE)) $MAIN_DATA_TABLE = "CDR_MAIN_DATA";
   return $MAIN_DATA_TABLE;
 }

 function my_price($MyPrm){
   if ($MyPrm==''){
     $MyVal = 0;
   }else{
     $MyVal = write_price($MyPrm);
   }
   return $MyVal;
 }
 
  function get_month($MyVal){
    global $LTIME, $PTIME, $BPTIME;
	switch($MyVal){
	  case 1: 
	    //$MyMonth = $dahaonceki;
		$MyTime = $BPTIME;
		break;
	  case 2: 
	    //$MyMonth = $onceki;
		$MyTime = $PTIME;
		break;
	  case 3: 
	    //$MyMonth = $gecen;
	    $MyTime = $LTIME;
	    break;
	}
	return $MyTime;
  }

 //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
 $kriter = "";
 $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
 $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
 $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "1"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
 $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
 $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.

 // Sehirlerarasi aramalar aliniyor (LocatiomTypeid = 1)
 for($i=1;$i<4;$i++){
   $MyDateVal = get_month($i);
   $CDR_MAIN_DATA = get_table_name($MyDateVal);
   $sqlStr = "SELECT Locationid,DATE_FORMAT(MY_DATE,'%Y-%m') AS MY_DATE,
                SUM(PRICE) AS PRICE 
			  FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA 
              WHERE ".$kriter." AND CountryCode = ".$local_country_code." AND LocationTypeid = 1 
			    AND DATE_FORMAT(MY_DATE,'%Y-%m')='".$MyDateVal."' 
		      GROUP BY Locationid
			  ORDER BY PRICE DESC LIMIT 10";
   //echo $sqlStr."<br>"; 
   if(!$cdb->execute_sql($sqlStr, $result, $err_msg)){
     print_error($err_msg);
   }
   if (mysqli_num_rows($result) > 0){
     while ($row = mysqli_fetch_object($result)){
       $nat_price[$row->Locationid][$row->MY_DATE] = $row->PRICE; // Local kod ve aya gore tutarlar
	   $city[$row->Locationid] = $row->Locationid;
     }
   }
 }
 $sqlStr = "";

 // GSM aramaları aliniyor (LocatiomTypeid = 2)
 for($i=1;$i<4;$i++){
   $MyDateVal = get_month($i);
   $CDR_MAIN_DATA = get_table_name($MyDateVal);
   $sqlStr = "SELECT Locationid,DATE_FORMAT(MY_DATE,'%Y-%m') AS MY_DATE,
                SUM(PRICE) AS PRICE 
			  FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA 
              WHERE ".$kriter." AND CountryCode = ".$local_country_code." AND LocationTypeid = 2 
			    AND DATE_FORMAT(MY_DATE,'%Y-%m')='".$MyDateVal."' 
		      GROUP BY Locationid
			  ORDER BY PRICE DESC";
   //echo $sqlStr."<br>"; 
   $cdb->execute_sql($sqlStr, $result, $err_msg);
   if (mysqli_num_rows($result) > 0){
     while ($row = mysqli_fetch_object($result)){
       $gsm = getGSMProvider($row->Locationid);
	   $gsm_names[$gsm] = $gsm; // GSM şebekeleri listesi oluşsun.
	   $gsm_price[$gsm][$row->MY_DATE] += $row->PRICE; // Local kod ve aya gore tutarlar
     }
   }
 }
 
 // Uluslararası aramalar aliniyor (LocatiomTypeid = 3)
 for($i=1;$i<4;$i++){
   $MyDateVal = get_month($i);
   $CDR_MAIN_DATA = get_table_name($MyDateVal);
   $sqlStr = "SELECT Locationid,DATE_FORMAT(MY_DATE,'%Y-%m') AS MY_DATE,
                SUM(PRICE) AS PRICE 
			  FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA 
              WHERE ".$kriter." AND CountryCode <> ".$local_country_code." AND LocationTypeid = 3 
			    AND DATE_FORMAT(MY_DATE,'%Y-%m')='".$MyDateVal."'
		      GROUP BY Locationid
			  ORDER BY PRICE DESC LIMIT 10";
   //echo $sqlStr."<br>"; 
   $cdb->execute_sql($sqlStr, $result, $err_msg);
   if (mysqli_num_rows($result) > 0){
     while ($row = mysqli_fetch_object($result)){
       $int_price[$row->Locationid][$row->MY_DATE] = $row->PRICE; // Local kod ve aya gore tutarlar
	   $int[$row->Locationid] = $row->Locationid;
     }
   }
 }

 //Genel Toplamlar
 for($i=1;$i<4;$i++){
   $MyDateVal = get_month($i);
   $CDR_MAIN_DATA = get_table_name($MyDateVal);
   $sqlStr = "SELECT LocationTypeid, DATE_FORMAT(MY_DATE,'%Y-%m') AS MY_DATE, SUM(PRICE) AS PRICE 
			  FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA 
              WHERE ".$kriter." AND DATE_FORMAT(MY_DATE,'%Y-%m')='".$MyDateVal."'
		      GROUP BY LocationTypeid";
   //echo $sqlStr."<br>";exit; 
   $cdb->execute_sql($sqlStr, $result, $err_msg);
   if (mysqli_num_rows($result) > 0){
     while ($row = mysqli_fetch_object($result)){
       $gen_tot[$row->LocationTypeid] +=$row->PRICE;
       if ($row->LocationTypeid < 4){
	     $tot_price[$row->LocationTypeid][$row->MY_DATE] = $row->PRICE; // Local kod ve aya gore tutarlar
	   }
     }
   }
 }
?>
<div id="bekle" name="bekle" style="position:absolute;left:;top:;">
<table align="center" border="0" cellspacing=0 cellpadding="0">

</table>   
</div>
<script>
   wid = screen.width-180;
   hei = screen.height-30;
   document.all("bekle").style.left=(wid/2);
   document.all("bekle").style.top=(hei/2);
   document.all("bekle").style.display='';
  
   <input type="button" value="Go Back From Whence You Came!" onclick="history.back(-1)" /> // bir önceki syafaya donmek icin
   
   window.onbeforeunload = function () {
    return "Do you really want to close?";
};
   
</script>

<script language="javascript">
  function submit_me(){
    document.site.action = 'umth_forw.php?site_id=' + document.all('SITE_ID').value;
    document.site.submit();
  }
</script>
 <center>
 <table  width="950" border="0" cellspacing="1" cellpadding="3" bgcolor="#FFFFFF" >
   <tr>
     <td colspan="12" width="100%" align="center" class="" align="center">
     <table BORDER="0" WIDTH="100%"><!-- table 2  -->
       <tr>
         <td><a href="http://www.crystalinfo.com.tr" target="_blank"><img border="0" SRC="<?=IMAGE_ROOT?>logo2.gif" ></a></td>
	     <td align=center class="header">
	       <b><br>UMTH ANALIZI<br><?echo "$dahaonceki-$onceki-$gecen"; ?>
	       <br><?echo get_site_name($SITE_ID)?><br></b>
         </td>
	     <td align=right><img SRC="<?=IMAGE_ROOT?>company.gif"></td>
       </tr>
     </table>
     </td>
   </tr>
   <form name="site" method="post" action="umth.php">
   <tr class="form">
     <td class="td1_koyu"  colspan="10" width="30%" align="center">Site Adı&nbsp;&nbsp;&nbsp;
       <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")){echo disabled;}?> onchange="javascript:submit_me();">
       <?
         $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
         echo $cUtility->FillComboValuesWSQL($conn, $strSQL, false,  $SITE_ID);
       ?>
       </select>
     </td>
   </tr>
   <tr class="rep2_tr">
	 <td colspan=10 class="rep2_header" height="28" align="center"><b>GSM TRAFIK ANALİZİ</b></td>
   </tr>
   <tr>
     <td class="rep2_cells3"><b>Aranan Şebeke</b></td> 
	 <td colspan="2"  height="22" align="center" class="rep2_cells3"><b><?=$dahaonceki?></b></td>
	 <td colspan="2" align="center" class="rep2_cells3"><b><?=$onceki?></b></td>
	 <td colspan="2" align="center" class="rep2_cells3"><b><?=$gecen?></b></td>
	 <td colspan="2" align="center" class="rep2_cells3"><b>Toplam</b></td>
  </tr>
   <tr bgcolor="#C6DDE9" align="center">
     <td></td> 
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
  </tr>
   <?
   $MyGenTot[1]  = 0;
   $MyGenTot[2]  = 0;
   $MyGenTot[3]  = 0;
   $MyOverAll    = 0;
   $i = 1;
   if(is_array($gsm_names)){
      foreach ($gsm_names as $key => $value){
	     $MyGSMVal1    = $gsm_price[$gsm_names[$key]][$BPTIME];
         $MyGenTot[1] += $MyGSMVal1;
         $MyGSMVal2    = $gsm_price[$gsm_names[$key]][$PTIME];
         $MyGenTot[2] += $MyGSMVal2;
         $MyGSMVal3    = $gsm_price[$gsm_names[$key]][$LTIME];
         $MyGenTot[3] += $MyGSMVal3;
	     $MyTot        = $MyGSMVal1 + $MyGSMVal2 + $MyGSMVal3 ;
	     $MyOverAll   += $MyTot;
	     $Mybg = bgc($i);
   ?>
   <tr bgcolor="<?=$$Mybg?>" height="18">
     <td width="150" nowrap class="cigate_header1"><?=$gsm_names[$key]?></td>
     <td width="16%" align="right" class="rep2_cells4"><?=my_price($MyGSMVal1);?></td>
     <td width="8%" align="right" class="rep2_cells4"><?=yuzdebul($tot_price['2'][$BPTIME], $MyGSMVal1);?></td>
     <td width="16%" align="right" class="rep2_cells4"><?=my_price($MyGSMVal2);?></td>
     <td width="8%" align="right" class="rep2_cells4"><?=yuzdebul($tot_price['2'][$PTIME], $MyGSMVal2);?></td>
     <td width="16%" align="right" class="rep2_cells4"><?=my_price($MyGSMVal3);?></td>
     <td width="8%" align="right" class="rep2_cells4"><?=yuzdebul($tot_price['2'][$LTIME], $MyGSMVal3);?></td>
     <td width="16%" align="right" class="rep2_cells4"><?=my_price($MyTot);?></td>
     <td width="8%" align="right" class="rep2_cells4"><?=yuzdebul($gen_tot['2'], $MyTot);?></td>
  </tr>
  <?$i++;}}//For döngüsü bitişi?>
   <tr bgcolor="#FFCC00" class="header_sm1">
     <td height="21">Genel Toplam</td>
     <td align="right" class="rep2_cells4"><?=my_price($MyGenTot[1]);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['2'][$BPTIME], $MyGenTot[1]);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyGenTot[2]);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['2'][$PTIME], $MyGenTot[2]);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyGenTot[3]);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['2'][$LTIME], $MyGenTot[3]);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyOverAll);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($gen_tot['2'], $MyOverAll);?></td>
  </tr>
  <tr bgcolor="#FFFFFF"><td height="25" colspan="10"></td></tr>
  <tr class="rep2_tr">
	 <td colspan=12 class="rep2_header" height="28" align="center"><b>ŞEHİRLERARASI TRAFIK ANALİZİ</b></td>
   </tr>
   <tr>
     <td class="rep2_cells3"><b>Aranan Şehir</b></td> 
	 <td colspan="2"  height="22" align="center" class="rep2_cells3"><b><?=$dahaonceki?></b></td>
	 <td colspan="2" align="center" class="rep2_cells3"><b><?=$onceki?></b></td>
	 <td colspan="2" align="center" class="rep2_cells3"><b><?=$gecen?></b></td>
	 <td colspan="2" align="center" class="rep2_cells3"><b>Toplam</b></td>
  </tr>
   <tr bgcolor="#C6DDE9" align="center">
     <td></td> 
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
  </tr>
   <?
    $MyGenTot[1]  = 0;
    $MyGenTot[2]  = 0;
    $MyGenTot[3]  = 0;
    $MyOverAll    = 0;
    $i = 1;
	if(is_array($city)){
       foreach ($city as $key => $value){
	      $MyCityVal1   = $nat_price[$city[$key]][$BPTIME];
          $MyGenTot[1] += $MyCityVal1;
          $MyCityVal2   = $nat_price[$city[$key]][$PTIME];
          $MyGenTot[2] += $MyCityVal2;
          $MyCityVal3   = $nat_price[$city[$key]][$LTIME];
          $MyGenTot[3] += $MyCityVal3;
	      $MyTot        = $MyCityVal1 + $MyCityVal2 + $MyCityVal3 ;
	      $MyOverAll   += $MyTot;
	      $Mybg = bgc($i);
   ?>
   <tr bgcolor="<?=$$Mybg?>" height="18">
     <td nowrap class="cigate_header1"><?=getCodeName($city[$key])?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyCityVal1);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['1'][$BPTIME], $MyCityVal1);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyCityVal2);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['1'][$PTIME], $MyCityVal2);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyCityVal3);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['1'][$LTIME], $MyCityVal3);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyTot);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($gen_tot['1'], $MyTot);?></td>
  </tr>
  <?$i++;}}?>
   <tr bgcolor="#FFCC00" class="header_sm1">
     <td height="21">Genel Toplam</td>
     <td align="right" class="rep2_cells4"><?=my_price($MyGenTot[1]);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['1'][$BPTIME], $MyGenTot[1]);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyGenTot[2]);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['1'][$PTIME], $MyGenTot[2]);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyGenTot[3]);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['1'][$LTIME], $MyGenTot[3]);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyOverAll);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($gen_tot['1'], $MyOverAll);?></td>
  </tr>
  <tr bgcolor="#FFFFFF"><td height="25" colspan="10"></td></tr>
  <tr class="rep2_tr">
	 <td colspan=12 class="rep2_header" height="28" align="center"><b>ULUSLARARASI TRAFIK ANALİZİ</b></td>
   </tr>
   <tr>
     <td class="rep2_cells3"><b>Aranan Ülke</b></td> 
	 <td colspan="2"  height="22" align="center" class="rep2_cells3"><b><?=$dahaonceki?></b></td>
	 <td colspan="2" align="center" class="rep2_cells3"><b><?=$onceki?></b></td>
	 <td colspan="2" align="center" class="rep2_cells3"><b><?=$gecen?></b></td>
	 <td colspan="2" align="center" class="rep2_cells3"><b>Toplam</b></td>
  </tr>
   <tr bgcolor="#C6DDE9" align="center">
     <td></td> 
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
	 <td><b>Tutar</b></td>
     <td><b>Yüzde</b></td>
  </tr>
   <?
    $MyGenTot[1]  = 0;
    $MyGenTot[2]  = 0;
    $MyGenTot[3]  = 0;
    $MyOverAll    = 0;
	$i = 1;
	if(is_array($int)){
       foreach ($int as $key => $value){
	     $MyIntVal1    = $int_price[$int[$key]][$BPTIME];
         $MyGenTot[1] += $MyIntVal1;
         $MyIntVal2    = $int_price[$int[$key]][$PTIME];
         $MyGenTot[2] += $MyIntVal2;
         $MyIntVal3    = $int_price[$int[$key]][$LTIME];
         $MyGenTot[3] += $MyIntVal3;
	     $MyTot        = $MyIntVal1 + $MyIntVal2 + $MyIntVal3 ;
	     $MyOverAll   += $MyTot;
	     $Mybg = bgc($i);
   ?>
   <tr bgcolor="<?=$$Mybg?>" height="18">
     <td nowrap class="cigate_header1"><?=getCodeName($int[$key])?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyIntVal1);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['3'][$BPTIME], $MyIntVal1);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyIntVal2);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['3'][$PTIME], $MyIntVal2);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyIntVal3);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['3'][$LTIME], $MyIntVal3);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyTot);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($gen_tot['3'], $MyTot);?></td>
  </tr>
  <?$i++;}}?>
   <tr bgcolor="#FFCC00" class="header_sm1">
     <td height="21">Genel Toplam</td>
     <td align="right" class="rep2_cells4"><?=my_price($MyGenTot[1]);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['3'][$BPTIME], $MyGenTot[1]);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyGenTot[2]);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['3'][$PTIME], $MyGenTot[2]);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyGenTot[3]);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($tot_price['3'][$LTIME], $MyGenTot[3]);?></td>
     <td align="right" class="rep2_cells4"><?=my_price($MyOverAll);?></td>
     <td align="right" class="rep2_cells4"><?=yuzdebul($gen_tot['3'], $MyOverAll);?></td>
  </tr>
</table>

<script  LANGUAGE="JavaScript1.2">
function reloadme(){
  document.site.submit();
}
 document.all("bekle").style.display='none'; 
</script>
