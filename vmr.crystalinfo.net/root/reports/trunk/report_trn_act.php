<?  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
    $header = "Hat Aktivite Raporu";
     $cUtility = new Utility();
  $cdb = new db_layer(); 
    $conn = $cdb->getConnection();
     require_valid_login();
    if (right_get("SITE_ADMIN")){
        //Site admin hakkı varsa herşeyi görebilir.  
    //Site id gelmemişse kişinin bulunduğu site raporu alınır.
      if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
    // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
      $SITE_ID = $_SESSION['site_id'];
    }else{
            print_error("Bu sayfayı Görme Hakkınız Yok!!!");
      exit;
    } 
     cc_page_meta();
  echo "<center>";
  ?>
   <form name="sort_me" method="post" action="report_trn_act.php">
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
     <input type="hidden" name="z" value="">  
     <input type="hidden" name="act" value="<?=$act?>">  
   </form>
  
<?
  function trunk_type($val){
    switch ($val){
      case 1:
        $my_val='Giriş';
        break;
      case 2:
        $my_val='Çıkış';
        break;
      case 3:
        $my_val='Giriş-Çıkış';
        break;
    }
    return $my_val;
  }
    
    function trunk_prop($trunk,$field,$SITE_ID){
        global $cdb;
        $sql_str="SELECT TRUNK_NAME,TRUNK_IO_TYPE FROM TRUNKS WHERE MEMBER_NO= '$trunk' AND SITE_ID = '".$SITE_ID."'"; 
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
            print_error($error_msg);
            exit;
        }
        $row = mysqli_fetch_object($result);
        return $row->$field;
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

   $report_type="Hat Aktivite Raporu";
   
   if ($act == "src") {

     $kriter = "";
     $kriter2 = "";
     $kriter3 = "";
     $kriter4 = "";
     //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
     $kriter4 .= $cdb->field_query($kriter4,   "SITE_ID"      ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
     $kriter2 .= $cdb->field_query($kriter2,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
     $kriter3 .= $cdb->field_query($kriter3,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
   
    add_time_crt();//Zaman kriteri
    if($forceMainTable)
      $CDR_MAIN_DATA = "CDR_MAIN_DATA";
    else
      $CDR_MAIN_DATA = getTableName($t0,$t1);
    if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";

////////////////////////////////////////////////////////    
    $kriter1 = $kriter;//Zaman kriterini kaybetmeyelim.

    //Gelen Çağrıların Toplamı
    $sql_str  = "SELECT COUNT(CDR_ID) AS AMOUNT, CALL_TYPE FROM CDR_MAIN_INB";
    if($kriter!="")
      $kriter = $kriter4." AND ".$kriter2." AND CALL_TYPE=2 AND ".$kriter3." AND ".$kriter;
    else 
      $kriter = $kriter4." AND ".$kriter2." AND CALL_TYPE=2 AND ".$kriter3;

    if ($kriter != "")
      $sql_str .= " WHERE " .$kriter;

    if ($record<>'' ||is_numeric($record)) {
      $sql_str .= " LIMIT 0,". $record ;
    }
    $sql_str .=" GROUP BY CALL_TYPE";
    //echo $sql_str;exit;
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
      print_error($error_msg);
      exit;
    }
    $row = mysqli_fetch_object($result);
      $INB_CNT = $row->AMOUNT;

    $kriter = $kriter1;//Zaman kriteri tekrar alındı.
    //Giden Çağrıların Toplamı
    $sql_str  = "SELECT COUNT(CDR_ID) AS AMOUNT, CALL_TYPE FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA";
    if($kriter!="")
      $kriter = $kriter4." AND ".$kriter2." AND CALL_TYPE=1 AND ".$kriter3." AND ".$kriter;
    else 
      $kriter = $kriter4." AND ".$kriter2." AND CALL_TYPE=1 AND ".$kriter3;

    if ($kriter != "")
      $sql_str .= " WHERE " .$kriter;

    if ($record<>'' ||is_numeric($record)) {
      $sql_str .= " LIMIT 0,". $record ;
    }
    $sql_str .=" GROUP BY CALL_TYPE";
    //echo $sql_str;exit;
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
      print_error($error_msg);
      exit;
    }
    $row = mysqli_fetch_object($result);
      $OUTB_CNT = $row->AMOUNT;
////////////////////////////////////////////////////////////

    $sql_str  = "SELECT MEMBER_NO AS TRUNK FROM TRUNKS WHERE SITE_ID = $SITE_ID";
    if (!($cdb->execute_sql($sql_str,$rs_t,$error_msg))){
      print_error($error_msg);
      exit;
    }
    $kriter = $kriter1;
    while($row_t = mysqli_fetch_object($rs_t)){
      $trunks[$row_t->TRUNK] = Array($row_t->TRUNK);
    }
    
    //Gelen Çağrıların hatlara Dağılımı
    $sql_str  = "SELECT COUNT(CDR_ID) AS AMOUNT, TER_TRUNK_MEMBER FROM CDR_MAIN_INB";

    if($kriter!="")
      $kriter = $kriter4." AND ".$kriter2." AND CALL_TYPE=2 AND ".$kriter3." AND ".$kriter;//Index'e uygun olmalı.
    else
      $kriter = $kriter4." AND ".$kriter2." AND CALL_TYPE=2 AND ".$kriter3;//Index'e uygun olmalı.
     
    if ($kriter != "")
      $sql_str .= " WHERE " .$kriter;  
       
    $sql_str .= " GROUP BY TER_TRUNK_MEMBER ORDER BY AMOUNT DESC";
          
    if ($record<>'' ||is_numeric($record)) {
      $sql_str .= " LIMIT 0,". $record ;
    }
    //echo $sql_str;exit;
    if (!($cdb->execute_sql($sql_str,$result_inb,$error_msg))){
      print_error($error_msg);
      exit;
    }
    $kriter = $kriter1;
    $i=0;
    while($row = mysqli_fetch_object($result_inb)){
      $trunks[$row->TER_TRUNK_MEMBER][2] = $row->AMOUNT;
      $i++;
    }
    $kriter = $kriter1;

    //Giden Çağrıların Trunklara Dağılımı
    $sql_str  = "SELECT COUNT(CDR_ID) AS AMOUNT,TER_TRUNK_MEMBER FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA";
    if($kriter!="")
      $kriter = $kriter4." AND ".$kriter2." AND CALL_TYPE=1 AND ".$kriter3." AND ".$kriter;//Index'e uygun olmalı.
    else
      $kriter = $kriter4." AND ".$kriter2." AND CALL_TYPE=1 AND ".$kriter3;//Index'e uygun olmalı.
        
    if ($kriter != "")
      $sql_str .= " WHERE " .$kriter;  
       
    $sql_str .= " GROUP BY TER_TRUNK_MEMBER ORDER BY AMOUNT DESC";
    
    if ($record<>'' ||is_numeric($record)) {
      $sql_str .= " LIMIT 0,". $record ;
    }
    //echo $sql_str;exit;
    if (!($cdb->execute_sql($sql_str,$result_outb,$error_msg))){
      print_error($error_msg);
      exit;
    }
    $kriter = $kriter1;
    $i=0;
    while($row = mysqli_fetch_object($result_outb)){
      $trunks[$row->TER_TRUNK_MEMBER][3] = $row->AMOUNT;
      $i++;
    }

////////Get the total counts, inbound and outbounds
    while($k < sizeof($trunks)){
      $t = key($trunks);
      $trunks[$t][4] = $trunks[$t][2] + $trunks[$t][3];
      next($trunks);
      $k++;
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
          <a style="width: 50px;height: 34px;font-size: 10px;text-decoration: none;top: 100%;left: 5%; z-index: 5;"  href="javascript:mailPage('inbound.html')"  class="dt-button buttons-excel buttons-html5">Mail</a>
				</TD>
				<TD width="25%" align=right><img SRC="<?=IMAGE_ROOT?>company.gif"></TD>
			</TR>
            </TABLE>
            <br>
      <table id="export"class="display nowrap" style="width:100%">
      <thead>
        <tr>
          <th>Hat Kodu</th>
              <th>Hat Adı</th>
              <th>Tipi</th>
              <th>Gelen Çağrı</th>
              <th>Giden Çağrı</th>
              <th>Toplam Çağrı</th>
          </tr>
          </thead>
          <tbody>
      <? 
        $k=0;
        if (is_array($trunks)){
///////////////////////////////////////////////////////////
//                  SORT Array according to wanted column
///////////////////////////////////////////////////////////
          if(!$z) $z = 4;
          function docmp($a,$b) { 
                  global $z;
          // test score in a versus score in b 
                  if ($a[$z] > $b[$z]) return -1; /* a.score > b.score */ 
                  if ($a[$z] < $b[$z]) return 1; /* a.score < b.score */ 
          // well, they are the same, so say so. 
                  return 0; 
               } 
          uasort($trunks,docmp); 
///////////////////////////////////////////////////////////
//                  END OF SORT
//////////////////////////////////////////////////////////
          $tot_cnt = 0;
             while($k < sizeof($trunks)){
              $t = key($trunks);
                       $bg_color = "E4E4E4";
            $trunk_id = $trunks[$t][0];
            if ($trunk_id==""){$trunk_id="<font color=red>".$t."</font>";}
            $trunk_name = trunk_prop($trunks[$t][0],'TRUNK_NAME',$SITE_ID);
            if ($trunk_name == ""){$trunk_name = "<font color=red>Bu Hat Tanımlanmamış</font>";}
            if($i%2) $bg_color = "FFFFFF"; $i++;
            echo " <tr>";
               echo " <td>".$trunk_id."</td>";
               echo " <td>".$trunk_name."</td>";
               echo " <td><b>".trunk_type(trunk_prop($trunks[$t][0],'TRUNK_IO_TYPE',$SITE_ID))."</b></td>";
               echo " <td><b>".number_format($trunks[$t][2],0,'','.')."</b></td>";
               echo " <td><b>".number_format($trunks[$t][3],0,'','.')."</b></td>";
               echo " <td><b>".number_format($trunks[$t][4],0,'','.')."</b></td>";
            echo " </tr>";
            $tot_cnt = $tot_cnt + $trunks[$t][4];
            next($trunks);
           $k++;
          }
         }
      ?>
      </tbody>
      <tfoot>
      <th>Hat Kodu</th>
              <th>Hat Adı</th>
              <th>Tipi</th>
              <th>Gelen Çağrı</th>
              <th>Giden Çağrı</th>
              <th>Toplam Çağrı</th>
      </tfoot>
      </table>
    </td>  
  </tr>
  <tr>
    <td  colspan="2" width="50%" class="rep_header" align="right">Toplam Akan Çağrı= <?=number_format($tot_cnt,0,'','.')?></td>
  </tr>
</table>  

<?}?>
</form>

<script>
function submit_f(val){
      document.all('z').value = val;
    document.all('sort_me').submit();
    document.sort_me.submit();
}
</script>