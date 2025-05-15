<? require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer(); 
   $conn = $cdb->getConnection();
   $show_chart=false;
   require_valid_login();

   error_reporting(E_ALL);
   	  //hata vermemesi icin eklendi  , halil 22092020
	  $field1_name ="";
	  $field2_name ="";
	  $field3_name ="";
	  $field4_name ="";
	  $field5_name ="";
	  $field6_name ="";
	  $field7_name ="";
	  $forceMainTable = "";
      $alert = "";
      $sort_type ="";
   
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
     //echo $dept_crt = get_depts_crt($_SESSION["user_id"]);
     $usr_crt = get_users_crt($_SESSION["user_id"], 1, $SITE_ID);
     $alert = "Bu rapor sadece sizin yetkinizde olan departmanlara ait dahililerin bilgilerini içerir.";
    }else{
      print_error("Bu sayfayı Görme Hakkınız Yok!!!");
      exit;
    } 

    //Cache lenecek sayfalar için kullanılan yapıdır.
	$cache_status = call_cache("/usr/local/wwwroot/multi.crystalinfo.net/root/reports/cache/ozet");
    ob_start();

    cc_page_meta();
      echo "<center>";


	  
    //Joinden kaçmak için Lokasyon tablosundaki bilgiler alınıyor.
  $sql_str="SELECT Locationid, LocationName FROM TLocation"; 
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
    $sql_str="SELECT AUTH_CODE,AUTH_CODE_DESC FROM AUTH_CODES"; 
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
    $arr_auth_code = array();
    while ($row = mysqli_fetch_object($result)){
        $arr_auth_code["$row->AUTH_CODE"] = $row->AUTH_CODE_DESC;
    }

    function get_def($fld1,$fld2){
        global $cdb,$arr_location_type,$arr_location,$arr_auth_code;
        switch ($fld1){
            case 'LocationTypeid':
                $ret_val = $arr_location_type[$fld2];
                break;
            case 'Locationid':
                $ret_val = $arr_location[$fld2];
                break;
            case 'AUTH_ID':
                $ret_val = $arr_auth_code["$fld2"];
                break;
            default: 
        }
        return $ret_val;
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
  
  $local_country_code = get_country_code($SITE_ID);
  if($CSV_EXPORT != 2){
 ?>
   <form name="sort_me" method="post" action="">
         <input type="hidden" name="SITE_ID" value="<?=$SITE_ID?>">
         <input type="hidden" name="MY_DATE" value="<?=$MY_DATE?>">
		 <input type="hidden" name="DEPT_ID" value="<?=$DEPT_ID?>">
         <input type="hidden" name="t0" value="<?=$t0?>">         
         <input type="hidden" name="t1" value="<?=$t1?>">         
         <input type="hidden" name="last" value="<?=$last?>">         
         <input type="hidden" name="hh0" value="<?=$hh0?>">
         <input type="hidden" name="hm0" value="<?=$hm0?>">
         <input type="hidden" name="hh1" value="<?=$hh1?>">
         <input type="hidden" name="hm1" value="<?=$hm1?>">
         <input type="hidden" name="hafta" value="<?=$hafta?>">
         <input type="hidden" name="record" value="<?=$record?>">
         <input type="hidden" name="type" value="<?=$type?>">  
         <input type="hidden" name="forceMainTable" VALUE="<?=$forceMainTable?>" >         
         <input type="hidden" class="cache1" name="withCache" value="<?=$withCache?>" >
         <input type="hidden" name="sort_type" value="<?=($sort_type=="desc")?"asc":"desc"?>">  
  </form>
<?}?>
<script language="JavaScript">
  function submit_form(sortby){
    document.all('sort_me').action='/reports/general/report_general_prn.php?act=src&type=<?=$type?>&order=' + sortby;    
    document.all('sort_me').submit();
   }
function CheckEmail (strng) {
    var error="";
    var emailFilter=/^.+@.+\..{2,3}$/;
    if (!(emailFilter.test(strng))) { 
       alert("Lütfen geçerli bir e-mail adresi giriniz.\n");
       return 0;
    }
    else {
       var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/
       if (strng.match(illegalChars)) {
             alert("Girdiğiniz e-mail geçersiz karakterler içermektedir.\n");
             return 0;
       }
    }
    return 1;
}   
 function mailPage(page){
      var keyword = prompt("Lütfen bir mail adresi giriniz.", "")
      if(CheckEmail(keyword)){
          var pagename = "/reports/htmlmail.php?page=/temp/"+page+  "&email="+ keyword;
          this.location.href = pagename;
      }    
   }
   
</script>



  
<?   

//echo $DEPT_ID; die;
   $report_type="GENEL RAPOR";
   
   if ($act == "src") {
      $kriter = "";   

        //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
    $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
    $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
    $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "1"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.
	$kriter .= $cdb->field_query($kriter,   "DEPTS.DEPT_ID"     ,  "=",  "$DEPT_ID"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor. BUARADA KALDI
    //$kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ACCESS_CODE"     ,  "=",  "$ACCESS_CODE"); // halil iptal etti
     
    add_time_crt();//Zaman kriteri
	$link  ="";

     if($forceMainTable)  // halil 22092020
       $CDR_MAIN_DATA = "CDR_MAIN_DATA";
    else  // halil 22092020
       $CDR_MAIN_DATA = getTableName($t0,$t1);
      
     if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";  

   switch ($type){
     case 'general':
      $sql_id="1";$grp_type="LocationTypeid";
      $field1="LocationTypeid";$field1_name="Tip Kodu";$width1="10%";$field1_ord="LocationTypeid";
      $field2="LocationType";$field2_name="Çağrı Tipi";$width2="25%";$field2_ord="LocationTypeid";
      $field3="AMOUNT";$field3_name="Adet";$width3="20%";$field3_ord="AMOUNT ";
      $field6="DURATION";$field6_name="Süre";$width6="20%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="20%";$field7_ord="PRICE";
      $header="Çağrıların Arama Türüne Göre Dağılımları";
      break;
     case 'gsm':
      $kriter.= $cdb->field_query($kriter, "CDR_MAIN_DATA.CountryCode"            ,"=",    $local_country_code);
      $kriter.= $cdb->field_query($kriter, "CDR_MAIN_DATA.LocationTypeid"         ,"=",    "2");
      $sql_id="4";$grp_type="Locationid";
      $field1="LocalCode";$field1_name="Kodu";$width1="8%";$field1_ord="LocalCode";
      $field2="Locationid";$field2_name="Şebeke Adı";$width2="20%";$field2_ord="LocalCode";
      $field3="AMOUNT";$field3_name="Adet";$width3="8%";$field3_ord="AMOUNT";
      $field6="DURATION";$field6_name="Süre";$width6="15%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="15%";$field7_ord="PRICE ";
      $header="GSM Operatör Çağrıları";
      break;   
     case 'nat':
      $kriter.= $cdb->field_query($kriter, "CDR_MAIN_DATA.CountryCode"            ,"=",    $local_country_code);
      $kriter.= $cdb->field_query($kriter, "CDR_MAIN_DATA.LocationTypeid"         ,"=",    "1");
      $sql_id="5";$grp_type="Locationid";
      $field1="LocalCode";$field1_name="İl Kodu";$width1="8%";$field1_ord="LocalCode";
      $field2="Locationid";$field2_name="İl Adı";$width2="20%";$field2_ord="LocalCode";
      $field3="AMOUNT";$field3_name="Adet";$width3="8%";$field3_ord="AMOUNT ";
      $field6="DURATION";$field6_name="Süre";$width6="15%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="15%";$field7_ord="PRICE ";
      $header="Şehirlerarası Çağrıların İllere Dağılımı";
      break;   
     case 'int':
      $kriter.= $cdb->field_query($kriter, "CDR_MAIN_DATA.LocationTypeid"      ,"=",    "3");
      $kriter.= $cdb->field_query($kriter, "CDR_MAIN_DATA.CountryCode"         ,"<>",    $local_country_code);
      $sql_id="6";$grp_type="CountryCode";
      $field1="CountryCode";$field1_name="Kodu";$width1="8%";$field1_ord="CountryCode";
      $field2="Locationid";$field2_name="Ülke Adı";$width2="20%";$field2_ord="CountryCode";
      $field3="AMOUNT";$field3_name="Adet";$width3="8%";$field3_ord="AMOUNT DESC";
      $field6="DURATION";$field6_name="Süre";$width6="15%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="15%";$field7_ord="PRICE ";
      $header="Uluslararası Çağrıların Ülkelere Dağılımı";
      break;
     case 'hour':
      $sql_id="7";$grp_type="TIME_STAMP_HOUR";
      $ext_crt="";//get_def için extra kriter
      $field1="TIME_INTERVAL";$field1_name="Saat Dilimi";$width1="10%";$field1_ord="TIME_STAMP_HOUR";
      $field3="AMOUNT";$field3_name="Adet";$width3="8%";$field3_ord="AMOUNT ";
      $field6="DURATION";$field6_name="Süre";$width6="20%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="20%";$field7_ord="PRICE ";
      $header="Çağrıların Günün Saatlerine Göre Dağılımı";
      break;   
    case 'day':
      $sql_id="8";$grp_type="TIME_STAMP_DAY";
            $ext_crt="";//get_def için extra kriter
      $field1="TIME_STAMP_DAY";$field1_name="Günler";$width1="8%";$field1_ord="TIME_STAMP_DAY";
            $field3="AMOUNT";$field3_name="Adet";$width3="10%";$field3_ord="AMOUNT";
      $field6="DURATION";$field6_name="Süre";$width6="15%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="15%";$field7_ord="PRICE ";
      $header="Çağrıların Günlere Göre Dağılımı";
      break;   
    case 'month':
      $sql_id="9";$grp_type="TIME_STAMP_MONTH";
            $ext_crt="";//get_def için extra kriter
      $field1="TIME_STAMP_MONTH";$field1_name="Aylar";$width1="8%";$field1_ord="TIME_STAMP_MONTH";
            $field3="AMOUNT";$field3_name="Adet";$width3="10%";$field3_ord="AMOUNT";
      $field6="DURATION";$field6_name="Süre";$width6="15%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="15%";$field7_ord="PRICE ";
      $header="Çağrıların Aylara Göre Dağılımı";
      break;   
     case 'auth':
      $sql_id="10";$grp_type="AUTH_ID";
            $ext_crt="";//get_def için extra kriter
      $field1="AUTH_ID";$field1_name="Auth. Kodu";$width1="12%";$field1_ord="AUTH_ID";
            $field2="AUTH_ID";$field2_name="Açıklama";$width2="22%";$field2_ord="AUTH_ID";
      $field3="AMOUNT";$field3_name="Adet";$width3="8%";$field3_ord="AMOUNT ";
            $field6="DURATION";$field6_name="Süre";$width6="15%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="15%";$field7_ord="PRICE ";
      $header="Çağrıların Auth. Kodlarına Göre Dağılımı";
      break;
    default:
      echo "Hatalı Durum Oluştu, Bir Rapor Seçmeniz Gerekir !.";
      exit;
   }

  switch ($sql_id){
    case 1:
      $sql_str  = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS AMOUNT, CDR_MAIN_DATA.LocationTypeid, TLocationType.LocationType,
                     SUM(CDR_MAIN_DATA.DURATION) AS DURATION, SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                   FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
				   LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
				   LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
				   LEFT JOIN TLocationType ON TLocationType.LocationTypeid  = CDR_MAIN_DATA.LocationTypeid
                  ";
      break;
    case 4:
      $sql_str  = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS AMOUNT,CDR_MAIN_DATA.LocalCode,
                     SUM(CDR_MAIN_DATA.DURATION) AS DURATION,CDR_MAIN_DATA.Locationid,
                     COUNT(CDR_MAIN_DATA.LocalCode) AS CITY_AMOUNT,
                     SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                   FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
				   LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
				   LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                   ";
      break;
    case 5:
      $sql_str  = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS AMOUNT,CDR_MAIN_DATA.LocalCode,
                     SUM(CDR_MAIN_DATA.DURATION) AS DURATION,CDR_MAIN_DATA.Locationid,
                     COUNT(CDR_MAIN_DATA.LocalCode) AS CITY_AMOUNT,
                     SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                    FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
					LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
				   LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                    ";
      break;
    case 6:
      $sql_str  = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS AMOUNT,CDR_MAIN_DATA.CountryCode,
                     SUM(CDR_MAIN_DATA.DURATION) AS DURATION,CDR_MAIN_DATA.Locationid,
                     COUNT(CDR_MAIN_DATA.CountryCode) AS COUNTRY_AMOUNT,
                     SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                   FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
				   LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
				   LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                   ";
      break;
    case 7:
      $sql_str  ="SELECT COUNT(CDR_ID) AS AMOUNT,SUM(DURATION) AS DURATION,
                    CONCAT(CONCAT(DATE_FORMAT(TIME_STAMP,'%H'),'-'),DATE_FORMAT(DATE_ADD(TIME_STAMP, INTERVAL 1 HOUR),'%H')) AS TIME_INTERVAL,
                    SUM(PRICE) AS PRICE
                  FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
				  LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
				   LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                  ";
      break;
    case 8:
      $sql_str  ="SELECT COUNT(CDR_ID) AS AMOUNT,SUM(DURATION) AS DURATION,
                    TIME_STAMP_DAY,  SUM(PRICE) AS PRICE
                    FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA 
					LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
				   LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID 
                 ";
      break;
    case 9:
      $sql_str  ="SELECT COUNT(CDR_ID) AS AMOUNT,SUM(DURATION) AS DURATION,
                    TIME_STAMP_MONTH,  SUM(PRICE) AS PRICE
                    FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
					LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
				   LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                 ";
      break;
    case 10:
      $sql_str  = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS AMOUNT,
                     SUM(CDR_MAIN_DATA.DURATION) AS DURATION,
                     CDR_MAIN_DATA.AUTH_ID,SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                   FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
				   LEFT JOIN EXTENTIONS ON CDR_MAIN_DATA.ORIG_DN = EXTENTIONS.EXT_NO AND CDR_MAIN_DATA.SITE_ID = EXTENTIONS.SITE_ID
				   LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                   ";
      break;
    default:
  } 
  
//  $usr_crtd = str_replace("CDR_MAIN_DATA", "$CDR_MAIN_DATA", "$usr_crt");
  if ($kriter != "")
    $sql_str .= " WHERE ".$kriter." ".$usr_crtd;  
       
  $sql_str .= " GROUP BY ". $grp_type ;
//echo $sql_str;exit;
$order =""; //halil 22092020
    switch ($order){
    case '1':
      $sql_str .= " ORDER BY ".$field1_ord." ".$sort_type; 
      break;
    case '2':
      $sql_str .= " ORDER BY ".$field2_ord." ".$sort_type; 
      break;
    case '3':
      $sql_str .= " ORDER BY ".$field3_ord." ".$sort_type; 
      break;
    case '4':
      $sql_str .= " ORDER BY ".$field4_ord." ".$sort_type; 
      break;
    case '5':
      $sql_str .= " ORDER BY ".$field5_ord." ".$sort_type; 
      break;
    case '6':
      $sql_str .= " ORDER BY ".$field6_ord." ".$sort_type; 
      break;
    case '7':
      $sql_str .= " ORDER BY ".$field7_ord." ".$sort_type; 
      break;
    default:
         }

     if ($record<>'' ||is_numeric($record)) {
               $sql_str .= " LIMIT 0,". $record ;
         }
//echo $sql_str;exit;

    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
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
            title: "<? echo $header; ?>"
        },
        {
            extend: 'csvHtml5',
            title: "<? echo $header; ?>"
        }, 
            'print', {text: "email",
        action: function (e ,dt, node, config){
          location.href = "javascript:mailPage('inbound.html')";
        }} ]
		
    } );

    function doTable (target, name){

    var $row = $(target).closest("tr");
    var addVal = $(target).val();
    var data = $("#example").DataTable().rows($row).data()[0];
    data[name] = parseInt(addVal) + data[name];

    $("#export").DataTable().rows($row).invalidate().draw();
    }

	} );
	  
	</script>	
