<?
  require_once("doc_root.cnf");
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  include("mail_send.php");
//   require_once(dirname($DOCUMENT_ROOT)."/root/crons/mail_send.php");
  $cUtility = new Utility();
  $cdb = new db_layer();
  require_valid_login();
  session_cache_limiter('nocache');
  $conn = $cdb->getConnection();
  cc_page_meta(0);
//  check_right("SITE_ADMIN");
  $cmp = get_system_prm("COMPANY_NAME");
  $field_dur = Array("LOC_DUR", "NAT_DUR", "GSM_DUR", "INT_DUR", "OTH_DUR", "INTERNAL_DUR", "INB_DUR");
  $field_amount = Array("LOC_AMOUNT", "NAT_AMOUNT", "GSM_AMOUNT", "INT_AMOUNT", "OTH_AMOUNT", "INTERNAL_AMOUNT", "INB_AMOUNT");
  $field_price = Array("LOC_PRICE", "NAT_PRICE", "GSM_PRICE", "INT_PRICE", "OTH_PRICE");

  $server_ip = get_system_prm("SERVER_IP");
  if (!right_get("SITE_ADMIN"))
     $SITE_ID = $_SESSION['site_id'];
  if (right_get("SITE_ADMIN") && ($SITE_ID=="" || $SITE_ID=="-1"))
     $SITE_ID = $_SESSION['site_id'];
  setlocale(LC_TIME, 'tr_TR');

//departmanın dahili numaralarını alarak bir kriter oluşturmak için kullanılır.
    function get_dept_extnos($DPT_ID, $ST_ID){
      global $cdb;
      global $conn;
      $dept_qry = "SELECT EXT_NO FROM EXTENTIONS WHERE DEPT_ID=".$DPT_ID." AND SITE_ID=".$ST_ID;
      if (!($cdb->execute_sql($dept_qry,$rsltdpt,$error_msg))){
         print_error($error_msg);
         exit;
      }
      if(mysqli_num_rows($rsltdpt)>0){
        while($rowDept = mysqli_fetch_object($rsltdpt)){
          if($retVal == ""){
            $retVal = "'".$rowDept->EXT_NO."'";
          }else{
            $retVal = $retVal.", '".$rowDept->EXT_NO."'";
          }
        }
        return "(".$retVal.")";
      }else{
        return "('-1')";
      }
    }


/*    if($act!="src" && $SITE_ID=="" && $DEPT_ID==""){
      echo "Hatalı Durum Oluştu!";
      exit;
    }*/
    
    $SQL_SITE = "SELECT SITE_ID, SITE_NAME, MAX_ACCE_DURATION, MONTHLY_MAILING_DEPT_DAY FROM SITES";
    if(!$cdb->execute_sql($SQL_SITE, $result1, $errmsg)){
      print_error($errmsg);
      exit;
	}
    
