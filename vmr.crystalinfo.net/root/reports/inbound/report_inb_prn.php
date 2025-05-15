<? 
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";

	 //ini_set('display_errors', 'On');
     //error_reporting(E_ALL);	

   $cUtility = new Utility();
   $cdb = new db_layer(); 
   $conn = $cdb->getConnection();
   require_valid_login();
   
   
   
   	//  ini_set('display_errors', 'On');
   //   error_reporting(E_ALL);
   
   //$DEPT_ID=[];
   

   //Kullanıiıla§in hak kontrolü oali
   //echo $DEPT_ID[0];echo "/".$DEPT_ID[1];echo "/".$DEPT_ID[2]."---";ECHO COUNT($DEPT_ID)."<br>";
   $kriter2 = "";
   $check_origs = false;
   $usr_crt = "";
   if (right_get("SITE_ADMIN")){
     //Site admin hakkı varsa hesşeyi orebilir.  
     //Site id gelmemişse ksişinin bulundgu site raporu aliıiır.
     if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
   }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
     // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
     $SITE_ID = $_SESSION['site_id'];
   }elseif(got_dept_right($_SESSION["user_id"])==1){
     //Bir departmanın raporunu orebiliyorsa kendi sitesindekileri girebilir.
     $SITE_ID = $_SESSION['site_id'];
     $dept_crt = get_depts_crt($_SESSION["user_id"],$SITE_ID);
     $usr_crt  = get_users_crt($_SESSION["user_id"],2,$SITE_ID);
     $alert = "Bu rapor sadece sizin yetkinizde olan departmanlara ait dahililerin bilgilerini icerir.";
    }else{
     $usr_crt  = get_ext($_SESSION["user_id"]);
    }
    $unrep_exts_crt = get_unrep_exts_crt($SITE_ID);
   ob_start();
   //Hak Kontroluüburada bitiyor
   cc_page_meta();
   echo "<center>";
?>