<br><br>
<TABLE BORDER="0" WIDTH="95%">
            <TR>
				<TD><a href="http://www.crystalinfo.com.tr" target="_blank"><img border="0" SRC="<?=IMAGE_ROOT?>logo2.gif" ></a></TD>
				<TD width="50%" align=center CLASS="header"><?echo $company;?><BR><?=$report_type?><br><?=$header?><BR>
          
				<?if($t0!=""){?>

				Tarih Aralığı (<?=date("d/m/Y",strtotime($t0))?>
				<?if($t1!=""){?>
				<?echo (" - ".date("d/m/Y",strtotime($t1)));}?>
				)<BR>

				<?}
   
				if($DEPT_ID || $D_ID == -2){?>
 
				Departman : <?echo get_dept_name($DEPT_ID,$SITE_ID);
				if($D_ID == -2 ) echo "Bir Departmana Bağlı Olmayan Dahililer";
				}?>
				</TD>
				<TD width="25%" align=right><img SRC="<?=IMAGE_ROOT?>company.gif"></TD>
			</TR>
            </TABLE>
            <br>
      <table id="export"class="display nowrap" style="width:100%">
      <thead>    
      <tr>
            <th><?echo $field1_name;?></th>
          <?if ($field2_name<>''){?>
            <th><?echo $field2_name;?></th>
          <?}?>
          <?if ($field3_name<>''){?>
            <th><?echo $field3_name;?></th>
          <?}?>
          <?if ($field4_name<>''){?>
            <th><?echo $field5_name;?></th>
          <?}?>
          <?if ($field5_name<>''){?>
            <th><?echo $field5_name;?></th>
          <?}?>
          <?if ($field6_name<>''){?>
            <th><?echo $field6_name;?></th>
          <?}?>
          <?if ($field7_name<>''){?>
            <th><?echo $field7_name;?></th>
          <?}?>
          </tr>
          </thead>
          <tbody>
      <? 
           $i=0;
           $j=1;
           $myrow = "row->$field1";
           $my_dur=0;
        $my_amount=0; 
        $my_pr=0; 
         if (mysqli_num_rows($result)>0)
           mysqli_data_seek($result,0);
         
         $row_count = mysqli_num_rows($result); //added by Yagmur

         while($row = mysqli_fetch_array($result)){
          $col_cnt=1;
          if ($row["$field1"] < 10){
            $row["$field1"] = "0" . $row["$field1"];
          }
                    $i++;                   $j++;
            echo " <td>".$row["$field1"]."</td>";
          if ($field2_name<>''){
             $def = get_def($field2,$row["$field2"]);
          //echo " <td>".$def."</td>";
		  echo " <td>".$row["$field2"]."</td>";
             }
          if ($field3_name<>''){
          echo " <td>".$row["$field3"]."</td>";
             }
             if ($field4_name<>''){
          echo " <td>".$row["$field4"]."</td>";
             }
            if ($field5_name<>''){
          echo " <td>".$row["$field5"]."</td>";
             }
            if ($field6_name<>''){
              if ($field6 = "DURATION"){
              echo " <td>".calculate_time($row["DURATION"],"hour")."  Saat  ".calculate_time($row["DURATION"],"min")."  Dk</td>";
              }else{
              echo " <td>".$row["$field6"]."</td>";  
              }
            }
            if ($field7_name<>''){
              if ($field7 = "PRICE"){
              echo " <td>".write_price($row["$field7"])."</td>";
              }else{
              echo " <td>".$row["$field7"]."</td>";
              }
             }
          echo "</tr>";
          $my_dur=$my_dur + $row["DURATION"];
          $my_amount=$my_amount + $row["AMOUNT"];
          $my_pr=$my_pr + $row["PRICE"];
          
         }
      ?>
      </tbody>
      <tfoot>
      <tr>
            <th  width="<?=$width1;?>"><?echo $field1_name;?></th>
          <?if ($field2_name<>''){?>
            <th  width="<?=$width2;?>"><?echo $field2_name;?></th>
          <?}?>
          <?if ($field3_name<>''){?>
            <th  width="<?=$width3;?>"><?echo $field3_name;?></th>
          <?}?>
          <?if ($field4_name<>''){?>
            <th  width="<?=$width4;?>"><?echo $field5_name;?></th>
          <?}?>
          <?if ($field5_name<>''){?>
            <th  width="<?=$width5;?>"><?echo $field5_name;?></th>
          <?}?>
          <?if ($field6_name<>''){?>
            <th  width="<?=$width6;?>"><?echo $field6_name;?></th>
          <?}?>
          <?if ($field7_name<>''){?>
            <th  width="<?=$width7;?>"><?echo $field7_name;?></th>
          <?}?>
          </tr>
        </tfoot>
      </table>
      <table>
  <tr>
    <td height="22" colspan="3" width="100%" align="right">
           <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD WIDTH="80%" ALIGN="left"><b>Toplam Görüşme Adedi :</b></TD>
              <TD WIDTH="20%" ><?=number_format($my_amount,0,'','.')?></TD>
