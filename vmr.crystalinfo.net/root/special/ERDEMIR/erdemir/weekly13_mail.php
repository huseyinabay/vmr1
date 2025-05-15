<?
   require_once("doc_root.cnf");    
   require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/functions.php");
   require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/class.phpmailer.php");
   $cUtility = new Utility();
   $cdb = new db_layer(); 
   $conn = $cdb->getConnection();
   require_once(dirname($DOCUMENT_ROOT)."/root/crons/mail_send.php");

   
   $raporlanacak_gun_sayisi = 29;
   
   
   $SITE_ID = 1;
   if($force!=1){
     $mymonth = strftime("%m", mktime(0, 0, 0, date("m")-1 ,1, date("Y")));
     $myyear  = strftime("%Y", mktime(0, 0, 0, date("m")-1 ,1, date("Y")));;
   }
    setlocale(LC_TIME, 'tr_TR');    
    $last_year = $myyear-1;

    // maximum acceptable duration aliniyor...
    $max_acc_dur = get_site_prm('MAX_ACCE_DURATION',$SITE_ID) * 60;

    function get_dept_extnos($DPT_ID, $ST_ID){
      global $cdb;
      global $conn;
      $dept_qry = "SELECT EXT_NO FROM EXTENTIONS WHERE DEPT_ID=".$DPT_ID." AND SITE_ID=".$ST_ID;
      if (!($cdb->execute_sql($dept_qry,$rsltdpt,$error_msg))){
         print_error($error_msg);
         exit;
      }
      if(mysql_num_rows($rsltdpt)>0){
        while($rowDept = mysql_fetch_object($rsltdpt)){
          if($retVal == ""){
            $retVal = $rowDept->EXT_NO;
          }else{
            $retVal = $retVal.", ".$rowDept->EXT_NO;
          }
        }
        return "(".$retVal.")";
      }else{
        return "('-1')";
      }
    }
    
    function get_work_days($tmon, $tyear){
        $lastday = strftime ("%d", mktime(0,0,0,$tmon+1,0,$tyear));
        for($dd=1;$dd<=$lastday;$dd++){
          $week_day = strftime("%u", mktime(0,0,0,$tmon,$dd,$tyear));
           if($week_day>=1 && $week_day<=5){
             $job_day = $job_day+1;
           }
        }
        return $job_day;
    }
    if($mymonth<10){
      $CDR_MAIN_DATA = "CDR_MAIN_0".$mymonth."_".$myyear;
    }else{
      $CDR_MAIN_DATA = "CDR_MAIN_".$mymonth."_".$myyear;
     }
     if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";
/*       Rapor gönderilecek mail adreslerini çek       */    

     $sql_mail = "SELECT EMAIL AS TO_EMAIL, ACCOUNT_NO from ACCOUNT_EMAIL WHERE EMAIL <> '' LIMIT 360,3";
     if (!($cdb->execute_sql($sql_mail, $res_mail, $error_msg))){
       print_error($error_msg);
       exit;
    }
    while($rw_mail = mysql_fetch_object($res_mail)){
         $file_name = "outboudrep.txt";
 	 $file_name = "hesap_" . md5(uniqid(rand(), true)).".txt";
		  
        getAccountDetail($rw_mail->ACCOUNT_NO);
        mail_send_erdemir($rw_mail->TO_EMAIL,"*** ERDEMIR RESMI GORUSMELER TELEFON RAPORU  ***", "*** ERDEMIR RESMI GORUSMELER TELEFON RAPORU  ***",0,$file_name);
        //mail_send_erdemir("skaya@vodasoft.com.tr","*** ERDEMIR OZEL TELEFON RAPORU  ***", "*** ERDEMIR OZEL TELEFON RAPORU  ***",0,$file_name);
    }  


 $kriter2 = "";
    $check_origs = false;
    $usr_crt = "";
     
    if (right_get("SITE_ADMIN")){
        //Site admin hakký varsa herþeyi görebilir. 
        //Site id gelmemiþse kiþinin bulunduðu site raporu alýnýr.
        if(!$SITE_ID){$SITE_ID = $SESSION['site_id'];}
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
        // Admin vaye ALL_REPORT hakký varsa kendi sitesindeki herþeyi görebilir.
        $SITE_ID = $SESSION['site_id'];
    }elseif(got_dept_right($SESSION["user_id"])==1){
        //Bir departmanýn raporunu görebiliyorsa kendi sitesindekileri girebilir.
        $SITE_ID = $SESSION['site_id'];
        $dept_crt = get_depts_crt($SESSION["user_id"],$SESSION["site_id"]);
        $usr_crt  = get_users_crt($SESSION["user_id"],1,$SESSION["site_id"]);
        $alert = "Bu rapor sadece sizin yetkinizde olan departmanlara ait dahililerin bilgilerini içerir.";
    }else{
      if($AUTH_CODE_CNTL == 1){
        $usr_crt  = get_auth_crt($SESSION["user_id"]);
      }else{
        $usr_crt  = get_ext($SESSION["user_id"]);
      }
    }
    $usr_crt  ="";
