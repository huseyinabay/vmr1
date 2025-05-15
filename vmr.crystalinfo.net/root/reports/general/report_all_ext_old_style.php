<?    
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	 require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/pagecache.php";
	 
	 // ini_set('display_errors', 'On');
      //error_reporting(E_ALL);
	   //error_reporting(E_ALL ^ E_NOTICE);
	 

     $cache_status = call_cache("/usr/local/wwwroot/multi.crystalinfo.net/root/reports/cache/ozet");
     ob_start();      

     $cUtility = new Utility();
     $cdb = new db_layer(); 
     $conn = $cdb->getConnection();
     $show_chart=false;
     require_valid_login();
	 
	 $kriter = "";
	 $ACCESS_CODE = "";
	 $forceMainTable = "";
	 $order ="";
	 $D_ID = "";
	 $alert = "";
      
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
     $my_time0 = $t0;
     $my_time1 = $t1;

     cc_page_meta();
     echo "<center>";
    
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
  
   $local_country_code = get_country_code($SITE_ID);//İlgili sitenin ülke kodu.

    function write_me($MyVal,$calc_type){
     if ($calc_type == 1){
       $MyRetVal = write_price($MyVal);
     }elseif($calc_type==2){
       $MyRetVal = calculate_all_time($MyVal);
     }elseif($calc_type==3){
       $MyRetVal = $MyVal;
     }else{
       print_error("Hatalı Durum Oluştu. Lütfen Tekrar Deneyiniz.");
      exit;
     }
     return $MyRetVal;
  }
  if($CSV_EXPORT != 2){
 ?>
   <form name="sort_me" method="post" action="">
         <input type="hidden" name="SITE_ID" value="<?=$SITE_ID?>">
         <input type="hidden" name="MY_DATE" value="<?=$MY_DATE?>">
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
         <input type="hidden" name="calc_type" value="<?=$calc_type?>">       
         <input type="hidden" name="DEPT_ID" value="<?=$DEPT_ID?>">  
        <input type="hidden" class="cache1" name="withCache" value="<?=$withCache?>" >
        <input type="hidden" name="forceMainTable" VALUE="<?=$forceMainTable?>" >         
         <input type="hidden" name="sort_type" value="<?=($sort_type=="asc")?"desc":"asc"?>">  
  </form>
  <?}?>
