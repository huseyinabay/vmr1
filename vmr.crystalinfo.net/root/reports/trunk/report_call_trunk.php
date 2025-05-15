<?
    require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
    $cUtility = new Utility();
    $cdb = new db_layer(); 
    $conn = $cdb->getConnection();
    $show_chart=false;
    require_valid_login();

    $usr_crt = "";
    if (right_get("SITE_ADMIN")){
      //Site admin hakkı varsa herşeyi görebilir.  
      //Site id gelmemişse kişinin bulunduğu site raporu alınır.
      if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
      //Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
      $SITE_ID = $_SESSION['site_id'];
    }elseif(got_dept_right($_SESSION["user_id"])==1){
      //Bir departmanın raporunu görebiliyorsa kendi sitesindekileri girebilir.
      $SITE_ID = $_SESSION['site_id'];
      //echo $dept_crt = get_depts_crt($_SESSION["user_id"]);
      $usr_crt = get_users_crt($_SESSION["user_id"],1,$SITE_ID);
      $alert = "Bu rapor sadece sizin yetkinizde olan departmanlara ait dahililerin bilgilerini içerir.";
    }else{
            print_error("Bu sayfayı Görme Hakkınız Yok!!!");
      exit;
    } 

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
   
    function write_me($MyVal,$calc_type){
     if ($calc_type == 1){
       $MyRetVal = write_price($MyVal);
     }elseif($calc_type==2){
       $MyRetVal = calculate_all_time($MyVal);
     }else{
       print_error("Hatalı Durum Oluştu. Lütfen Tekrar Deneyiniz.");
      exit;
     }
     return $MyRetVal;
  }
  
    function get_trunk_name($trunk,$SITE_ID){
        global $cdb;
        $sql_str="SELECT TRUNK_NAME FROM TRUNKS WHERE MEMBER_NO= '$trunk' AND SITE_ID = ".$SITE_ID; 
        if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
            print_error($error_msg);
            exit;
        }
        $row = mysqli_fetch_object($result);
        return $row->TRUNK_NAME;
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
       <input type="hidden" name="hh0" value="<?=$hh1?>">
         <input type="hidden" name="hm0" value="<?=$hm1?>">
       <input type="hidden" name="hafta" value="<?=$hafta?>">
         <input type="hidden" name="record" value="<?=$record?>">
         <input type="hidden" name="type" value="<?=$type?>">  
         <input type="hidden" name="calc_type" value="<?=$calc_type?>">       
         <input type="hidden" name="sort_type" value="<?=($sort_type=="asc")?"desc":"asc"?>">  
  </form>
<script language="JavaScript">
  function submit_form(sortby){
    document.all('sort_me').action='report_call_trunk.php?act=src&type=<?=$type?>&order=' + sortby;    
    document.all('sort_me').submit();
   }
  function drill_down(d_id){
    document.all('sort_me').action='/reports/outbound/report_outb_prn.php?act=src&type=<?=$type?>&SUMM=trunk&TRUNK=' + d_id;    
    document.all('sort_me').submit();
   }
   
</script>
  