###################################
# txt_write_line
# Date: 17.05.2002
# Coding: SEYKAY
###################################
function txt_write_line($data, $filename, $fp, $summ_rep=0) {
  if(is_array($data)){
    $k=0;
    foreach ($data as $k1 => $v1) {
      $mul  = 1;
      if($summ_rep){
        if($k=0){
          if(strlen($v1)<24){$mul=24-strlen($v1);}
          $file .= $v1.str_repeat(" ", $mul);
        }else{
          if($v1!=""){
          $file .= $v1."     ";}
        }
      }else{
        switch ($k){
          case 0:
            if(strlen($v1)<7){$mul=7-strlen($v1);}
            $file .= $v1.str_repeat(" ", $mul);
            break;  
          case 1:
            if(strlen($v1)<6){$mul=6-strlen($v1);}
            $file .= $v1.str_repeat(" ", $mul);
            break;  
          case 2:
            if(strlen($v1)<6){$mul=6-strlen($v1);}
            $file .= $v1.str_repeat(" ", $mul);
            break;  
          case 3:
	    if(strlen($v1)<19){$mul=19-strlen($v1);}
            $file .= str_repeat(" ", $mul).$v1;
            break;  
          case 4:
            if(strlen($v1)<17){$mul=19-strlen($v1);}
            $file .= str_repeat(" ", $mul) .$v1;
            break;  
          case 5:
            if(strlen($v1)<6){$mul=19-strlen($v1);}
            $file .= str_repeat(" ", $mul).$v1;
            break;  
          case 6:
            $file .= " ".$v1;
            break;  
          default:
            $file .= " ".$v1;
            break;  
        }
      }
      $k++;
    }
    $file = substr($file, 0, -1);
    $file .= "\r\n";
    fwrite($fp, $file);
    return true;
  }
}



