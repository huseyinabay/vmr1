<?  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";

	 //ini_set('display_errors', 'On');
     //error_reporting(E_ALL);	


    $cUtility = new Utility();
    $cdb = new db_layer(); 
    $conn = $cdb->getConnection();
    require_valid_login();
    //Kullanıcılar için hak kontrolü olmalı 
    //echo $DEPT_ID[0];echo "/".$DEPT_ID[1];echo "/".$DEPT_ID[2]."---";ECHO COUNT($DEPT_ID)."<br>";
    $kriter2 = "";
    $check_origs = false;
    $usr_crt = "";
	//$DEPT_ID =[];    // halil array olanlar bu şekilde olacak
	$DURATION_SN = "";
	$dept_crt ="";
	$in_str ="";
	$auth ="";
	$access ="";
	$TRUNK ="";
	//$order ="";
	$forceMainTable = "";
	$code_type ="";
	$local ="";
	$country ="";
	$digits ="";
	$dur ="";
	$tel_provider ="";
	$alert ="";
	$sort_type= "";
	$IN_FIHRIST ="";
	$OUT_FIHRIST ="";

     
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
	

    $sql_str1="SELECT VALUE FROM SYSTEM_PRM WHERE NAME= 'MAX_OUTBOUND_COUNT'";
      if (!($cdb->execute_sql($sql_str1,$result1,$error_msg))){
              print_error($error_msg);
                exit;
            }
    $row1=mysqli_fetch_object($result1);
    $max_outb_count = $row1->VALUE;
	
	
    //$max_outb_count = get_system_prm1(MAX_OUTBOUND_COUNT);
	if ($max_outb_count=='0'){$max_outb_count=6000;}
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
           <input type="hidden" name="IN_FIHRIST" value="<?=$IN_FIHRIST?>"> 
           <input type="hidden" name="OUT_FIHRIST" value="<?=$OUT_FIHRIST?>"> 
           <input type="hidden" name="sort_type" value="<?=($sort_type=="asc")?"desc":"asc"?>"> 
       <div id="dept" style="display:none">
            <select name="DEPT_ID" class="select1" style="width:250;" multiple>
              <?foreach($DEPT_ID as $value){
                        echo "<OPTION value='$value'  selected ></OPTION>";
               }?>
            </select>
        </div>
    </form>
    <?
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
	
    if ((($DEPT_ID[0] == "-1") || ($DEPT_ID[0]=="")) && count((array)$DEPT_ID)<="1"){
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
                        CDR_MAIN_DATA.AUTH_ID, TIME_STAMP, DEPTS.DEPT_NAME
                   ";
                   
              $sql_str1 = " FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                        LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
                        LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
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
                            PHONES.CONTACT_ID, PHONES.DESCRIPTION AS PHONE_DESC, CONTACTS.NAME, CONTACTS.SURNAME, TIME_STAMP, DEPTS.DEPT_NAME
              ";
                        $sql_str1 = " FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                            LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
                            LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
							$fih_join_prm JOIN PHONES ON
                                (CDR_MAIN_DATA.CountryCode = PHONES.COUNTRY_CODE AND 
                                CDR_MAIN_DATA.LocalCode = PHONES.CITY_CODE AND
                                CDR_MAIN_DATA.PURE_NUMBER = PHONES.PHONE_NUMBER AND
                                CDR_MAIN_DATA.SITE_ID = PHONES.SITE_ID)
                            LEFT JOIN CONTACTS ON PHONES.CONTACT_ID = CONTACTS.CONTACT_ID ".$provider_join;
         }
         if ($kriter != ""){
               $sql_str2 = "  WHERE ".$kriter.$fih_where_prm;
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
        //echo $sql_str.$sql_str1.$sql_str2;exit;
        if (!($cdb->execute_sql($sql_str.$sql_str1.$sql_str2,$result,$error_msg))){
           print_error($error_msg);
           exit;
    }	
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script LANGUAGE="javascript" src="/reports/scripts/jquery-3.5.1.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/jquery.dataTables.min.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/dataTables.buttons.min.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/jszip.min.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/pdfmake.min.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/vfs_fonts.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/buttons.html5.min.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/buttons.print.min.js"></script>
	
		<link rel="stylesheet" type="text/css" href="/reports/scripts/jquery.dataTables.min.css" />
		<link rel="stylesheet" type="text/css" href="/reports/scripts/buttons.dataTables.min.css"/>

        <script language="JavaScript">
	//https://datatables.net/extensions/buttons/examples/initialisation/export.html
	//https://datatables.net/reference/option/
	 
    $(document).ready(function() {
    $('#export').DataTable( {
		//scrollY: 600,
        //paging: false,
        dom: 'Bfrtip',
		"pageLength": 100,
        buttons: ['copy', 
        {
            extend: 'excelHtml5',
			charset: 'utf-8',
			bom: true,
            title: "Giden Çağrı"

        },
        {
            extend: 'csvHtml5',
            title: "Giden Çağrı"
        }, 
            'print', {text: "email",
        action: function (e ,dt, node, config){
          location.href = "javascript:mailPage('inbound.html')";
        }} ]
		
    } );
	} );
	  
	 
	</script>	

			    <tr>
        <td width="100%" align="center" class="rep_header" align="center">
            <TABLE BORDER="0" WIDTH="100%">
            <TR>
                <TD><a href="https://www.crystalinfo.com.tr" target="_blank"><img border="0" SRC="<?=IMAGE_ROOT?>logo2.gif" ></a></TD>
                <TD width="50%" align=center CLASS="header"><?echo $company;?><BR>GİDEN ÇAĞRI RAPORU</TD>
                <TD width="25%" align=right><img SRC="<?=IMAGE_ROOT?>company.gif"></TD>
            </TR>
            </TABLE>
          
        </td>
    </tr>
	  
	
    <tr>
        <td width="100%" class="rep_header" align="right">
        <table width="100%" border="0">
            <tr>
   <?if($t0!=""){?>
                <td width="50%" class="rep_header" align="left">Tarih (<?=date("d/m/Y",strtotime($t0))?>
                   <? 

				   if($MY_DATE=="b" || $MY_DATE=="f" ){
					   echo  " - ".date("d/m/Y",strtotime($t0));
				   }	   
				   else{
                    echo (" - ".date("d/m/Y",strtotime("$t1")));
					}

				?>
                    )
				</td>
					

				
   <?}?>
                <td width="50%" class="rep_header" align="right" cellspacing=0 cellpadding=0>
                </td>
            </tr>
        </table>
        </td>
    </tr>  
	  
	  
	  
<br>
  <br>
	  
    <tr>
        <td>
        <table id="export"class="display nowrap" style="width:100%">
		
	
        <thead>
		
                <tr>
<?  if($AUTH_CODE_CNTL==1){?>
                    <td width="20%" ALIGN="center">Auth Code</td>
  <?}else{?>
                    <td width="20%" ALIGN="center">Dahili</td>
  <?}?>                    
                    <td width="15%" ALIGN="center">Departman</td>
         			<td width="10%" ALIGN="left">Tarih</td>
                    <td width="10%" ALIGN="left">Saat</td>
                    <td width="10%" ALIGN="left">Sure</td>
                    <td width="15%" ALIGN="right">Telefon</td>
                    <td width="15%" ALIGN="left">Aranan</td>
                    <td width="12%" ALIGN="left">Ücret</td>

                </tr>
                </thead>
	
                <tbody>
            <?
      

            $my_dur=0;
            $my_amount=0; 
            $my_pr=0; 
            $i;
            if (mysqli_num_rows($result)>0)
                mysqli_data_seek($result,0);
                $cnt=0;
                $numeric=0;
                switch ($order){
                    case 'auth':
                        $order1 ='AUTH_ID';
                        break;  
                    case 'Dahili':
                        $order1 ='ORIG_DN';
                        break;
					case 'Departman':
                        $order1 ='DEPT_NAME';
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


                while($row_array[$cnt] = mysqli_fetch_array($result)){$cnt++;}
                
                    if($sort_type=="desc"){
                      usort($row_array, "cmp_desc");
                    }else{
                      usort($row_array, "cmp");
                    }
                    foreach($row_array as $row){
                        $m++;

                    if(is_array($row)) {
                        $i++;
                        $bg_color = "E4E4E4";   
                        if($i%2) $bg_color ="FFFFFF";
                        if ($row["CountryCode"] == $local_country_code){
                            $my_place = $arr_location[$row["Locationid"]];
                            if ($row["LocalCode"] == $LOC_CODE || $row["LocalCode"]==""){//Şehiriçi Kabul edilen kod.
                              $my_place = "sehirici";
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


                        
                            if($AUTH_CODE_CNTL==1){
                                $name = substr($arr_auth_code[$row["AUTH_ID"]],0,18)."(".$row["ORIG_DN"].")";
                            }else{
                                $name = $row["ORIG_DN"]." - ".substr($row["DESCRIPTION"],0,18);
                            }
                            echo " <tr>";
                            echo " <td>".$name."</td>";
							echo " <td>" .$row["DEPT_NAME"]."</td>";
                            echo " <td><a class=\"a1\" style=\"cursor:hand;\" title=\"Kayıt Detayı\"><span onclick=\"javascript:popup('/audit/cdr_detail.php?id=".$row["CDR_ID"]."','report_outb_prn',600,300)\">".$row["MY_DATE"]."</span></a></td>";
                            echo " <td>" .$row["MY_TIME"]."</td>";                 
                            echo " <td>".calculate_all_time($row["DURATION"])."</td>";     
                            echo " <td align=\"right\"><a class=\"a1\" href=\"javascript:drill_down_pn('". $row["PURE_NUMBER"]."')\">";
                            if(isset($_POST['KVKK'])){
                            echo substr($TEL_NUMBER, 0, -2) . "**</a></td>";
                            }else {
                                echo $TEL_NUMBER . "</a></td>";
                            }
                            echo " <td >".$called."</td>";
                            echo " <td>".write_price($row["PRICE"])."</td>";
                            echo "</tr>";
                        
                        $my_dur=$my_dur + $row["DURATION"];
                        $my_amount=$my_amount + 1;
                        $my_pr=$my_pr + number_format($row["PRICE"],2, '.', '');

                    }
                }
      
            ?>
            </tbody>

            </table>
			
				
			
        </td>
    </tr>
    <tr height="20">
        <td></td>
    </tr>
    <tr>
        <td height="22" colspan="1"  align="right">
           <TABLE BORDER="0" WIDTH="100%">
            <TR>
                <TD WIDTH="80%" ALIGN="right"><b>Toplam Gorusme Adedi :</b></TD>
                <TD WIDTH="20%" ><?=number_format($my_amount,0,'','.')?></TD>
            </TR>
            </TABLE>
      </td>
    </tr>
    <tr>
        <td height="22" colspan="3" width="100%" align="right">
           <TABLE BORDER="0" WIDTH="100%">
            <TR>
                <TD WIDTH="80%" ALIGN="right"><b>Toplam Sure :</b></TD>
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
    <td class="rep_header" nowrap valign="top">Secili Departmanlar:</td>
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

?>
 <br>
  <br>
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
   window.setInterval('document.all(\'sort_me\').submit()', 300000);
</script>
