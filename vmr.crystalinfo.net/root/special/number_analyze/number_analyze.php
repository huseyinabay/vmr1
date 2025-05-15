<?  //INCLUDES
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   
   require_valid_login();

   if (!defined("IMAGE_ROOT")){ // Note that it should be quoted
      define("IMAGE_ROOT", "/images/");
   }  
   $cUtility = new Utility();
   $cdb = new db_layer();
   $conn = $cdb->getConnection();
   
   if (right_get("SITE_ADMIN")){
      if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
        //Site admin hakkı varsa herşeyi görebilir.
   }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
    // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
      $SITE_ID = $_SESSION['site_id'];
   }else{
      print_error("Bu sayfayı görmeye hakkınız yok!");
      exit;
   }
   cc_page_meta(0);
   page_header("");           
   $DATE = str_replace(".", "/", $DATE);
   $start_datetime = "$DATE $START_HOUR:00:00";   
   $start_date = convert_date_time($start_datetime,'');
   
   $bas_tar = strtotime($start_date) ;
   $birsaat = 3600;
   
   
//   $DATE = normal2db($DATE);
   $CDR_MAIN_DATA = getTableName($start_date,$start_date);
   if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";

   ?>

   <?
   function numara_analizi($ext_no="", $start_date="", $hour=""){
         global $cdb, $GSM_CALL, $numkriter, $SITE_ID;
//         $data[] = 0;$data[] = 0;$data[] = 0;
          
       $sql ="SELECT TIME_STAMP_DAY,TIME_STAMP_HOUR, TIME_STAMP_MN, TIME_STAMP,  
							 (DURATION_MN + CEILING((DURATION_SN-(60-TIME_STAMP_SN))/60)) AS NEXT_MN
               FROM CDR_MAIN_DATA
               WHERE CALL_TYPE>0 AND ERR_CODE=0
							 AND	(TIME_STAMP>=DATE_FORMAT('$start_date','%Y-%m-%d %H:%i:%s') 
							 AND TIME_STAMP<=DATE_FORMAT(DATE_ADD('$start_date',INTERVAL 4 HOUR),'%Y-%m-%d %H:%i:%s'))               
							 AND (DATE_FORMAT(TIME_STAMP,'%H:%i:%S')>=DATE_FORMAT('$start_date','%H:%i:%S') 
							 AND DATE_FORMAT(TIME_STAMP,'%H:%i:%S')<DATE_FORMAT(DATE_ADD('$start_date',INTERVAL 5 HOUR),'%H:%i:%S'))
               AND (ORIG_DN='$ext_no') AND SITE_ID=".$SITE_ID." 
               ".$numkriter."
               GROUP BY TIME_STAMP_DAY,TIME_STAMP_HOUR, TIME_STAMP_MN
         ";

           $cdb->execute_sql($sql, $result, $error);
           while($row = mysqli_fetch_object($result)){
                $data[$row->TIME_STAMP_HOUR][$row->TIME_STAMP_MN] = 1;
                $nm='0';
                if($row->NEXT_MN>$nm){
                   while($row->NEXT_MN>=$nm){
                      $nm++;
                      $data[$row->TIME_STAMP_HOUR][$row->TIME_STAMP_MN+$nm] = 1;
                   }
                      $nm=0; 
                }
           }   
       return $data;    
   }
   function numara_alizi_yaz($data=Array(),$hour){
         global $START_HOUR, $sub_totals;
         if(!is_array($data))
            $data=Array(1,1,1,1,1);
           
/*          for ($km=$hour;$km< (($hour))+5; $km++){
               if(!is_array($data['$km']))
                  $data['$km']=Array();
          }
  */         
         // if(is_array($data)){
             for($i=$START_HOUR;$i<$START_HOUR+4;$i++){
                for($k=0;$k<60;$k++){
                     if($data[$i][$k]) {
                        $sub_totals[$i][$k]= $sub_totals[$i][$k]+1;
                        echo "<td bgcolor=#FF0000>&nbsp;</td>\n";
                     }else{
                        echo "<td >&nbsp;</td>\n";
                     }
                }
               echo "<td>|</td>";
             }   
         // } 
   }
  if(!$HAT_CNT) $HAT_CNT = 10;
  
   ?>