function getAccountDetail($accountNo){
   global $SITE_ID,$cdb,$DOCUMENT_ROOT,$raporlanacak_gun_sayisi, $file_name;

    //Raporlanmak istenmeyen dahililer alýnýyor.
    $unrep_exts_crt = get_unrep_exts_crt($SITE_ID);
    //Hak Kontrolü Burada Bitiyor
    $max_outb_count = get_system_prm(MAX_OUTBOUND_COUNT);
	if ($max_outb_count=='0'){$max_outb_count=5000;}
  ob_start();
   
  cc_page_meta();
    echo "<center>";
  

    //Joinden kaçmak için Lokasyon tablosundaki bilgiler alýnýyor.
  $sql_str="SELECT Locationid,LocationName FROM TLocation"; 
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
    $arr_location = array();     
    while ($row=mysql_fetch_object($result)){
        $arr_location[$row->Locationid] = $row->LocationName;
    }
    //Joinden kaçmak için Çaðrý türündeki tablosundaki bilgiler alýnýyor.
    $sql_str="SELECT LocationTypeid, LocationType FROM TLocationType";
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
    $arr_location_type = array();
    while ($row=mysql_fetch_object($result)){
        $arr_location_type[$row->LocationTypeid] = $row->LocationType;
    }

    //Joinden kaçmak için Auth_Code tablosundaki bilgiler alýnýyor.
    $sql_str="SELECT AUTH_CODE,AUTH_CODE_DESC, ACCOUNT_NO FROM AUTH_CODES WHERE SITE_ID=".$SITE_ID; 
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
    $arr_auth_code = array();
    while ($row = mysql_fetch_object($result)){
        $arr_auth_code[$row->AUTH_CODE][0] = $row->AUTH_CODE_DESC;
        $arr_auth_code[$row->AUTH_CODE][1] = $row->ACCOUNT_NO;
    }
    
    $sql_str1="SELECT SITE_NAME, MAX_ACCE_DURATION FROM SITES WHERE SITE_ID = ".$SITE_ID; 
    if (!($cdb->execute_sql($sql_str1,$result1,$error_msg))){
        print_error($error_msg);
        exit;
    }
    if (mysql_num_rows($result1)>0){
        $row1 = mysql_fetch_object($result1);
        $company = $row1->SITE_NAME;
        $max_acc_dur =  ($row1->MAX_ACCE_DURATION)*60;
    }else{
        print_error("Site paramatreleri bulunamadý.");
        exit;
    }
    $local_country_code = get_country_code($SITE_ID);//Lokal ülke kodu.
	
   $report_type="Giden Çaðrý Raporu";
 
   $act = "src";
   if ($act == "src") {
     
      $kriter = "";

      //Temel kriterler. Verinini hýzlý gelmesi için baþa konuldu.
      $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalý.Ýlgili siteyi belirliyor.
      $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalý.Hatasýz kayýt olduðunu gösteriyor.        
      $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "1"); //Bu mutlaka olmalý.Dýþ arama olduðunu gösteriyor.
      $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalý.Dýþ arama olduðunu gösteriyor.
      $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ORIG_DN"     ,  " <> ",  " '' "); //Bu mutlaka olmalý.Hatasýz kayýt olduðunu gösteriyor.

      //**Bunlar birlikte olmalý ve bu sýrada olmalý.
      add_time_crt();//Zaman kriteri 
      if($forceMainTable)
        $CDR_MAIN_DATA = "CDR_MAIN_DATA"; 
      else
	     $CDR_MAIN_DATA = getTableName($t0,$t1);
      if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";
      //**
  	 

     //Genel Arama Bilgileri. Bu alanlar baþlýk basýlmasýnda kullanýlacaktýr.
    if ($ORIG_DN <> '') 
        $orig = 'Yes';
    if ((($DEPT_ID[0] == '-1') || ($DEPT_ID[0]=='')) && count($DEPT_ID)<=1){
        $dept = '';
    }else{
        $dept = 'Yes';
    }

    $provider_join="";//Ne olur ne olmaz bu alan burada boþaltýlsýn.
    if (($LocationTypeid <> '-1') && ($LocationTypeid<>'')) 
        $code_type='Yes';
    if ($LocalCode <> '')
        $local='Yes';
    if ($DIGITS <> '')
        $digits='Yes';
    if ($CountryCode <> '' && $CountryCode<>$local_country_code)
        $country='Yes';
    if ($AUTH_CODE <> '')
        $auth='Yes';
    if ($ACCESS_CODE <> '')
        $access='Yes';
    if (($TelProviderid <> '-1') && ($TelProviderid<>'')){
        $tel_provider='Yes';
        $provider_join =" LEFT JOIN TTelProvider ON CDR_MAIN_DATA.TO_PROVIDER_ID = TTelProvider.TelProviderid ";
    }
    if ($DURATION <> ''){
        $dur='Yes';
        $DURATION_SN = $DURATION*60;
     }




    $in_str = "";
    if(is_array($DEPT_ID)){
        if ((($DEPT_ID[0] == '-1') || ($DEPT_ID[0]=='')) && count($DEPT_ID)==1){
        //Nothing to do
        }else{
            for($i=0;$i < count($DEPT_ID);$i++){
                if($in_str != ""){
                    if($DEPT_ID[$i] != "-1" && $DEPT_ID[$i] != "")
                        $in_str .= ", ".$DEPT_ID[$i];
                }else{
                    if($DEPT_ID[$i] != "-1" && $DEPT_ID[$i] != "")
                        $in_str .= $DEPT_ID[$i];
                }
            }
        }
    }

        $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.LocationTypeid"   ,"=",        "$LocationTypeid");
        $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.TO_PROVIDER_ID"   ,"=",        "$TelProviderid");
        $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.LocalCode"        ,"=",        "'$LocalCode'");
        $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.CountryCode"      ,"=",        "'$CountryCode'");
        $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.PURE_NUMBER"      ,"LIKE",     "'%$DIGITS%'");
        $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.AUTH_ID"          ,"=",        "'$AUTH_CODE'");
        $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.ACCESS_CODE"      ,"=",        "'$ACCESS_CODE'");
        $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.PRICE"            ,">=",       "'$PRICE'");
        $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.DURATION"         ,">",        "'$DURATION_SN'");
        $kriter .= $cdb->field_query($kriter, " ACCOUNT_NO"                    ,"=",        "'$accountNo'");
        $kriter .= " AND ACCOUNT_NO<>''" ;
        $kriter .= " AND (TO_DAYS(NOW()) - TO_DAYS(CDR_MAIN_DATA.MY_DATE))<='$raporlanacak_gun_sayisi'" ;



        if($FROM_PROVIDER_ID != -1){
          $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.FROM_PROVIDER_ID"          ,"=",       "'$FROM_PROVIDER_ID'");
        }
        
        
//////////////////////Auth code control ////////////////////////////      
      if($AUTH_CODE_CNTL==1){
            $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.AUTH_ID"          ,"<>",       "' '");      
      }else if($AUTH_CODE_CNTL==2){
            $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.AUTH_ID"          ,"=",       "' '");
      }          

    $sql_loc="SELECT SITE_CODE,SITE_NAME, PRICE_FACTOR FROM SITES WHERE SITE_ID = $SITE_ID"; 
    if (!($cdb->execute_sql($sql_loc,$rslt_loc,$error_msg))){
        print_error($error_msg);
        exit;
    }
    $row_loc=mysql_fetch_object($rslt_loc);
    $LOC_CODE = $row_loc->SITE_CODE;
    $company  = $row_loc->SITE_NAME;
    $prc_fct  = $row_loc->PRICE_FACTOR;
    $fih_join_prm = " LEFT ";
    $fih_where_prm = "";
    //Fihrist kayýtlaý isteniyorsa Contacts ile INNER JOIN olmalý
    $fihrist_state = "";
    if($IN_FIHRIST && !$OUT_FIHRIST){
        $fih_join_prm = " INNER ";
        if($CONTACT_TYPE == '0' || $CONTACT_TYPE == '1'){
            $kriter .= $cdb->field_query($kriter, "CONTACTS.IS_GLOBAL"          ,"=",       "'$CONTACT_TYPE'");
            if($CONTACT_TYPE == '1'){$fihrist_state = "Þirket Kontaklarý";}
            else{$fihrist_state = "Özel Kontaklar";}
        } else{$fihrist_state = "Tüm Kayýtlý Olanlar";}  
    }
    if(!$IN_FIHRIST && $OUT_FIHRIST){
      $fih_where_prm = " AND PHONES.COUNTRY_CODE IS NULL";
      $fihrist_state = "Kayýtlý Olmayanlar";
    }
    //Fihirist ile ilgili birþey seçildiyse kriter gelmeli yoksa gerek yok.
    //Fihrist dýþý kayýtlar ise joinde karþýlýðý null gelecektir. Contacktlarda 
    //Country code mutlaka dolu olduðundan bu alan boþsa fihrist karþýlýðý yoktur.
   $write_to_csv = "";
    //Trunk raporlarý üzerinden gelmekte. Bir trunktan yapýlan aramalar.
   $sql_str  = "SELECT CONCAT(CONTACTS.NAME, ' ', CONTACTS.SURNAME) AS DESTINATION,   ACCOUNT_NO, EXT_NO, PRICE, DATE_FORMAT(CDR_MAIN_DATA.MY_DATE,\"%d/%m\") AS TXTMY_DATE,
                            DATE_FORMAT(TIME_STAMP,\"%H:%i\") AS TXTMY_TIME,CDR_MAIN_DATA.CountryCode, CDR_MAIN_DATA.LocalCode, CDR_MAIN_DATA.PURE_NUMBER, CDR_MAIN_DATA.COUNTER,1 AS TYPE, CDR_MAIN_DATA.DURATION, EXTENTIONS.DESCRIPTION  ";
   $sql_str1 = " FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
               LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
               $fih_join_prm JOIN PHONES ON
               (CDR_MAIN_DATA.CountryCode = PHONES.COUNTRY_CODE AND 
               CDR_MAIN_DATA.LocalCode = PHONES.CITY_CODE AND
               CDR_MAIN_DATA.PURE_NUMBER = PHONES.PHONE_NUMBER AND
               CDR_MAIN_DATA.SITE_ID = PHONES.SITE_ID)
               LEFT JOIN CONTACTS ON PHONES.CONTACT_ID = CONTACTS.CONTACT_ID ".$provider_join;
               
      if ($kriter != ""){
         $sql_str2 .= "  WHERE ".$kriter.$fih_where_prm;
         if ($dept_crt)
            $sql_str2 .= $dept_crt;
         if ($usr_crt)
            $sql_str2 .= $usr_crt;
         if ($unrep_exts_crt)
            $sql_str2 .= $unrep_exts_crt;
         if($SUMM == "trunk" && $TRUNK!=""){
            $sql_str2 .= " AND CDR_MAIN_DATA.TER_TRUNK_MEMBER='".$TRUNK."'";
         }
      }else{
         echo "Lütfen Kriter Seçiniz";
         exit;
      } 
      
      if ($record<>'' ||is_numeric($record)) {
         //$sql_str2 .= " LIMIT 0,". $record ;
      }

#####################################################
#AUTH   CODE LARA GÖRE RAPOR ALMAK ÝÇÝN
 
   $sql_str1_a = " FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
               LEFT JOIN AUTH_CODES ON CDR_MAIN_DATA.AUTH_ID = AUTH_CODES.AUTH_CODE AND CDR_MAIN_DATA.SITE_ID = AUTH_CODES.SITE_ID
               $fih_join_prm JOIN PHONES ON
               (CDR_MAIN_DATA.CountryCode = PHONES.COUNTRY_CODE AND 
               CDR_MAIN_DATA.LocalCode = PHONES.CITY_CODE AND
               CDR_MAIN_DATA.PURE_NUMBER = PHONES.PHONE_NUMBER AND
               CDR_MAIN_DATA.SITE_ID = PHONES.SITE_ID)
               LEFT JOIN CONTACTS ON PHONES.CONTACT_ID = CONTACTS.CONTACT_ID ".$provider_join;
             
   $sql_str_a  = "SELECT   CONCAT(CONTACTS.NAME, ' ', CONTACTS.SURNAME) AS DESTINATION, ACCOUNT_NO, AUTH_ID, PRICE, DATE_FORMAT(CDR_MAIN_DATA.MY_DATE,\"%d/%m\") AS TXTMY_DATE,
                            DATE_FORMAT(TIME_STAMP,\"%H:%i\") AS TXTMY_TIME,CDR_MAIN_DATA.CountryCode, CDR_MAIN_DATA.LocalCode, CDR_MAIN_DATA.PURE_NUMBER, CDR_MAIN_DATA.COUNTER,2 AS TYPE, CDR_MAIN_DATA.DURATION,AUTH_CODE_DESC AS DESCRIPTION ";
 //  $sql_str3 = " GROUP BY ACCOUNT_NO, AUTH_ID ";
      $sql = $sql_str.$sql_str1.$sql_str2. " AND AUTH_ID=''" . $sql_str3 . " UNION ". $sql_str_a.$sql_str1_a.$sql_str2. " AND AUTH_ID<>'' ORDER BY ACCOUNT_NO, TYPE,  EXT_NO, TXTMY_DATE, TXTMY_TIME" ;

      if ($record<>'' ||is_numeric($record)) {
         $sql .= " LIMIT 0,". $record ;
      }
      
      
      //echo $sql;
      
      if (!($cdb->execute_sql($sql ,$result,$error_msg))){
         print_error($error_msg);
         exit;
      }
#####################################################

      ///////////////////////////////////////////////////////////////77
      
         //Gelen kayýt adedi çok fazla ise bunlar csv ile kaydedilsin.
            $CSV_EXPORT = 1;

      ///////////////////////////////////////////////////////////////77
            $my_dur=0;
            $my_amount=0; 
            $my_pr=0; 
            $i;
            if (mysql_num_rows($result)>0)
                mysql_data_seek($result,0);
                $cnt=0;
                $numeric=0;
                switch ($order){
                    case 'auth':
                        $order1 ='AUTH_ID';
                        break;  
                    case 'Dahili':
                        $order1 ='ORIG_DN';
                        break;  
                    case 'tarih':
                        $order1 ='TIME_STAMP';
                        break;
                    case 'saat':
                        $order1 ='MY_TIME';
                        break;
                    case 'sure':
                        $order1 ='DURATION';
                        $numeric=1;
                        break;
                    case 'contact':
                        $order1 ='CONTACT_ID';
                        break;
                    case 'local':
                        $order1 ='LocalCode';
                        break;
                    case 'number':
                        $order1 ='PURE_NUMBER';
                        break;
                    case 'ucret':
                        $order1 ='PRICE';
                        $numeric=1;
                        break;
                    default:
                        $order1='MY_DATE';
                }

                

            
                if($CSV_EXPORT==1)
                    $fp = fopen(dirname($DOCUMENT_ROOT)."/root/temp/$file_name", "w+");
                if($CSV_EXPORT==1)
                  fwrite($fp, "");
                  unset($rowd) ;
                  unset($rowd1) ;
                  unset($DataArray) ;
                  
                 echo "<table border=0 width=400>\n";

  

                     if($CSV_EXPORT==1){
                        while($rowd = mysql_fetch_object($result)){                     
                           txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);
                           if($old_ext_no<>$rowd->EXT_NO && $old_ext_no!=""){ 
                              $csv_data[] = "---------------------------------";
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp); unset($csv_data);
                             
                              $csv_data[] = "Toplam";
                              $csv_data[] = "..........................:  $total";
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);
                              $total=0;
                           }
                          if($old_account_no<>$rowd->ACCOUNT_NO){ 
                              $csv_data[] = "\n\n*** ERDEMIR RESMI GORUSME TELEFON RAPORU  ***   	Hesap Kodu :".$rowd->ACCOUNT_NO;
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);

                          }
                          if($old_ext_no<>$rowd->EXT_NO){ 
                            $TYPE = $rowd->TYPE==2?" SIFRE ":"DAHILI NUMARA";
                              $csv_data[] = " ";
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);
                              $csv_data[] = $TYPE ;
                              $csv_data[] = $rowd->EXT_NO . "   -------->   " . $rowd->DESCRIPTION;
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);
                              $csv_data[] = "Tarih" ;
                              $csv_data[] = "Saat" ;
                              $csv_data[] = "Süre" ;
                              $csv_data[] = "Aranan No" ;
                              $csv_data[] = "    Ücret " ;
                              $csv_data[] = "    Aranan Yer " ;
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);
                              $csv_data[] = "====================================" ;
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);
                            }
                            $TEL_NUMBER = $rowd->CountryCode." ".$rowd->LocalCode." ".$rowd->PURE_NUMBER;

                              $csv_data[] = $rowd->TXTMY_DATE;
                              $csv_data[] = $rowd->TXTMY_TIME;
                              $csv_data[] = calculate_all_time($rowd->DURATION) ;
                              $TEL_NUMBER = $rowd->CountryCode." ".$rowd->LocalCode." ".$rowd->PURE_NUMBER;
                              $csv_data[] = $TEL_NUMBER;
                              $csv_data[] = $rowd->PRICE;
                              $csv_data[] = $rowd->DESTINATION;
			      
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);
                            
                       	    $total = $total + $rowd->PRICE;
                       	    $anatotal = $anatotal + $rowd->PRICE;
                            
                            $old_account_no=$rowd->ACCOUNT_NO;
                            $old_ext_no    =$rowd->EXT_NO;
                      }
                              $csv_data[] = "---------------------------------";
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);
                              $csv_data[] = "Toplam";
                              $csv_data[] = "..............................:  $total";
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);
                              $total=0;
                              $csv_data[] = "Ana Toplam";
                              $csv_data[] = "..............................:  $anatotal";
                              txt_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name", $fp);unset($csv_data);
      
                         $total=0;                        
                        
                     }else{
                        while($rowd = mysql_fetch_object($result)){
                           if($old_ext_no<>$rowd->EXT_NO && $old_ext_no!=""){ 
                     	       echo "<tr><td colspan=5>-------------------------------------------------------------------------------------------------------------------------------------</td></tr>";
                     	       echo "<tr><td>Toplam</td><td colspan=4>.......................................................................................:  $total</td></tr>";
                               $total=0;
                           }
                          if($old_account_no<>$rowd->ACCOUNT_NO){ 
                            echo "<tr>";
                            echo "<td colspan=5>*** Erdemir Resmi Gorusme Telefon Faturasý ***   	Hesap Kodu :".$rowd->ACCOUNT_NO."</td></tr><tr>\n";
                          }
                          if($old_ext_no<>$rowd->EXT_NO){ 
                            $TYPE = $rowd->TYPE==2?" Auth Kod ":"Dahili  Numara";
                            echo "<tr><td colspan=4 height=30></td> </tr>  \n";
                            echo "<tr><td colspan=1>". $TYPE ." </td><td  colspan=3>:". $rowd->EXT_NO . "   -------->   " . $rowd->DESCRIPTION."</td>   \n";
                     	    echo "</tr>\n";
                     	    echo "<tr><td>Tarih</td><td>Saat</td><td>Süre</td><td>Aranan No</td><td>Ücret</td></tr>";
                     	    echo "<tr><td colspan=5>==========================================================================</td></tr>";
                            }
                            $TEL_NUMBER = $rowd->CountryCode." ".$rowd->LocalCode." ".$rowd->PURE_NUMBER;
                            echo "<tr>\n";
                       	    echo "<td> ".$rowd->TXTMY_DATE."</td>\n";
                       	    echo "<td> ".$rowd->TXTMY_TIME."</td>\n";
                       	    echo "<td> ".calculate_all_time($rowd->DURATION)."</td>\n";
                       	    echo "<td> ".$TEL_NUMBER."</td>\n";
                       	    echo "<td> ".$rowd->PRICE."</td>\n";
                       	    echo "<td> ".$rowd->DESTINATION."</td>\n";
                            echo "</tr>\n";
                       	    $total = $total + $rowd->PRICE;
                       	    $anatotal = $anatotal + $rowd->PRICE;
                            
                            $old_account_no=$rowd->ACCOUNT_NO;
                            $old_ext_no    =$rowd->EXT_NO;
                      }
               	       echo "<tr><td colspan=5>-------------------------------------------------------------------------------------------------------------------------------------</td></tr>";
               	       echo "<tr><td>Toplam</td><td colspan=4>.........................................................................:  $total</td></tr>";
                         $total=0;
      
               	       echo "<tr><td colspan=5>-------------------------------------------------------------------------------------------------------------------------------------</td></tr>";
               	       echo "<tr><td>Ana Toplam</td><td colspan=4>.......................................................................................:  $anatotal</td></tr>";
                         $total=0;
               } 
}
	
}
?>
 <? if($CSV_EXPORT==1){?>
 <iframe SRC="/csv_download.php?filename=<?=$file_name?>" WIDTH=0 HEIGHT=0 ></iframe>
 <a HREF="/temp/<?=$file_name?>">TXT Download</a>
<?}?>


<?
  require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/functions.php");
  require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/class.phpmailer.php");

  function mail_send_erdemir($mail,$subject,$DATA,$add_img=1, $file_name=""){
     global $DOCUMENT_ROOT;
     $Mail = new phpmailer();
     $Mail->From       = SMTP_FROM;
     $Mail->FromName   = SMTP_FROMNAME;
     $Mail->Sender     = SMTP_PMASTER;
     $Mail->AddCustomHeader("Errors-To: <".SMTP_PMASTER.">");
     $Mail->CharSet    = SMTP_CHARSET;
     $Mail->IsHTML(SMTP_HTML);
     $Mail->ClearAttachments ();
     $Mail->AddAttachment(dirname($DOCUMENT_ROOT)."/root/temp/$file_name");

	 $Mail->ClearAllRecipients();
	 if ($DATA!=""){
        $Mail->AddAddress($mail);
        $Mail->Subject = $subject;
        $Mail->Body = $DATA;
        $Mail->IsSmtp();
        if (!$Mail->Send()){
            echo $Mail->ErrorInfo. "<br>". $mail ." Adresine Atamadým. \n";
        }else{
           echo $mail ." Adresine Atýldý. \n<BR>";
        }
        
     }
  }


?>