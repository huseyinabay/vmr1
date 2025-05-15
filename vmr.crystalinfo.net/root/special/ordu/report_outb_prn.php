<?  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
    $cUtility = new Utility();
    $cdb = new db_layer(); 
    $conn = $cdb->getConnection();
    require_valid_login();
    //Kullanıcılar için hak kontrolü olmalı 
    //echo $DEPT_ID[0];echo "/".$DEPT_ID[1];echo "/".$DEPT_ID[2]."---";ECHO COUNT($DEPT_ID)."<br>";
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
    //CSV oluştururken kullanılacak boş satır dizisi
    $csv_emp[0] = "";$csv_emp[1] = "";$csv_emp[2] = "";$csv_emp[3] = "";$csv_emp[4] = "";$csv_emp[5] = "";$csv_emp[6] = "";$csv_emp[7] = "";
    //Raporlanmak istenmeyen dahililer alınıyor.
    $unrep_exts_crt = get_unrep_exts_crt($SITE_ID);
    //Hak Kontrolü Burada Bitiyor
    $max_outb_count = get_system_prm(MAX_OUTBOUND_COUNT);
	if ($max_outb_count=='0'){$max_outb_count=5000;}
  ob_start();

  cc_page_meta();
    echo "<center>";
    ?>
<script>

function CheckEmail (strng) {
    var error="";
    var emailFilter=/^.+@.+\..{2,3}$/;
    if (!(emailFilter.test(strng))) { 
       alert("Lütfen geçerli bir e-mail adresi giriniz.\n");
       return 0;
    }
    else {
       var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/;
       var xxx= /\""/;
       if (strng.match(illegalChars)) {
             alert("Girdiğiniz e-mail geçersiz karakterler içermektedir.\n");
             return 0;
       }
    }
    return 1;
}

   function mailPage(){
      var keyword = prompt("Lütfen bir mail adresi giriniz.", "")
      if(CheckEmail(keyword)){
          var pagename = "/reports/htmlmail.php?page=/temp/outbound.html&email="+ keyword;
          this.location.href = pagename;
      }    
   }