<script LANGUAGE="javascript" src="/scripts/popup.js"></script>
<script language="JavaScript">
  function man_add_div(){
    if(document.all('manual_add_form').style.display=='none'){
      document.all('manual_add_form').style.display='';
      document.all('manual_add').value='Fihristten Ekle';
      document.all('num_add').style.display='none';
    }else{
      document.all('manual_add_form').style.display='none';
      document.all('num_add').style.display='';
      document.all('manual_add').value='Elle Ekle';
    }
  }

  function manual_add_to_list(){
    var myList = document.all('PHONE_NUMS[]');
    var ListText = document.all('CountryCode').value+' '+document.all('LocalCode').value+' '+document.all('PURE_NUMBER').value
    var ValueText = document.all('CountryCode').value+';'+document.all('LocalCode').value+';'+document.all('PURE_NUMBER').value
    var TempVal='', cnt;
    if(document.all('CountryCode').value=='' && document.all('LocalCode').value=='' && document.all('PURE_NUMBER').value==''){
      return false;
    }
    if(isNaN(document.all('CountryCode').value) || isNaN(document.all('LocalCode').value) || isNaN(document.all('PURE_NUMBER').value)){
      alert('Numara alanlarına sayısal olmayan bir değer girdiniz!');
      return false;
    }
    for(cnt=0;cnt<=myList.options.length-1;cnt++){
      TempVal = myList.options[cnt].value;
      if(TempVal==ValueText){
        return false;
      }
    }
      myList.options[myList.options.length] = new Option(ListText, ValueText, false, false);
  }

  function remove_items(){
    var myList = document.all('PHONE_NUMS[]');
    var cnt=0;
    while(myList.options.length>cnt){
      if(myList.options[cnt].selected){
        myList.options.remove(cnt);
      }else{
        cnt++;
      }
    }
  
  }
  
  function submit_me(){
    var myList = document.all('PHONE_NUMS[]');
    for(cnt=0;cnt<=myList.options.length-1;cnt++){
      myList.options[cnt].selected=true;
    }
    document.all('numara_analiz').submit();
  }
</script>   
   <?table_header("VOIP Kanal Analizi", "80%");?>
   <center>