<script language="JavaScript">
  function submit_form(sortby){
    document.all('sort_me').action='/reports/general/report_call_ext.php?act=src&type=<?=$type?>&SITE_ID=<?=$SITE_ID?>&order=' + sortby;    
    document.all('sort_me').submit();
     }
  function drill_down(o_id){
    document.all('sort_me').action='/reports/outbound/report_outb_prn.php?act=src&type=<?=$type?>&SITE_ID=<?=$SITE_ID?>&ORIG_DN=' + o_id;    
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
   if($DEPT_ID==-2){$D_ID = $DEPT_ID;$DEPT_ID = "";}
   
   $report_type="Dahili Özet Raporu";
   if (($type=='ext') || ($type=='dept')){
       $myfld= "PRICE";
       $calc_type =1;   
   }elseif(($type=='ext_all') || ($type=='dept_time')){
       $myfld= "DURATION";
       $calc_type =2;   
   }else{
      print_error("Hatalı Durum Oluşu. Lütfen Tekrar Deneyiniz");
   exit;
   }

   if ($act == "src") {

             //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
    $kriter = $cdb->field_query($kriter,   "SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
    $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.  
	$kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  ">",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
    $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.

    //Zaman kriterleri ve tablo ismi seçimi başlangıç
    add_time_crt();//Zaman kriteri
	$link  ="";

     if($forceMainTable)
       $CDR_MAIN_DATA = "CDR_MAIN_DATA";
     else
       $CDR_MAIN_DATA = getTableName($t0,$t1);
      
     if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";  

    //Zaman kriterleri ve tablo ismi seçimi bitiş
	
    $header="Çağrıların Giden Ve Gelen Aramlara Göre Dağılımı";
	/*
    $sql_str  = "SELECT CDR_MAIN_DATA.LocationTypeid AS TYPE, 
				 LTRIM(CDR_MAIN_DATA.ORIG_DN) AS ORIG_DN, 
				 SUM(CDR_MAIN_DATA.DURATION) AS TOTAL,
				 COUNT(CDR_MAIN_DATA.CDR_ID)  AS COUNT_ID
                 FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                 ";
*/

    $sql_str_del="TRUNCATE TABLE `MCRYSTALINFONE`.`CDR_MAIN_ALL`";
    if (!($cdb->execute_sql($sql_str_del,$R1,$error_msg))){
        print_error($error_msg);
        exit;
    }
	
	//echo $sql_str_del;exit;
	
	    $sql_str_out=  "INSERT INTO CDR_MAIN_ALL 
						SELECT SITE_ID,REC_TYPE,ORIG_DN,TIME_STAMP_YEAR,TIME_STAMP_MONTH,TIME_STAMP_DAY,TIME_STAMP_HOUR,TIME_STAMP_MN,TIME_STAMP_SN,DURATION,DIGITS,
						CLID,CALL_TYPE,ACCESS_CODE,CountryCode,LocalCode,PURE_NUMBER,PRICE,ERR_CODE,TIME_STAMP,MY_DATE
						FROM $CDR_MAIN_DATA "; 
		$sql_str_out .= " WHERE ".$kriter." ".$usr_crt;
//echo $sql_str_out;exit;
    if (!($cdb->execute_sql($sql_str_out,$R2,$error_msg))){
        print_error($error_msg);
        exit;
    }
	
	
	     $sql_str_inb ="INSERT INTO CDR_MAIN_ALL 
						SELECT SITE_ID,REC_TYPE,ORIG_DN,TIME_STAMP_YEAR,TIME_STAMP_MONTH,TIME_STAMP_DAY,TIME_STAMP_HOUR,TIME_STAMP_MN,TIME_STAMP_SN,DURATION,DIGITS,
						CLID,CALL_TYPE,ACCESS_CODE,CountryCode,LocalCode,PURE_NUMBER,PRICE,ERR_CODE,TIME_STAMP,MY_DATE
						FROM CDR_MAIN_INB";  
		$sql_str_inb .= " WHERE ".$kriter." ".$usr_crt;
		
    if (!($cdb->execute_sql($sql_str_inb,$R3,$error_msg))){
        print_error($error_msg);
        exit;
    }

//	echo $sql_str_inb;exit;


	   $sql_str  = "SELECT
					LTRIM(ORIG_DN) AS ORIG_DN, 
					COUNT(IF(CALL_TYPE=1,1,NULL)) AS OUT_COUNT,
					SUM(CASE WHEN CALL_TYPE=1 THEN DURATION END) AS OUT_DUR, 
					SUM(CASE WHEN CALL_TYPE=1 THEN DURATION END)/COUNT(IF(CALL_TYPE=1,1,NULL)) AS OUT_AVRG,
					COUNT(IF(DURATION>'0' AND CALL_TYPE=2,1,NULL)) AS IN_COUNT,
					SUM(CASE WHEN CALL_TYPE=2 THEN DURATION END) AS IN_DUR,
					SUM(CASE WHEN CALL_TYPE=2 THEN DURATION END)/COUNT(IF(CALL_TYPE=1,1,NULL)) AS IN_AVRG, 
					COUNT(IF(DURATION='0' AND CALL_TYPE=2,1,NULL  )) AS ABANDON_COUNT,
					COUNT(CALL_TYPE) AS TOTAL_COUNT,   
					SUM(DURATION) AS TOTAL_DUR,    
					SUM(DURATION)/COUNT(SITE_ID) AS TOTAL_AVRG   
					FROM CDR_MAIN_ALL
                 ";


      if ($kriter != "")
            $sql_str .= " WHERE ".$kriter." ".$usr_crt;
     $sql_str .= " GROUP BY ORIG_DN";
	 

    //echo $sql_str;exit;

    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      $row_count = mysqli_num_rows($result); //added by Yagmur
      while($row = mysqli_fetch_object($result)){  
            $my_dept = get_orig_dept_id($row->ORIG_DN, $SITE_ID);
            if ($my_dept=="")     $my_dept = 0;
            if(($DEPT_ID && $DEPT_ID != '-1') || $D_ID){
               if($my_dept == $DEPT_ID){$datas[$my_dept][$row->ORIG_DN][10]=1;
              $datas[$my_dept][$row->ORIG_DN][0] += $row->OUT_COUNT;
              $datas[$my_dept][$row->ORIG_DN][1] += $row->OUT_DUR;
              $datas[$my_dept][$row->ORIG_DN][2] += $row->OUT_AVRG;
              $datas[$my_dept][$row->ORIG_DN][3] += $row->IN_COUNT;
              $datas[$my_dept][$row->ORIG_DN][4] += $row->IN_DUR;
              $datas[$my_dept][$row->ORIG_DN][5] += $row->IN_AVRG;
			  $datas[$my_dept][$row->ORIG_DN][6] += $row->ABANDON_COUNT;
			  $datas[$my_dept][$row->ORIG_DN][7] += $row->TOTAL_COUNT;
			  $datas[$my_dept][$row->ORIG_DN][8] += $row->TOTAL_DUR;
			  $datas[$my_dept][$row->ORIG_DN][9] += $row->TOTAL_AVRG;
                }
            }else{
                if($row->ORIG_DN != ""){$datas[$my_dept][$row->ORIG_DN][10]=1;
              $datas[$my_dept][$row->ORIG_DN][0] += $row->OUT_COUNT;
              $datas[$my_dept][$row->ORIG_DN][1] += $row->OUT_DUR;
              $datas[$my_dept][$row->ORIG_DN][2] += $row->OUT_AVRG;
              $datas[$my_dept][$row->ORIG_DN][3] += $row->IN_COUNT;
              $datas[$my_dept][$row->ORIG_DN][4] += $row->IN_DUR;
              $datas[$my_dept][$row->ORIG_DN][5] += $row->IN_AVRG;
			  $datas[$my_dept][$row->ORIG_DN][6] += $row->ABANDON_COUNT;
			  $datas[$my_dept][$row->ORIG_DN][7] += $row->TOTAL_COUNT;
			  $datas[$my_dept][$row->ORIG_DN][8] += $row->TOTAL_DUR;
			  $datas[$my_dept][$row->ORIG_DN][9] += $row->TOTAL_AVRG;
                }else{
                $datas[0][0][0] += $row->OUT_COUNT;
                $datas[0][0][1] += $row->OUT_DUR;
                $datas[0][0][2] += $row->OUT_AVRG;
                $datas[0][0][3] += $row->IN_COUNT;
				$datas[0][0][4] += $row->IN_DUR;
				$datas[0][0][5] += $row->IN_AVRG;
				$datas[0][0][6] += $row->ABANDON_COUNT;
				$datas[0][0][7] += $row->TOTAL_COUNT;
				$datas[0][0][8] += $row->TOTAL_DUR;
				$datas[0][0][9] += $row->TOTAL_AVRG;
                }
            }
        }
//print_r ($datas);die;	
?>



<br><br>
<table width="95%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" width="100%" align="center" class="rep_header" align="center">
<?if($CSV_EXPORT != 2){?>
          <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD><a href="http://www.crystalinfo.net" target="_blank"><img border="0" SRC="<?=IMAGE_ROOT?>logo2.gif" ></a></TD>
              <TD width="50%" align=center CLASS="header"><?echo $company;?><BR><?=$report_type?><br><?=$header?></TD>
              <TD width="25%" align=right><img SRC="<?=IMAGE_ROOT?>company.gif"></TD>
            </TR>
            </TABLE>
<?}?>
      </td>
  </tr>
   <?if($t0!=""){?>
  <tr>
    <td width="100%" class="rep_header" align="right">Tarih (<?=date("d/m/Y",strtotime($t0))?>
       <?if($t1!=""){?>
         <?echo (" - ".date("d/m/Y",strtotime($t1)));}?>
    )</td>
  </tr>
   <?}?>
   <?if($DEPT_ID || $D_ID == -2){?>
  <tr>
    <td width="100%" class="rep_header" align="left">Departman :
         <?echo get_dept_name($DEPT_ID,$SITE_ID);
            if($D_ID == -2 ) echo "Bir Departmana Bağlı Olmayan Dahililer";
            ?>
    </td>
  </tr>
   <?}?>
    <tr><td colspan=2 align=right>
    <?if($CSV_EXPORT<>2){?>
      <table cellspacing=0 cellpadding=0>
        <tr>
          <td><img src="<?=IMAGE_ROOT?>report/top02.gif" border=0></td>
          <td><a href="javascript:mailPage('general.html')"><img src="<?=IMAGE_ROOT?>report/mail.gif" border=0 title="Mail"></a></td>
          <td><a href="javascript:history.back(1);"><img src="<?=IMAGE_ROOT?>report/geri.gif" border=0 title="Geri"></a></td>
          <td><a href="javascript:history.forward(1);"><img src="<?=IMAGE_ROOT?>report/ileri.gif" border=0 title="İleri"></a></td>
          <td><a href="javascript:document.all('sort_me').submit();"><img src="<?=IMAGE_ROOT?>report/yenile.gif" border=0 title="Yenile"></a></td>
          <td><a href="javascript:window.print();"><img src="<?=IMAGE_ROOT?>report/print.gif" border=0 title="Yazdır"></a></td>
          <td><img src="<?=IMAGE_ROOT?>report/top01.gif" border=0></td>
        </tr>
      </table>
    <?}?>        
    </td></tr>
  <tr>
    <td colspan="2">
      <table width="100%" border="0" bgcolor="#C7C7C7" cellspacing="1" cellpadding="0">
            <?
            if($sort_type=="desc")
                $sort_gif = "report/top.gif";    
            else
                $sort_gif = "report/down.gif";
            ?>
          <tr>
<?if($CSV_EXPORT==2){?>
                <td class="rep_table_header" width="12%">Dahili</td>
				<td class="rep_table_header" width="10%">Departman</td>
                <td class="rep_table_header" width="5%">Giden<br> Çağrı<br> Adet</td>
                <td class="rep_table_header" width="10%">Giden<br> Çağrı<br> Süre</td>
                <td class="rep_table_header" width="10%">Giden<br> Çağrı<br> Ortalama Süre</td>
                <td class="rep_table_header" width="5%">Gelen<br> Çağrı<br> Adet</td>
                <td class="rep_table_header" width="10%">Gelen<br> Çağrı<br> Süre</td>
                <td class="rep_table_header" width="10%">Gelen<br> Çağrı<br> Ortalama Süre</td>
				<td class="rep_table_header" width="5%">Cevapsız<br> Çağrı</td>
				<td class="rep_table_header" width="5%">Toplam<br>  Çağrı<br> Sayısı</td>
				<td class="rep_table_header" width="10%">Toplam<br> Çağrı<br> Süresi</td>
				<td class="rep_table_header" width="10%">Çağrı<br> Süresi<br> Ortalaması</td>
<?}else{?>
                <td class="rep_table_header" width="12%">Dahili<a style="cursor:hand;" onclick="javascript:submit_form('8');"></a></td>
                <td class="rep_table_header" width="10%">Departman<a style="cursor:hand;" onclick="javascript:submit_form('8');"></a></td>
				<td class="rep_table_header" align="center" width="5%">Giden<br> Çağrı<br> Adet<a style="cursor:hand;" onclick="javascript:submit_form('0');"></a></td>
                <td class="rep_table_header" align="center" width="10%">Giden<br> Çağrı<br> Süre<a style="cursor:hand;" onclick="javascript:submit_form('1');"></a></td>
                <td class="rep_table_header" align="center" width="10%">Giden<br> Çağrı<br> Ortalama Süre<a style="cursor:hand;" onclick="javascript:submit_form('2');"></a></td>
                <td class="rep_table_header" align="center" width="5%">Gelen<br> Çağrı<br> Adet<a style="cursor:hand;" onclick="javascript:submit_form('3');"></a></td>
                <td class="rep_table_header" align="center" width="10%">Gelen<br> Çağrı<br> Süre<a style="cursor:hand;" onclick="javascript:submit_form('4');"></a></td>
                <td class="rep_table_header" align="center" width="10%">Gelen<br> Çağrı<br> Ortalama Süre<a style="cursor:hand;" onclick="javascript:submit_form('6');"></a></td>
				<td class="rep_table_header" align="center" width="5%">Cevapsız<br> Çağrı<a style="cursor:hand;" onclick="javascript:submit_form('6');"></a></td>
				<td class="rep_table_header" align="center" width="5%">Toplam<br> Çağrı<br> Sayısı<a style="cursor:hand;" onclick="javascript:submit_form('6');"></a></td>
				<td class="rep_table_header" align="center" width="10%">Toplam<br> Çağrı<br> Süresi<a style="cursor:hand;" onclick="javascript:submit_form('6');"></a></td>
				<td class="rep_table_header" align="center" width="10%">Çağrı<br> Süresi<br> Ortalaması<a style="cursor:hand;" onclick="javascript:submit_form('6');"></a></td>
<?}?>
          </tr>
        <tr>
          <td colspan="12" bgcolor="#000000" height="1"></td>
        </tr>
      <? 
         $i = 0;;
		
		$my_gid_ad = 0;
		$my_gid_sur = 0;
		$my_gel_ad = 0;
		$my_gel_sur = 0;
		$my_pr_ad = 0;
		$my_pr = 0;
		
                $csv_data[0][0] = "Dahili";
				$csv_data[0][1] = "Departman";
                $csv_data[0][2] = "Giden Çağrı Adet";
                $csv_data[0][3] = "Giden Çağrı Süre" ;
                $csv_data[0][4] = "Giden Çağrı Ortalama Süre";
                $csv_data[0][5] = "Gelen Çağrı Adet";
                $csv_data[0][6] = "Gelen Çağrı Süre";
                $csv_data[0][7] = "Gelen Çağrı Ortalama Süre";
				$csv_data[0][8] = "Cevapsız Çağrı";
				$csv_data[0][9] = "Toplam Çağrı Sayısı";
				$csv_data[0][9] = "Toplam Çağrı Süresi";
				$csv_data[0][9] = "Çağrı Süresi Ortalaması";
             
            $m = 0;
            $j = 0;
         if(is_array($datas)){    
            foreach($datas as $key=>$value){
            if ($key>0) $my_dept_name = get_dept_name($key,$SITE_ID);
			else $my_dept_name = "Kaydedilmemis Dahililer";
            echo  " <tr  bgcolor=\"FFFFFF\"><td class=\"rep_td\" colspan=12>&nbsp;<b>".$my_dept_name."</b></td></tr>\n";
            unset($dept_totals);
			unset($dept_tot_adet);
            $i = 0;
            $j++;
            $csv_data[$j][0] = $my_dept_name;
			//$csv_data[$j][1] = $my_dept_name;
            foreach($value as $keynew=>$valuenew){
                   $i++;$j++;
                   $bg_color = "E4E4E4";   
                   if($i%2) $bg_color ="FFFFFF";
                 echo " <tr  BGCOLOR=$bg_color>\n";
                   $k_x = ($keynew=="0"?"Dahili Yok":$keynew);
                   $k_y = ($keynew=="0"?"-2":$keynew);
                   
                   echo  " <td class=\"rep_td\">&nbsp;";
                   if($CSV_EXPORT==2){
                     echo  "<b>".$k_x."</b> - ".get_ext_name2($keynew, $SITE_ID)."</td>\n";
                   }else{
                     echo  "<a class=\"a1\" HREF=\"javascript:drill_down('$k_y')\"><b>".$k_x."</b> - ".get_ext_name2($keynew, $SITE_ID)."</a></td>\n";
                   }  
                   $total = 0;
				   $total_giden = 0;
				   $total_gid_sure = 0;
				   $total_gelen = 0;
				   $total_gel_sure = 0;
				   $total_abandon = 0;
				   $total_adet = 0;
				   
             $csv_data[$j][0] =  $k_x." - ".get_ext_name2($keynew, $SITE_ID);
			 
			$csv_data[$j][1] =  $my_dept_name;										//yeni satır halil
			echo  "<td class=\"rep_td\" align=\"left\">".$my_dept_name."</td>";	//yeni satır halil
			
                
                   //Giden adet hesaplama
				    for($k=0;$k<=0;$k++){
                       echo " <td class=\"rep_td\" align=\"center\">".$value[$keynew][$k]."</td>\n";
                       $total_giden += $value[$keynew][$k];
                 $csv_data[$j][$k+1] =  $value[$keynew][$k];
                       $dept_tot_adet[$k] += $value[$keynew][$k];
                   }

					//Giden sure
					for($k=1;$k<=1;$k++){
                       echo " <td class=\"rep_td\" align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
					$total_gid_sure += $value[$keynew][$k];
                 $csv_data[$j][$k+1] =  write_me($value[$keynew][$k],$calc_type);
                       $dept_totals[$k] += $value[$keynew][$k];
                   }
				   
				   
				   	//Giden ortalama sure
					for($k=2;$k<=2;$k++){
                       echo " <td class=\"rep_td\" align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
                //       $total += $value[$keynew][$k];
                 $csv_data[$j][$k+1] =  write_me($value[$keynew][$k],$calc_type);
                       $dept_totals[$k] += $value[$keynew][$k];
                   }
				   
				    //Gelen adet hesaplama
				   for($k=3;$k<=3;$k++){
                       echo " <td class=\"rep_td\" align=\"center\">".$value[$keynew][$k]."</td>\n";
                       $total_gelen += $value[$keynew][$k];
                 $csv_data[$j][$k+1] =  $value[$keynew][$k];
                       $dept_tot_adet[$k] += $value[$keynew][$k];
                   }
				   
				   //Gelen sure 
				   for($k=4;$k<=4;$k++){
                       echo " <td class=\"rep_td\" align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
                    $total_gel_sure += $value[$keynew][$k];
                 $csv_data[$j][$k+1] =  write_me($value[$keynew][$k],$calc_type);
                       $dept_totals[$k] += $value[$keynew][$k];
                   }
				   
				  //Ortalama sure
				   for($k=5;$k<=5;$k++){
                       echo " <td class=\"rep_td\" align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
                    //   $total += $value[$keynew][$k];
                 $csv_data[$j][$k+1] =  write_me($value[$keynew][$k],$calc_type);
                       $dept_totals[$k] += $value[$keynew][$k];
                   }
				   
				   
				   	//Gelen Abandon 
				   for($k=6;$k<=6;$k++){
                       echo " <td class=\"rep_td\" align=\"center\">".$value[$keynew][$k]."</td>\n";
                     $total_abandon += $value[$keynew][$k];
                 $csv_data[$j][$k+1] =  $value[$keynew][$k];
                       $dept_tot_adet[$k] += $value[$keynew][$k];
                   }
				   
				   
				  //Gelen Toplam Adet
				   for($k=7;$k<=7;$k++){
                       echo " <td class=\"rep_td\" align=\"center\">".$value[$keynew][$k]."</td>\n";
                     $total_adet += $value[$keynew][$k];
                 $csv_data[$j][$k+1] =  $value[$keynew][$k];
                       $dept_tot_adet[$k] += $value[$keynew][$k];
                   }

				   
				  // Toplam Sure
				    for($k=8;$k<=8;$k++){
                       echo " <td class=\"rep_td\" align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
                       $total += $value[$keynew][$k];
                 $csv_data[$j][$k+1] =  write_me($value[$keynew][$k],$calc_type);
                       $dept_totals[$k] += $value[$keynew][$k];
                   }
				  
				  // Ortalama sure
				    for($k=9;$k<=9;$k++){
                       echo " <td class=\"rep_td\" align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
                      // $total += $value[$keynew][$k];
                 $csv_data[$j][$k+1] =  write_me($value[$keynew][$k],$calc_type);
                       $dept_totals[$k] += $value[$keynew][$k];
                   }
				   

              $csv_data[$j][10] =  write_me($total,$calc_type);
                 
				 $my_gid_ad = $my_gid_ad + $total_giden;
				 $my_gid_sur = $my_gid_sur + $total_gid_sure;
				 $my_gel_ad = $my_gel_ad + $total_gelen;
				 $my_gel_sur = $my_gel_sur + $total_gel_sure;
				 $my_pr_ab = $my_pr_ab + $total_abandon;
				 $my_pr_ad = $my_pr_ad + $total_adet;
				 $my_pr = $my_pr + $total;
				 
                   $m++;
            }       


            $j++;
            $csv_data[$j][0] = "Alt Toplamlar";  
			
			echo  " <tr bgcolor=\"E8E4E4\"><td class=\"rep_td\">&nbsp;<b>Alt Toplamlar</b></td>\n";
			echo "<td></td>\n"; // yeni eklendi halil
			
			// Giden Adet Toplam
			for($k=0;$k<=0;$k++){
               echo " <td class=\"rep_td\" align=\"center\"><b>".$dept_tot_adet[$k]."</b></td>\n";
               $csv_data[$j][$k+1] =  $dept_tot_adet[$k];
               }
			
			//sure
               for($k=1;$k<=2;$k++){

                   echo " <td class=\"rep_td\" align=\"center\"><b>".write_me($dept_totals[$k],$calc_type)."</b></td>\n";
               $csv_data[$j][$k+1] =  write_me($dept_totals[$k],$calc_type);
               }
			   
			//adet
			   for($k=3;$k<=3;$k++){

               echo " <td class=\"rep_td\" align=\"center\"><b>".$dept_tot_adet[$k]."</b></td>\n";
               $csv_data[$j][$k+1] =  $dept_tot_adet[$k];
               }
			   
			   
			 //sure
			   for($k=4;$k<=5;$k++){

                   echo " <td class=\"rep_td\" align=\"center\"><b>".write_me($dept_totals[$k],$calc_type)."</b></td>\n";
               $csv_data[$j][$k+1] =  write_me($dept_totals[$k],$calc_type);
               }
			   
			  //adet 
			   for($k=6;$k<=7;$k++){

               echo " <td class=\"rep_td\" align=\"center\"><b>".$dept_tot_adet[$k]."</b></td>\n";
               $csv_data[$j][$k+1] =  $dept_tot_adet[$k];
               }
			   
			  //sure 
			   for($k=8;$k<=9;$k++){

                   echo " <td class=\"rep_td\" align=\"center\"><b>".write_me($dept_totals[$k],$calc_type)."</b></td>\n";
               $csv_data[$j][$k+1] =  write_me($dept_totals[$k],$calc_type);
               }
			   
			   
            echo "</tr>";   
			
            echo  " <tr bgcolor=\"000000\"><td colspan=12 height=\"1\" align=center valign=center></td></tr>\n";   
      }
          }  
          $j++;
      ?>
      </table>
  <tr height="20">
    <td></td>
  </tr>
  <tr>
    <td height="22" colspan="3" width="100%" align="right">
         <TABLE BORDER="0" WIDTH="100%">
            <TR>
			<TD WIDTH="10%" ALIGN="left"><b>Genel Toplamlar -></b></TD>
			<TD WIDTH="10%" ALIGN="left"><b>Giden Çağrı Sayısı : <?=write_me($my_gid_ad,3)?></b></TD>
			<TD WIDTH="10%" ALIGN="left"><b>Giden Görüşme Süresi : <?=write_me($my_gid_sur,$calc_type)?></b></TD>
			<TD WIDTH="10%" ALIGN="left"><b>Gelen Çağrı Sayısı : <?=write_me($my_gel_ad,3)?></b></TD>
			<TD WIDTH="10%" ALIGN="left"><b>Gelen Görüşme Süresi : <?=write_me($my_gel_sur,$calc_type)?></b></TD>
			<TD WIDTH="10%" ALIGN="left"><b>Toplam Cevapsız Sayısı : <?=write_me($my_pr_ab,3)?></b></TD>
			<TD WIDTH="10%" ALIGN="left"><b>Toplam Çağrı Sayısı : <?=write_me($my_pr_ad,3)?></b></TD>
            <TD WIDTH="10%" ALIGN="left"><b>Toplam Görüşme Süresi : <?=write_me($my_pr,$calc_type)?></b></TD>


                <?$i++;$csv_data[$j][0] =  "Toplam";?>
                <?$csv_data[$j][1] =  write_me($my_pr,$calc_type)?>
            </TR>
         </TABLE>
      </td>
  </tr>
    <tr>
        <td><?echo $alert;?></td>
    </tr>
</table>  

<?}?>
<?
 make_cache("/usr/local/wwwroot/multi.crystalinfo.net/root/reports/cache/ozet", $cache_status, $row_count);
 
 if($CSV_EXPORT==2){
   $fd = fopen($DOCUMENT_ROOT."/temp/ext_disps.xls", "w");
   fwrite($fd,ob_get_contents());
 }else{
   $fd = fopen($DOCUMENT_ROOT."/temp/ext_disps.html", "w");
   fwrite($fd,ob_get_contents());
 }
 ob_end_flush();

 csv_out($csv_data, $DOCUMENT_ROOT."/temp/ext_disps.csv"); 
 
 if($CSV_EXPORT==1){?>
 <iframe SRC="/csv_download.php?filename=ext_disps.csv" WIDTH=0 HEIGHT=0 ></iframe>
  <a HREF="/temp/<?=$file_name?>ext_disps.csv">CSV Download</a>
 <?}else if($CSV_EXPORT==2){?>
 <iframe SRC="/csv_download.php?filename=ext_disps.xls" WIDTH=0 HEIGHT=0 ></iframe>
  <a HREF="/temp/ext_disps.xls">XLS Download</a>
 <?}?>
<br>
<br>