<?if($CSV_EXPORT != 2){?>
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
    <input type="hidden" name="TER_DN" value="<?=$TER_DN?>">
	<input type="hidden" name="ORIG_TRUNK_MEMBER" value="<?=$ORIG_TRUNK_MEMBER?>">
    <div id="dept" style="display:none">
      <select name="DEPT_ID" class="select1" style="width:250;" multiple></select>
    </div>
    <input type="hidden" name="DURATION" value="<?=$DURATION?>">
    <input type="hidden" name="CLID" value="<?=$CLID?>">
    <input type="hidden" name="show_clid" value="<?=$show_clid?>">
	<input type="hidden" name="abandon" value="<?=$abandon?>">
	<input type="hidden" name="ansvered" value="<?=$ansvered?>">
    <input type="hidden" name="record" value="<?=$record?>">
    <input type="hidden" name="sort_type" value="<?=($sort_type=="asc")?"desc":"asc"?>">  
  </form>
<?}

    function get_contact($myclid){
       $cdb = new db_layer(); 
       $kriter1 ='';
       $myclid = str_replace("X", "", $myclid);
       if ($myclid<>''){
          if (substr($myclid,0,1)=='0') {
             $myclid = substr($myclid,1,strlen($myclid));
          } 
          if (strlen($myclid)<=10){
             $kriter1 .= $cdb->field_query($kriter1,"CONCAT(PHONES.CITY_CODE,PHONES.PHONE_NUMBER)" ,"=","'$myclid'");
          }else{
             $kriter1 .= $cdb->field_query($kriter1,"CONCAT(PHONES.COUNTRY_CODE,PHONES.CITY_CODE,PHONES.PHONE_NUMBER)","=",  "'$myclid'");
          }
          if($kriter1 == ""){ return "";}
          $sql_str2 = "SELECT CONCAT(CONTACTS.NAME,' ',CONTACTS.SURNAME)AS CONTACT
                         FROM CONTACTS 
                       INNER JOIN PHONES ON CONTACTS.CONTACT_ID=PHONES.CONTACT_ID
                       WHERE ".$kriter1;
          if (!($cdb->execute_sql($sql_str2,$result2,$error_msg))){
             print_error($error_msg);
             exit;
          }
		  
	 // echo $hafta;die;

      //  echo $sql_str2;die;
          if(mysqli_num_rows($result2) == 0){
            $contact ='';
          }else{
            $row2=mysqli_fetch_object($result2);
            $contact = $row2->CONTACT;
          }
       }else{
          $contact ='';
       }
       return $contact;
    }
    //end of get_contact func.
   
    // maximum acceptable duration aliniyor...
    $max_acc_dur = get_site_prm('MAX_ACCE_DURATION',$SITE_ID) * 60;

    $report_type = "Gelen Cagri Raporu";
    if ($act == "src") {
       $kriter = "";

       //Temel kriterler verinin hiıziı gmesi icin basa konuldu.
       //Bu mutlaka olmali ilgili siteyi belirliyor.
       $kriter .= $cdb->field_query($kriter,"CDR_MAIN_INB.SITE_ID","=","$SITE_ID"); 
       //Bu mutlaka olmali; hatasiz kayit durumunu gosteriyor.
       $kriter .= $cdb->field_query($kriter,"ERR_CODE","=","0");
       //Bu mutlaka olmaliı Disış arama olgunu gosteriyor.
       $kriter .= $cdb->field_query($kriter,"CALL_TYPE","=","2");
       //Bu mutlaka olmaliı.Dış arama ogunu gosteriyor.
       $kriter .= $cdb->field_query($kriter,"DURATION","<","$max_acc_dur");
       $kriter .= $cdb->field_query($kriter,"CDR_MAIN_INB.TER_DN","<>","''");
       //Bu mutlaka olmaliı.Hataiız kaiıt oldgunu gosteriyor.
	   	   if ($ORIG_TRUNK_MEMBER != ""){
	   $kriter .= $cdb->field_query($kriter,"CDR_MAIN_INB.ORIG_TRUNK_MEMBER","=","$ORIG_TRUNK_MEMBER");
	   }

       add_time_crt();  //Zaman kriteri 

      //Genel Arama Bilgileri. Bu alanlar başlık basılmasında kullanılacaktır.
      if ($TER_DN <> '') 
         $ter = 'Yes';
      if ( (($DEPT_ID[0] == '-1') || ($DEPT_ID[0] == '')) && count((array)$DEPT_ID) <= 1){
         $dept = '';
      }else{
         $dept = 'Yes';
      }
      if ($CLID <> '') 
         $clid = 'Yes';
      if ($DURATION <> ''){
         $dur = 'Yes';
         $DURATION_SN = $DURATION * 60;
      }
      //Aranan dahili kriteri olusşturuluyor.
      if($TER_DN){
         $TER_ARRAY = explode(",", $TER_DN);
         $in_str = "";
         for ( $i = 0; $i < count($TER_ARRAY); $i++){
            if ($in_str == ""){
               if ($TER_ARRAY[$i] != ""){
                  $in_str .= "'".$TER_ARRAY[$i]."'";
               }    
            }else{
               if ($TER_ARRAY[$i] != ""){
                  $in_str .= ", '".$TER_ARRAY[$i]."'";
               }
            }
        }
      }

      if ($in_str != ""){
         if ($kriter == ""){
            $kriter .= " CDR_MAIN_INB.TER_DN IN (".$in_str.")";
         }else{
            $kriter .= " AND CDR_MAIN_INB.TER_DN IN (".$in_str.")";
         }
      }else{
         $ter = '';
      }

      $in_str = "";
      if (is_array($DEPT_ID)){
         if ((($DEPT_ID[0] == '-1') || ($DEPT_ID[0]=='')) && count($DEPT_ID)==1){
            //Nothing to do
         }else{
           for ($i = 0; $i < count($DEPT_ID); $i++){
               if ($in_str != ""){
                  if ($DEPT_ID[$i] != "-1" && $DEPT_ID[$i] != "")
                     $in_str .= ", ".$DEPT_ID[$i];
               }else{
                  if ($DEPT_ID[$i] != "-1" && $DEPT_ID[$i] != "")
                     $in_str .= $DEPT_ID[$i];
               }
          }
         }
      }

      if ($in_str != ""){
         if ($kriter == ""){
            $kriter .= " EXTENTIONS.DEPT_ID IN (".$in_str.")";
         }else{
            $kriter .= " AND EXTENTIONS.DEPT_ID IN (".$in_str.")";
         }
      }
   
      $kriter .= $cdb->field_query($kriter, "CDR_MAIN_INB.CLID", "LIKE", "'%$CLID%'");
      $kriter .= $cdb->field_query($kriter, "(CDR_MAIN_INB.DURATION)", ">", "'$DURATION_SN'");
	  
	  if ($abandon == 1){
		  $kriter .= $cdb->field_query($kriter, "(CDR_MAIN_INB.DURATION)", "=", "'0'");
	  }
	  
	  if ($ansvered == 1){
		  $kriter .= $cdb->field_query($kriter, "(CDR_MAIN_INB.DURATION)", ">", "'0'");
	  }
          
      $sql_str  = "SELECT CDR_MAIN_INB.CDR_ID, CDR_MAIN_INB.TER_DN, DATE_FORMAT(TIME_STAMP,\"%d.%m.%Y\") AS MY_DATE, 
                     CDR_MAIN_INB.ORIG_TRUNK_MEMBER, DATE_FORMAT(TIME_STAMP,\"%H:%i:%s\") AS MY_TIME, EXTENTIONS.DESCRIPTION,
                     CDR_MAIN_INB.DURATION AS DURATION, CDR_MAIN_INB.CLID, CDR_MAIN_INB.CHG_INFO, EXTENTIONS.DEPT_ID,DEPTS.DEPT_NAME,  
                     EXTENTIONS.DESCRIPTION
                   FROM CDR_MAIN_INB
                   LEFT JOIN EXTENTIONS ON CDR_MAIN_INB.TER_DN = EXTENTIONS.EXT_NO AND 
                                           CDR_MAIN_INB.SITE_ID = EXTENTIONS.SITE_ID
                   LEFT JOIN DEPTS ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID";
       if ($dept_crt)
            $kriter2 .= str_replace("CDR_MAIN_DATA","CDR_MAIN_INB",str_replace("ORIG_DN","TER_DN",$dept_crt));
       if ($usr_crt)
            $kriter2 .= str_replace("CDR_MAIN_DATA","CDR_MAIN_INB",str_replace("ORIG_DN","TER_DN",$usr_crt));
       if ($unrep_exts_crt)
            $kriter2 .= str_replace("CDR_MAIN_DATA","CDR_MAIN_INB",str_replace("ORIG_DN","TER_DN",$unrep_exts_crt));
      
      $sql_str .= " WHERE ".$kriter." ".$kriter2;
       //echo $sql_str;exit;
      switch ($order) {
        case 'Dahili':
          $order ='TER_DN';
          break;
		case 'HatNo':
          $order ='ORIG_TRUNK_MEMBER';
          break;  
		case 'Departman':
          $order ='DEPT_NAME';
          break;	
        case 'Arayan':
          $order ='CLID';
          break;
        case 'tarih':
          $order ='TIME_STAMP';
          break;
        case 'saat':
          $order ='MY_TIME';
          break;
        case 'sure';
          $order ='DURATION ';
          break;
		case 'ring';
          $order ='CHG_INFO ';
          break; 
        default:
          $order='TIME_STAMP';
      }
     
      if ($order) {
         $sql_str .= " ORDER BY ".$order." ".$sort_type; 
      }

      if ($record <> '' || is_numeric($record)) {
         $sql_str .= " LIMIT 0,". $record ;
      }
	  
	  $sql_str = str_replace("CDR_MAIN_DATA","CDR_MAIN_INB",$sql_str);
	  
    //  echo $sql_str;exit;
      if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
         print_error($error_msg);
         exit;
      }

?>
<br>

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
            title: "Gelen Çağrı"
        },
        {
            extend: 'csvHtml5',
            title: "Gelen Çağrı"
        }, 
            'print', {text: "email",
        action: function (e ,dt, node, config){
          location.href = "javascript:mailPage('inbound.html')";
        }} ]
		
    } );
	} );
	  
	</script>	

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

