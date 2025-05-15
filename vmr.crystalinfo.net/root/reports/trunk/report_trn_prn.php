<? require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
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

    $header = "Hatların Kullanım Yüzdeleri";
     cc_page_meta();
  echo "<center>";
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
        $sql_str="SELECT TRUNK_NAME,TRUNK_IO_TYPE FROM TRUNKS WHERE MEMBER_NO= '$trunk' AND SITE_ID ='".$SITE_ID."'"; 
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

   $report_type="Hat Kullanım Raporu";
   
   if ($act == "src") {
     
       $kriter = "";
       $kriter2 = "";
       $kriter3 = "";
       $kriter4 = "";

       //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
    $kriter4 .= $cdb->field_query($kriter4,   "SITE_ID"      ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
    $kriter2 .= $cdb->field_query($kriter2,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
    $kriter3 .= $cdb->field_query($kriter3,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.

    //Tarih Kontrolü burada başlıyor.
    add_time_crt();//Zaman kriteri
    if($forceMainTable)
      $CDR_MAIN_DATA = "CDR_MAIN_DATA";
    else
      $CDR_MAIN_DATA = getTableName($t0,$t1);
    if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";

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

    $kriter = $kriter1;//Zaman kriteri tekrar alındı.

    //Gelen Çağrıların hatlara Dağılımı
    $sql_str  = "SELECT COUNT(CDR_ID) AS AMOUNT, ORIG_TRUNK_MEMBER FROM CDR_MAIN_INB";

    if($kriter!="")
      $kriter = $kriter4." AND ".$kriter2." AND CALL_TYPE=2 AND ".$kriter3." AND ".$kriter;//Index'e uygun olmalı.
    else
      $kriter = $kriter4." AND ".$kriter2." AND CALL_TYPE=2 AND ".$kriter3;//Index'e uygun olmalı.
     
    if ($kriter != "")
      $sql_str .= " WHERE " .$kriter;
       
    $sql_str .= " GROUP BY ORIG_TRUNK_MEMBER ORDER BY AMOUNT DESC";
          
    if ($record<>'' ||is_numeric($record)) {
               $sql_str .= " LIMIT 0,". $record ;
        }
    //echo $sql_str;
    if (!($cdb->execute_sql($sql_str,$result_inb,$error_msg))){
      print_error($error_msg);
      exit;
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
    //echo $sql_str;
    if ($record<>'' ||is_numeric($record)) {
      $sql_str .= " LIMIT 0,". $record ;
    }

    if (!($cdb->execute_sql($sql_str,$result_outb,$error_msg))){
      print_error($error_msg);
      exit;
    }
    $kriter = $kriter1;
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
    $('#export1').DataTable( {
		//scrollY: 600,
        //paging: false,
        dom: 'Bfrtip',
		"pageLength": 100,
        buttons: ['copy', 'csv', 'excel', 'print' ]
		
    } );

    $('#export2').DataTable( {
		//scrollY: 600,
        //paging: false,
        dom: 'Bfrtip',
		"pageLength": 100,
        buttons: ['copy', 'csv', 'excel', 'print' ]
		
    } );
	} );
	  
	</script>	

<table width="85%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" width="100%" align="center" class="rep_header" align="center">
            <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD><a href="http://www.crystalinfo.net" target="_blank"><img border="0" SRC="<?=IMAGE_ROOT?>logo2.gif" ></a></TD>
              <TD width="50%" align=center CLASS="header"><?echo $company;?><BR>HAT RAPORU<br><?=$header?></TD>
              <TD width="25%" align=right><img SRC="<?=IMAGE_ROOT?>company.gif"></TD>
            </TR>
            </TABLE>
        </td>
  </tr>
  <tr>
    <td colspan="2" width="100%"  align="right">&nbsp;</td>
  </tr>
    <tr>
     <td colspan="2">
     <table width="100%" cellspacing=0 cellpadding=0>
       
    <tr>
      <td width="50%" class="rep_header" align="left">
         <?if($t0!=""){?>
         Tarih Aralığı(<?=date("d/m/Y",strtotime($t0))." - ".date("d/m/Y",strtotime($t1));?>)
         <?}?>
         <a style="width: 50px;height: 34px;font-size: 10px;text-decoration: none;top: 100%;left: 5%; z-index: 5;"  href="javascript:mailPage('inbound.html')"  class="dt-button buttons-excel buttons-html5">Mail</a>
       </td><td width="50%" align="right"> 
      </td></tr></table>
      </td>
  </tr>
  <tr>
    <td width="50%" valign="top">
    <table id="export1"class="display nowrap" style="width:100%">
    <thead>
        <tr>
          <th colspan="4" width="100%">Giden Çağrılar</th>
          </tr>
        <tr>
          <th>Hat Kodu</th>
              <th>Tipi</th>
              <th>Adet</th>
          <th>Yüzde</th>
          </tr>
          </thead>
      <? 
           $i=0;
         if (mysqli_num_rows($result_outb)>0)
         while($row = mysqli_fetch_object($result_outb)){
          $i++;
             echo " <tr>";
             echo " <td><b>".$row->TER_TRUNK_MEMBER."</b></td>";
             echo " <td><b>".trunk_type(trunk_prop($row->TER_TRUNK_MEMBER,'TRUNK_IO_TYPE',$SITE_ID))."</b></td>";
             echo " <td><b>".number_format($row->AMOUNT,0,'','.')."</b></td>";
             echo " <td><b>% ".percent($row->AMOUNT,$OUTB_CNT)."</b></td>";
         }
      ?>
      </table>
    </td>  
    <td width="50%" valign="top">
    <table id="export2"class="display nowrap" style="width:100%">
    <thead>
          <tr>
          <th colspan="4" width="100%">Gelen Çağrılar</th>
          </tr>
        <tr>
          <th>Hat Kodu</th>
              <th>Tipi</th>
              <th>Adet</th>
          <th>Yüzdesi</th>
          </tr>
      </thead>
      <? 
           $i=0;
         if (mysqli_num_rows($result_inb)>0)
         while($row = mysqli_fetch_object($result_inb)){
          $i++;
              echo " <tr>";
             echo " <td><b>".$row->ORIG_TRUNK_MEMBER."</b></td>";
             echo " <td><b>".trunk_type(trunk_prop($row->ORIG_TRUNK_MEMBER,'TRUNK_IO_TYPE',$SITE_ID))."</b></td>";
             echo " <td><b>".number_format($row->AMOUNT,0,'','.')."</b></td>";
             echo " <td><b>% ".percent($row->AMOUNT,$INB_CNT)."</b></td>";
                }
      ?>
      </table>
    </td>  
  </tr>  
  <tr>
    <td width="50%" class="rep_header" align="right">Toplam Giden Çağrı= <?echo number_format($OUTB_CNT,0,'','.');?></td>
    <td width="50%" class="rep_header" align="right">Toplam Gelen Çağrı= <?echo number_format($INB_CNT,0,'','.');?></td>
  </tr>
</table>  
<?}?>