<form name="numara_analiz" method="post" action="number_analyze.php?act=src">
       	<input type="hidden" name="p" VALUE="<?echo $p?>">
       	<input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
       	<table cellpadding="0" cellspacing="0" border="0" width="80%">
          <tr>  
             <td width="35%" align="LEFT" class="td1_koyu">Site Adı</td>
                 <td>
                     <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>>
                         <?
                             $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ORDER BY SITE_NAME";
                             echo $cUtility->FillComboValuesWSQL($conn, $strSQL, false,  $SITE_ID);
                         ?>
                     </select>
                 </td>
          </tr>
          	<tr> 
                    <td width="30%" class="td1_koyu">Tarih</td>
                    <td width="80%" NOWRAP>
                      	<input type="teXt" class="input1" name="DATE" VALUE="<?echo $DATE?>" Maxlength="10">  
                      	<img style="cursor:hand" SRC="<?=IMAGE_ROOT?>takvim_icon.gif" onclick="javascript:show_calendar(document.all('DATE').name,null,null,null,event.screenX,event.screenY,1);">
                       	<img style="cursor:hand" SRC="<?=IMAGE_ROOT?>sil_icon.gif" onclick="javascript:document.all('DATE').value=''">
                    </td>
          	</tr>

           	<tr> 
                    <td width="50%" class="td1_koyu" NOWRAP>Dahili Adedi</td>
                    <td width="50%"><input type="text" class="input1" name="HAT_CNT" VALUE="<?echo $HAT_CNT?>" size="4" Maxlength="2"></td>
          	</tr>
           	<tr> 
                    <td width="50%" class="td1_koyu" NOWRAP>Başlangıç Saati</td>
                    <td width="50%"><input type="text" class="input1" name="START_HOUR" VALUE="<?echo $START_HOUR?>" size="4" Maxlength="2"></td>
          	</tr>

           	<tr> 
                    <td width="50%" class="td1_koyu" NOWRAP>Aranan Numaralar</td>
                    <td width="50%">  
                    <table><tr><td>
                    <select name="PHONE_NUMS[]" id="phn_list" class="select1" style="width:250;" multiple>
                    <?
                      if($act=="src" && is_array($PHONE_NUMS)){
                          for($i=0;$i<count($PHONE_NUMS);$i++){
                            $num_arr = split(";",$PHONE_NUMS[$i]);
                            echo  "<option value=\"".$PHONE_NUMS[$i]."\">".trim($num_arr[0])." ".trim($num_arr[1])." ".$num_arr[2]."</option>";
                          }
                      }    
                    ?>
                   </select></td>
                   <td>
                     <input type="button" class="input" name="num_rem" VALUE="Çıkar" size="15" Maxlength="20" onclick="remove_items()">
                   </td>
                   </tr></table>
                   <br><br>
                   <input type="button" class="input" name="num_add" VALUE="Fihrist" size="15" Maxlength="20" onclick="javascript:popup('contact_phones.php?SITE_ID=<?=$SITE_ID?>','contact_phones',700,500)">&nbsp;
                   <input type="button" class="input" name="manual_add" VALUE="Elle Ekle" size="15" Maxlength="20" onclick="javascript:man_add_div();">
                   <br><br>
                   </td>
            </tr>                     
            <tr id="manual_add_form" style="display:none">                     
                <td width="50%" colspan=2>          
                   <table width=100% border=1>
                     <tr><td align=center valign=center>Ülke Kodu:<input type="text" class="input1" name="CountryCode" VALUE="" size="4">&nbsp;
                     Alan Kodu:<input type="text" class="input1" name="LocalCode" VALUE="" size="5">&nbsp;
                     Numara:<input type="text" class="input1" name="PURE_NUMBER" VALUE="" size="12">
                     <input type="button" class="input" name="now_add" VALUE="Şimdi Ekle" onclick="javascript:manual_add_to_list();"></td></tr>
                   </table>
                   <br>
                    </td>
          	</tr>
           	<tr> 
                    <td width="50%" class="td1_koyu"></td>
                    <td width="50%"><input type="button" class="input" name="Göster" VALUE="Rapor Al" size="15" Maxlength="20" onclick="javascript:submit_me();"></td>
          	</tr>
      	</table>
   		</form>
      </center>  
