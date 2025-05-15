<?    
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	 require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/pagecache.php";
 
	 // ini_set('display_errors', 'On');
    //  error_reporting(E_ALL);
	 // error_reporting(E_ALL ^ E_NOTICE);

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
    
    $sql_str1="SELECT SITE_NAME, SWITCH_LOG_TYPE_ID, MAX_ACCE_DURATION FROM SITES WHERE SITE_ID = ".$SITE_ID; 
    if (!($cdb->execute_sql($sql_str1,$result1,$error_msg))){
        print_error($error_msg);
        exit;
    }
    if (mysqli_num_rows($result1)>0){
    $row1 = mysqli_fetch_object($result1);
        $company = $row1->SITE_NAME;
		$switch_id = $row1->SWITCH_LOG_TYPE_ID;
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

    $sql_str_del="TRUNCATE TABLE `MCRYSTALINFONE`.`CDR_MAIN_ALL`";
    if (!($cdb->execute_sql($sql_str_del,$R1,$error_msg))){
        print_error($error_msg);
        exit;
    }
	
	//echo $sql_str_del;exit;
	
	$usr_crtd = str_replace("CDR_MAIN_DATA", "$CDR_MAIN_DATA", "$usr_crt");
	
	    $sql_str_out=  "INSERT INTO CDR_MAIN_ALL 
						SELECT SITE_ID,REC_TYPE,ORIG_DN,TIME_STAMP_YEAR,TIME_STAMP_MONTH,TIME_STAMP_DAY,TIME_STAMP_HOUR,TIME_STAMP_MN,TIME_STAMP_SN,DURATION,DIGITS,
						CLID,CALL_TYPE,ACCESS_CODE,CountryCode,LocalCode,PURE_NUMBER,PRICE,ERR_CODE,TIME_STAMP,MY_DATE
						FROM $CDR_MAIN_DATA "; 
		$sql_str_out .= " WHERE ".$kriter." ".$usr_crtd;
 // echo $sql_str_out;exit;
    if (!($cdb->execute_sql($sql_str_out,$R2,$error_msg))){
        print_error($error_msg);
        exit;
    }
	
	$usr_crti = str_replace("CDR_MAIN_DATA", "CDR_MAIN_INB", "$usr_crt");
	
	     $sql_str_inb ="INSERT INTO CDR_MAIN_ALL 
						SELECT SITE_ID,REC_TYPE,ORIG_DN,TIME_STAMP_YEAR,TIME_STAMP_MONTH,TIME_STAMP_DAY,TIME_STAMP_HOUR,TIME_STAMP_MN,TIME_STAMP_SN,DURATION,DIGITS,
						CLID,CALL_TYPE,ACCESS_CODE,CountryCode,LocalCode,PURE_NUMBER,PRICE,ERR_CODE,TIME_STAMP,MY_DATE
						FROM CDR_MAIN_INB";  
		$sql_str_inb .= " WHERE ".$kriter." ".$usr_crti;
		
    if (!($cdb->execute_sql($sql_str_inb,$R3,$error_msg))){
        print_error($error_msg);
        exit;
    }

//	echo $sql_str_inb;exit;

	//echo $switch_id;
	//$REC_TYPE = "OU";
	
	$IN_ABANDON = "COUNT(IF(DURATION='0' AND CALL_TYPE=2,2,NULL )) AS IN_ABANDON,";
	$OUT_ABANDON = "COUNT(IF(DURATION='0' AND CALL_TYPE=1,1,NULL )) AS OUT_ABANDON,";

	if ($switch_id == "25"){
	$IN_ABANDON = "COUNT(IF(REC_TYPE='R' AND CALL_TYPE=2,2,NULL )) AS IN_ABANDON,"; 
	$OUT_ABANDON = "COUNT(IF(REC_TYPE='OU' AND CALL_TYPE=1,1,NULL )) AS OUT_ABANDON,";
	}
	if ($switch_id == "3" || "4" ||  "14" ||  "19" || "20" ||  "26"){
	$IN_ABANDON = "COUNT(IF(REC_TYPE='R' OR REC_TYPE='G' AND CALL_TYPE=2,2,NULL )) AS IN_ABANDON,";
	$OUT_ABANDON = "COUNT(IF(REC_TYPE='O' AND DURATION='0' AND CALL_TYPE=1,1,NULL )) AS OUT_ABANDON,";
	}
	
	$usr_crta = str_replace("CDR_MAIN_DATA", "CDR_MAIN_ALL", "$usr_crt");
	   $sql_str  = "SELECT
					LTRIM(ORIG_DN) AS ORIG_DN, 
					COUNT(IF(CALL_TYPE=1,1,NULL)) AS OUT_COUNT,
					$OUT_ABANDON 
					SUM(CASE WHEN CALL_TYPE=1 THEN DURATION END) AS OUT_DUR, 
					SUM(CASE WHEN CALL_TYPE=1 THEN DURATION END)/COUNT(IF(CALL_TYPE=1,1,NULL)) AS OUT_AVRG,
					COUNT(IF(CALL_TYPE=2,2,NULL)) AS IN_COUNT,
					$IN_ABANDON 
					SUM(CASE WHEN CALL_TYPE=2 THEN DURATION END) AS IN_DUR,
					SUM(CASE WHEN CALL_TYPE=2 THEN DURATION END)/COUNT(IF(CALL_TYPE=1,1,NULL)) AS IN_AVRG, 
					COUNT(CALL_TYPE) AS TOTAL_COUNT,   
					SUM(DURATION) AS TOTAL_DUR,    
					SUM(DURATION)/COUNT(SITE_ID) AS TOTAL_AVRG   
					FROM CDR_MAIN_ALL
                 ";


      if ($kriter != "")
            $sql_str .= " WHERE ".$kriter." ".$usr_crta;
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
			  $datas[$my_dept][$row->ORIG_DN][6] += $row->IN_ABANDON;
			  $datas[$my_dept][$row->ORIG_DN][7] += $row->TOTAL_COUNT;
			  $datas[$my_dept][$row->ORIG_DN][8] += $row->TOTAL_DUR;
			  $datas[$my_dept][$row->ORIG_DN][9] += $row->TOTAL_AVRG;
			  $datas[$my_dept][$row->ORIG_DN][11] += $row->OUT_ABANDON;
                }
            }else{
                if($row->ORIG_DN != ""){$datas[$my_dept][$row->ORIG_DN][10]=1;
              $datas[$my_dept][$row->ORIG_DN][0] += $row->OUT_COUNT;
              $datas[$my_dept][$row->ORIG_DN][1] += $row->OUT_DUR;
              $datas[$my_dept][$row->ORIG_DN][2] += $row->OUT_AVRG;
              $datas[$my_dept][$row->ORIG_DN][3] += $row->IN_COUNT;
              $datas[$my_dept][$row->ORIG_DN][4] += $row->IN_DUR;
              $datas[$my_dept][$row->ORIG_DN][5] += $row->IN_AVRG;
			  $datas[$my_dept][$row->ORIG_DN][6] += $row->IN_ABANDON;
			  $datas[$my_dept][$row->ORIG_DN][7] += $row->TOTAL_COUNT;
			  $datas[$my_dept][$row->ORIG_DN][8] += $row->TOTAL_DUR;
			  $datas[$my_dept][$row->ORIG_DN][9] += $row->TOTAL_AVRG;
			  $datas[$my_dept][$row->ORIG_DN][11] += $row->OUT_ABANDON;
                }else{
                $datas[0][0][0] += $row->OUT_COUNT;
                $datas[0][0][1] += $row->OUT_DUR;
                $datas[0][0][2] += $row->OUT_AVRG;
                $datas[0][0][3] += $row->IN_COUNT;
				$datas[0][0][4] += $row->IN_DUR;
				$datas[0][0][5] += $row->IN_AVRG;
				$datas[0][0][6] += $row->IN_ABANDON;
				$datas[0][0][7] += $row->TOTAL_COUNT;
				$datas[0][0][8] += $row->TOTAL_DUR;
				$datas[0][0][9] += $row->TOTAL_AVRG;
				$datas[0][0][11] += $row->OUT_ABANDON;
                }
            }
        }
		
	if($MY_DATE == "f"){
	$t1 = trim($t1);
	$startDateArray = explode('/',$t1);
	$mysqlStartDate = $startDateArray[2]."-".$startDateArray[1]."-".$startDateArray[0];
	$t1 = $mysqlStartDate;
	}

	if($MY_DATE == "b"){
	$t1 = "";
	}