<script language="JavaScript">
  function submit_form(sortby){
    document.all('sort_me').action='report_inb_prn.php?act=src&order=' + sortby;    
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
//     this.location.reload(true);
       this.location.href = pagename;

    }    
  }   
     
</script>
            <table>
              <tr  <?if ($ter<>'Yes') echo "style=\"display:none;\""?>>
                <td nowrap width="20%" valign="top">Dahili:</td>
                <td width="80%" valign="top">
                <?if (is_array($TER_ARRAY)){ 
                     for ($i = 0; $i < count($TER_ARRAY); $i++){
                         if (is_numeric($TER_ARRAY[$i])) {?>
                           <?echo $TER_ARRAY[$i];?> - <? echo get_ext_name($TER_ARRAY[$i]).";";
                         }
                     }
                 }
               ?>
              </td>
            </tr> 
            <tr  <?if ($dept<>'Yes') echo "style=\"display:none;\""?>>
              <td class="rep_header" nowrap width="20%" valign="top">Departman:</td>
              <td width="80%" valign="top">
              <?if (is_array($DEPT_ID)) { 
                  for ($i = 0; $i < count($DEPT_ID); $i++) {
                      if(is_numeric($DEPT_ID[$i])) {?>
                      <? echo get_dept_name($DEPT_ID[$i],$SITE_ID).";";}
                      }
                  }?>
              </td>
           </tr>
           <tr <?if ($clid<>'Yes') echo "style=\"display:none;\""?>>
              <td class="rep_header" nowrap width="20%">Arayan:</td>
              <td width="80%"><? echo $CLID;?>
           </tr>
         </table>  
       </td>
       <td width="50%">
          <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr <?if ($dur<>'Yes') echo "style=\"display:none;\""?>>
              <td class="rep_header" align="right" nowrap width="40%">Süre:</td>
              <td width="60%"><?echo $DURATION;?></td>
            </tr>
          </table>
       </td>
     </tr>
   </table>
  </td>  
 </tr>
 <tr>
   <td align="right">
     <table width="100%" cellspacing=0 cellpadding=0>
        <tr>
          <td width="50%" class="rep_header" align="left">
            <?if($t0!=""){?> Tarih (<?=date("d/m/Y",strtotime($t0))?>
                   <? 

				   if($MY_DATE=="b" || $MY_DATE=="f" ){
					   echo  " - ".date("d/m/Y",strtotime($t0));
				   }	   
				   else{
                    echo (" - ".date("d/m/Y",strtotime("$t1")));
					}

				?>
                    )<?}?>
          </td> 
          <td width="50%" class="rep_header" align="right">
         </td></tr>
        </table>
        </td>
    </tr>    
  <tr>
    <td>
	
    <table id="export"class="display nowrap" style="width:100%">
        <thead>
          <tr>
          <td width="18%" ALIGN="center"><b>Dahili</b></td>
            <td width="9%" ALIGN="center"><b>Hat No</b></td>
            <td width="15%" ALIGN="center"><b>Departman</b></td>
            <td width="14%" ALIGN="center"><b>Arayan No</b></td>
            <td width="14%" ALIGN="center"><b>Arayan</b></td>
            <td width="9%" ALIGN="center"><b>Tarih</b></td>
            <td width="9%" ALIGN="center"><b>Saat</b></td>
            <td width="9%" ALIGN="center"><b>Süre</b></td>
            <td width="9%" ALIGN="center"><b>Ring</b></td>
          </tr>
        </thead>
		
      <?
        $my_dur = 0;
        $my_amount = 0; 
        $i = 0;
        $csv_data[0][0] = "";
            
        if (mysqli_num_rows($result)>0)
           mysqli_data_seek($result,0);
           $csv_data[0][0] = "Dahili";
		   $csv_data[0][1] = "HatNo";
		   $csv_data[0][2] = "Departman";
           $csv_data[0][3] = "Arayan No";
           $csv_data[0][4] = "Arayan" ;
           $csv_data[0][5] = "Tarih";
           $csv_data[0][6] = "Saat";
           $csv_data[0][7] = "Sure";
		   $csv_data[0][8] = "Ring";

           while ($row = mysqli_fetch_object($result)){
                //if (!in_array ($row->TER_DN, $arr_ext)){ 
                   $i++;
                   $csv_data[$i][0] = "$row->TER_DN - $row->DESCRIPTION";
				   $csv_data[$i][1] = $row->ORIG_TRUNK_MEMBER;
                   $csv_data[$i][2] = $row->DEPT_NAME;
				   $csv_data[$i][3] = $row->CLID;
				   $csv_data[$i][4] = get_contact($row->CLID);
                   $csv_data[$i][5] = $row->MY_DATE;
                   $csv_data[$i][6] = $row->MY_TIME;
                   $csv_data[$i][7] = calculate_all_time($row->DURATION);
				   $csv_data[$i][8] = $row->CHG_INFO; 

                   $bg_color = "E4E4E4";   
                   if ($i % 2) $bg_color ="FFFFFF";
                   if ($show_clid <> 1){
                      echo ' <tr style="text-align: center;">';
                      echo " <td>$row->TER_DN - $row->DESCRIPTION</td>";
					  echo " <td>$row->ORIG_TRUNK_MEMBER</td>";
					  echo " <td>".str_replace("X","",$row->DEPT_NAME)."</td>";
                      echo " <td>".str_replace("X","",$row->CLID)."</td>";
                      echo " <td>".get_contact(str_replace("X","",$row->CLID))."</td>";
                      echo " <td>$row->MY_DATE</td>";
                      echo " <td>$row->MY_TIME</td>";
                      echo " <td>".calculate_all_time($row->DURATION);
					  echo " <td>$row->CHG_INFO</td>";
                      echo "</tr>";
                      $my_dur = $my_dur + $row->DURATION;
                      $my_amount = $my_amount + 1;
                   }else if ($show_clid == 1){
                      if ($row->CLID <> ''){
                         echo " <tr  BGCOLOR=$bg_color>";
                         echo " <td>$row->TER_DN - $row->DESCRIPTION</td>";
						 echo " <td>$row->ORIG_TRUNK_MEMBER</td>";
						 echo " <td ALIGN=right>".str_replace("X","",$row->DEPT_NAME)."</td>";
                         echo " <td ALIGN=right>".str_replace("X","",$row->CLID)."</td>";
                         echo " <td>".get_contact(str_replace("X","",$row->CLID))."</td>";
                         echo " <td>$row->MY_DATE</td>";
                         echo " <td>$row->MY_TIME</td>";
                         echo " <td>".calculate_all_time($row->DURATION);
						 echo " <td class=\"rep_td\">$row->CHG_INFO</td>";
                         echo "</tr>";
                         $my_dur = $my_dur + $row->DURATION;
                         $my_amount=$my_amount + 1;
                      }
                   }
                
            }
            $i++;
            $csv_data[$i][4] = "Toplam Gorusme Adedi";
            $csv_data[$i][5] = $my_amount;
            $i++;
            $csv_data[$i][4] = "Toplam Sure Adedi";
            $csv_data[$i][5] = $my_dur;
			csv_out($csv_data, "/usr/local/wwwroot/multi.crystalinfo.net/root/temp/inbound.csv"); 
                   
				  //echo $csv_data[$i][5]; die;
      ?>
      <tfoot>
      <tr>
            <td width="18%" ALIGN="center"><b>Dahili</b></td>
            <td width="9%" ALIGN="center"><b>Hat No</b></td>
            <td width="15%" ALIGN="center"><b>Departman</b></td>
            <td width="14%" ALIGN="center"><b>Arayan No</b></td>
            <td width="14%" ALIGN="center"><b>Arayan</b></td>
            <td width="9%" ALIGN="center"><b>Tarih</b></td>
            <td width="9%" ALIGN="center"><b>Saat</b></td>
            <td width="9%" ALIGN="center"><b>Süre</b></td>
            <td width="9%" ALIGN="center"><b>Ring</b></td>
          </tr>
      </tfoot>
      </table>
	  
    </td>
  </tr>
  <tr height="20">
    <td>
    </td>
  </tr>
  <tr>
    <td height="22" colspan="1"  align="right">
    <br>
      <TABLE BORDER="0" WIDTH="100%">
        <TR>
          <TD WIDTH="80%" ALIGN="right"><b>Toplam Görüşme Adedi :</b></TD>
          <TD WIDTH="20%" ><?=number_format($my_amount,0,'','.')?></TD>
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
       <br>
  </tr>   
