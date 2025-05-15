<?  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
    $cUtility = new Utility();
    $cdb = new db_layer(); 
    $conn = $cdb->getConnection();
    $show_chart=false;
    require_valid_login();

    check_right("SITE_ADMIN");
    cc_page_meta();
    echo "<center>";

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

    function get_def($fld1,$fld2){
        global $cdb,$arr_location_type,$arr_location,$arr_auth_code;
        switch ($fld1){
            case 'LocationTypeid':
                $ret_val = $arr_location_type[$fld2];
                break;
            case 'Locationid':
                $ret_val = $arr_location[$fld2];
                break;
            default: 
        }
        return $ret_val;
    }

    $sql_str1="SELECT NAME, VALUE FROM SYSTEM_PRM WHERE NAME = 'COMPANY_NAME' OR NAME='MAX_ACCE_DURATION'";
    if (!($cdb->execute_sql($sql_str1,$result1,$error_msg))){
        print_error($error_msg);
        exit;
    }
    if (mysqli_num_rows($result1)>0){
    while($row1 = mysqli_fetch_object($result1)){
      switch ($row1->NAME){
        case 'COMPANY_NAME':
              $company = $row1->VALUE;
              break;
        case 'MAX_ACCE_DURATION':
              $max_acc_dur =  ($row1->VALUE)*60;
              break;
      }
    }
  }else{
    print_error("Site parametreleri bulunamadı.");
    exit;
  }
 ?>
<script language="JavaScript">
  function submit_form(sortby){
    document.all('sort_me').action='report_multiside_prn.php?act=src&type=<?=$type?>&order=' + sortby;    
    document.all('sort_me').submit();
   }
</script>
   <form name="sort_me" method="post" action="">
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
       <input type="hidden" name="sort_type" value="<?=($sort_type=="asc")?"desc":"asc"?>">  
  </form>
