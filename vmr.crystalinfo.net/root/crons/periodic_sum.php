<?php
  //require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/mail/PHPMailerAutoload.php');
  $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
  if(!$conn) exit;
  
  
 error_reporting(0);
  /*
       ini_set('display_errors', 'On');
	   error_reporting(E_ALL);
  
  */
  /////////////////////////////////////////////////////////////////

  function write_price($price){
    $price = number_format($price,2, ',', '.');
    return $price;
  }
  
  function get_site_prm($MyVal,$site_id){
      global $conn;
    $sql_str="SELECT $MyVal FROM SITES WHERE SITE_ID= '$site_id'";
    $result = mysqli_query($conn,$sql_str);
    $row=mysqli_fetch_object($result);
        return($row->$MyVal);
}

  function get_site_name($site_id){  
      global $conn;
	  $sql_str_0="SELECT SITE_NAME FROM SITES WHERE SITE_ID = '$site_id'";
	  $result_0 = mysqli_query($conn,$sql_str_0);
      $row_0 = mysqli_fetch_object($result_0);
      return ($row_0->SITE_NAME);
 }   
  
  
  function periodic_summary($SITE_ID,$type){
    global $conn, $HTTP_HOST;
    $company = get_site_prm('SITE_NAME',$SITE_ID);
	$max_acc_dur = (get_site_prm('MAX_ACCE_DURATION',$SITE_ID))*60;
	
    //Joinden kaçmak için Çağrı türündeki tablosundaki bilgiler alınıyor.
    $sql_str_1="SELECT LocationTypeid, LocationType FROM TLocationType";
    $result_1 = mysqli_query($conn,$sql_str_1);
    $arr_location_type = array();
    while ($row=mysqli_fetch_object($result_1)){
        $arr_location_type[$row->LocationTypeid] = $row->LocationType;
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


function day_of_last_week($val){
  if (($val > 0  || $val < 8)  && is_numeric($val)){
    $last_week_sec = mktime(0,0,0,date("m"),date("d")-7,date("Y"));
    $week_day = date('w');
    if ($week_day == 0)//Php'de hafta Pazar ile başlar ve değeri 0'dır. Pazartesi'den
      $week_day = 7;// başlatmak için bu ayar yapılmalı.
    $offset = ($week_day-1) * 86400 ;//Haftanın ilk gününü bulmak için (Pazartesi) bu düzenleme gerekti.86400 bir günün sn'yesi.
    $first_day = $last_week_sec - $offset;
    $nth_day_UNC =$first_day + ($val-1)*86400;
    $nth_day = date("Y-m-d", $nth_day_UNC);
    return $nth_day;  
  }else
    echo "Yanlış Paramatre Girişi";
}

//$type="day";  //  test icin  

    if ($type=='day'){
      $report_name = "Günlük Görüşme Özet Raporu";
      $t0 = strftime("%Y-%m-%d", mktime(0,0,0,date("m"),date("d")-1,date("y")));
      $kriter_date = "AND MY_DATE = '".$t0."'";
    }elseif($type=='week'){
      $report_name = "Haftalık Görüşme Özet Raporu";
      $t0 = day_of_last_week(1);
      $t1 = day_of_last_week(7);
      $kriter_date = "AND MY_DATE >= '".$t0."'";
      $kriter_date .= " AND MY_DATE <= '".$t1."'";
    }elseif($type=='month'){
      $report_name = "Aylık Görüşme Özet Raporu";
      $t0 = day_of_last_month("first");
      $t1 = day_of_last_month("last");
      $kriter_date = "AND MY_DATE >= '".$t0."'";
      $kriter_date .= " AND MY_DATE <= '".$t1."'";
    }else{
      return 0;
      exit;
    }
    //////////////////////LAST RECORD////////////////////////////// 

    $sql_str_2 = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS AMOUNT, CDR_MAIN_DATA.LocationTypeid, 
                  SUM(DURATION) AS DURATION, SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                FROM CDR_MAIN_DATA
					WHERE CALL_TYPE = 1 
						AND ERR_CODE = 0	  
						AND DURATION<'".$max_acc_dur."' 
						AND CDR_MAIN_DATA.SITE_ID = '".$SITE_ID."'
						".$kriter_date."		
						AND CDR_MAIN_DATA.ORIG_DN <> ''
						GROUP BY CDR_MAIN_DATA.LocationTypeid";
    

     $result_2 = mysqli_query($conn,$sql_str_2);  
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
            <td><a href=\"http://www.crystalinfo.net\" target=\"_blank\"><img border=0 SRC=\"cid:my-crystal\" ></a></td>
            <TD align=center CLASS=\"header\">".get_site_name($SITE_ID)."<BR>".$report_name."</TD>
            <TD align=right><img SRC=\"cid:my-attach\"></TD>
          </TR>
        </TABLE>
        </td>
      </tr>
      <tr>
        <td width=\"100%\" class=\"rep_header\" align=\"right\">Tarih ($date_show)</td>
	  </tr>
      <tr>
        <td colspan=\"2\">
        <table width=\"100%\" border=\"0\" bgcolor=\"#C7C7C7\" cellspacing=\"1\" cellpadding=\"0\">
          <tr>
            <td class=\"rep_table_header\" width=\"25%\">Arama Tipi</td>
            <td class=\"rep_table_header\" width=\"20%\">Adet</td>
            <td class=\"rep_table_header\" width=\"20%\">Süre</td>
            <td class=\"rep_table_header\" width=\"20%\">Tutar(TL)</td>
          </tr>
          <tr>
           
          </tr>";
          //$data_cntl = -1;
          while($row = mysqli_fetch_object($result_2)){
            $i++;
            $bgcolor = $i%2==1 ? "FFFFFF" : "E4E4E4";
            $DATA .="<tr  BGCOLOR=$bgcolor> 
                       <td class=\"rep_td\"><b>".$arr_location_type[$row->LocationTypeid]."</b></td> 
                       <td class=\"rep_td\">".number_format($row->AMOUNT,0,"",".")."</td> 
                       <td class=\"rep_td\">".calculate_all_time($row->DURATION)."</td> 
                       <td class=\"rep_td\" ALIGN=right>" .write_price($row->PRICE)."</td>
                     </tr>";
            $t_cnt += $row->AMOUNT;
            $t_dur += $row->DURATION;
            $t_pri += $row->PRICE;
            //$data_cntl = 1;
          } 
          $t_cnt = number_format($t_cnt,0,"",".");
          $t_pri = write_price($t_pri);
          echo $DATA .= "
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
          <TR>
            <TD WIDTH=\"80%\" ALIGN=\"right\"><b>Toplam Tutar :</b></TD>
            <TD WIDTH=\"20%\">".$t_pri."</TD>
          </TR>
        </TABLE>
        </td>
      </tr>
    </table>  
  ";  //if($data_cntl==1)
  return $DATA;
  }
?>