</table>        
<? } // End of if ($act == 'src')

###################################
# CSV OUT 
# Date: 
###################################
//ECHO $data; die;
function xcsv_out($csv_data, $filename) {
  if(is_array($csv_data))
  {
  foreach ($csv_data as $k1 => $v1) {
    foreach($csv_data[$k1] as $k2 => $v2) {
      $file .= $v2.";";
    }
    $file = substr($file, 0, -1);
    $file .= "\n";
  }

  //Header ( "Content-Type: application/octet-stream");
  //Header ( "Content-Length: ".filesize(2000)); 
  //Header( "Content-Disposition: attachment; filename=$filename"); 
  //echo $file;
      $fp = fopen($filename, "w+");
      fwrite($fp, $file);
	  
	  //ECHO $file; die;
      return true;
  }
}


 $fd = fopen($DOCUMENT_ROOT."/temp/inbound.html", "w");
 fwrite($fd,ob_get_contents());
 
?>

<?
  if($CSV_EXPORT == 2){
    $fd = fopen($DOCUMENT_ROOT."/temp/inbound.xls", "w");
    fwrite($fd,ob_get_contents());
  }else{
    $fd = fopen($DOCUMENT_ROOT."/temp/inbound.html", "w");
    fwrite($fd,ob_get_contents());
  }
  ob_end_flush();
 
   if($CSV_EXPORT==1){?>
 <iframe SRC="/csv_download.php?filename=inbound.csv" WIDTH=0 HEIGHT=0 ></iframe>
  <a HREF="/temp/inbound.csv" style="font-size:20px">CSV Download</a>
<?}else if($CSV_EXPORT==2){?>
 <iframe SRC="/csv_download.php?filename=inbound.xls" WIDTH=0 HEIGHT=0 ></iframe>
 <a HREF="/temp/inbound.xls" style="font-size:20px">XLS Download</a>
 <?}?>





 
