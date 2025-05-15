<?
 require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
 require_valid_login();
 cc_page_meta();
 $cUtility = new Utility();
 $cdb = new db_layer();
 $SITE_ID = $_SESSION['site_id'];

  $t0 = date("Y-m-d");
  $t1 = date("Y-m-01");
  $CDR_MAIN_DATA = getTableName($t0,$t1);
  if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";
  //echo $t0.$t1.$CDR_MAIN_DATA;exit;
 ///////////////////////////////////////////Süre - Ücret
  $sql_str = "SELECT TLocationType.LocationType as NAME, CDR_MAIN_DATA.LocationTypeid as MTR_ID,
                SUM(PRICE) AS PRICE, SUM(DURATION) AS DURATION
              FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
              INNER JOIN TLocationType ON TLocationType.LocationTypeid = CDR_MAIN_DATA.LocationTypeid
              WHERE SITE_ID = $SITE_ID AND ERR_CODE=0 AND CALL_TYPE = 1 AND CDR_MAIN_DATA.ORIG_DN IN($DAHILI1) AND 
         MY_DATE <= DATE_FORMAT(NOW(),\"%Y-%m-%d\") AND MY_DATE >= CONCAT(DATE_FORMAT(NOW(),\"%Y-%m\"),'-','01') 
              GROUP BY CDR_MAIN_DATA.LocationTypeid
      ";
  $M_LEN = date("t");//Ayda bulunan gün adedi
  $M_DAY = date("j");//Ayın bulunulan günü
//  echo $sql_str;exit;
  if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
    print_error($error_msg);
    exit;
  }
  $total_prs_pr = 0;
  $total_prs_dr = 0;
  if(mysqli_num_rows($result)>0){
    while($row = mysqli_fetch_object($result)){
      $total_prs_pr += $row->PRICE; 
      $total_prs_dr += $row->DURATION;
      switch($row->MTR_ID){
        case "0":
          $PRS_SI[] = $row->DURATION;
          $PRS_SI[] = $row->PRICE;
          break;
        case "1":
          $PRS_SA[] = $row->DURATION;
          $PRS_SA[] = $row->PRICE;
          break;
        case "2":
          $PRS_GSM[] = $row->DURATION;
          $PRS_GSM[] = $row->PRICE;
          break;
        case "3":
          $PRS_UA[] = $row->DURATION;
          $PRS_UA[] = $row->PRICE;
          break; 
      }
    }
  }
?>
<table width="100%" border="0" cellspacing="10" cellpadding="0" height="100%">
  <tr> 
    <td valign="top" class="text"> 
    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="F0F8FF">
      <tr> 
        <td height="20" colspan="5" bgcolor="#91B5D9" align="center" class="header">Kişisel Durum ve Ay Sonu Tahmini</td>
      </tr>
    <tr> 
        <td rowspan="2" height="41" bgcolor="#508AC5" align="center">
        <img src="<?=IMAGE_ROOT?>kisisel.gif" title="Kişisel">
    </td>
        <td colspan="2" align="center" valign="bottom" height="18" bgcolor="#FFCC00" class="header_sm">BU AY</td>
        <td colspan="2" align="center" valign="bottom" height="18" bgcolor="#FFCC00" class="header_sm">AY SONU TAHMİNİ</td>
      </tr>
      <tr> 
        <td height="2" bgcolor="#508AC5" width="21%" align="center" class="header_beyaz2">Süre</td>
        <td height="2" bgcolor="#508AC5" width="19%" align="center" class="header_beyaz2">Tutar(TL)</td>
        <td height="2" bgcolor="#508AC5" width="21%" align="center" class="header_beyaz2">Süre</td>
        <td height="2" bgcolor="#508AC5" width="22%" align="center" class="header_beyaz2">Tutar(TL)</td>
      </tr>
      <tr> 
        <td height="20" bgcolor="#FFCC00" width="17%" class="header">Ş.İçi</td>
        <td height="20" bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time($PRS_SI[0])?></td>
        <td height="20" bgcolor="#91B5D9" width="19%" align="right" class=text"><?=write_price($PRS_SI[1]) ?></td>
        <td height="20" bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time(($PRS_SI[0] * $M_LEN) / $M_DAY)?></td>
        <td height="20" bgcolor="#91B5D9" width="22%" align="right" class=text"><?=write_price((($PRS_SI[1] * $M_LEN) / $M_DAY))?></td>
      </tr>
      <tr> 
        <td height="18" bgcolor="#ECBD00" width="17%" class="header">Ş.Arası</td>
        <td height="20" bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time($PRS_SA[0])?></td>
        <td height="20" bgcolor="#BED3E9" width="19%" align="right" class=text"><?=write_price($PRS_SA[1]) ?></td>
        <td height="20" bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time(($PRS_SA[0] * $M_LEN) / $M_DAY)?></td>
        <td height="20" bgcolor="#BED3E9" width="22%" align="right" class=text"><?=write_price((($PRS_SA[1] * $M_LEN) / $M_DAY))?></td>
      </tr>
      <tr> 
        <td height="18" bgcolor="#FFCC00" width="17%" class="header">GSM</td>
        <td height="20" bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time($PRS_GSM[0])?></td>
        <td height="20" bgcolor="#91B5D9" width="19%" align="right" class=text"><?=write_price($PRS_GSM[1]) ?></td>
        <td height="20" bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time(($PRS_GSM[0] * $M_LEN) / $M_DAY)?></td>
        <td height="20" bgcolor="#91B5D9" width="22%" align="right" class=text"><?=write_price((($PRS_GSM[1] * $M_LEN) / $M_DAY))?></td>
      </tr>
      <tr> 
        <td height="18" bgcolor="#ECBD00" width="17%" class="header">Ü.Arası</td>
        <td height="20" bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time($PRS_UA[0])?></td>
        <td height="20" bgcolor="#BED3E9" width="19%" align="right" class=text"><?=write_price($PRS_UA[1]) ?></td>
        <td height="20" bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time(($PRS_UA[0] * $M_LEN) / $M_DAY)?></td>
        <td height="20" bgcolor="#BED3E9" width="22%" align="right" class=text"><?=write_price((($PRS_UA[1] * $M_LEN) / $M_DAY))?></td>
      </tr>
      <tr> 
        <td height="24" width="17%" bgcolor="#508AC5" class="header_beyaz2">Toplam</td>
        <td height="24" width="21%" align="right" bgcolor="#508AC5" class="header_beyaz2"><?=calculate_all_time($total_prs_dr) ?></td>
        <td height="24" width="19%" align="right" bgcolor="#508AC5" class="header_beyaz2"><?=write_price($total_prs_pr) ?></td>
        <td height="24" width="21%" align="right" bgcolor="#508AC5" class="header_beyaz2"><?=calculate_all_time(($total_prs_dr * $M_LEN) / $M_DAY) ?></td>
        <td height="24" width="22%" align="right" bgcolor="#508AC5" class="header_beyaz2"><?=write_price((($total_prs_pr * $M_LEN) / $M_DAY))?></td>
      </tr>
    </table>
    </td>
  </tr>
</table>
</body>
</html>
