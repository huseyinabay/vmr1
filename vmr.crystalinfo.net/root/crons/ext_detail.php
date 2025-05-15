<?   
  	//require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/mail/PHPMailerAutoload.php');
     
   $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
   include("mail_send.php");
   
  if(!$conn) exit;
  
 error_reporting(0);
 /*
    	   ini_set('display_errors', 'On');
	   error_reporting(E_ALL);
  */
  
  function write_price($price){
    $price = number_format($price,2, ',', '.');
    return $price;
  }

function day_of_last_month($val){
  if ($val <>"first" && $val <> "last"){
  echo "Yanlış Paramatre Girişi";
  }else{
     $month=date("m")-1;
    if ($month=='0')
      $month='12';
    $lastday="01";
      $year =date("y");
    /* Figure out how many days are in this month */ 
    while (checkdate($month, $lastday, $year)): 
        $lastday++; 
       endwhile;
      --$lastday;  
  if ($val=='first')
    return strftime("%Y-%m-%d", mktime(0,0,0,date("m")-1,1,date("y")));
  else if ($val=='last')
    return  strftime("%Y-%m-%d", mktime(0,0,0,date("m")-1,$lastday,date("y")));
  }
}

     function int_format($my_val){
    if ($my_val <= '9')
      return '0'.$my_val;
    else 
      return $my_val;  
  }


  function calculate_all_time($time){
        $hr = floor($time/3600);
        $rest = $time - $hr*3600;
        $min = floor($rest/60);
        $sn = $rest - $min*60;
        $sn = number_format($sn,0,'','');
        $my_time = int_format($hr).":".int_format($min).":".int_format($sn);
        return $my_time;
  }
  


	$result_1 = mysqli_query($conn,"SELECT Locationid,LocationName FROM TLocation");
	//$rwe = mysqli_fetch_object($result_1);
    //$DESCR = $rwe->DESCR;
  
  $arr_location = array();
  while ($row=mysqli_fetch_object($result_1)){
    $arr_location[$row->Locationid] = $row->LocationName;
  }

  $arr_contact = array();
  $arr_ext = array();
  
  $result_2 = mysqli_query($conn,"SELECT * FROM SITES ");
  while($row = mysqli_fetch_object($result_2)){//Bütün siteler taranmalı Site döngüsü start
    $SITE_ID = $row->SITE_ID;
    $SITE_NAME = $row->SITE_NAME;
    $max_acc_duration = $row->MAX_ACCE_DURATION*60;
    $PRICE_FACTOR = $row->PRICE_FACTOR;
    $MONTHLY_MAILING_DAY = $row->MONTHLY_MAILING_DAY;
    $LOC_CODE = $row->SITE_CODE;

    ///Joinden kaçmak için ilgili sitenin contact bilgileri diziye alınıyor.
    $sql_str1="SELECT IF(IS_COMPANY=1,COMPANY,CONCAT(NAME,' ',SURNAME)) AS NAME,
              CONCAT(PHONES.COUNTRY_CODE,IFNULL(PHONES.CITY_CODE,''),PHONES.PHONE_NUMBER) AS PHONE_NUM  
              FROM CONTACTS
              INNER JOIN PHONES ON CONTACTS.CONTACT_ID = PHONES.CONTACT_ID
              WHERE CONTACTS.SITE_ID = ".$SITE_ID." AND IS_GLOBAL = 1 ORDER BY PHONE_NUM ASC
             "; 
$result_3 = mysqli_query($conn,$sql_str1);
//echo $result_3;

    unset($arr_contact);
    while ($row1=mysqli_fetch_object($result_3)){
      $arr_contact[$row1->PHONE_NUM] = $row1->NAME;
    }

    if($row->MONTHLY_MAILING_DAY > '0'){//0'dan büyükse ilgili günde mailing yapılacakır. Mail atılacak mı start
      if($row->MONTHLY_MAILING_DAY == date("d") || $force==1){//Mail günü geldi mi start
        $report_name = "Aylık Dahili Görüşme Raporu";
        $DATA_HEADER ="
        <html>
        <head>
        <title>Crystal Info</title>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1254\">
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-9\">
        <style>
          body {font-family:Verdana, Arial, Helvetica, sans-serif;     font-size: 8pt;     font-weight: normal;     font-variant: normal;     text-transform: none;     color: #000000;     text-decoration: none}
          .homebox {font-family: Verdana, Arial, Helvetica, sans-serif;         font-size: 7pt;         font-weight: bold;         font-variant: normal;         text-transform: none;         color: FF6600;         text-decoration: none}
          .header {font-family: Verdana,Ariel,Helvatica, san-serif;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #2C5783;     text-decoration: none}
          .header_beyaz2 {font-family: Verdana,Ariel,Helvatica, san-serif;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: FFFFFF;        background-color:  508AC5;    text-decoration: none}
          .header_sm {font-family: Verdana,Ariel,Helvatica, san-serif;     font-size: 7pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #2C5783;     text-decoration: none}
          a.a1 {font-family: Verdana,Ariel,Helvatica, san-serif;     font-size: 8pt;     font-weight: normal;     font-variant: normal;     text-transform: none;     color: #1B4E81;     text-decoration: none}
          a.a1:hover {font-family: Verdana,Ariel,Helvatica, san-serif;     font-size: 8pt;     font-weight: normal;     font-variant: normal;     text-transform: none;     color: #000000;     text-decoration: none}
          a {font-family: Geneva, Arial, Helvetica, san-serif;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #FF6600;     text-decoration: none}
          .text {font-family: Verdana, Arial, Helvetica, sans-serif;  font-size: 8pt;  font-weight: normal;  font-variant: normal;  text-transform: none;  color: #1b4e81 ;  text-decoration: none}
          a:hover {font-family: Geneva, Arial, Helvetica, san-serif;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #FF9000;     text-decoration: none}
          .copyright {font-family: Geneva, Arial, Helvetica, san-serif;     font-size: 7pt;     font-weight: normal;     font-variant: normal;     text-transform: none;     color: #0099CC;     text-decoration: none}
          .table_header {font-family: Geneva, Arial, Helvetica, san-serif;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #ECF9FF;     text-decoration: none}
          td.td1 {font-size: 8pt;    border-style: solid ;     border-width: 0};
          td.header1 {background-color: #0099CC;     font-size: 8pt;     font-weight: Bold;     font-variant: normal;     text-transform: none;     color: #000000;     text-decoration: none}
          td.td1_koyu {font-family: Verdana, Arial, Helvetica, sans-serif;     font-size: 8pt;     font-weight: Bold;     font-variant: normal;     text-transform: none;     color: #1B4E81;     border:0;    height:22px;    text-decoration: none}
          tr.header1 {background-color: #0099CC;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #000000;     text-decoration: none}
          tr.bgc1 {background-color: #B1CBE4}
          tr.bgc2 {background-color: #C6D9EC}
          table{font-family: Verdana, Arial, Helvetica, sans-serif;     font-size: 8pt;     font-weight: normal;     font-variant: normal;     text-transform: none;     color: #000000; text-decoration: none}
          .header_beyaz{font-family: Verdana, Arial, Helvetica, sans-serif;     font-size: 9pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: F0F8FF;     text-decoration: none;background-color:#6699CC}
          .font_beyaz {font-family: Verdana, Arial, Helvetica, sans-serif;     font-size: 9pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: F0F8FF;     height:22px;    text-decoration: none;}
          .header_mavi {font-family: Verdana, Arial, Helvetica, sans-serif;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #1B4E81;     text-decoration: none}
          .rep_td {font-size: 9pt;     font-family: Courier New, Courier, mono;     font-variant: normal;     text-transform: none;     color: #000000;     height:22px;}  
          .rep_header {font-size: 8pt;     font-family: Verdana,Courier New, Courier, mono;     font-variant: normal;     text-transform: none;     font-weight:bold;    color: #000000;     height:20px;    background-color:#FFFFFF}
          .rep_table_header {font-size: 9pt;     font-family: Verdana,Courier New, Courier, mono;     font-variant: normal;     text-transform: none;     font-weight:bold;    color: #ffffff;     height:25px;    background-color:#959595      }
        </style>
        </head>
        <body bgcolor=\"#FFFFFF\" text=\"#000000\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
        <center>  
        <br><br>
        <table width=\"65%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
          <tr>
            <td colspan=\"2\" width=\"100%\" align=\"center\" class=\"rep_header\" align=\"center\">
            <TABLE BORDER=\"0\" WIDTH=\"100%\">
              <TR>
                <TD><a href=\"http://www.crystalinfo.net\" target=\"_blank\"><img border=0 SRC=\"cid:my-crystal\" ></a></TD>
                <TD width=\"50%\" align=center CLASS=\"header\">".$SITE_NAME . "<BR>Aylık Dahili Raporu<br><STRONG>Dönem</STRONG> : <!-- DONEM --></TD>
                <TD width=\"25%\" align=right><img SRC=\"cid:my-attach\"></TD>
              </TR>
            </TABLE>
            </td>
          </tr>
          <tr>
            <td colspan=\"2\" width=\"100%\" align=\"Left\" class=\"rep_header\" align=\"center\">
              Dahili : <!-- DAHILI -->
            </td>
          </tr>
          <tr>
            <td colspan=\"2\">
            <table border=\"0\"  width=100% bgcolor=\"#C7C7C7\" cellspacing=\"1\" cellpadding=\"0\">
              <tr>
                <td align=center class=\"rep_table_header\" width=\"30%\">Tarih</td>
                <td  align=center class=\"rep_table_header\" width=\"15%\">Süre</td>
                <td  align=center class=\"rep_table_header\" width=\"20%\">Telefon</td>
                <td  align=center class=\"rep_table_header\" width=\"20%\">Aranan Yer</td>
                <td  align=center class=\"rep_table_header\" width=\"15%\">Ücret</td>
              </tr>
              <tr>
                <td colspan=\"7\" bgcolor=\"#000000\" height=\"1\"></td>
              </tr>
        ";
        $DATA_FOOTER = "
            </table>
            </td>
          </tr>
        </table>";
        $QRY = "SELECT * FROM EXTENTIONS WHERE (EMAIL<>'' AND INSTR(EMAIL,'@')) AND SITE_ID = ".$SITE_ID;
        $result_4 = mysqli_query($conn,$QRY);
        while($rwx = mysqli_fetch_object($result_4)){//Sitedeki dahililer döngüsü start
          $orig_dn = $rwx->EXT_NO;
          $descr = substr($rwx->DESCRIPTION,0,50);
          $email = $rwx->EMAIL;

          $t0 = day_of_last_month("first");
          $t1 = day_of_last_month("last");


          $local_country_code = 90;

          $DATA = "";
          if ($email<>""){//Mail adresi yoksa göndermenin bir anlamı yok Email var mı start
            $sql_str_orig  = "SELECT LTRIM(ORIG_DN) AS ORIG_DN,
                                DATE_FORMAT(TIME_STAMP,\"%d.%m.%Y %H:%i:%s\") AS MY_DATE, LocationTypeid,
                                DURATION, Locationid, CountryCode, LocalCode, PURE_NUMBER, 
								CONCAT(CountryCode, LocalCode, PURE_NUMBER) AS PHONE_NUM,
                                (CDR_MAIN_DATA.PRICE*$PRICE_FACTOR) AS PRICE
                              FROM CDR_MAIN_DATA
							  WHERE CALL_TYPE = 1 
						AND ERR_CODE = 0	  
						AND DURATION<'".$max_acc_duration."' 
						AND CDR_MAIN_DATA.SITE_ID = '".$SITE_ID."' 
						AND MY_DATE >= '".$t0."'
						AND MY_DATE <= '".$t1."'
						AND PRICE > 0
						AND LTRIM(CDR_MAIN_DATA.ORIG_DN = '".$orig_dn."')
						ORDER BY MY_DATE ASC ";
                            

//            echo $sql_str_orig."<BR>";
//			continue;
			$result_orig = mysqli_query($conn,$sql_str_orig);
            $my_pr=0;
            while($row_orig = mysqli_fetch_object($result_orig)){//Rapor içeriği start
              if($row_orig->ORIG_DN != ""){//Dahili alanı dolumu start
                if ($row_orig->CountryCode == $local_country_code){
                  if ($row_orig->LocalCode == $LOC_CODE || $row_orig->LocalCode==""){
                    $TEL_NUMBER = $LOC_CODE ." ".$row_orig->PURE_NUMBER;
                  }else{
                    $TEL_NUMBER = $row_orig->LocalCode." ".$row_orig->PURE_NUMBER;
                  }
                }else{
                  $TEL_NUMBER = $row_orig->CountryCode." ".$row_orig->LocalCode." ".$row_orig->PURE_NUMBER;
                } 
                if ($arr_contact[$row_orig->PHONE_NUM]){
                  $called = "<b>".$arr_contact[$row_orig->PHONE_NUM]."</b>";
                }else{
                  $called = $arr_location[$row_orig->Locationid];
                }
                $i++;
                $bg_color = "E4E4E4";   
                if($i%2) $bg_color ="FFFFFF";
                $DATA  .= " <tr  BGCOLOR=$bg_color>\n";
                $DATA  .= " <td class=\"rep_td\" align=\"center\">&nbsp;".$row_orig->MY_DATE."</td>\n";
                $DATA  .= " <td class=\"rep_td\" align=\"center\">&nbsp;".calculate_all_time($row_orig->DURATION)."</td>\n";
                $DATA  .= " <td class=\"rep_td\" align=\"center\">&nbsp;".$TEL_NUMBER."</td>\n";
                $DATA  .= " <td class=\"rep_td\" align=\"center\">&nbsp;".$called."</td>\n";
                $DATA  .= " <td class=\"rep_td\" align=\"center\">".write_price($row_orig->PRICE * $PRICE_FACTOR)."</td>\n";
                $DATA  .= "</tr>\n";
                $my_pr = $my_pr + $row_orig->PRICE * $PRICE_FACTOR;
              }//Dahili alanı dolu mu end
            }//Rapor içeriği doldu end
            $DATA .= "
            <tr>
              <td colspan=7 height=3 BGCOLOR=#000000></td>
            </tr>
            <tr >
              <td class=\"rep_table_header\" width=\"30%\">Toplam</td>
              <td colspan=3 class=\"rep_table_header\" width=\"60%\"></td>
              <td class=\"rep_table_header\" width=\"10%\">".write_price($my_pr)."</td>
            </tr>";
            $DATA_HEAD = $DATA_HEADER;
            $DATA_HEADER = str_replace("<!-- DAHILI -->", $orig_dn . "  -- ". $descr, $DATA_HEADER);
            $DATA_HEADER = str_replace("<!-- DONEM -->", ($t0." - ".$t1) ,$DATA_HEADER);
            if($DATA !=""){
              $DATA = $DATA_HEADER.$DATA.$DATA_FOOTER;
			  mail_send($email,"Dahili Raporu -- $orig_dn.",$DATA);
              $DATA=""; //Data değişkenini boşalt
              $DATA_HEADER = $DATA_HEAD;
            }
            $email = "";
            $orig_dn = "";
          }//Email var mı end. 
        }//Sitedeki dahililer dönüyor end
      }//Mail günü geldi mi end
    }//Mail atılacak mı end
  }//Site döngüsü kapandı.
 ?>
