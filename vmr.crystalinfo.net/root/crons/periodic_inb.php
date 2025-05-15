<?php
 require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/mail/PHPMailerAutoload.php');
  $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
  if(!$conn) exit;
  /////////////////////////////////////////////////////////////////
  
  function periodic_inb($SITE_ID,$ORIG_DN,$type){
    global $cdb, $conn, $HTTP_HOST;
    $company = get_site_prm('SITE_NAME',$SITE_ID);

    $kriter = "";   
    //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
    $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_INB.SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
    $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
    $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "2"); //Bu mutlaka olmalı.Gelen arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "TER_DN"     ,  "=",  "$ORIG_DN"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    //$kriter .= $cdb->field_query($kriter,   "CDR_MAIN_INB.ORIG_DN"     ,  "=",  "2341"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.
    
	
	
    if ($type=='day'){
      $report_name = "Günlük Gelen Çağrı Raporu";
      //$t0 = strftime("%Y-%m-%d", mktime(0,0,0,date("m"),date("d")-1,date("y")));   //DUNKU
	  $t0 = strftime("%Y-%m-%d", mktime(0,0,0,date("m"),date("d"),date("y")));    //BU GUNKU
      $kriter .= $cdb->field_query($kriter, "MY_DATE"     ,"=",  "'$t0'");
    }

	else{
      return 0;
      exit;
    }
	//echo $kriter;
	
    //////////////////////LAST RECORD////////////////////////////// 
    $sql_str = "SELECT CDR_ID, ORIG_DN, TER_DN, DATE_FORMAT(TIME_STAMP,\"%d.%m.%Y\") AS MY_DATE, 
				DATE_FORMAT(TIME_STAMP,\"%H:%i:%s\") AS MY_TIME, DURATION, CLID
                FROM CDR_MAIN_INB 
               ";
    if ($kriter != "")
      $sql_str .= " WHERE ".$kriter;

    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
      print_error($error_msg);
      exit;
    }
    if($t0!="")
      $date_show = date("d/m/Y",strtotime($t0));
    if($t1!="")
      $date_show .= " - ".date("d/m/Y",strtotime($t1));
  

  
    $DATA ="
    <html>
    <head>
    <title>Crystal Info</title>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1254\">
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-9\">
    </head>
    <style>
      body {font-family:Verdana, Arial, Helvetica, sans-serif;     font-size: 8pt;     font-weight: normal;     font-variant: normal;     text-transform: none;     color: #000000;     text-decoration: none}
      .homebox {font-family: Verdana, Arial, Helvetica, sans-serif;         font-size: 7pt;         font-weight: bold;         font-variant: normal;         text-transform: none;         color: FF6600;         text-decoration: none}
      .header {font-family: Verdana,Ariel,Helvatica, san-serif;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #2C5783;     text-decoration: none}
      .header_beyaz2 {font-family: Verdana,Ariel,Helvatica, san-serif;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: FFFFFF;        background-color:  508AC5;    text-decoration: none}
      .header_sm {font-family: Verdana,Ariel,Helvatica, san-serif;     font-size: 7pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #2C5783;     text-decoration: none}
      a.a1 {font-family: Verdana,Ariel,Helvatica, san-serif;     font-size: 8pt;     font-weight: normal;     font-variant: normal;     text-transform: none;     color: #1B4E81;     text-decoration: none}
      a.a1:hover {font-family: Verdana,Ariel,Helvatica, san-serif;     font-size: 8pt;     font-weight: normal;     font-variant: normal;     text-transform: none;     color: #000000;     text-decoration: none}
      a {font-family: Geneva, Arial, Helvetica, san-serif;     font-size: 8pt;     font-weight: bold;     font-variant: normal;     text-transform: none;     color: #FF6600;     text-decoration: none}
      .text {  font-family: Verdana, Arial, Helvetica, sans-serif;  font-size: 8pt;  font-weight: normal;  font-variant: normal;  text-transform: none;  color: #1b4e81 ;  text-decoration: none}
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
    <body bgcolor=\"#FFFFFF\" text=\"#000000\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
    <table width=\"85%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
      <tr>
        <td colspan=\"2\" width=\"100%\" align=\"center\" class=\"rep_header\" align=\"center\">
        <TABLE BORDER=\"0\" WIDTH=\"100%\">
          <TR>

            <TD align=center CLASS=\"header\">".get_site_name($SITE_ID)."<BR>".$report_name."</TD>

          </TR>
        </TABLE>
        </td>
      </tr>
      <tr>
	    <td class=\"rep_td\" align=\"left\"><b>".$ORIG_DN."</b>&nbsp;Numaralı&nbsp;Dahiliye&nbsp;Gelen&nbsp;Çağrı&nbsp;Detayı</td>&nbsp;&nbsp;&nbsp;&nbsp; 
        <td width=\"100%\" class=\"rep_header\" align=\"right\">Tarih ($date_show)</td>
	  </tr>
      <tr>
        <td colspan=\"2\">
        <table width=\"100%\" border=\"0\" bgcolor=\"#C7C7C7\" cellspacing=\"1\" cellpadding=\"0\">
          <tr>
            <td class=\"rep_table_header\" width=\"20%\">Arayan No</td>
			<td class=\"rep_table_header\" width=\"20%\">Tarih</td>
			<td class=\"rep_table_header\" width=\"20%\">Saat</td>
            <td class=\"rep_table_header\" width=\"20%\">Süre</td>
          </tr>
          <tr>
            <td colspan=\"4\" bgcolor=\"#000000\" height=\"1\"></td>
          </tr>";
          //$data_cntl = -1;
          while($row = mysqli_fetch_object($result)){
            $i++;
            $bgcolor = $i%2==1 ? "FFFFFF" : "E4E4E4";
            $DATA .="<tr  BGCOLOR=$bgcolor> 
                       <td class=\"rep_td\">".$row->CLID."</td> 
                       <td class=\"rep_td\">".$row->MY_DATE."</td> 
                       <td class=\"rep_td\">".$row->MY_TIME."</td> 
					   <td class=\"rep_td\">".calculate_all_time($row->DURATION)."</td> 
                     </tr>";

            $t_cnt += count($row->CDR_ID);
            $t_dur += $row->DURATION;

          } 

            $t_pri = write_price($t_pri);
          $DATA .= "
          </table>
          </td>  
        </tr>  
        <tr height=\"20\">
        <td></td>
      </tr>
      <tr>
        <td height=\"22\" colspan=\"3\" width=\"100%\" align=\"right\">
        <TABLE BORDER=\"0\" WIDTH=\"100%\">
          <TR>
            <TD WIDTH=\"80%\" ALIGN=\"right\"><b>Toplam Görüşme Adedi :</b></TD>
            <TD WIDTH=\"20%\">".$t_cnt."</TD>
          </TR>
        </TABLE>
        </td>
      </tr>
      <tr>
	    <td height=\"22\" colspan=\"3\" width=\"100%\" align=\"right\">
        <TABLE BORDER=\"0\" WIDTH=\"100%\">
          <TR>
            <TD WIDTH=\"80%\" ALIGN=\"right\"><b>Toplam Süre :</b></TD>
            <TD WIDTH=\"20%\" >".calculate_all_time($t_dur)."</TD>
          </TR>
        </TABLE>
        </td>
      </tr>
      <tr>
        <td height=\"22\" colspan=\"3\" width=\"100%\" align=\"right\">
        <TABLE BORDER=\"0\" WIDTH=\"100%\">

        </TABLE>
        </td>
      </tr>
    </table>  
  ";  //if($data_cntl==1)
  //echo $DATA;  //ekrana basar
  return $DATA; // mail atar
  }
?>