<?   
   $report_type="MULTISITE RAPOR";

   if ($act == "src") {
   
    $kriter = "";   
    //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
    $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
    $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "1"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.

    //**Bunlar birlikte olmalı ve bu sırada olmalı.
    add_time_crt();//Zaman kriteri 
    $CDR_MAIN_DATA = getTableName($t0,$t1);
    if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";
    //**

     $link  ="";
   switch ($type){
     case 'general':
      $sql_id="1";$grp_type="LocationTypeid";
      $field1="LocationTypeid";$field1_name="Tip Kodu";$width1="10%";$field1_ord="LocationTypeid";
      $field2="LocationTypeid";$field2_name="Çağrı Tipi";$width2="25%";$field2_ord="LocationTypeid";
      $field3="AMOUNT";$field3_name="Adet";$width3="20%";$field3_ord="AMOUNT ";
      $field6="DURATION";$field6_name="Süre";$width6="20%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="20%";$field7_ord="PRICE ";
      $header="Çağrıların Arama Türüne Göre Dağılımları";
      break;
     case 'gsm':
      $kriter.= $cdb->field_query($kriter, "CDR_MAIN_DATA.LocationTypeid"         ,"=",    "2");
      $sql_id="4";$grp_type="LocalCode";
      $field1="LocalCode";$field1_name="Kodu";$width1="8%";$field1_ord="LocalCode";
      $field2="Locationid";$field2_name="Şebeke Adı";$width2="20%";$field2_ord="LocalCode";
      $field3="AMOUNT";$field3_name="Adet";$width3="8%";$field3_ord="AMOUNT ";
      $field6="DURATION";$field6_name="Süre";$width6="15%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="15%";$field7_ord="PRICE ";
      $header="GSM Operatör Çağrıları";
      break;   
     case 'nat':
      $kriter.= $cdb->field_query($kriter, "CDR_MAIN_DATA.LocationTypeid"         ,"=",    "1");
      $sql_id="5";$grp_type="LocalCode";
      $field1="LocalCode";$field1_name="İl Kodu";$width1="8%";$field1_ord="LocalCode";
      $field2="Locationid";$field2_name="İl Adı";$width2="20%";$field2_ord="LocalCode";
      $field3="AMOUNT";$field3_name="Adet";$width3="8%";$field3_ord="AMOUNT ";
      $field6="DURATION";$field6_name="Süre";$width6="15%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="15%";$field7_ord="PRICE ";
      $header="Şehirlerarası Çağrıların İllere Dağılımı";
      break;   
     case 'int':
      $kriter.= $cdb->field_query($kriter, "CDR_MAIN_DATA.LocationTypeid"         ,"=",    "3");
      $sql_id="6";$grp_type="CountryCode";
      $field1="CountryCode";$field1_name="Kodu";$width1="8%";$field1_ord="CountryCode";
      $field2="Locationid";$field2_name="Ülke Adı";$width2="20%";$field2_ord="CountryCode";
      $field3="AMOUNT";$field3_name="Adet";$width3="8%";$field3_ord="AMOUNT ";
      $field6="DURATION";$field6_name="Süre";$width6="15%";$field6_ord="DURATION ";
      $field7="PRICE";$field7_name="Tutar";$width7="15%";$field7_ord="PRICE ";
      $header="Uluslararası Çağrıların Ülkelere Dağılımı";
      break;
    default:
      echo "Hatalı Durum Oluştu";
      exit;
   }

  switch ($sql_id){
    case 1:
      $sql_str  = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS AMOUNT, CDR_MAIN_DATA.LocationTypeid,
                     SUM(CDR_MAIN_DATA.DURATION) AS DURATION, SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                   FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
            ";
      break;
    case 4:
      $sql_str  = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS AMOUNT,CDR_MAIN_DATA.LocalCode,
                     SUM(CDR_MAIN_DATA.DURATION) AS DURATION,CDR_MAIN_DATA.Locationid,
                     COUNT(CDR_MAIN_DATA.LocalCode) AS CITY_AMOUNT,
                     SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                   FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                  ";
      break;
    case 5:
      $sql_str  = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS AMOUNT,CDR_MAIN_DATA.LocalCode,
                     SUM(CDR_MAIN_DATA.DURATION) AS DURATION,CDR_MAIN_DATA.Locationid,
                     COUNT(CDR_MAIN_DATA.LocalCode) AS CITY_AMOUNT,
                     SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                   FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                  ";
      break;
    case 6:
      $sql_str  = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS AMOUNT,CDR_MAIN_DATA.CountryCode,
                     SUM(CDR_MAIN_DATA.DURATION) AS DURATION,CDR_MAIN_DATA.Locationid,
                     COUNT(CDR_MAIN_DATA.CountryCode) AS COUNTRY_AMOUNT,
                     SUM(CDR_MAIN_DATA.PRICE) AS PRICE
                   FROM CDR_MAIN_DATA
                  ";
      break;
    default:
  } 
       
         if ($kriter != "")
               $sql_str .= " WHERE ".$kriter." ".$usr_crt;  

        $sql_str .= " GROUP BY ". $grp_type ;
//echo $sql_str;exit;
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
//echo $sql_str;
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
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
              <th width="<?=$width1;?>"><?echo $field1_name;?></th>
                    <?$csv_data[1][0] = $field1_name;?>
          <?if ($field2_name<>''){?>
            <th width="<?=$width2;?>"><?echo $field2_name;?></th>
                    <?$csv_data[1][$col_cnt] = $field2_name;?>
          <?}?>
          <?if ($field3_name<>''){?>
            <th width="<?=$width3;?>"><?echo $field3_name;?></th>
                    <?$col_cnt++; $csv_data[1][$col_cnt] = $field3_name;?>
          <?}?>
          <?if ($field4_name<>''){?>
            <th width="<?=$width4;?>"><?echo $field5_name;?></th>
                    <?$col_cnt++; $csv_data[1][$col_cnt] = $field4_name;?>
          <?}?>
          <?if ($field5_name<>''){?>
            <th width="<?=$width5;?>"><?echo $field5_name;?></th>
                    <?$col_cnt++; $csv_data[1][$col_cnt] = $field5_name;?>
          <?}?>
          <?if ($field6_name<>''){?>
            <th width="<?=$width6;?>"><?echo $field6_name;?></th>
                    <?$col_cnt++; $csv_data[1][$col_cnt] = $field6_name;?>
          <?}?>
          <?if ($field7_name<>''){?>
            <th width="<?=$width7;?>"><?echo $field7_name;?></th>
                    <?$col_cnt++; $csv_data[1][$col_cnt] = $field7_name;?>
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
           mysql_data_seek($result,0);
         while($row = mysqli_fetch_array($result)){
          $col_cnt=1;
                    $i++;                   $j++;
               $bg_color = "E4E4E4";   
               if($i%2) $bg_color ="FFFFFF";
            echo " <tr>";
             echo " <td>&nbsp;<b>".$row["$field1"]."</b></td>";
                    $csv_data[$j][0] = $row["$field1"];
          if ($field2_name<>''){
                        $def = get_def($field2,$row["$field2"]);
            echo " <td>".$def."</td>";
                        $csv_data[$j][$col_cnt] =$def;$col_cnt++;
             }
          if ($field3_name<>''){
            echo " <td>".$row["$field3"]."</td>";
                        $csv_data[$j][$col_cnt] = $row["$field3"];$col_cnt++;
             }
             if ($field4_name<>''){
            echo " <td>".$row["$field4"]."</td>";
                        $csv_data[$j][$col_cnt] = $row["$field4"];$col_cnt++;
             }
            if ($field5_name<>''){
            echo " <td>".$row["$field5"]."</td>";
                        $csv_data[$j][$col_cnt] = $row["$field5"];$col_cnt++;
             }
          if ($field6_name<>''){
            if ($field6 = "DURATION"){
              echo " <td>".calculate_time($row["DURATION"],"hour")."  Saat  ".calculate_time($row["DURATION"],"min")."  Dk</td>";
                            $csv_data[$j][$col_cnt] = calculate_time($row["DURATION"],"hour")."  Saat  ".calculate_time($row["DURATION"],"min")."  Dk";$col_cnt++;
            }else{
              echo " <td>".$row["$field6"]."</td>";  
                            $csv_data[$j][$col_cnt] = $row["$field6"];$col_cnt++;
                        }    
          }    
            if ($field7_name<>''){
            if ($field7 = "PRICE"){
              echo " <td>".write_price($row["$field7"])."</td>";
                            $csv_data[$j][$col_cnt] = write_price($row["$field7"]);$col_cnt++;
            }else{
              echo " <td>".$row["$field7"]."</td>";
                            $csv_data[$j][$col_cnt] = $row["$field7"];$col_cnt++;
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
              <th width="<?=$width1;?>"><?echo $field1_name;?></th>
                    <?$csv_data[1][0] = $field1_name;?>
          <?if ($field2_name<>''){?>
            <th width="<?=$width2;?>"><?echo $field2_name;?></th>
                    <?$csv_data[1][$col_cnt] = $field2_name;?>
          <?}?>
          <?if ($field3_name<>''){?>
            <th width="<?=$width3;?>"><?echo $field3_name;?></th>
                    <?$col_cnt++; $csv_data[1][$col_cnt] = $field3_name;?>
          <?}?>
          <?if ($field4_name<>''){?>
            <th width="<?=$width4;?>"><?echo $field5_name;?></th>
                    <?$col_cnt++; $csv_data[1][$col_cnt] = $field4_name;?>
          <?}?>
          <?if ($field5_name<>''){?>
            <th width="<?=$width5;?>"><?echo $field5_name;?></th>
                    <?$col_cnt++; $csv_data[1][$col_cnt] = $field5_name;?>
          <?}?>
          <?if ($field6_name<>''){?>
            <th width="<?=$width6;?>"><?echo $field6_name;?></th>
                    <?$col_cnt++; $csv_data[1][$col_cnt] = $field6_name;?>
          <?}?>
          <?if ($field7_name<>''){?>
            <th width="<?=$width7;?>"><?echo $field7_name;?></th>
                    <?$col_cnt++; $csv_data[1][$col_cnt] = $field7_name;?>
          <?}?>
          </tr>
      </tfoot>
      </table>
  <tr height="20">
    <td></td>
  </tr>
  <tr>
    <td height="22" colspan="3" width="100%" align="right">
           <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD WIDTH="80%" ALIGN="right"><b>Toplam Görüşme Adedi :</b></TD>
              <TD WIDTH="20%" ><?=number_format($my_amount,0,'','.')?></TD>
<?                            $j++;$csv_data[$j][0] = "Toplam Görüşme Adedi :";?>
<?                            $csv_data[$j][1] = number_format($my_amount,0,'','.');?>
            </TR>
            </TABLE>
      </td>
  </tr>
  <tr><td height="22" colspan="3" width="100%" align="right">
           <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD WIDTH="80%" ALIGN="right"><b>Toplam Süre :</b></TD>
              <TD WIDTH="20%" ><?=calculate_time($my_dur,"hour")."  Saat  ".calculate_time($my_dur,"min")."  Dk";?></TD>
<?                            $j++;$csv_data[$j][0] = "Toplam Süre :";?>
<?                            $csv_data[$j][1] = calculate_time($my_dur,"hour");?>
            </TR>
            </TABLE>
      </td>
  </tr>
  <tr>
    <td height="22" colspan="3" width="100%" align="right">
         <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD WIDTH="80%" ALIGN="right"><b>Toplam Tutar :</b></TD>
              <TD WIDTH="20%" ><?=write_price($my_pr)?></TD>
<?                            $j++;$csv_data[$j][0] = "Toplam Tutar :";?>
<?                            $csv_data[$j][1] = write_price($my_pr)?>
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
                  csv_out($csv_data, "../../temp/multiside_calls.csv"); 
if($CSV_EXPORT==1){?>
 <iframe SRC="/csv_download.php?filename=multiside_calls.csv" WIDTH=0 HEIGHT=0 ></iframe>
 <?}?>
<br>
<br>

