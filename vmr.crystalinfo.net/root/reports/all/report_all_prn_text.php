<?  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
    $cUtility = new Utility();
    $cdb = new db_layer(); 
    $conn = $cdb->getConnection();
    require_valid_login();
    //Kullanıcılar için hak kontrolü olmalı 
    //echo $DEPT_ID[0];echo "/".$DEPT_ID[1];echo "/".$DEPT_ID[2]."---";ECHO COUNT($DEPT_ID)."";
    $kriter2 = "";
    $check_origs = false;
    $usr_crt = "";
     
    if (right_get("SITE_ADMIN")){
        //Site admin hakkı varsa herşeyi görebilir. 
        //Site id gelmemişse kişinin bulunduğu site raporu alınır.
        if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
        // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
        $SITE_ID = $_SESSION['site_id'];
    }elseif(got_dept_right($_SESSION["user_id"])==1){
        //Bir departmanın raporunu görebiliyorsa kendi sitesindekileri girebilir.
        $SITE_ID = $_SESSION['site_id'];
        $dept_crt = get_depts_crt($_SESSION["user_id"],$_SESSION["site_id"]);
        $usr_crt  = get_users_crt($_SESSION["user_id"],1,$_SESSION["site_id"]);
        $alert = "Bu rapor sadece sizin yetkinizde olan departmanlara ait dahililerin bilgilerini içerir.";
    }else{
      if($AUTH_CODE_CNTL == 1){
        $usr_crt  = get_auth_crt($_SESSION["user_id"]);
      }else{
        $usr_crt  = get_ext($_SESSION["user_id"]);
      }
    }
  
    //Raporlanmak istenmeyen dahililer alınıyor.
    $unrep_exts_crt = get_unrep_exts_crt($SITE_ID);
    //Hak Kontrolü Burada Bitiyor
    $max_outb_count = get_system_prm(MAX_OUTBOUND_COUNT);
	if ($max_outb_count=='0'){$max_outb_count=5000;}
  ob_start();
   
    //Joinden kaçmak için Lokasyon tablosundaki bilgiler alınıyor.
  $sql_str="SELECT Locationid,LocationName FROM TLocation"; 
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
    $arr_location = array();     
    while ($row=mysqli_fetch_object($result)){
        $arr_location[$row->Locationid] = $row->LocationName;
    }
    //Joinden kaçmak için Çağrı türündeki tablosundaki bilgiler alınıyor.
    $sql_str="SELECT LocationTypeid, LocationType FROM TLocationType";
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
    $arr_location_type = array();
    while ($row=mysqli_fetch_object($result)){
        $arr_location_type[$row->LocationTypeid] = $row->LocationType;
    }

    //Joinden kaçmak için Auth_Code tablosundaki bilgiler alınıyor.
    $sql_str="SELECT AUTH_CODE,AUTH_CODE_DESC FROM AUTH_CODES WHERE SITE_ID=".$SITE_ID; 
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
    $arr_auth_code = array();
    while ($row = mysqli_fetch_object($result)){
        $arr_auth_code[$row->AUTH_CODE] = $row->AUTH_CODE_DESC;
    }
    
    $sql_str1="SELECT SITE_NAME, MAX_ACCE_DURATION FROM SITES WHERE SITE_ID = ".$SITE_ID; 
    if (!($cdb->execute_sql($sql_str1,$result1,$error_msg))){
        print_error($error_msg);
        exit;
    }
    if (mysqli_num_rows($result1)>0){
        $row1 = mysqli_fetch_object($result1);
        $company = $row1->SITE_NAME;
        $max_acc_dur =  ($row1->MAX_ACCE_DURATION)*60;
    }else{
        print_error("Site paramatreleri bulunamadı.");
        exit;
    }
    $local_country_code = get_country_code($SITE_ID);//Lokal ülke kodu.
	
   $report_type="Giden Çağrı Raporu";

   if ($act == "src") {
     
      $kriter = "";

      //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
      $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
      $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.        
      $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "1"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
      $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
      $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.

     
      //**Bunlar birlikte olmalı ve bu sırada olmalı.
      add_time_crt();//Zaman kriteri 
      if($forceMainTable)
        $CDR_MAIN_DATA = "CDR_MAIN_DATA"; 
      else
	     $CDR_MAIN_DATA = getTableName($t0,$t1);
      if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";
      //**
  	 
	 //Genel raporlardan dahili çağrıların detayları için gelindiğinde dahili kısmı boş olanlar için.
     if ($ORIG_DN == "-2"){
        if ($kriter == ""){
            $kriter = " CDR_MAIN_DATA.ORIG_DN = ''" ;
        }else if ($kriter <> ""){
            $kriter = $kriter. " AND CDR_MAIN_DATA.ORIG_DN = ''";
            $ORIG_DN = "";
        }
    }

     //Genel Arama Bilgileri. Bu alanlar başlık basılmasında kullanılacaktır.
    if ($ORIG_DN <> '') 
        $orig = 'Yes';
    if ((($DEPT_ID[0] == '-1') || ($DEPT_ID[0]=='')) && count($DEPT_ID)<=1){
        $dept = '';
    }else{
        $dept = 'Yes';
    }

    $provider_join="";//Ne olur ne olmaz bu alan burada boşaltılsın.
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

     if($ORIG_DN){
        $ORIG_ARRAY = explode(",", $ORIG_DN);
        $in_str = "";
        for($i=0;$i<count($ORIG_ARRAY);$i++){
            if($in_str == ""){
                if(($ORIG_ARRAY[$i])!=""){
                     $in_str .= "'".$ORIG_ARRAY[$i]."'";
                }    
            }else{
                if(($ORIG_ARRAY[$i])!=""){
                    $in_str .= ", '".$ORIG_ARRAY[$i]."'";
                }
            }
        }
    }

    if($in_str != ""){
        if($kriter == ""){
            $kriter .= " CDR_MAIN_DATA.ORIG_DN IN (".$in_str.")";
        }else{
            $kriter .= " AND CDR_MAIN_DATA.ORIG_DN IN (".$in_str.")";
        }
    }else{
        $orig = '';
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

    if($in_str != ""){
        if($kriter == ""){
            $kriter .= " EXTENTIONS.DEPT_ID IN (".$in_str.")";
        }else{
            $kriter .= " AND  EXTENTIONS.DEPT_ID IN (".$in_str.")";
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
/*        if($MEMBER_NO != -1){
          $kriter .= $cdb->field_query($kriter, "CDR_MAIN_DATA.TER_TRUNK_MEMBER"          ,"=",       "'$MEMBER_NO'");
        }*/
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
    $row_loc=mysqli_fetch_object($rslt_loc);
    $LOC_CODE = $row_loc->SITE_CODE;
    $company  = $row_loc->SITE_NAME;
    $prc_fct  = $row_loc->PRICE_FACTOR;
    $fih_join_prm = " LEFT ";
    $fih_where_prm = "";
    //Fihrist kayıtlaı isteniyorsa Contacts ile INNER JOIN olmalı
    $fihrist_state = "";
    if($IN_FIHRIST && !$OUT_FIHRIST){
        $fih_join_prm = " INNER ";
        if($CONTACT_TYPE == '0' || $CONTACT_TYPE == '1'){
            $kriter .= $cdb->field_query($kriter, "CONTACTS.IS_GLOBAL"          ,"=",       "'$CONTACT_TYPE'");
            if($CONTACT_TYPE == '1'){$fihrist_state = "Şirket Kontakları";}
            else{$fihrist_state = "Özel Kontaklar";}
        } else{$fihrist_state = "Tüm Kayıtlı Olanlar";}  
    }
    if(!$IN_FIHRIST && $OUT_FIHRIST){
      $fih_where_prm = " AND PHONES.COUNTRY_CODE IS NULL";
      $fihrist_state = "Kayıtlı Olmayanlar";
    }
    //Fihirist ile ilgili birşey seçildiyse kriter gelmeli yoksa gerek yok.
    //Fihrist dışı kayıtlar ise joinde karşılığı null gelecektir. Contacktlarda 
    //Country code mutlaka dolu olduğundan bu alan boşsa fihrist karşılığı yoktur.
   $write_to_csv = "";
    //Trunk raporları üzerinden gelmekte. Bir trunktan yapılan aramalar.
    if($SUMM == "trunk" && $TRUNK!=""){
         $sql_str = "   SELECT CDR_MAIN_DATA.CDR_ID,LTRIM(CDR_MAIN_DATA.ORIG_DN) AS ORIG_DN,
                        DATE_FORMAT(MY_DATE,\"%d.%m.%Y\") AS MY_DATE, DATE_FORMAT(TIME_STAMP,\"%H:%i:%s\") AS MY_TIME, 
                        CDR_MAIN_DATA.DURATION, CDR_MAIN_DATA.LocationTypeid, EXTENTIONS.DESCRIPTION, 
                        CDR_MAIN_DATA.LocalCode, CDR_MAIN_DATA.CountryCode, CDR_MAIN_DATA.PURE_NUMBER, CDR_MAIN_DATA.Locationid,
                        (CDR_MAIN_DATA.PRICE*$prc_fct) AS PRICE,
                        CDR_MAIN_DATA.TER_TRUNK_MEMBER AS TRUNK, CDR_MAIN_DATA.TER_TRUNK_MEMBER,
                        CDR_MAIN_DATA.AUTH_ID, TIME_STAMP
                   ";
                   
              $sql_str1 = " FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                        LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
                        LEFT JOIN PHONES ON (CDR_MAIN_DATA.CountryCode = PHONES.COUNTRY_CODE AND 
                                            TRIM(CDR_MAIN_DATA.LocalCode) = TRIM(PHONES.CITY_CODE) AND 
                                            CDR_MAIN_DATA.PURE_NUMBER = PHONES.PHONE_NUMBER AND 
                                            CDR_MAIN_DATA.SITE_ID = PHONES.SITE_ID)
                      LEFT JOIN CONTACTS ON PHONES.CONTACT_ID = CONTACTS.CONTACT_ID ".$provider_join;
         }else{
         $sql_str  = "SELECT CDR_MAIN_DATA.CDR_ID,LTRIM(CDR_MAIN_DATA.ORIG_DN) AS ORIG_DN,DATE_FORMAT(CDR_MAIN_DATA.MY_DATE,\"%d/%m/%Y\") AS MY_DATE,
                            DATE_FORMAT(TIME_STAMP,\"%H:%i:%s\") AS MY_TIME,CDR_MAIN_DATA.LocationTypeid,
                            CDR_MAIN_DATA.DURATION, EXTENTIONS.DEPT_ID,CDR_MAIN_DATA.Locationid,
                            CDR_MAIN_DATA.CountryCode, CDR_MAIN_DATA.LocalCode, CDR_MAIN_DATA.PURE_NUMBER, 
                            CDR_MAIN_DATA.AUTH_ID, EXTENTIONS.DESCRIPTION, (CDR_MAIN_DATA.PRICE*$prc_fct) AS PRICE,
                            PHONES.CONTACT_ID, PHONES.DESCRIPTION AS PHONE_DESC, CONTACTS.NAME, CONTACTS.SURNAME, TIME_STAMP
              ";
                        $sql_str1 = " FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                            LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
                            $fih_join_prm JOIN PHONES ON
                                (CDR_MAIN_DATA.CountryCode = PHONES.COUNTRY_CODE AND 
                                CDR_MAIN_DATA.LocalCode = PHONES.CITY_CODE AND
                                CDR_MAIN_DATA.PURE_NUMBER = PHONES.PHONE_NUMBER AND
                                CDR_MAIN_DATA.SITE_ID = PHONES.SITE_ID)
                            LEFT JOIN CONTACTS ON PHONES.CONTACT_ID = CONTACTS.CONTACT_ID ".$provider_join;
         }
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
               $sql_str2 .= " LIMIT 0,". $record ;
     }
//echo $sql_str.$sql_str1.$sql_str2;
        if (!($cdb->execute_sql($sql_str.$sql_str1.$sql_str2,$result,$error_msg))){
           print_error($error_msg);
           exit;
    }


      ///////////////////////////////////////////////////////////////77
            $my_dur=0;
            $my_amount=0; 
            $my_pr=0; 
            $i;
            if (mysqli_num_rows($result)>0)
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

                function cmp ($a, $b) {
                    global $order1,$numeric;
                    if($numeric){
                        if ($a[$order1] < $b[$order1]) 
                            return  1;
                        else 
                            return -1;
                       }    
                       return strcmp($a[$order1], $b[$order1]);
                }

                function cmp_desc ($a, $b) {
                    global $order1,$numeric;
                    if($numeric){
                        if ($a[$order1] > $b[$order1]) 
                            return  1;
                        else 
                            return -1;
                       }    
                       return strcmp($b[$order1], $a[$order1]);
                }

                $file_name = "outboudrep.csv";
                    $m=0;
                    echo " ;".$company."; ;Giden;Çagrı;Raporu;\n";
                    $m++;
                    echo "Dahili;Tarih;Saat;Süre;Telefon;Aranan;Ücret<BR>\n";


                while($row_array[$cnt] = mysqli_fetch_array($result)){$cnt++;}
                
                    if($sort_type=="desc"){
                      usort($row_array, "cmp_desc");
                    }else{
                      usort($row_array, "cmp");
                    }
                    foreach($row_array as $row){
                        $m++;
                        if($AUTH_CODE_CNTL==1){
                          $csv_data[0] = substr($arr_auth_code[$row["AUTH_ID"]],0,18)."(".$row["ORIG_DN"].")";
                        }else{
                          $csv_data[0] = $row["ORIG_DN"]." - ".substr($row["DESCRIPTION"],0,18);
                        }
                        $csv_data[1] = $row["MY_DATE"];
                        $csv_data[2] = $row["MY_TIME"] ;
                        $csv_data[3] = calculate_all_time($row["DURATION"]);

                    if(is_array($row)) {
                        $i++;
                        $bg_color = "E4E4E4";   
                        if($i%2) $bg_color ="FFFFFF";
                        if ($row["CountryCode"] == $local_country_code){
                            $my_place = $arr_location[$row["Locationid"]];
                            if ($row["LocalCode"] == $LOC_CODE || $row["LocalCode"]==""){//Şehiriçi Kabul edilen kod.
                              $my_place = "Şehiriçi";
                              $TEL_NUMBER = $LOC_CODE ." ".substr($row["PURE_NUMBER"],0,7);
                            }else{
                              $TEL_NUMBER = $row["LocalCode"]." ".substr($row["PURE_NUMBER"],0,7);
                            }
                        }else{
                            $my_place = $arr_location[$row["Locationid"]];
                            $TEL_NUMBER = $row["CountryCode"]." ".$row["LocalCode"]." ".$row["PURE_NUMBER"];
                        } 

                        if ($row["NAME"]<>''){
                            $called = "<b>".substr($row["NAME"],0,10)." ".substr($row["SURNAME"],0,10)." ".substr($row["PHONE_DESC"],0,10)."</b>";
                        }else{
                            $called = $arr_location[$row["Locationid"]];
                        }

                        $csv_data[4] = $TEL_NUMBER;
                        $csv_data[5] = str_replace("</b>","",str_replace("<b>","",$called));
                        $csv_data[6] = write_price($row["PRICE"]);
                        $wr_line=$csv_data[0];
                        for($ewr=1;$ewr<=6;$ewr++){
                          $wr_line = $wr_line.";".$csv_data[$ewr];
                        }
                        echo $wr_line."\n";
                        $my_dur=$my_dur + $row["DURATION"];
                        $my_amount=$my_amount + 1;
                        $my_pr=$my_pr + number_format($row["PRICE"],2, '.', '');
                        if($CSV_EXPORT==1){
                            csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                        }
                    }
                }
                $m++;  
                $csv_data[0] = "";
                $csv_data[1] = "";
                $csv_data[2] = "" ;
                $csv_data[3] = "";
                $csv_data[4] = "Toplam Görüşme Adedi";
                $csv_data[5] = "Toplam Süre ";
                $csv_data[6] = "Toplam Ücret";
                $m++;
                $wr_line=$csv_data[0];
                for($ewr=1;$ewr<=6;$ewr++){
                  $wr_line = $wr_line.";".$csv_data[$ewr];
                }
                echo $wr_line."\n";
                $csv_data[0] = "";
                $csv_data[1] = "";
                $csv_data[2] = "" ;
                $csv_data[3] = "";
                $csv_data[4] = $my_amount;
                $csv_data[5] = $my_dur;
                $csv_data[6] = write_price($my_pr);
                $wr_line=$csv_data[0];
                for($ewr=1;$ewr<=6;$ewr++){
                  $wr_line = $wr_line.";".$csv_data[$ewr];
                }
                echo $wr_line."\n";
            ?>

<?}
  $fd = fopen($DOCUMENT_ROOT."/temp/outbound.csv", w);
  fwrite($fd,ob_get_contents());
  ob_end_flush();
 
?>
<iframe SRC="/csv_download.php?filename=outbound.csv" WIDTH=0 HEIGHT=0 ></iframe>
