<?    
	//require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/mail/PHPMailerAutoload.php');
     
   $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
   include("mail_send.php");

   /*
   	   ini_set('display_errors', 'On');
	   error_reporting(E_ALL);
   */
   error_reporting(0);
   
   if(!$conn) exit;
   
   
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

 
   $MONTH_LIST = Array("Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık");
   
   $sql_str = "SELECT * FROM SITES";
   $rs = mysqli_query($conn,$sql_str);

   while($row = mysqli_fetch_object($rs)){//Bütün siteler taranmalı ///Site taraması start
      $SITE_ID = $row->SITE_ID;
      $SITE_NAME = $row->SITE_NAME;
      $max_acc_duration = $row->MAX_ACCE_DURATION*60;
      $PRICE_FACTOR = $row->PRICE_FACTOR;
      //echo $row->SITE_ID."<br>";
      if($row->MONTHLY_MAILING_DEPT_DAY > '0'){//0'dan büyükse ilgili günde mailing yapılacakır.  ///Mail atımonayı  start
      if($row->MONTHLY_MAILING_DEPT_DAY == date("d") || $force==1){ ///Mail gün kontrolü start

			
          $report_name = "Aylık Departman Görüşme Özet Raporu";
          $t0 = day_of_last_month("first");
          $t1 = day_of_last_month("last");
		  
		 		
			
		  $CDR_MAIN_DATA = "CDR_MAIN_DATA";  
          $local_country_code = 90;

          $DATA_HEAD ="
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
            table {font-family: Verdana, Arial, Helvetica, sans-serif;     font-size: 8pt;     font-weight: normal;     font-variant: normal;     text-transform: none;     color: #000000; text-decoration: none}
            .header_beyaz{font-family: Verdana, Arial, Helvetica, sans-serif;     font-size: 9pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: F0F8FF;     text-decoration: none;background-color:#6699CC}
            .font_beyaz {font-family: Verdana, Arial, Helvetica, sans-serif;     font-size: 9pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: F0F8FF;     height:22px;    text-decoration: none;}
            .header_mavi {font-family: Verdana, Arial, Helvetica, sans-serif;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #1B4E81;     text-decoration: none}
            .rep_td {font-size: 9pt;     font-family: Courier New, Courier, mono;     font-variant: normal;     text-transform: none;     color: #000000;     height:22px;}  
            .rep_header {font-size: 8pt;     font-family: Verdana,Courier New, Courier, mono;     font-variant: normal;     text-transform: none;     font-weight:bold;    color: #000000;     height:20px;    background-color:#FFFFFF}
            .rep_table_header {font-size: 9pt;     font-family: Verdana,Courier New, Courier, mono;     font-variant: normal;     text-transform: none;     font-weight:bold;    color: #ffffff;     height:25px;    background-color:#959595      }
          </style>
          </head>
          <body bgcolor=\"#FFFFFF\" text=\"#000000\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
          <table width=\"85%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
            <tr>
              <td colspan=\"2\" width=\"100%\" align=\"center\" class=\"rep_header\" align=\"center\">
              <TABLE BORDER=\"0\" WIDTH=\"100%\">
                <TR>
                  <TD><a href=\"http://www.crystalinfo.net\" target=\"_blank\"><img border=0 SRC=\"cid:my-crystal\" ></a></TD>
                  <TD width=\"50%\" align=center CLASS=\"header\">".$SITE_NAME. "<BR>".$report_name."<br><!--DEPT_NAME--></TD>
                  <TD width=\"25%\" align=right><img SRC=\"cid:my-attach\"></TD>
                </TR>
              </TABLE>
              </td>
            </tr>
            <tr>
              <td width=\"100%\" class=\"rep_header\" align=\"right\"></td>
			</tr>
            <tr>
              <td colspan=\"2\">
              <table width=\"100%\" border=\"0\" bgcolor=\"#C7C7C7\" cellspacing=\"1\" cellpadding=\"0\">
                <tr>
                  <td class=\"rep_table_header\" width=\"28%\">Dahili</td>
                  <td class=\"rep_table_header\" width=\"12%\">Şehiriçi</td>
                  <td class=\"rep_table_header\" width=\"14%\">Şehirlerarası</td>
                  <td class=\"rep_table_header\" width=\"12%\">GSM</td>
                  <td class=\"rep_table_header\" width=\"12%\">Uluslararası</td>
                  <td class=\"rep_table_header\" width=\"10%\">Diğer</td>
                  <td class=\"rep_table_header\" width=\"12%\">Toplam</td>
                  <td class=\"rep_table_header\" width=\"12%\" NOWRAP> Fark % si</td>
                </tr>
                <tr>
                  <td colspan=\"8\" bgcolor=\"#000000\" height=\"1\"></td>
                </tr>
          ";

          $QRY = "SELECT * FROM DEPTS WHERE (DEPT_RSP_EMAIL<>'' AND INSTR(DEPT_RSP_EMAIL,'@')) AND SITE_ID = ".$SITE_ID."";
          $rslt = mysqli_query($conn,$QRY);
	  
          while($rwx = mysqli_fetch_object($rslt)){ ///Departman e-mail adresleri dönüşü start
            if($rwx->DEPT_RSP_EMAIL != ""){  ///Departman e-mail kontrolü start
              $DATA = "";
              $sql_str1="SELECT CDR_MAIN_DATA.LocationTypeid AS TYPE, CDR_MAIN_DATA.ORIG_DN, 
                          SUBSTRING(EXTENTIONS.DESCRIPTION,1,50) AS DESCRIPTION,
                          SUM(CDR_MAIN_DATA.PRICE) AS PRICE, MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) as MON
                        FROM CDR_MAIN_DATA
                        LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
                        WHERE CALL_TYPE = 1 
						AND DURATION<'".$max_acc_duration."' 
						AND CDR_MAIN_DATA.SITE_ID = '".$SITE_ID."' 
						AND CDR_MAIN_DATA.ORIG_DN <> '' 
						AND MY_DATE >= '".$t0."'
						AND MY_DATE <= '".$t1."'
						AND EXTENTIONS.DEPT_ID = '".$rwx->DEPT_ID."'
                        GROUP BY ORIG_DN, LocationTypeid";

			$result = mysqli_query($conn,$sql_str1);
		
              UNSET($datas);
              $genel_toplam = 0;$ortalama = 0;
              $cnt = 0;
              while($row = mysqli_fetch_object($result)){ /// Data toplama start
                $CURR_MON = $row->MON;
                $genel_toplam += $row->PRICE * $PRICE_FACTOR;
		
                if($row->ORIG_DN != ""){  //Dahili kontrolü ve toplama start
                  $datas[$row->ORIG_DN][5]=1;
                  if($row->TYPE == 0){ $datas[$row->ORIG_DN][0] = $row->PRICE * $PRICE_FACTOR;
                  }else if($row->TYPE == 1){ $datas[$row->ORIG_DN][1] = $row->PRICE * $PRICE_FACTOR;
                  }else if($row->TYPE == 2){ $datas[$row->ORIG_DN][2] = $row->PRICE * $PRICE_FACTOR;
                  }else if($row->TYPE == 3){ $datas[$row->ORIG_DN][3] = $row->PRICE * $PRICE_FACTOR;
                  }else{                     $datas[$row->ORIG_DN][4] += $row->PRICE * $PRICE_FACTOR;
                  }
                  $datas[$row->ORIG_DN][5] += $row->PRICE * $PRICE_FACTOR;
                }else{
                  if($row->TYPE == 0){
                    $datas[0][0] = $row->PRICE * $PRICE_FACTOR;
                  }else if($row->TYPE == 1){
                    $datas[0][1] = $row->PRICE * $PRICE_FACTOR;
                  }else if($row->TYPE == 2){
                    $datas[0][2] = $row->PRICE * $PRICE_FACTOR;
                  }else if($row->TYPE == 3){
                    $datas[0][3] = $row->PRICE * $PRICE_FACTOR;
                  }else{
                    $datas[0][4] += $row->PRICE * $PRICE_FACTOR;
                  }
                  $datas[0][6] += $row->PRICE;
	
                }///Dahili kontrolü ve toplama end
              }  ///Data toplama end
			  
              $cnt = sizeof($datas);
              if($cnt > 0) $ortalama = $genel_toplam/$cnt;
              $i = 0;
              $my_pr=0;

				$result_1 = mysqli_query($conn,"SELECT DEPT_NAME FROM DEPTS WHERE SITE_ID='".$SITE_ID."' AND DEPT_ID = '".$rwx->DEPT_ID."'");	
				$rwd = mysqli_fetch_object($result_1);
                $DEPT_NAME = $rwd->DEPT_NAME;
					
              $DATA_HEADER =  $DATA_HEAD;
              $DATA_HEADER = str_replace("<!--DEPT_NAME-->", "<STRONG>Departman</STRONG> : ".$DEPT_NAME."<BR> <STRONG>Dönem</STRONG> : ".($MONTH_LIST[$CURR_MON])."  ".date("Y"),$DATA_HEADER);
              $m = 0;
              $si  = 0;
              $sa  = 0;
              $gsm = 0;
              $ua  = 0;
              $oth = 0;
              if(is_array($datas)){   ///Dahili ekran düzenleme start
                foreach($datas as $key=>$value){ ///Data dönme start
                  $i++;
                  $bg_color = "E4E4E4";   
                  if($i%2) $bg_color ="FFFFFF";
                  $DATA .= " <tr  BGCOLOR=$bg_color>\n";
                  $k_x = ($key=="0"?"Dahili Yok":$key);
                  $k_y = ($key=="0"?"-2":$key);
				  
				$result_2 = mysqli_query($conn,"SELECT SUBSTRING(DESCRIPTION,1,50) AS DESCR FROM EXTENTIONS WHERE EXT_NO = '".$key."' AND SITE_ID = '".$SITE_ID."'");
				$rwe = mysqli_fetch_object($result_2);
                $DESCR = $rwe->DESCR;
				
				$DATA .= " <td height=20 class=\"rep_td\">&nbsp;<b>".$k_x."</b> - ".$DESCR."</td>\n";
                  $total = 0;
                  for($k=0;$k<=4;$k++){ /// Data ekleme start
                    $DATA .= " <td class=\"rep_td\" align=\"right\">&nbsp;".write_price($datas[$key][$k])."</td>\n";
                    $total += $datas[$key][$k];
                  }///Data ekleme end
                  $si  += $datas[$key][0];
                  $sa  += $datas[$key][1];
                  $gsm += $datas[$key][2];
                  $ua  += $datas[$key][3];
                  $oth += $datas[$key][4];
                  if($ortalama>0)
                    $yuzde = (($total-$ortalama)*100/$ortalama);
                  else
                    $yuzde;
                  $yuzde>0?$color = "#ff0000" : $color = "#008000";
                  $DATA .= " <td class=\"rep_td\" align=\"right\">&nbsp;<b>".write_price($total)."</b></td>\n";
                  $DATA .= " <td class=\"rep_td\" align=\"right\">&nbsp;<b><font COLOR=$color>".number_format($yuzde,1,',',',')."</font></b></td>\n";
                  $DATA .= "</tr>\n";
                  $my_pr = $my_pr + $total;
                  $m++;
                } /// Data dönme end
                $DATA .= "
                <tr>
                  <td height=3 colspan=8 BGCOLOR=#000000></td>
                </tr>
                <tr>
                  <td  width=\"28%\" ALIGN=\"center\">Toplam</td>
                  <td width=\"12%\" ALIGN=\"right\">".write_price($si)."</td>
                  <td width=\"14%\" ALIGN=\"right\">".write_price($sa)."</td>
                  <td width=\"12%\" ALIGN=\"right\">".write_price($gsm)."</td>
                  <td width=\"12%\" ALIGN=\"right\">".write_price($ua)."</td>
                  <td width=\"10%\" ALIGN=\"right\">".write_price($oth)."</td>
                  <td width=\"12%\" ALIGN=\"right\"></td>
                  <td width=\"12%\" ALIGN=\"right\"></td>
                </tr>
                ";
              } ///Dahili ekran düzenleme end
              $DATA_FOOT = "
              </table>
              <TABLE width=\"100%\">
                <TR>
                  <td class=\"rep_td\" align=\"right\">
                  <B>Toplam Tutar : ".write_price($my_pr)." TL</B>
                  </TD>
                </TR>
                <TR>
                  <td class=\"rep_td\" align=\"right\">
                  <B>Departman Ortalaması : ".write_price($ortalama)." TL</B>
                  </TD>
                </TR>
              </TABLE>
              </td>  
            </tr>  
          </table>  
          <br><br>
          </body>
          ";
          if($DATA !=""){
            $DATA = $DATA_HEADER.$DATA.$DATA_FOOT;
			mail_send($rwx->DEPT_RSP_EMAIL,"Departman Ayrıntı Raporu.",$DATA);
          }
        }///Departman e-mail kontrolü end
      } ///Departman e-mail adresleri dönüşü end
    } ///Mail gün kontrolü end 
  }///Mail atılacak mı end
} ///Site dönüşü end
?>