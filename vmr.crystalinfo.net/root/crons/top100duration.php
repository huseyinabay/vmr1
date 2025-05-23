﻿<?   
  require_once("doc_root.cnf");
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/class.phpmailer.php";
  $cUtility = new Utility();
  $cdb = new db_layer(); 
  $conn = $cdb->getConnection();
  include("mail_send.php");
  if(strlen($site)==0){$site=0;}
  if(!$conn) exit;
  $sql_str="SELECT Locationid,LocationName FROM TLocation"; 
  if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
    print_error($error_msg);
    exit;
  }
  $arr_location = array();
  while ($row=mysqli_fetch_object($result)){
    $arr_location[$row->Locationid] = $row->LocationName;
  }
  
  $SITE_ADMIN = get_system_prm("ADMIN_EMAIL");
  
  $arr_contact = array();
  $arr_ext = array();
  $sql_str = "SELECT * FROM SITES ";
  if (!($cdb->execute_sql($sql_str, $rs, $error_msg))){
    print_error($error_msg);
    exit;
  }
  while($row = mysqli_fetch_object($rs)){//Bütün siteler taranmalı Site döngüsü start
    $SITE_ID = $row->SITE_ID;
    if($site>0){
      if($site!=$SITE_ID){
        continue;
      }
    }
    $SITE_NAME = $row->SITE_NAME;
    $max_acc_duration = $row->MAX_ACCE_DURATION*60;
    $PRICE_FACTOR = $row->PRICE_FACTOR;
    $MONTHLY_MAILING_DAY = $row->MONTHLY_MAILING_DAY;
    $LOC_CODE = $row->SITE_CODE;
    $ADMIN_EMAIL = $row->ADMIN_EMAIL;
	
    ///Joinden kaçmak için ilgili sitenin contact bilgileri diziye alınıyor.
    $sql_str1="SELECT IF(IS_COMPANY=1,COMPANY,CONCAT(NAME,' ',SURNAME)) AS NAME,
                CONCAT(PHONES.COUNTRY_CODE,IFNULL(PHONES.CITY_CODE,''),PHONES.PHONE_NUMBER) AS PHONE_NUM  
              FROM CONTACTS
              INNER JOIN PHONES ON CONTACTS.CONTACT_ID = PHONES.CONTACT_ID
              WHERE CONTACTS.SITE_ID = ".$SITE_ID." AND IS_GLOBAL = 1 ORDER BY PHONE_NUM ASC
             "; 
    if (!($cdb->execute_sql($sql_str1,$result1,$error_msg))){
      print_error($error_msg);
      exit;
    }
    unset($arr_contact);
    while ($row1=mysqli_fetch_object($result1)){
      $arr_contact[$row1->PHONE_NUM] = $row1->NAME;
    }

    if($row->MONTHLY_MAILING_DAY > '0'){//0'dan büyükse ilgili günde mailing yapılacakır. Mail atılacak mı start
      if($row->MONTHLY_MAILING_DAY == date("d") || $force==1){//Mail günü geldi mi start
        $report_name = "Aylık Top 100 Süre Raporu";
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
                <TD width=\"50%\" align=center CLASS=\"header\">".$SITE_NAME . "<BR>Aylık Top 100 Süre Raporu<br><STRONG>Dönem</STRONG> : <!-- DONEM --></TD>
                <TD width=\"25%\" align=right><img SRC=\"cid:my-attach\"></TD>
              </TR>
            </TABLE>
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
        $QRY = "SELECT * FROM EXTENTIONS WHERE SITE_ID = ".$SITE_ID;
        if (!($cdb->execute_sql($QRY, $rslt, $error_msg))){
          print_error($error_msg);
          exit;
        }
        if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
        }
        $arr_orig = array();
        while ($row=mysqli_fetch_object($rslt)){
          $arr_orig[$row->EXT_NO] = substr($row->DESCRIPTION,0,30);
        }

	    $kriter = "";
        //Temel kriterler. Verinin hızlı gelmesi için başa konuldu.
        $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
        $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
        $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "1"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
        $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_duration"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
        $kriter .= $cdb->field_query($kriter,   "PRICE"     ,  ">",  "0"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
        if(strlen($t0)>0 && strlen($t1)>0){
  	      $kriter .= $cdb->field_query($kriter, "MY_DATE"     ,">=",  "'$t0'");
          $kriter .= $cdb->field_query($kriter, "MY_DATE"     ,"<=",  "'$t1'");
        }else{
          $t0 = day_of_last_month("first");
          $t1 = day_of_last_month("last");
  	      $kriter .= $cdb->field_query($kriter, "MY_DATE"     ,">=",  "'$t0'");
          $kriter .= $cdb->field_query($kriter, "MY_DATE"     ,"<=",  "'$t1'");
        }

		//Önceki ayın datasından alsın.
	    $CDR_MAIN_DATA = getTableName($t0,$t1);
        if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";  
        $local_country_code = get_country_code($SITE_ID);


        $DATA = "";
          if ($ADMIN_EMAIL<>"" || $SITE_ADMIN<>""){//Mail adresi yoksa göndermenin bir anlamı yok Email var mı start
            $sql_str = "SELECT LTRIM(ORIG_DN) AS ORIG_DN, 
                          DATE_FORMAT(TIME_STAMP,\"%d.%m.%Y %H:%i:%s\") AS MY_DATE, LocationTypeid,
                          DURATION, Locationid, CountryCode, LocalCode, PURE_NUMBER, 
						  CONCAT(CountryCode, LocalCode, PURE_NUMBER) AS PHONE_NUM
                        FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                        ";
            $sql_str .= " WHERE ".$kriter."  ORDER BY DURATION DESC LIMIT 100 ";
            //echo $sql_str;
			if (!($cdb->execute_sql($sql_str,$result_data,$error_msg))){
              print_error($error_msg);
              exit;
            }
            $my_pr=0;
            while($row_data = mysqli_fetch_object($result_data)){//Rapor içeriği start
              if($row_data->ORIG_DN != ""){//Orig alanı dolumu start
                if ($row_data->CountryCode == $local_country_code){
                  if ($row_data->LocalCode == $LOC_CODE || $row_data->LocalCode==""){
                    $TEL_NUMBER = $LOC_CODE ." ".$row_data->PURE_NUMBER;
                  }else{
                    $TEL_NUMBER = $row_data->LocalCode." ".$row_data->PURE_NUMBER;
                  }
                }else{
                  $TEL_NUMBER = $row_data->CountryCode." ".$row_data->LocalCode." ".$row_data->PURE_NUMBER;
                } 
                if ($arr_contact[$row_data->PHONE_NUM]){
                  $called = "<b>".$arr_contact[$row_data->PHONE_NUM]."</b>";
                }else{
                  $called = $arr_location[$row_data->Locationid];
                }
                $i++;
                $bg_color = "E4E4E4";   
                if($i%2) $bg_color ="FFFFFF";
                $DATA  .= " <tr  BGCOLOR=$bg_color>\n";
                $DATA  .= " <td class=\"rep_td\" align=\"center\">&nbsp;".$row_data->ORIG_DN."-".$arr_orig[$row_data->ORIG_DN]."</td>\n";
                $DATA  .= " <td class=\"rep_td\" align=\"center\">&nbsp;".$row_data->MY_DATE."</td>\n";
                $DATA  .= " <td class=\"rep_td\" align=\"center\">&nbsp;".calculate_all_time($row_data->DURATION)."</td>\n";
                $DATA  .= " <td class=\"rep_td\" align=\"center\">&nbsp;".$TEL_NUMBER."</td>\n";
                $DATA  .= " <td class=\"rep_td\" align=\"center\">&nbsp;".$called."</td>\n";
                $DATA  .= "</tr>\n";
                $my_dur = $my_dur + $row_data->DURATION;
              }//Auth_Code alanı dolu mu end
            }//Rapor içeriği doldu end
            $DATA .= "
            <tr>
              <td colspan=7 height=3 BGCOLOR=#000000></td>
            </tr>
            <tr >
              <td class=\"rep_table_header\" width=\"30%\">Toplam</td>
              <td colspan=3 class=\"rep_table_header\" width=\"60%\"></td>
              <td class=\"rep_table_header\" width=\"10%\">".calculate_all_time($my_dur)."</td>
            </tr>";
            $DATA_HEAD = $DATA_HEADER;
            $DATA_HEADER = str_replace("<!-- DONEM -->", ($t0." - ".$t1) ,$DATA_HEADER);
            if($DATA !=""){
              $DATA = $DATA_HEADER.$DATA.$DATA_FOOTER;
              if($prnscreen==1){
                echo $DATA;
              }else{
                if(strlen($sendadres)){
    			    mail_send($sendadres,"Top 100  Süre Raporu -- $SITE_NAME",$DATA);
                }else{
    			    mail_send($SITE_ADMIN,"Top 100  Süre Raporu -- $SITE_NAME",$DATA);
    			    mail_send($ADMIN_EMAIL,"Top 100  Süre Raporu -- $SITE_NAME",$DATA);
                }
              }
              $DATA=""; //Data değişkenini boşalt
              $DATA_HEADER = $DATA_HEAD;
            }
		}
      }//Mail günü geldi mi end
    }//Mail atılacak mı end
  }//Site döngüsü kapandı.
 ?>