/*    $row_site = mysqli_fetch_object($result1);
    $max_acc_dur =  ($row_site->MAX_ACCE_DURATION)*60;
    $SQL_DEPT = "SELECT DEPT_ID, DEPT_NAME FROM DEPTS WHERE DEPT_ID=".$DEPT_ID;
    if(!$cdb->execute_sql($SQL_DEPT, $result2, $errmsg)){
      print_error($errmsg);
      exit;
    }
    $DEPT_CNT = 0;
    $row_dept = mysqli_fetch_object($result2);*/
    $mymonth = strftime("%m", mktime(0, 0, 0, date("m")-1 ,1, date("Y")));
    $myyear  = strftime("%Y", mktime(0, 0, 0, date("m")-1 ,1, date("Y")));;
    $today = date("d");
    $t0 = strftime("%Y-%m-%d %T", mktime(0,0,0,$mymonth,1,$myyear));
    $t1 = strftime("%Y-%m-%d %T", mktime(23,59,59,$mymonth+1,0,$myyear));

    $CDR_MAIN_DATA = getTableName($t0,$t1);
    if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";
    while($row_site = mysqli_fetch_object($result1)){
        if($row_site->MONTHLY_MAILING_DEPT_DAY != $today){continue;}
        $SITE_ID = $row_site->SITE_ID;
        $max_acc_dur =  ($row_site->MAX_ACCE_DURATION)*60;
        $SQL_DEPT = "SELECT DEPT_ID, DEPT_NAME, DEPT_RSP_EMAIL FROM DEPTS WHERE SITE_ID=".$SITE_ID;
        if(!$cdb->execute_sql($SQL_DEPT, $result2, $errmsg)){
          print_error($errmsg);
          exit;
    	}
        $DEPT_CNT = 0;
        while($row_dept = mysqli_fetch_object($result2)){
          //$DEPT_CNT = $DEPT_CNT+1;
          //if($DEPT_CNT==5){die;}
            if($row_dept->DEPT_RSP_EMAIL == ""){continue;}
            $mailaddr = $row_dept->DEPT_RSP_EMAIL;
            $DATA1="";
            $DATA1 .= "<center>
             <table border=0 width=70% height=20%>
               <tr>
                <td><a href=\"http://www.crystalinfo.net\" target=\"_blank\"><img border=0 SRC=\"http://".$server_ip.IMAGE_ROOT."logo2.gif\"></a></TD>
                 <td align=center class=header>".$row_site->SITE_ID." - ".$row_site->SITE_NAME."<br><br>Aylık Telefon Çağrı Profili
                 <br><br>".get_dept_name($row_dept->DEPT_ID, $SITE_ID)."<br><br>"
                 .strftime("%B", mktime(0,0,0,$mymonth,1,$myyear))."</td>";
            $DATA1 .= " <td align=right><img src=\"http://".$server_ip.IMAGE_ROOT."company.gif\"></td>
                    </tr>
                    <tr class='form'>";
            $DATA1 .= "   </td>
                     </tr>   
                    </table>
                    <table cellspacing=1 cellpadding=2 width= 70%>
                      <tr align=center>
                       <td width=70% height=22 colspan=4 class=\"header\"></td>
                      </tr>";

            $local_country_code = get_country_code($SITE_ID);//Sitenin ülke kodu
            $kriter = "";
            $DEPT_CRT = get_dept_extnos($row_dept->DEPT_ID, $SITE_ID); 
             //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
             $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
             $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
             $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "1"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
             $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
             $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.
             $kriter .= " AND ORIG_DN IN ".$DEPT_CRT." ";
             $MY_DATE = "e";
//            echo "<br>".$t0."<br>".$t1;
             $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.TIME_STAMP"     ,  ">=",  "'$t0'"); 
             $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.TIME_STAMP"     ,  "<=",  "'$t1'"); 
             
            $strsql = "SELECT CONCAT(CONCAT(IF(CountryCode <>'' AND CountryCode<>'".$local_country_code."',CONCAT('00',CountryCode),''),
                    IF(LocalCode <>'',CONCAT('0',LocalCode),'')),PURE_NUMBER)
                    AS PHONE_NUMBER, 
                    COUNT(CDR_ID) AS AMOUNT, SUM(DURATION) AS DURATION, SUM(PRICE) AS PRICE
                    FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA";
	        $strsql = $strsql." WHERE ".$kriter." 
            GROUP BY PHONE_NUMBER 
            ORDER BY AMOUNT DESC
            LIMIT 10";
            $kriter = "";
        
             //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
             $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
             $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
             $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "2"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
             $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
             $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.TER_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.
             $kriter .= " AND TER_DN IN ".$DEPT_CRT." ";
             $MY_DATE = "e";
             $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.TIME_STAMP"     ,  ">=",  "'$t0'"); 
             $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.TIME_STAMP"     ,  "<=",  "'$t1'"); 
             $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.CLID"     ,  "<>",  "''"); 
            $sql_inb = "SELECT REPLACE(CLID,'X','') AS PHONE_NUMBER, COUNT(CDR_ID) AS AMOUNT, 
                SUM(DURATION) AS DURATION, SUM(PRICE) AS PRICE 
                FROM CDR_MAIN_INB AS CDR_MAIN_DATA ";

	        $sql_inb = $sql_inb." WHERE ".$kriter." 
            GROUP BY PHONE_NUMBER  ORDER BY AMOUNT DESC
            LIMIT 10";
            
            /*echo $strsql."<br>";
            echo $sql_inb;exit;*/
            if(!$cdb->execute_sql($strsql, $result, $errmsg)){
               print_error($errmsg);
		       exit;
            }
            if(mysqli_num_rows($result)==0){
 //             continue;
            }

            