<?
table_footer();
if($act=="src" && $DATE){?>   
<table border="1" cellspacing="0" CELLpadding=0>
 <tr>
    <td ROWSPAN=2><b>Arayan Dahili</b></td>
    <td colspan="60">
      <p align="center"><b><?=$start_datetime?></b>
    </td>
    <td>|</td>
    <td colspan="60">
      <p align="center"><b><?=date("d/m/Y H:i:s", $bas_tar+$birsaat)?></b>
    </td>
    <td>|</td>
    <td colspan="60">
      <p align="center"><b><?=date("d/m/Y H:i:s", $bas_tar+(2*$birsaat))?></b>
    </td>
    <td>|</td>
    <td colspan="60">
      <p align="center"><b><?=date("d/m/Y H:i:s", $bas_tar+(3*$birsaat))?><b>
    </td>
    <td>|</td>

  </tr>

  <tr>

  <?for($i=1;$i<=60;$i++){?>
    <td>.</td>
  <?}?>  
    <td>|</td>
  <?for($i=1;$i<=60;$i++){?>
    <td>.</td>
  <?}?>  
      <td>|</td>
  <?for($i=1;$i<=60;$i++){?>
    <td>.</td>
  <?}?>  
      <td>|</td>
  <?for($i=1;$i<=60;$i++){?>
    <td>.</td>
  <?}?>  
    <td>|</td>
  </tr>
  <?
  
    if(is_array($PHONE_NUMS)){
      for($i=0;$i<count($PHONE_NUMS);$i++){
        $num_arr = split(";",$PHONE_NUMS[$i]);
        if($numkriter==""){
          $numkriter = " (CountryCode='".trim($num_arr[0])."' AND LocalCode='".trim($num_arr[1])."' AND PURE_NUMBER='".$num_arr[2]."')";
        }else{
          $numkriter .= " OR (CountryCode='".trim($num_arr[0])."' AND LocalCode='".trim($num_arr[1])."' AND PURE_NUMBER='".$num_arr[2]."')";
        }
      }
    }

   if($numkriter !=""){$numkriter = "AND (".$numkriter.")";}
   $sql = "
      SELECT ORIG_DN, COUNT(TER_TRUNK_MEMBER) AS CNT FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
      WHERE CALL_TYPE=1
      AND (TIME_STAMP>=DATE_FORMAT('$start_date','%Y-%m-%d %H:%i:%s') 
      AND TIME_STAMP<=DATE_FORMAT(DATE_ADD('$start_date',INTERVAL 4 HOUR),'%Y-%m-%d %H:%i:%s'))
       AND SITE_ID=".$SITE_ID." 
      ".$numkriter."
      GROUP BY  ORIG_DN
      ORDER BY CNT DESC
      LIMIT $HAT_CNT
   ";
   $exts = Array();
   $sub_totals = Array();
   if(!$cdb->execute_sql($sql, $result, $error)){
        echo   $error;
   }
   while($row = mysqli_fetch_object($result)){
       $exts[] = $row->ORIG_DN;
   }

  
  foreach($exts as $value){
      echo "  <tr>   <td><b>$value</b></td>\n";     
      numara_alizi_yaz(numara_analizi($value, $start_date, $START_HOUR),$START_HOUR);
      echo "</tr>\n";
  }
  echo "<tr><td>&nbsp;</td>\n";
  $MaxChn = 0;
  for($i=$START_HOUR;$i<$START_HOUR+4;$i++){
    for($j=0;$j<60;$j++){
      if($sub_totals[$i][$j]){ 
        echo "<td>".$sub_totals[$i][$j]."</td>";
        if($sub_totals[$i][$j]>$MaxChn){
          $MaxChn = $sub_totals[$i][$j];
        }
        $ChnCntDurs[$sub_totals[$i][$j]]++;
      }else{
        echo "<td>0</td>";
      }
    }
    echo "<td>|</td>\n";
  }
  echo "</tr>\n";
  $rmn = 1;
  ?>
</table>
<br><br>
<?table_header("Özet Bilgiler", "50%");?>
<table border=0 width="100%">
  <tr class="bgc1">
    <td><b>Max Aynı Anda Görüşme Sayısı</b></td>
    <td><?=$MaxChn?></td>
  </tr>
  <tr class="bgc2">
    <td><b>Görüşme Sayıları</b></td>
    <td><b>Süre(dk)</b></td>
  </tr>
<?  for($i=$MaxChn;$i>0;$i--){
      if($rmn == 1){$rmn = 2;}else{$rmn = 1;}
      echo "<tr class=\"bgc".$rmn."\">";?>
    <td><?=$i?></td>
    <td><?=$ChnCntDurs[$i]?></td>
  </tr>
  <?}?>
</table>
<?table_footer();?>
<br>
<?}page_footer(0);?>
</body>
</html>