<?   
//   $report_type="GENEL RAPOR";
   
     if ($act == "src") {
       $myfld= "PRICE";
       $calc_type =1;   

       $kriter = "";   

       //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
       $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.SITE_ID"     ,   "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
       $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,   "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
       $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "1"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
       $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
       $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.

       add_time_crt();//Zaman kriteri
       if($forceMainTable)
         $CDR_MAIN_DATA = "CDR_MAIN_DATA";
       else
        $CDR_MAIN_DATA = getTableName($t0,$t1);
       if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";

       $header="Çağrıların Hatlara Göre Dağılımı";

       $sql_str  = "SELECT LocationTypeid AS TYPE, TER_TRUNK_MEMBER, SUM(CDR_MAIN_DATA.$myfld) AS TOTAL
                    FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                   ";
       if ($kriter != "")
         $sql_str .= " WHERE ".$kriter." ".$usr_crt;  
       $sql_str .= " GROUP BY TER_TRUNK_MEMBER, TYPE";
       
    switch ($order){
      case '0':
             $z = 0;
        break;
      case '1':
             $z = 1;
        break;
      case '2':
             $z = 2;
             break;
      case '3':
             $z = 3;
        break;
      case '4':
             $z = 4;
        break;
      case '5':
             $z = 5;
        break;
      case '6':
             $z = 6;
        break;
      case '7':
             $z = 7;
        break;
      default:
             $z = 8;
         }

     if ($record<>'' ||is_numeric($record)) {
               $sql_str .= " LIMIT 0,". $record ;
         }
//echo $sql_str;exit;
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
      while($row = mysqli_fetch_object($result)){
            $my_trunk = $row->TER_TRUNK_MEMBER;
            if($my_trunk != ""){
                $datas[$my_trunk][5]=1;
                if($row->TYPE == 0){
                    $datas[$my_trunk][0] += $row->TOTAL;
                }else if($row->TYPE == 1){
                    $datas[$my_trunk][1] += $row->TOTAL;
                }else if($row->TYPE == 2){
                    $datas[$my_trunk][2] += $row->TOTAL;
                }else if($row->TYPE == 3){
                    $datas[$my_trunk][3] += $row->TOTAL;
                }else{
                    $datas[$my_trunk][4] += $row->TOTAL;
                }
                $datas[$my_trunk][6] += $row->TOTAL;
            }    
            if($my_trunk == ""){
                $datas[0][5]=1;
                if($row->TYPE == 0){
                    $datas[0][0] += $row->TOTAL;
                }else if($row->TYPE == 1){
                    $datas[0][1] += $row->TOTAL;
                }else if($row->TYPE == 2){
                    $datas[0][2] += $row->TOTAL;
                }else if($row->TYPE == 3){
                    $datas[0][3] += $row->TOTAL;
                }else{
                    $datas[0][4] += $row->TOTAL;
                }
                $datas[0][6] += $row->TOTAL;
            }
        }
         
if (is_array($datas)){
    //array_multisort ($datas, SORT_DESC, array_keys ($datas));
    //asort($datas);

///////////////////////////////////////////////////////////
//                  SORT Array according to wanted column
///////////////////////////////////////////////////////////
    function docmp($a,$b) { 
            global $z;
    // test score in a versus score in b 
            if ($a[$z] > $b[$z]) return -1; /* a.score > b.score */ 
            if ($a[$z] < $b[$z]) return 1; /* a.score < b.score */ 
    // well, they are the same, so say so. 
            return 0; 
         } 
            function cmp_desc ($a, $b) {
              global $z;
            if ($a[$z] < $b[$z]) return -1; /* a.score < b.score */ 
            if ($a[$z] > $b[$z]) return 1; /* a.score > b.score */ 
            return 0; 
            }
           if($z<7){
                if ($sort_type=="desc")
                  uasort($datas,cmp_desc); 
                else
                  uasort($datas,docmp); 
           }       
///////////////////////////////////////////////////////////
//                  END OF SORT
//////////////////////////////////////////////////////////
}
?>

<br><br>
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
        buttons: ['copy', 'csv', 'excel', 'print' ]
		
    } );
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
            <a style="width: 50px;height: 34px;font-size: 10px;text-decoration: none;top: 100%;left: 5%; z-index: 5;"  href="javascript:mailPage('inbound.html')"  class="dt-button buttons-excel buttons-html5">Mail</a>
      <table id="export"class="display nowrap" style="width:100%">
        <thead>
          <tr>
              <th>Hat No</th>
                <th>Şehir İçi</th>
                <th>Şehirler Arası</th>
                <th>GSM</th>
                <th>Uluslar Arası</th>
                <th>Diğer</th>
                <th>Toplam</th>
          </tr>
          </thead>
        <tbody>
      <? 
           $i = 0;;
        $my_pr=0; 
                $csv_data[0][0] = "Hat No";
                $csv_data[0][1] = "Şehir İçi";
                $csv_data[0][2] = "Şehirler Arası" ;
                $csv_data[0][3] = "GSM";
                $csv_data[0][4] = "Uluslar Arası";
                $csv_data[0][5] = "Diğer";
                $csv_data[0][6] = "Toplam";
                
         if(is_array($datas)){   
            foreach($datas as $key=>$value){
                   $i++;
                   $bg_color = "E4E4E4";   
                   if($i%2) $bg_color ="FFFFFF";
                   $key_x = $key;
                   $key_y = $key;
                   if($key=="0") $key_x = "Tanımsız Hat";
                   if($key=="0") $key_y = -2;
                                      
             echo " <tr  BGCOLOR=$bg_color>\n";
                   echo  " <td class=\"rep_td\">&nbsp;<b><a class=\"a1\" href=\"javascript:drill_down('".$key_y."')\">".$key_x."-".substr(get_trunk_name($key_x,$SITE_ID),0,25)."</a></b></td>\n";
             $csv_data[$i][0] =  $key_x;
                   $total = 0;
             for($k=0;$k<=4;$k++){
                       echo " <td class=\"rep_td\" align=\"right\">&nbsp;".write_me($datas[$key][$k],$calc_type)."</td>\n";
                 $csv_data[$i][$k+1] =  write_me($datas[$key][$k],$calc_type);
                       $total += $datas[$key][$k];
                   }
                   echo  " <td class=\"rep_td\" align=\"right\">&nbsp;<b>".write_me($total,$calc_type)."</b></td>\n";
             echo "</tr>\n";
                $csv_data[$i][6] =  write_me($total,$calc_type);
             $my_pr = $my_pr + $total;
         }
    }
          ?>
          </tbody>
          <tfoot>
          <th>Hat No</th>
                <th>Şehir İçi</th>
                <th>Şehirler Arası</th>
                <th>GSM</th>
                <th>Uluslar Arası</th>
                <th>Diğer</th>
                <th>Toplam</th>
          </tfoot>
      </table>
  <tr height="20">
    <td></td>
  </tr>
  <tr>
    <td height="22" colspan="3" width="100%" align="right">
         <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD WIDTH="80%" ALIGN="right"><b>Toplam  :</b></TD>
              <TD WIDTH="20%" ><?=write_me($my_pr,$calc_type)?></TD>
                <?$i++;$csv_data[$i][0] =  "Toplam";?>
                <?$csv_data[$i][1] =  write_me($my_pr,$calc_type);?>
            </TR>
         </TABLE>
    <tr>
        <td><?echo $alert;?></td>
    </tr>
      </td>
  </tr>
</table>  
<?}?>
<br>
<br>
<?
 csv_out($csv_data, "../../temp/dept_disps.csv"); 
 if($CSV_EXPORT==1){?>
 <iframe SRC="/csv_download.php?filename=dept_disps.csv" WIDTH=0 HEIGHT=0 ></iframe>
 <?}?>