?>

			<TABLE BORDER="0" WIDTH="95%">
            <TR>
				<TD><a href="http://www.crystalinfo.com.tr" target="_blank"><img border="0" SRC="<?=IMAGE_ROOT?>logo2.gif" ></a></TD>
				<TD width="50%" align=center CLASS="header"><?echo $company;?><BR><?=$report_type?><br><?=$header?><BR>
          
				<?if($t0!=""){?>

				Tarih Aralığı (<?=date("d/m/Y",strtotime($t0))?>
				<?if($t1!=""){?>
				<?
				echo (" - ".date("d/m/Y",strtotime($t1)));}?>
						
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
			
	
</BR>

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
        buttons: ['copy', 'csv', 'excel', 'print' ]
		
    } );
	} );
	  
	</script>	


<table id="export" class="display nowrap" style="width:100%">
        <thead>
            <tr>
				<th>Dahili</th>
				<th>Açıklama</th>
                <th>Departman</th>
				<th>Giden Çağrı Sayısı</th>
				<th>Giden Çağrı Cevapsız</th>
                <th>Giden Çağrı Süre</th>
                <th>Giden Çağrı Ortalama Süre</th>
                <th>Gelen Çağrı Sayısı</th>
				<th>Gelen Çağrı Cevapsız</th>
                <th>Gelen Çağrı Süre</th>
                <th>Gelen Çağrı Ortalama Süre</th>
				<th>Toplam Çağrı Sayısı</th>
				<th>Toplam Çağrı Süresi</th>
				<th>Çağrı Süresi Ortalaması</th>
            </tr>
        </thead>
        <tbody>
		
	<? 
         $i = 0;
         $m = 0;
         $j = 0;
		 
         if(is_array($datas)){    
            foreach($datas as $key=>$value){
            if ($key>0) $my_dept_name = get_dept_name($key,$SITE_ID);
			else $my_dept_name = "Kaydedilmemis Dahililer";
            unset($dept_totals);
			unset($dept_tot_adet);
            $i = 0;
            $j++;
            $csv_data[$j][0] = $my_dept_name;
            foreach($value as $keynew=>$valuenew){
                   $i++;$j++;
                   $bg_color = "E4E4E4";   
                   if($i%2) $bg_color ="FFFFFF";
                 echo " <tr>\n";
                   $k_x = ($keynew=="0"?"Dahili Yok":$keynew);
                   $k_y = ($keynew=="0"?"-2":$keynew);

                // echo  "<td>".$k_x." - ".get_ext_name2($keynew, $SITE_ID)."</td>\n";
				
			     echo  "<td>".$k_x."</td>\n";    // Dahili no
				 echo  "<td>".get_ext_name2($keynew, $SITE_ID)."</td>\n";   // Aciklama
			   
            // $csv_data[$j][0] =  $k_x." - ".get_ext_name2($keynew, $SITE_ID);
			 
			//$csv_data[$j][1] =  $my_dept_name;										//yeni satır halil
			echo  "<td>".$my_dept_name."</td>";	//yeni satır halil
                
                   //Giden adet hesaplama
				    for($k=0;$k<=0;$k++){ echo " <td align=\"center\">".$value[$keynew][$k]."</td>\n";
					$total_giden += $value[$keynew][$k];
                   }
				   
				    //Giden Abandon 
				   for($k=11;$k<=11;$k++){ echo " <td align=\"center\">".$value[$keynew][$k]."</td>\n";
				   $out_abandon += $value[$keynew][$k];
                   }

					//Giden sure
					for($k=1;$k<=1;$k++){ echo " <td align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
					$total_gid_sure += $value[$keynew][$k];
                   }
				   
				   	//Giden ortalama sure
					for($k=2;$k<=2;$k++){ echo " <td align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
					$total_gid_ort_sure += $value[$keynew][$k];
                   }
			   
				    //Gelen adet hesaplama
				   for($k=3;$k<=3;$k++){ echo " <td align=\"center\">".$value[$keynew][$k]."</td>\n";
				   $total_gelen += $value[$keynew][$k];
                   }
				      				   
				   	//Gelen Abandon 
				   for($k=6;$k<=6;$k++){ echo " <td align=\"center\">".$value[$keynew][$k]."</td>\n";
				   $in_abandon += $value[$keynew][$k];
                   }
		   
				   //Gelen sure 
				   for($k=4;$k<=4;$k++){ echo " <td align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
				   $total_gel_sure += $value[$keynew][$k];
                   }
				   
				  //Gelen Ortalama sure
				   for($k=5;$k<=5;$k++){ echo " <td align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
				   $total_gel_ort_sure += $value[$keynew][$k];
                   }
	   
				  //Gelen Toplam Adet
				   for($k=7;$k<=7;$k++){ echo " <td align=\"center\">".$value[$keynew][$k]."</td>\n";
				   $total_adet += $value[$keynew][$k];
                   }
				   
				  // Toplam Sure
				    for($k=8;$k<=8;$k++){ echo " <td align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
					$total += $value[$keynew][$k];
                   }
				  
				  // Total Ortalama sure
				    for($k=9;$k<=9;$k++){ echo " <td align=\"center\">".write_me($value[$keynew][$k],$calc_type)."</td>\n";
					$total_ort_sure += $value[$keynew][$k];
				
                   }

                   $m++;
            }       

            $j++;

         echo "</tr>";   
          
      }
          }  
          $j++;

			$total_gid_ort_sure = $total_gid_ort_sure/$m;
			$total_gel_ort_sure = $total_gel_ort_sure/$m;
			$total_ort_sure = $total_ort_sure/$m;
		  
		  ?>
			
  </tbody>
        <tfoot>
            <tr>
				<th>Dahili</th>
				<th>Açıklama</th>
                <th>Departman</th>
				<th style="text-align:center"><?=write_me($total_giden,3)?></th>
				<th style="text-align:center"><?=write_me($out_abandon,3)?></th>
                <th style="text-align:center"><?=write_me($total_gid_sure,$calc_type)?></th>
				<th style="text-align:center"><?=write_me($total_gid_ort_sure,$calc_type)?></th>
                <th style="text-align:center"><?=write_me($total_gelen,3)?></th>
				<th style="text-align:center"><?=write_me($in_abandon,3)?></th>
                <th style="text-align:center"><?=write_me($total_gel_sure,$calc_type)?></th>
                <th style="text-align:center"><?=write_me($total_gel_ort_sure,$calc_type)?></th>
				<th style="text-align:center"><?=write_me($total_adet,3)?></th>
				<th style="text-align:center"><?=write_me($total,$calc_type)?></th>
				<th style="text-align:center"><?=write_me($total_ort_sure,$calc_type)?></th>
            </tr>
        </tfoot>
    </table>

<?}?>

<br>
<br>