<?                            $j++;$csv_data[$j][0] = "Toplam Görüşme Adedi :";?>
<?                            $csv_data[$j][1] = number_format($my_amount,0,'','.');?>
            </TR>
            </TABLE>
           <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD WIDTH="50%" ALIGN="left"><b>Toplam Süre :</b></TD>
              <TD WIDTH="50%" ><?=calculate_time($my_dur,"hour")."  Saat  ".calculate_time($my_dur,"min")."  Dk";?></TD>
<?                            $j++;$csv_data[$j][0] = "Toplam Süre :";?>
<?                            $csv_data[$j][1] = calculate_time($my_dur,"hour");?>
            </TR>
            </TABLE>
            <TABLE BORDER="0" WIDTH="100%">
            <TR>
            <TD WIDTH="80%" ALIGN="left"><b>Toplam Tutar :</b></TD>
              <TD WIDTH="20%" ><?=write_price($my_pr)?></TD>
<?                            $j++;$csv_data[$j][0] = "Toplam Tutar :";?>
<?                            $csv_data[$j][1] = write_price($my_pr);?>
            </TR>
            </TABLE>
<?}?>
 <?

 make_cache("/usr/local/wwwroot/multi.crystalinfo.net/root/reports/cache/ozet", $cache_status, $row_count);
 if($CSV_EXPORT == 2){
	 

   $fd = fopen($DOCUMENT_ROOT."/temp/general.xls", "w");
   fwrite($fd,ob_get_contents());
 }else{
   $fd = fopen($DOCUMENT_ROOT."/temp/general.html", "w");
   fwrite($fd,ob_get_contents());
 }
 	 
 ob_end_flush();
 csv_out($csv_data, $DOCUMENT_ROOT."/temp/general_calls.csv"); 

if($CSV_EXPORT==1){?>
 <iframe SRC="/csv_download.php?filename=general_calls.csv" WIDTH=0 HEIGHT=0 ></iframe>
 <a HREF="/temp/<?=$file_name?>general.csv">CSV Download</a>
<?}else if($CSV_EXPORT==2){?>
 <iframe SRC="/csv_download.php?filename=general.xls" WIDTH=0 HEIGHT=0 ></iframe>
 <a HREF="/temp/general.xls" style="font-size:20px";>Excel XLS Download</a>
 <?}?>
<br>
<br>


