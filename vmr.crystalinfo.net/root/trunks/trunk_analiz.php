<?  //INCLUDES
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   
   require_valid_login();

   if (!defined("IMAGE_ROOT")){ // Note that it should be quoted
      define("IMAGE_ROOT", "/images/");
   }  
   $cUtility = new Utility();
   $cdb = new db_layer();
   $conn = $cdb->getConnection();

   cc_page_meta(0);
   page_header("");           
   $DATE = str_replace(".", "/", $DATE);
   $start_date = "$DATE $START_HOUR:00:00";   
   $start_date = convert_date_time($start_date,'');
//   $DATE = normal2db($DATE);
   $CDR_MAIN_DATA = getTableName($start_date,$start_date);
   if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";

   ?>

   <?
   function trunk_analizi($trunk="", $start_date="", $hour=""){
         global $cdb, $GSM_CALL;
//         $data[] = 0;$data[] = 0;$data[] = 0;
          
       $sql ="
							 
							 SELECT TIME_STAMP_DAY,TIME_STAMP_HOUR, TIME_STAMP_MN, TIME_STAMP,  
							 (DURATION_MN + CEILING((DURATION_SN-(60-TIME_STAMP_SN))/60)) AS NEXT_MN
               FROM CDR_MAIN_DATA
               WHERE CALL_TYPE>0 AND ERR_CODE=0
							 AND	(TIME_STAMP>=DATE_FORMAT('$start_date','%Y-%m-%d %H:%i:%s') 
							 AND TIME_STAMP<=DATE_FORMAT(DATE_ADD('$start_date',INTERVAL 5 HOUR),'%Y-%m-%d %H:%i:%s'))               
							 AND (DATE_FORMAT(TIME_STAMP,'%H:%i:%S')>=DATE_FORMAT('$start_date','%H:%i:%S') 
							 AND DATE_FORMAT(TIME_STAMP,'%H:%i:%S')<=DATE_FORMAT(DATE_ADD('$start_date',INTERVAL 5 HOUR),'%H:%i:%S'))
               AND (TER_TRUNK_MEMBER='$trunk')
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
   function trunk_alizi_yaz($data=Array(),$hour){
         if(!is_array($data))
            $data=Array(1,1,1,1,1);
           
/*          for ($km=$hour;$km< (($hour))+5; $km++){
               if(!is_array($data['$km']))
                  $data['$km']=Array();
          }
  */         
         // if(is_array($data)){
             foreach($data as $key=>$value){
                for($k=0;$k<60;$k++){
                     if($data[$key][$k]) {
                        echo "<td><img src='red.gif'></td>\n";
                     }else{
                        echo "<td >&nbsp;</td>\n";
                     }
                }
               echo "<td>|</td>";
             }   
         // } 
   }
   ?>
<form name="trunk_analiz" method="post" action="trunk_analiz.php?act=src">
       	<input type="hidden" name="p" VALUE="<?echo $p?>">
       	<input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
       	<table cellpadding="0" cellspacing="0" border="0" width="60%">
          	<tr> 
                    <td width="30%" class="font_beyaz">Tarih</td>
                    <td width="80%" NOWRAP>
                      	<input type="teXt" class="input1" name="DATE" VALUE="<?echo $DATE?>" Maxlength="10">  
                      	<img style="cursor:hand" SRC="<?=IMAGE_ROOT?>takvim_icon.gif" onclick="javascript:show_calendar(document.all('DATE').name,null,null,null,event.screenX,event.screenY,1)">
                       	<img style="cursor:hand" SRC="<?=IMAGE_ROOT?>sil_icon.gif" onclick="javascript:document.all('DATE').value=''">
                    </td>
          	</tr>
           	<tr> 
                    <td width="50%" class="font_beyaz" NOWRAP>GSM Aramaları</td>
                    <td width="50%"><input type="text" class="input1" name="GSM_CALL" VALUE="<?echo $GSM_CALL?>" size="4" Maxlength="3"></td>
          	</tr>

           	<tr> 
                    <td width="50%" class="font_beyaz" NOWRAP>Hat Adedi</td>
                    <td width="50%"><input type="text" class="input1" name="HAT_CNT" VALUE="<?echo $HAT_CNT?>" size="4" Maxlength="2"></td>
          	</tr>
           	<tr> 
                    <td width="50%" class="font_beyaz" NOWRAP>Başlangıç Saati</td>
                    <td width="50%"><input type="text" class="input1" name="START_HOUR" VALUE="<?echo $START_HOUR?>" size="4" Maxlength="2"></td>
          	</tr>

           	<tr> 
                    <td width="50%" class="font_beyaz"></td>
                    <td width="50%"><input type="submit" class="input1" name="Göster" VALUE="Göster" size="15" Maxlength="20"></td>
          	</tr>
      	</table>
   		</form>
   
<table border="1" cellspacing="0" CELLpadding=0>
 <tr>
    <td colspan="60">
      <p align="center"><?= $start_date?>
    </td>
    <td colspan="60">
      <p align="center">Sonraki 1 Saat
    </td>
    <td colspan="60">
      <p align="center">Sonraki 1 Saat
    </td>
    <td colspan="60">
      <p align="center">Sonraki 1 Saat
    </td>
    <td colspan="60">
      <p align="center">Sonraki 1 Saat
    </td>

  </tr>

  <tr>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>|</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
      <td>|</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
      <td>|</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
      <td>|</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    <td>.</td>
    
    
  </tr>
  <?
  
  if(!$HAT_CNT) $HAT_CNT = 10;
 
  
 echo  $sql = "
      SELECT TER_TRUNK_MEMBER, COUNT(TER_TRUNK_MEMBER) AS CNT FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
      WHERE CALL_TYPE=1
      AND (TIME_STAMP>=DATE_FORMAT('$start_date','%Y-%m-%d %H:%i:%s') AND TIME_STAMP<=DATE_FORMAT(DATE_ADD('$start_date',INTERVAL 5 HOUR),'%Y-%m-%d %H:%i:%s'))
      GROUP BY  TER_TRUNK_MEMBER
      ORDER BY CNT DESC
      LIMIT $HAT_CNT
   ";
   $trunks = Array();
   if(!$cdb->execute_sql($sql, $result, $error)){
        echo   $error;
   }
   while($row = mysqli_fetch_object($result)){
       $trunks[] = $row->TER_TRUNK_MEMBER;
   }

   
   
   $sql = "
      SELECT TER_TRUNK_MEMBER, COUNT(TER_TRUNK_MEMBER) AS CNT FROM CDR_MAIN_INB AS CDR_MAIN_DATA
      WHERE CALL_TYPE=2
      AND (TIME_STAMP>=DATE_FORMAT('$start_date','%Y-%m-%d %H:%i:%s') AND TIME_STAMP<=DATE_FORMAT(DATE_ADD('$start_date',INTERVAL 5 HOUR),'%Y-%m-%d %H:%i:%s'))
      GROUP BY  TER_TRUNK_MEMBER
      ORDER BY CNT DESC
      LIMIT $HAT_CNT
   ";
   
   $cdb->execute_sql($sql, $result, $error);
   while($row = mysqli_fetch_object($result)){
    //     if(!in_array($row->ORIG_ID,$trunks))
  //          $trunks[] = $row->ORIG_ID;
   }
  
  foreach($trunks as $value){
      echo "  <tr>   <td>$value</td>";     
      trunk_alizi_yaz(trunk_analizi($value, $start_date, $START_HOUR),$START_HOUR);
      echo "</tr>";
  }
  
  ?>
</table>



</body>
</html>