/*          GİDEN ÇAĞRILAR         */            
            $DATA1.= "<tr bgcolor='#FFCC00' height=20><td colspan=4 align=center><b>Giden Çağrılar</b></td> \n
                      </tr> \n";
            $DATA1 .= "<tr bgcolor=\"#88ACD5\" class='rep2_header' height='22' align=center> \n
		              <td width='30%'> Aranan Numara </td> \n
		              <td width='20%'> Adet </td> \n
		              <td width='20%'> Süre </td> \n
	                  <td width='20%'> Ücret </td> \n
              </tr> \n";
            $ii = 0;
	        while($row=mysqli_fetch_object($result)){
              $bgcolor = "#B3CAE3";
              if ($ii%2 == 0)
                $bgcolor = "#D8E4F1";
              $total = 0;
              $DATA1.= "<tr class='cigate_header1' bgcolor='$bgcolor' height=18> \n";
              $DATA1.="<td align='left'> ".$row->PHONE_NUMBER." </td> \n";
              $DATA1.="<td align='right'> ".$row->AMOUNT." </td> \n";
              $DATA1.="<td align='right'> ".calculate_all_time($row->DURATION)." </td> \n";
              $DATA1.="<td align='right'> ".write_price($row->PRICE)." </td> \n";
              $DATA1.="</tr> \n";
              $ii++;
            }
            $DATA1.= "<tr bgcolor='#FFFFFF' height=20><td colspan=9 align=center><b></b></td> \n
                      </tr> \n";

/*          GELEN ÇAĞRILAR         */            
            if(!$cdb->execute_sql($sql_inb, $resulti, $errmsg)){
               print_error($errmsg);
		       exit;
            }
            $DATA1.= "<tr bgcolor='#FFCC00' height=20><td colspan=4 align=center><b>Gelen Çağrılar</b></td> \n
                      </tr> \n";
            $DATA1 .= "<tr bgcolor=\"#88ACD5\" class='rep2_header' height='22' align=center> \n
		              <td width='30%'> Arayan Numara </td> \n
		              <td width='20%'> Adet </td> \n
		              <td width='20%'> Süre </td> \n
	                  <td width='20%'> Ücret </td> \n
              </tr> \n";
            $ii = 0;
	        while($row1=mysqli_fetch_object($resulti)){                 
              $bgcolor = "#B3CAE3";
              if ($ii%2 == 0)
                $bgcolor = "#D8E4F1";
              $total = 0;
              $DATA1.= "<tr class='cigate_header1' bgcolor='$bgcolor' height=18> \n";
              $DATA1.="<td align='left'> ".$row1->PHONE_NUMBER." </td> \n";
              $DATA1.="<td align='right'> ".$row1->AMOUNT." </td> \n";
              $DATA1.="<td align='right'> ".calculate_all_time($row1->DURATION)." </td> \n";
              $DATA1.="<td align='right'> ".write_price($row1->PRICE)." </td> \n";
              $DATA1.="</tr> \n";
              $ii++;
            }
            $DATA1.= "<tr bgcolor='#FFFFFF' height=20><td colspan=9 align=center><b></b></td> \n
                      </tr> \n";

            $DATA1 .= "</table> \n
                   <tr><td hright='15'></td></tr> \n
                  </table><br><br> \n
                  <script language='javascript'> \n
                     function submit_me(){ \n
                        document.acc_code.action = 'report_acc_code.php?site_id=' + document.all('SITE_ID').value; \n
                        document.acc_code.submit(); \n
                     } \n 
                 </script> \n
                  </center> \n
                 ";

            //echo $mailaddr."<br>".$DATA1; //die;
             mail_send($mailaddr,"AYLIK TOP 10 ÇAĞRILAR",$DATA1);
            
        }  
    }    
?>
