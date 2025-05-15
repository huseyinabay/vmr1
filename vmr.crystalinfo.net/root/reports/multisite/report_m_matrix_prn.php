<? require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer(); 
   $conn = $cdb->getConnection();
   require_valid_login();

    check_right("SITE_ADMIN");

    $max_acc_dur = get_system_prm("MAX_ACCE_DURATION");
    if ($act == "src") {
     
      $kriter = "";   
     
        //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
      $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.
      $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     , "=",  "1"); //Bu mutlaka olmalı.Giden Çağrı olduğunu gösteriyor.
      $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Giden Çağrı olduğunu gösteriyor.

      //Zaman kriterleri ve tablo ismi seçimi başlangıç
      add_time_crt();//Zaman kriteri
  	  $link  ="";

      $CDR_MAIN_DATA = getTableName($t0,$t1);
      if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";  

      //Zaman kriterleri ve tablo ismi seçimi bitiş

      $sql_str = "SELECT TelProviderid, TelProvider FROM TTelProvider";
      if (!($cdb->execute_sql($sql_str,$result_row,$error_msg))){
        print_error($error_msg);
        exit;
      }
      $i=1;
      while($res_row = mysqli_fetch_array($result_row)){
        $row_arr[$res_row["TelProviderid"]] = $res_row["TelProvider"];
        $i = $i + 1;
      }

     function write_me($MyVal,$calc_type){
       if ($calc_type == 1){
         $MyRetVal = write_price($MyVal);
       }elseif($calc_type==2){
         $MyRetVal = calculate_all_time($MyVal);
       }elseif($calc_type==3){
         $MyRetVal = number_format($MyVal,0,'','.');
       }else{
         print_error("Hatalı Durum Oluştu. Lütfen Tekrar Deneyiniz.");
        exit;
       }
       return $MyRetVal;
     }
   
     $sql_str = "SELECT FROM_PROVIDER_ID, TO_PROVIDER_ID, SUM(DURATION) AS SURE, SUM(PRICE) AS TUTAR, COUNT(CDR_ID) AS ADET
               FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
			  ";
   
     if ($kriter != "")
       $sql_str .= " WHERE ".$kriter;
     $sql_str .= " GROUP BY FROM_PROVIDER_ID, TO_PROVIDER_ID
		   	       ORDER BY FROM_PROVIDER_ID ASC";

   //echo $sql_str."<br>";
   if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
     print_error($error_msg);
     exit;
   }
   if (mysqli_num_rows($result)==0){
     print_error("Kayıtlara Ulaşılamadı");
	 exit;
   }
  $report_type="Operatörlerin Çağrı Analiz Matrisi";
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
         <input type="hidden" name="forceMainTable" VALUE="<?=$forceMainTable?>" >         
         <input type="hidden" class="cache1" name="withCache" value="<?=$withCache?>" >
  </form>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" width="100%" align="center" class="rep_header" align="center">
          <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD><a href="http://www.crystalinfo.net" target="_blank"><img border="0" SRC="<?=IMAGE_ROOT?>logo2.gif"></a></TD>
              <TD width="50%" align=center CLASS="header"><?echo $company?><BR><?=$report_type?></TD>
              <TD width="25%" align=right><img SRC="<?=IMAGE_ROOT?>company.gif"></TD>
            </TR>
            </TABLE>

      </td>
  </tr>
  <tr>
    <td width="100%" class="rep_header" align="left">
    <table  width="100%" cellspacing=0 cellpadding=0>
      <tr>
        <td width="70%" class="rep_header" align="left">
        <table width="50%" cellspacing="0" cellpadding="0" border="0">
          <tr <?if($record=="") echo "style=\"display:none;\""?>>
            <td class="rep_header" align="left" nowrap width="40%">Kayıt Adedi :</td>
            <td width="60%" align="right"><?echo $record;?></td>            
          </tr>
          <tr <?if($hafta=="3") echo "style=\"display:none;\""?>>
            <td class="rep_header" align="left" nowrap width="40%" colspan="2"><?=($hafta=="1")?"Hafta İçi":"Hafta Sonu"?></td>
          </tr>
          <tr <?if($hh0==-1 && $hh1 ==-1) echo "style=\"display:none;\""?>>
            <td class="rep_header" align="left" nowrap width="40%">Saat Dilimi :</td>
            <td width="60%" align="right"><?=($hh0==-1)?"":$hh0.":".$hm0?> - <?=($hh1==-1)?"":$hh1.":".$hm1?></td>            
          </tr>
        </table>    
      </tr>
      <tr>
        <td width="50%" class="rep_header" align="left">
         <?if($t0!=""){?> Tarih (<?=date("d/m/Y",strtotime($t0))?>
           <?if($t1!=""){?> <?echo (" - ".date("d/m/Y",strtotime($t1)));}?>)
        <?}?></td>
        <td width="50%" align="right"></td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
    <td width="100%">
	<table  width="100%" cellspacing=0 cellpadding=0>
    <?for($i = 1;$i< sizeof($row_arr);$i++){
        if($i==6){continue;}?>
	<tr>
      <td bgcolor="#B3CAE3" width="3%" valign="center" align="center"><img src="<?=IMAGE_ROOT?>ok2.gif" border="0"></td>
	  <td height="18" class="homebox" bgcolor="#B3CAE3" valign="center">Operatör : <?=$row_arr[$i]?></td>
	</tr>
    <tr> 
      <td bgcolor="#508AC5"></td>
	  <td height="22" class="header_beyaz2" width="30%" bgcolor="#508AC5">Aranan Şebeke</td>
	  <td height="22" class="header_beyaz2" bgcolor="#508AC5">Çağrı Adedi</td>
	  <td height="22" class="header_beyaz2" bgcolor="#508AC5">Toplam Süre</td>
	  <td height="22" class="header_beyaz2" bgcolor="#508AC5">Toplam Tutar</td>	  	  
    </tr>
    <?mysql_data_seek($result,0);
      while ($row = mysqli_fetch_object($result)){
	    if ($row->FROM_PROVIDER_ID ==  $i){
		  if($row->FROM_PROVIDER_ID == $row->TO_PROVIDER_ID)
		    $my_bgcolor = '#FFCC00';
		  else
		    $my_bgcolor = '#BED3E9';
		  ?>
          <tr height="20">
            <td bgcolor="<?=$my_bgcolor?>"></td>
            <td bgcolor="<?=$my_bgcolor?>" width="30%" class="text"><?=$row_arr[$row->TO_PROVIDER_ID]?></td>
			<td bgcolor="<?=$my_bgcolor?>" width="20%" class="text"><?=write_me($row->ADET,3)?></td>
			<td bgcolor="<?=$my_bgcolor?>" width="20%" class="text"><?=write_me($row->SURE,2)?></td>
			<td bgcolor="<?=$my_bgcolor?>" width="25%" class="header"><?=write_me($row->TUTAR,1)?></td>
          </tr>    
    <?  }
	  }?>
         <tr valign="bottom"> 
	       <td height="5" colspan="5" class="header_beyaz2" bgcolor="#508AC5"></td>
          </tr>
          <tr valign="bottom"> 
            <td height="30" colspan="5" class="header"></td>
         </tr>
    <?}?>
	</table>
	</td>
  </tr>
</table>
<?}?>