</script>
<?if($CSV_EXPORT != 2){?>
    <form name="sort_me" method="post" action="">
           <input type="hidden" name="SITE_ID" value="<?=$SITE_ID?>">   
           <input type="hidden" name="MY_DATE" value="<?=$MY_DATE?>">
           <input type="hidden" name="TelProviderid" value="<?=$TelProviderid?>">
           <input type="hidden" name="FROM_PROVIDER_ID" value="<?=$FROM_PROVIDER_ID?>">
           <input type="hidden" name="t0" value="<?=$t0?>">        
           <input type="hidden" name="t1" value="<?=$t1?>">        
           <input type="hidden" name="last" value="<?=$last?>">        
           <input type="hidden" name="hh0" value="<?=$hh0?>">
           <input type="hidden" name="hm0" value="<?=$hm0?>">
           <input type="hidden" name="hh1" value="<?=$hh1?>">
           <input type="hidden" name="hm1" value="<?=$hm1?>">
           <input type="hidden" name="hafta" value="<?=$hafta?>">
           <input type="hidden" name="ORIG_DN" value="<?=$ORIG_DN?>">
           <input type="hidden" name="CSV_EXPORT" value="<?=$CSV_EXPORT?>">
           <input type="hidden" name="MEMBER_NO" value="<?=$MEMBER_NO?>">
           <input type="hidden" name="LocationTypeid" value="<?=$LocationTypeid?>">
           <input type="hidden" name="LocalCode" value="<?=$LocalCode?>">
           <input type="hidden" name="CountryCode" value="<?=$CountryCode?>">
           <input type="hidden" name="AUTH_CODE" value="<?=$AUTH_CODE?>">
           <input type="hidden" name="AUTH_CODE_CNTL" value="<?=$AUTH_CODE_CNTL?>">
           <input type="hidden" name="ACCESS_CODE" value="<?=$ACCESS_CODE?>">
           <input type="hidden" name="DURATION" value="<?=$DURATION?>">
           <input type="hidden" name="record" value="<?=$record?>">
           <input type="hidden" name="DIGITS" value="<?=$DIGITS?>">        
           <input type="hidden" name="type" value="<?=$type?>">        
           <input type="hidden" name="TRUNK" value="<?=$TRUNK?>">   
           <input type="hidden" name="SUMM" value="<?=$SUMM?>"> 
           <input type="hidden" name="PRICE" value="<?=$PRICE?>">   
           <input type="hidden" name="CONTACT_TYPE" value="<?=$CONTACT_TYPE?>"> 
           <input type="hidden" name="sort_type" value="<?=($sort_type=="asc")?"desc":"asc"?>"> 
       <div id="dept" style="display:none">
            <select name="DEPT_ID" class="select1" style="width:250;" multiple>
              <?foreach($DEPT_ID as $value){
                        echo "<OPTION value='$value'  selected ></OPTION>";
               }?>
            </select>
        </div>
    </form>
    <?}
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
         $sql_str = "   SELECT CDR_MAIN_DATA.CDR_ID,LTRIM(CDR_MAIN_DATA.ORIG_DN) AS ORIG_DN,EXTENTIONS.DEPT_ID,
                        DATE_FORMAT(MY_DATE,\"%d.%m.%Y\") AS MY_DATE, DATE_FORMAT(TIME_STAMP,\"%H:%i:%s\") AS MY_TIME, 
                        CDR_MAIN_DATA.DURATION, CDR_MAIN_DATA.LocationTypeid, EXTENTIONS.DESCRIPTION, 
                        CDR_MAIN_DATA.LocalCode, CDR_MAIN_DATA.CountryCode, CDR_MAIN_DATA.PURE_NUMBER, CDR_MAIN_DATA.Locationid,
                        (CDR_MAIN_DATA.PRICE*$prc_fct) AS PRICE,
                        CDR_MAIN_DATA.TER_TRUNK_MEMBER AS TRUNK, CDR_MAIN_DATA.TER_TRUNK_MEMBER,
                        CDR_MAIN_DATA.AUTH_ID, TIME_STAMP, CDR_MAIN_DATA.COUNTER
                   ";
                   
              $sql_str1 = " FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                        LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
                        LEFT JOIN PHONES ON (CDR_MAIN_DATA.CountryCode = PHONES.COUNTRY_CODE AND 
                                            TRIM(CDR_MAIN_DATA.LocalCode) = TRIM(PHONES.CITY_CODE) AND 
                                            CDR_MAIN_DATA.PURE_NUMBER = PHONES.PHONE_NUMBER AND 
                                            CDR_MAIN_DATA.SITE_ID = PHONES.SITE_ID)
                      LEFT JOIN CONTACTS ON PHONES.CONTACT_ID = CONTACTS.CONTACT_ID ".$provider_join;
         }else{
         $sql_str  = "SELECT CDR_MAIN_DATA.CDR_ID,LTRIM(CDR_MAIN_DATA.ORIG_DN) AS ORIG_DN,DATE_FORMAT(CDR_MAIN_DATA.MY_DATE,\"%d/%m/%Y\") AS MY_DATE,EXTENTIONS.DEPT_ID,
                            DATE_FORMAT(TIME_STAMP,\"%H:%i:%s\") AS MY_TIME,CDR_MAIN_DATA.LocationTypeid,
                            CDR_MAIN_DATA.DURATION, EXTENTIONS.DEPT_ID,CDR_MAIN_DATA.Locationid,
                            CDR_MAIN_DATA.CountryCode, CDR_MAIN_DATA.LocalCode, CDR_MAIN_DATA.PURE_NUMBER, 
                            CDR_MAIN_DATA.AUTH_ID, EXTENTIONS.DESCRIPTION, (CDR_MAIN_DATA.PRICE*$prc_fct) AS PRICE,
                            PHONES.CONTACT_ID, PHONES.DESCRIPTION AS PHONE_DESC, CONTACTS.NAME, CONTACTS.SURNAME, TIME_STAMP, CDR_MAIN_DATA.COUNTER
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
      $sql_str2 .= " ORDER BY DEPT_ID, ORIG_DN ";
         if ($record<>'' ||is_numeric($record)) {
               $sql_str2 .= " LIMIT 0,". $record ;
     }
//echo $sql_str.$sql_str1.$sql_str2;exit;
        if (!($cdb->execute_sql($sql_str.$sql_str1.$sql_str2,$result,$error_msg))){
           print_error($error_msg);
           exit;
    }

?>
<div style="width: 77; height: 28; position: absolute; left: 709; top: 3; display: none">
  <input type="button" value="Anasayfa" onclick="javascript:history.go(-1);">
</div>

<table width="95%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="100%" align="center" class="rep_header" align="center">
           <?if($CSV_EXPORT != 2){?>
            <TABLE BORDER="0" WIDTH="100%">
            <TR>
                <TD><a href="http://www.crystalinfo.net" target="_blank"><img border="0" SRC="http://<?=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']?><?=IMAGE_ROOT?>logo2.gif" ></a></TD>
                <TD width="50%" align=center CLASS="header"><?echo $company;?><BR>TELEFON GÖRÜŞME MALİYETLERİ DETAY RAPORU</TD>
                <TD width="25%" align=right><img SRC="http://<?=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']?><?=IMAGE_ROOT?>company.gif"></TD>
            </TR>
            </TABLE>
          <?}?>  
        </td>
    </tr>


    <tr>
        <td width="100%" class="rep_header" align="right">
        <table width="100%" border="0">
            <tr>
   <?if($t0!=""){?>
                <td width="50%" class="rep_header" align="left">Tarih (<?=date("d/m/Y",strtotime($t0))?>
                    <?if($t1!=""){?>
                    <?echo (" - ".date("d/m/Y",strtotime($t1)));}?>
                    )
                </td>
   <?}?>
                <td width="50%" class="rep_header" align="right" cellspacing=0 cellpadding=0>
                </td>
            </tr>
        </table>
        </td>
    </tr>
    <tr>
        <td>
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td width="50%" valign="top">
                        <table>
                            <tr  <?if ($orig<>'Yes') echo "style=\"display:none;\""?>>
                                <td class="rep_header" nowrap width="20%" valign="top">
                                <?if($AUTH_CODE_CNTL==1)
                                  echo "Auth Code :";
                                else
                                  echo "Dahili :";?>
                                </td>
                                <td width="80%" valign="top">
                                <?if(is_array($ORIG_ARRAY)){ 
                                    for($i=0;$i<count($ORIG_ARRAY);$i++){
                                        if(is_numeric($ORIG_ARRAY[$i])){?>
                                        <?echo $ORIG_ARRAY[$i];?> - <? echo get_ext_name2($ORIG_ARRAY[$i],$SITE_ID).";";}
                                    }
                                   }
                                ?></td>
                            </tr>
                        
                            <tr  <?if ($dept<>'Yes') echo "style=\"display:none;\""?>>
                                <td class="rep_header" nowrap width="20%" valign="top">Departman:</td>
                                <td width="80%" valign="top">
                                <?if(is_array($DEPT_ID)){ 
                                    for($i=0;$i<count($DEPT_ID);$i++){
                                        if(is_numeric($DEPT_ID[$i])){?>
                                        <? echo get_dept_name($DEPT_ID[$i],$SITE_ID).";";}
                                    }
                                   }
                                ?></td>
                            </tr>
                            <tr <?if($TRUNK=="" || $TRUNK=="-1") echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Hat :</td>
                                <td width="60%"><?echo $TRUNK;?></td>               
                            </tr>
                            <tr <?if($FROM_PROVIDER_ID=="" || $FROM_PROVIDER_ID=="-1") echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Çıkış Şebekesi :</td>
                                <td width="60%"><?echo get_tel_provider($FROM_PROVIDER_ID);?></td>               
                            </tr>
                            <tr <?if ($auth<>'Yes') echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Auth. Kodu:</td>
                                <td width="60%"><?echo $AUTH_CODE." - ".$arr_auth_code[$AUTH_CODE];?></td>
                            </tr>
                            <tr <?if ($access<>'Yes') echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Çıkış Kodu:</td>
                                <td width="60%"><?echo $ACCESS_CODE;?></td>
                            </tr>
                        </table>    
                    </td>
                    <td width="50%">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr><td width="50%" valign="top">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr  <?if ($code_type<>'Yes') echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Arama Tipi:</td>
                                <td width="60%"><?echo $arr_location_type[$LocationTypeid];?></td>
                            </tr>
                            <tr  <?if ($local<>'Yes') echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Opt. Kodu:</td>
                                <td width="60%"><?echo $LocalCode;?></td>
                            </tr>
                            <tr  <?if ($country<>'Yes') echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Ülke Adı:</td>
                                <td width="60%"><?echo $CountryCode;?></td>
                            </tr>
                            <tr  <?if ($digits<>'Yes') echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Tel No:</td>
                                <td width="60%"><?echo $DIGITS;?></td>
                            </tr>
                            <tr <?if ($dur<>'Yes') echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Min. Süre:</td>
                                <td width="60%"><?echo $DURATION;?> dk</td>
                            </tr>
                            <tr <?if ($tel_provider<>'Yes') echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Aranan Şebeke:</td>
                                <td width="60%"><?echo get_tel_provider($TelProviderid);?></td>
                            </tr>
                            <tr <?if ($PRICE==1) echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Min. Ücret:</td>
                                <td width="60%"><?echo $PRICE;?></td>
                            </tr>
                        </table></td>
                        <td width="50%" valign="top">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr <?if($record=="") echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Kayıt Adedi :</td>
                                <td width="60%" align="right"><?echo $record;?></td>                
                            </tr>
                            <tr <?if($fihrist_state=="") echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Fihrist :</td>
                                <td width="60%" align="right"><?echo $fihrist_state;?></td>             
                            </tr>
                            <tr <?if($hafta=="3") echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%" colspan="2"><?=($hafta=="1")?"Hafta İçi":"Hafta Sonu"?></td>
                            </tr>
                            <tr <?if($hh0==-1 && $hh1 ==-1) echo "style=\"display:none;\""?>>
                                <td class="rep_header" align="left" nowrap width="40%">Saat Dilimi :</td>
                                <td width="60%" align="right"><?=($hh0==-1)?"":$hh0.":".$hm0?> - <?=($hh1==-1)?"":$hh1.":".$hm1?></td>              
                            </tr>
                        </table>    
                        </td></tr>
                        <tr><td colspan=2 align=right>
                        <?if($CSV_EXPORT<>2){?>
                          <table cellspacing=0 cellpadding=0>
                            <tr>
                              <td><img src="http://<?=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']?><?=IMAGE_ROOT?>report/top02.gif" border=0></td>
                              <td><a href="javascript:mailPage()"><img src="http://<?=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']?><?=IMAGE_ROOT?>report/mail.gif" border=0 title="Mail"></a></td>
                              <td><a href="javascript:history.back(1);"><img src="http://<?=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']?><?=IMAGE_ROOT?>report/geri.gif" border=0 title="Geri"></a></td>
                              <td><a href="javascript:history.forward(1);"><img src="http://<?=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']?><?=IMAGE_ROOT?>report/ileri.gif" border=0 title="İleri"></a></td>
                              <td><a href="javascript:location.reload();"><img src="http://<?=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']?><?=IMAGE_ROOT?>report/yenile.gif" border=0 title="Yenile"></a></td>
                              <td><a href="javascript:window.print();"><img src="http://<?=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']?><?=IMAGE_ROOT?>report/print.gif" border=0 title="Yazdır"></a></td>
                              <td><img src="http://<?=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']?><?=IMAGE_ROOT?>report/top01.gif" border=0></td>
                            </tr>
                          </table>
                          <?}?>
                        </td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>   
    </tr>
      </td>
<!--    </tr>
  </table>
<TABLE WIDTH="95%">
-->
    <tr>
        <td>
            <table width="100%" border="0" bgcolor="C0C0C0" cellspacing="1" cellpadding="0" >
            <?
      
      ///////////////////////////////////////////////////////////////77
      
         //Gelen kayıt adedi çok fazla ise bunlar csv ile kaydedilsin.
		 if(mysqli_num_rows($result)>$max_outb_count){
            $CSV_EXPORT=1;
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
                $file_name = "outboudrep.csv";
                if($CSV_EXPORT==1)
                    $fp = fopen(dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", "w+");
                if($CSV_EXPORT==1)
                    fwrite($fp, " ");
                $m=0;
                $csv_data[0] = " ";
                $csv_data[1] = $company;
                $csv_data[2] = " " ;
                $csv_data[3] = "Giden";
                $csv_data[4] = "Çagrı";
                $csv_data[5] = "Raporu";
                $csv_data[6] = " ";
                $csv_data[7] = " ";
                $m++;
                if($CSV_EXPORT==1)
                    csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                while($row_array[$cnt] = mysqli_fetch_array($result)){$cnt++;}
                $tmp_dept_id = -1;
                $tmp_orig_dn = "";
                $show_summs = 0;
                foreach($row_array as $row){
                    if(is_array($row)) {
                        $i++;
                        $bg_color = "E4E4E4";   
                        if($i%2) $bg_color ="FFFFFF";
                        $m++;
                        if($show_summs && $tmp_orig_dn!=$row["ORIG_DN"]){
                           if($CSV_EXPORT!=1){
                        ?>
              </table>      <br>
                      <table cellpadding=0 cellspacing=0 border=0 width="100%">
                        <tr>
                          <td rowspan=2 width="80%" style="border-bottom:1px solid thin;border-right:1px solid thin"><b>Dahili Toplamı</b></td>
                          <td style="border-bottom:1px solid thin;border-right:1px solid thin">Toplam Kontör :</td>
                          <td align="right" style="border-bottom:1px solid thin;border-right:1px solid thin"><?=$orig_counter?></td>
                        </tr>
                        <tr>
                          <td style="border-bottom:1px solid thin;border-right:1px solid thin">Toplam Ücret :</td>
                          <td align="right" style="border-bottom:1px solid thin;border-right:1px solid thin"><?=write_price($orig_price)?></td>
                        </tr>
                      </table><br>
            <table width="100%" border="0" bgcolor="C0C0C0" cellspacing="1" cellpadding="0" >
<?
                        }
                        $csv_data[0] = "Dahili Toplamı";$csv_data[1] = "";$csv_data[2] = "";$csv_data[3] = "Toplam Kontör";$csv_data[4] = $orig_counter;$csv_data[5] = "Toplam Ücret";$csv_data[6] = write_price($orig_price);$csv_data[7] = "";
                        if($CSV_EXPORT==1)
                            csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                        //Boş satır Yazdır....
                        if($CSV_EXPORT==1){csv_write_line($csv_emp, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);}
                        $orig_counter=0;$orig_price=0;
                         }
                        if($show_summs && $tmp_dept_id != $row["DEPT_ID"]){
                           if($CSV_EXPORT!=1){
                        ?>
            </table>
                      <table cellpadding=0 cellspacing=0 border=0 width="100%">
                        <tr>
                          <td rowspan=2 width="80%" style="border-bottom:1px solid thin;border-right:1px solid thin"><b>Departman Toplamı</b></td>
                          <td style="border-bottom:1px solid thin;border-right:1px solid thin">Toplam Kontör :</td>
                          <td align="right" style="border-bottom:1px solid thin;border-right:1px solid thin"><?=$dept_counter?></td>
                        </tr>
                        <tr>
                          <td style="border-bottom:1px solid thin;border-right:1px solid thin">Toplam Ücret :</td>
                          <td align="right" style="border-bottom:1px solid thin;border-right:1px solid thin"><?=write_price($dept_price)?></td>
                        </tr>
                      </table>
            <table width="100%" border="0" bgcolor="C0C0C0" cellspacing="1" cellpadding="0" ><br>
<?                                          }
                        //Boş satır Yazdır....
                        if($CSV_EXPORT==1){csv_write_line($csv_emp, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);}
                        $csv_data[0] = "Departman Toplamı";$csv_data[1] = "";$csv_data[2] = "";$csv_data[3] = "Toplam Kontör";$csv_data[4] = $dept_counter;$csv_data[5] = "Toplam Ücret";$csv_data[6] = write_price($dept_price);$csv_data[7] = "";
                        if($CSV_EXPORT==1)
                            csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                        //Boş satır Yazdır....
                        if($CSV_EXPORT==1){csv_write_line($csv_emp, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);}
                       $dept_counter=0;$dept_price=0;
                 }
                        if($tmp_dept_id != $row["DEPT_ID"]){
                          $csv_data[1] = "";$csv_data[0] = "Departman : ".$dept_name;$csv_data[2] = "";$csv_data[3] = "";$csv_data[4] = "";$csv_data[5] = "";$csv_data[6] = "";$csv_data[6] = "";
                          if($CSV_EXPORT==1)
                            csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                          $dept_name = get_dept_name($row["DEPT_ID"],$SITE_ID);
                           if($CSV_EXPORT!=1){
                          ?>
                <tr>
                    <td class="rep_table_header" colspan="8">Departman : <?=($dept_name=="")?"Tanımsız Dahililer":$dept_name;?></td>
                </tr> 
<?                          }
                          }
                        if($tmp_orig_dn!=$row["ORIG_DN"]){
                          if($AUTH_CODE_CNTL==1){
                                $name = substr($arr_auth_code[$row["AUTH_ID"]],0,18)."(".$row["ORIG_DN"].")";
                          }else{
                                $name = $row["ORIG_DN"]." - ".substr($row["DESCRIPTION"],0,18);
                          }
                          $csv_data[1] = "";$csv_data[0] = $name;$csv_data[2] = "";$csv_data[3] = "";$csv_data[4] = "";$csv_data[5] = "";$csv_data[6] = "";$csv_data[7] = "";
                          if($CSV_EXPORT==1)
                            csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                          $csv_data[0] = "Tarih";
                          $csv_data[1] = "Saat" ;
                          $csv_data[2] = "Süre";
                          $csv_data[3] = "Telefon";
                          $csv_data[4] = "Aranan";
                          $csv_data[5] = "Kontör";
                          $csv_data[6] = "Ücret";
                          $csv_data[7] = "";
                          if($CSV_EXPORT==1)
                            csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                          if($CSV_EXPORT!=1){
?>                   
                <tr bgcolor="FFFFFF">
                    <td class="rep_td" colspan="8"><b><?=$name?></b></td>
                </tr> 
                <tr>
                    <td class="rep_table_header" width="10%" ALIGN="center">Tarih</td>
                    <td class="rep_table_header" width="10%" ALIGN="center">Saat</td>
                    <td class="rep_table_header" width="10%" ALIGN="center">Süre</td>
                    <td class="rep_table_header" width="15%" ALIGN="center">Telefon</td>
                    <td class="rep_table_header" width="20%" ALIGN="center">Aranan</td>
                    <td class="rep_table_header" width="12%" ALIGN="center">Kontör</td>
                    <td class="rep_table_header" width="12%" ALIGN="center">Ücret</td>
                </tr>
                <tr>
                    <td colspan="10" bgcolor="#000000" height="1"></td>
                </tr>
<?                         }
                        }
                        $tmp_dept_id = $row["DEPT_ID"];
                        $tmp_orig_dn = $row["ORIG_DN"];
                        $csv_data[0] = $row["MY_DATE"];
                        $csv_data[1] = $row["MY_TIME"] ;
                        $csv_data[2] = calculate_all_time($row["DURATION"]);

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

                        $csv_data[3] = $TEL_NUMBER;
                        $csv_data[4] = str_replace("</b>","",str_replace("<b>","",$called));
                        $csv_data[5] = $row["COUNTER"];
                        $csv_data[6] = write_price($row["PRICE"]);
                        $csv_data[7] = "";

                        if($CSV_EXPORT<>1){
                            echo " <tr  BGCOLOR=$bg_color>";
                            if($CSV_EXPORT == 2){
                              echo " <td class=\"rep_td\">".$row["MY_DATE"]."</td>";
                            }else{
                              echo " <td class=\"rep_td\"><a class=\"a1\" style=\"cursor:hand;\" title=\"Kayıt Detayı\"><span onclick=\"javascript:popup('/audit/cdr_detail.php?id=".$row["CDR_ID"]."','report_outb_prn',600,300)\">".$row["MY_DATE"]."</span></a></td>";
                            }
                            echo " <td class=\"rep_td\">" .$row["MY_TIME"]."</td>";                 
                            echo " <td class=\"rep_td\">".calculate_all_time($row["DURATION"])."</td>";     
                            if($CSV_EXPORT == 2){
                              echo " <td class=\"rep_td\" align=\"right\">".$TEL_NUMBER."</td>";
                            }else{
                              echo " <td class=\"rep_td\" align=\"right\"><a class=\"a1\" href=\"javascript:drill_down_pn('". $row["PURE_NUMBER"]."')\">$TEL_NUMBER</a></td>";
                            }
                            echo " <td class=\"rep_td\" >&nbsp&nbsp ".$called."</td>";
                            echo " <td class=\"rep_td\" >&nbsp&nbsp ".$row["COUNTER"]."</td>";
                            echo " <td align=right class=\"rep_td\">".write_price($row["PRICE"])."</td>";
                            echo "</tr>";
                        }
                        $show_summs = 1;
                        $dept_counter = $dept_counter+$row["COUNTER"]; $dept_price=$dept_price + number_format($row["PRICE"],3, '.', '');
                        $orig_counter = $orig_counter+$row["COUNTER"]; $orig_price=$orig_price + number_format($row["PRICE"],3, '.', '');
                        $my_dur=$my_dur + $row["DURATION"];$my_counter=$my_counter + $row["COUNTER"];
                        $my_amount=$my_amount + 1;$my_pr=$my_pr + number_format($row["PRICE"],3, '.', '');$my_counter = $my_counter+$row["COUNTER"]=0;
                        if($CSV_EXPORT==1){
                            csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                        }
                    }
                }
            ?>
    <tr><td height="22" bgcolor="FFFFFF" colspan="8">&nbsp;</td></tr>        
            </table>
<?
                        if($show_summs){
                        $csv_data[0] = "Dahili Toplamı";$csv_data[1] = "";$csv_data[2] = "";$csv_data[3] = "Toplam Kontör";$csv_data[4] = $orig_counter;$csv_data[5] = "Toplam Ücret";$csv_data[6] = write_price($orig_price);$csv_data[7] = "";
                        if($CSV_EXPORT==1)
                            csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                        //Boş satır Yazdır....
                        if($CSV_EXPORT==1){csv_write_line($csv_emp, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);}
                        $csv_data[0] = "Departman Toplamı";$csv_data[1] = "";$csv_data[2] = "";$csv_data[3] = "Toplam Kontör";$csv_data[4] = $dept_counter;$csv_data[5] = "Toplam Ücret";$csv_data[6] = write_price($dept_price);$csv_data[7] = "";
                        if($CSV_EXPORT==1)
                            csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                        if($CSV_EXPORT!=1){
                        ?><br>
                      <table cellpadding=0 cellspacing=0 border=0 width="100%">
                        <tr>
                          <td rowspan=2 width="80%" style="border-bottom:1px solid thin;border-right:1px solid thin"><b>Dahili Toplamı</b></td>
                          <td style="border-bottom:1px solid thin;border-right:1px solid thin">Toplam Kontör :</td>
                          <td align="right" style="border-bottom:1px solid thin;border-right:1px solid thin"><?=$orig_counter?></td>
                        </tr>
                        <tr>
                          <td style="border-bottom:1px solid thin;border-right:1px solid thin">Toplam Ücret :</td>
                          <td align="right" style="border-bottom:1px solid thin;border-right:1px solid thin"><?=write_price($orig_price)?></td>
                        </tr>
                      </table><br>
                      <table cellpadding=0 cellspacing=0 border=0 width="100%">
                        <tr>
                          <td rowspan=2 width="80%" style="border-bottom:1px solid thin;border-right:1px solid thin"><b>Departman Toplamı</b></td>
                          <td style="border-bottom:1px solid thin;border-right:1px solid thin">Toplam Kontör :</td>
                          <td align="right" style="border-bottom:1px solid thin;border-right:1px solid thin"><?=$dept_counter?></td>
                        </tr>
                        <tr>
                          <td style="border-bottom:1px solid thin;border-right:1px solid thin">Toplam Ücret :</td>
                          <td align="right" style="border-bottom:1px solid thin;border-right:1px solid thin"><?=write_price($dept_price)?></td>
                        </tr>
                      </table>
<?                        }
                        }
                //Boş satır Yazdır....
                if($CSV_EXPORT==1){csv_write_line($csv_emp, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);}
                $csv_data[0] = "Genel Toplam";$csv_data[1] = "";$csv_data[2] = "";$csv_data[3] = "";$csv_data[4] = "";$csv_data[5] = "";$csv_data[6] = "";$csv_data[7] = "";
                if($CSV_EXPORT==1){
                    csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                }
                $csv_data[0] = "";
                $csv_data[1] = "" ;
                $csv_data[2] = "";
                $csv_data[3] = "Toplam Görüşme Adedi";
                $csv_data[4] = "Toplam Kontör";
                $csv_data[5] = "Toplam Süre ";
                $csv_data[6] = "Toplam Ücret";
                $csv_data[7] = "";
                if($CSV_EXPORT==1){
                    csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                }
                $csv_data[0] = "";
                $csv_data[1] = "" ;
                $csv_data[2] = "";
                $csv_data[3] = $my_amount;
                $csv_data[4] = $my_counter;
                $csv_data[5] = calculate_all_time($my_dur);
                $csv_data[6] = write_price($my_pr);
                $csv_data[7] = "";
                if($CSV_EXPORT==1){
                    csv_write_line($csv_data, dirname($DOCUMENT_ROOT)."/root/temp/$file_name.csv", $fp);
                }
                        ?>         
        </td>
    </tr>
    <tr height="20">
        <td></td>
    </tr>
    <tr>
        <td height="22" colspan="1"  align="right">
           <TABLE BORDER="0" WIDTH="100%">
            <TR>
                <TD WIDTH="80%" ALIGN="right"><b>Toplam Görüşme Adedi :</b></TD>
                <TD WIDTH="20%" ><?=number_format($my_amount,0,'','.')?></TD>
            </TR>
            </TABLE>
      </td>
    </tr>
    <tr>
        <td height="22" colspan="1"  align="right">
           <TABLE BORDER="0" WIDTH="100%">
            <TR>
                <TD WIDTH="80%" ALIGN="right"><b>Toplam Kontör :</b></TD>
                <TD WIDTH="20%" ><?=number_format($my_counter,0,'','.')?></TD>
            </TR>
            </TABLE>
      </td>
    </tr>
    <tr>
        <td height="22" colspan="3" width="100%" align="right">
           <TABLE BORDER="0" WIDTH="100%">
            <TR>
                <TD WIDTH="80%" ALIGN="right"><b>Toplam Süre :</b></TD>
                <TD WIDTH="20%"><?=calculate_time($my_dur,"hour")."  Saat  ".calculate_time($my_dur,"min")."  Dk";?></TD>
            </TR>
            </TABLE>
    </tr>
    <tr>
        <td height="22" colspan="3" width="100%" align="right">
           <TABLE BORDER="0" WIDTH="100%">
            <TR>
                <TD WIDTH="80%" ALIGN="right"><b>Toplam Tutar :</b></TD>
                <TD WIDTH="20%"><?=write_price($my_pr)?></TD>
            </TR>
            </TABLE>
      </td>
    </tr>
     <tr>
        <td><?echo $alert;?></td>
    </tr>


<tr  <?if ($dept<>'Yes') echo "style=\"display:none;\""?>>
<td>
<?if($dept == "yes"){?>
  <table width ="100%">
   <tr>
    <td class="rep_header" nowrap valign="top">Seçili Departmanlar:</td>
    <td valign="top" colspan=2>
<? if (is_array($DEPT_ID))   
        for($i=0;$i<count($DEPT_ID);$i++){
            if($DEPT_ID[$i] != "-1" && $DEPT_ID[$i] != "")
               echo get_dept_name($DEPT_ID[$i],$SITE_ID)."<br> ";
     }?>
    </td>
   </tr>
  </table> 
<?}?>
</td>                        
</tr>
</table>                
</table>    
<?}
  if($CSV_EXPORT == 2){
    $fd = fopen($DOCUMENT_ROOT."/temp/outbound.xls", w);
    fwrite($fd,ob_get_contents());
  }else{
    $fd = fopen($DOCUMENT_ROOT."/temp/outbound.html", w);
    fwrite($fd,ob_get_contents());
  }
  
  ob_end_flush();

  if($CSV_EXPORT==1){?>
 <iframe SRC="/csv_download.php?filename=<?=$file_name?>.csv" WIDTH=0 HEIGHT=0 ></iframe>
 <a HREF="/temp/<?=$file_name?>.csv">CSV Download</a>
<?}else if($CSV_EXPORT==2){?>
 <iframe SRC="/csv_download.php?filename=outbound.xls" WIDTH=0 HEIGHT=0 ></iframe>
 <a HREF="/temp/outbound.xls">XLS Download</a>
 <?}?>
<script language="JavaScript">
    function submit_form(sortby){
        document.all('sort_me').action='report_outb_prn.php?act=src&order=' + sortby;       
        document.all('DEPT_ID').name = 'DEPT_ID[]';     
        document.all('sort_me').submit();
   }
    function drill_down_pn(pure_n){
        document.all('DIGITS').value = pure_n;
        document.all('sort_me').action='report_outb_prn.php?act=src';       
        document.all('sort_me').submit();
    }   
  // window.setInterval('document.all(\'sort_me\').submit()', 300000);
</script>
