﻿<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   if (!defined("IMAGE_ROOT")){ // Note that it should be quoted
      define("IMAGE_ROOT", "/images/");
   }  
   session_cache_limiter('nocache');
//   require_valid_login();
   $cUtility = new Utility();
   $cdb = new db_layer();
   $conn = $cdb->getConnection();
   page_track();
   cc_page_meta(0);
   $SITE_ID = 1;
function get_heavy_sql_result1($sql_id, $result_arr = Array()){
  $cdb = new db_layer();
  $SITE_ID = 1;
    $sql_str = "SELECT ID, NAME, VALUE FROM HEAVY WHERE SQL_ID = '$sql_id'" ;
  if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
     print_error($error_msg);
        exit;
  }
  $str = "";
   $row = mysqli_fetch_object($result);
  $rec_array = explode("##", $row->VALUE);
   for($m=0;$m<sizeof($rec_array)-1;$m++){
      $field_array = explode(";;", $rec_array[$m]);
    $localSiteId = explode("||",$field_array[0]);
    if($localSiteId[1]==$SITE_ID){
        for($k=0;$k<sizeof($field_array);$k++){
      $one_rec = explode("||", $field_array[$k]);
      $result_arr[$m][$k][0] = $one_rec[0];
          $result_arr[$m][$k][1] = $one_rec[1];
      }
      }
    }
  return $result_arr;
}
   
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
  <tr> 
    <td valign="top" height="74"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td valign="bottom" height="96" rowspan="2" width="58%"> 
            <table width="775" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td height="70" valign="bottom" width="199"><a href="http://www.crystalinfo.net/" target = "_blank"><img src="<?=IMAGE_ROOT?>logo1.gif" border="0"></a></td>
                <td height="70" align="center" width="387" valign="bottom"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" width="390" height="70">
                    <param name=movie value="<?=IMAGE_ROOT?>ci_homebanner1.swf">
                    <param name=quality value=high>
                    <embed src="<?=IMAGE_ROOT?>ci_homebanner1.swf" quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="390" height="70">
                    </embed> 
                  </object></td>
                <td height="70" valign="bottom" rowspan="2" width="189"> 
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td align="center" valign="bottom"><img src="<?=IMAGE_ROOT?>hp_left.gif" width="14" height="27"><img src="<?=IMAGE_ROOT?>hp_home.gif" width="27" height="27" border="0"><img src="<?=IMAGE_ROOT?>hp_logout.gif" width="27" height="27" border="0"><img src="<?=IMAGE_ROOT?>hp_login.gif" width="27" height="27" border="0"><img src="<?=IMAGE_ROOT?>hp_passchange.gif" width="27" height="27" border="0"><img src="<?=IMAGE_ROOT?>hp_kisisel.gif" width="27" height="27" border="0"><img src="<?=IMAGE_ROOT?>hp_admin.gif" width="27" height="27" border="0"><img src="<?=IMAGE_ROOT?>hp_right.gif" width="13" height="27"></td>
                    </tr>
                    <tr> 
                      <td background="<?=IMAGE_ROOT?>hp_bg.gif" height="20"></td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr> 
                <td background="<?=IMAGE_ROOT?>menu8.gif" colspan="2"><img src="<?=IMAGE_ROOT?>menu1.gif" width="53" height="26"><a href="/redirect.php"><img src="<?=IMAGE_ROOT?>menu3.gif" width="64" height="26" border="0"></a><img src="<?=IMAGE_ROOT?>menu2.gif" width="25" height="26" border="0"><a HREF="/special/news/news_list.php"><img src="<?=IMAGE_ROOT?>menu4.gif" width="76" height="26" border="0"></a><img src="<?=IMAGE_ROOT?>menu2.gif" width="25" height="26" border="0"><img src="<?=IMAGE_ROOT?>menu5.gif" width="59" height="26" border="0"><img src="<?=IMAGE_ROOT?>menu2.gif" width="25" height="26" border="0"><a HREF="/special/fihrist/rehber.php"><img src="<?=IMAGE_ROOT?>menu6.gif" width="49" height="26" border="0"></a><img src="<?=IMAGE_ROOT?>menu2.gif" width="25" height="26" border="0"><a href="#"><img src="<?=IMAGE_ROOT?>menu9.gif" width="60" height="26" border="0" ></a><img src="<?=IMAGE_ROOT?>menu7.gif" width="35" height="26" border="0"></td>
              </tr>
            </table>
          </td>
          <td width="65%" height="70">&nbsp;</td>
        </tr>
        <tr> 
          <td background="<?=IMAGE_ROOT?>menu8.gif" height="26" width="80%">&nbsp;</td>
        </tr>
        <tr> 
          <td height="21" colspan="2" background="<?=IMAGE_ROOT?>bg_menu_alt.gif">
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td valign="top" align="center" height="95%" background="<?=IMAGE_ROOT?>hp_bg1.gif"> 
      <table width="777" border="0" cellspacing="0" cellpadding="0" height="100%" align="left">
        <tr>
          <td valign="top" background="<?=IMAGE_ROOT?>hp_bg2.gif" width="4"></td>
          <td valign="top" bgcolor="#D8E4F1" width="769" align="center"> 
            <center> 

   <div id="clockDiv" class="homebox" >
     <?=date('d.m.Y H:i:s'); ?>
   </div>
   <script>
     function placeDiv(){
       clockDiv.style.position='absolute';
       clockDiv.style.left=630;
       clockDiv.style.top=99;
     }
     placeDiv();
   </script>

<script language=javascript>
    function src_form_submit(){
        if(document.main_search.src_str.value==''){
            alert("Arama Kriteri Girmediniz!..");
        }else{
            document.main_search.submit();
        }
    }
</script>
 
  <table width="769" border="0" cellspacing="2" cellpadding="1">
      <tr> 
        <td colspan="3" height="0" width="145"></td>
      </tr>
      <tr> 
        <td valign="top" width="145"> 
      <table width="145" border="0" cellspacing="1" cellpadding="1" bgcolor="F0F8FF">
            <tr> 
              <td colspan="2" height="20" bgcolor="#91B5D9" class="header"><img src="<?=IMAGE_ROOT?>ok_header.gif" width="9" height="8">HABERLER</td>
            </tr>
            <tr> 
              <td valign="center" class="text" colspan="2"> 
              <!-- Haberler Start -->
              <?=put_genel_haber_applet();
              ////////////////////////////////////////////////////////////////////////////////
              // 15-  PUT_GENEL_HABER_APPLET: Given a group id, the full name + Note is returned
              ////////////////////////////////////////////////////////////////////////////////
                function put_genel_haber_applet($arg_dept_id=0){
                  $no_recs = TRUE;
                  $lookup = "SELECT SIRA_NO, BASLIK FROM NEWS ORDER BY INSERT_DATE DESC LIMIT 10 ";
                  //// If any records found
                  if ($result = mysql_query($lookup)){
                    $no_recs = FALSE;
                     $num_recs = mysqli_num_rows($result);
                  }
                 // Connection to the database is normally completed
               ?>
                 <applet code=ryLinkScroll codebase="../scripts" width=150 height=79>
                 <param name=Copyright value="LinkScroll © 1999 Cool Focus [www.coolfocus.com]">
               <?
                 $i=1;
                 if ($num_recs>0){
                   while ($row=mysqli_fetch_array($result)){
                     printf("<param name=Entry%d  value=\"%d) %s \">",
                     $i,$i,$row["BASLIK"],CR);
                     $sira=$row["SIRA_NO"];
                     printf("<param name=URL%d value=\"%s%d\">%s",$i,"/special/news/news_win.php?frm_sira_no=",$sira,CR);
                     $i++;
                   }
                 }
               ?>
                 <param name=BgColor value="BED3E9"">
                 <param name=Textcolor value="1b4e81">
                 <param name=FocusTextColor value="FF6600">
                 <param name=PressTextColor value="009000">
                 <param name=Spacing value="2">
                 <param name=Font value="Arial,plain,11">
                 <param name=DefaultTarget value="main">
                 <param name=Speed value="100">
                 <param name=Align value="left"></applet>
                <?}?>
                <!-- Haberler End -->                
            </tr>
            <tr> 
            <?$sql = "SELECT USERS.*, A.EXT_NO AS DAHILI1, B.EXT_NO AS DAHILI2, C.EXT_NO AS DAHILI3, AUTH_CODES.AUTH_CODE AS AUTH_CODE
                      FROM USERS
                        LEFT JOIN EXTENTIONS A ON (USERS.EXT_ID1 = A.EXT_ID)
                        LEFT JOIN EXTENTIONS B ON (USERS.EXT_ID2 = B.EXT_ID)
                        LEFT JOIN EXTENTIONS C ON (USERS.EXT_ID3 = C.EXT_ID)
                        LEFT JOIN AUTH_CODES ON USERS.AUTH_CODE_ID = AUTH_CODES.AUTH_CODE_ID
                      WHERE USER_ID = '".$_SESSION['user_id']."'";
              $cdb->execute_sql($sql, $result, $error);
              $row = mysqli_fetch_object($result);
              $AUTH_CODE = $row->AUTH_CODE;
              $DAHILI1 = $row->DAHILI1;
              $DAHILI2 = $row->DAHILI2;
              $DAHILI3 = $row->DAHILI3;
            ?>
            <tr> 
              <td height="20" colspan="2" bgcolor="#91B5D9" class="header"><img src="<?=IMAGE_ROOT?>ok_header.gif" width="9" height="8">Kişisel Bilgiler</td>
            </tr>
            <tr>
              <td colspan="2" bgcolor="#BED3E9" class="header"><? echo $_SESSION["adi"]."   ".$_SESSION["soyadi"]."<br>";?></td>
            </tr>
            <tr>
              <td class="Header" bgcolor="#BED3E9" height="18">Dahili</td>
              <td class="text" bgcolor="#D8E4F1"><?echo $DAHILI1?$DAHILI1:"";echo $DAHILI2?"-".$DAHILI2:"";echo $DAHILI3?"-".$DAHILI3:""?></td>
            <tr>
              <td class="Header" bgcolor="#BED3E9" height="18">GSM </td>
              <td class="text" bgcolor="#D8E4F1"><?=$row->GSM ?></td>
            </tr>
            <tr>
              <td align="left" colspan="2"></td>
            </tr>
            <tr> 
              <td height="20" colspan="2" bgcolor="#91B5D9" class="header"><img src="<?=IMAGE_ROOT?>ok_header.gif" width="9" height="8">Son Mesajınız</td>
            </tr>
            <?
            $sql = "SELECT * FROM MESSAGES WHERE TRG_USER_ID = '".$_SESSION["user_id"]."' ORDER BY SIRA_NO DESC LIMIT 1";
            $cdb->execute_sql($sql, $result, $error);
            $row = mysqli_fetch_object($result);
            if($row->KONU){?>
            <tr>
              <td colspan="2"  height="18" bgcolor="#BED3E9" class="text">+ <?=substr($row->KONU,0,30)?>
                <a href="javascript:popup('/messages/mesaj_win.php?frm_sira_no=<?=$row->SIRA_NO?>', 'yenipencere',550,450);">>>></a>
              </td>
            </tr>
            <tr> 
              <td colspan="2" bgcolor="#91B5D9" class="header"><img src="<?=IMAGE_ROOT?>resim_tech1.jpg"></td>
            </tr>
            <?}else{?>         
            <tr> 
              <td colspan="2" bgcolor="#91B5D9" class="header"><img src="<?=IMAGE_ROOT?>resim_tech.jpg"></td>
            </tr>
            <?}?>         
          </table>
        </td>
        <td height="100" valign="top" width="425"> 
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td height="6"> 
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr valign="middle" align="center"> 
                    <td height="195"><img border="0" id="topten" src="../last_week_chart.php"></td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr> 
              <td valign="top"> 
              <?
                $res_arr = get_heavy_sql_result1(10);
        $total_mn = 0;$total_price=0;$total_amn=0;
        if (is_array($res_arr)){
                  for($i=0;$i<=sizeof($res_arr);$i++){//Gelen dizi içinde benim sitem bulunmalı
            if ($res_arr[$i][0][1]==$SITE_ID){ //İlgili kaydın ilk alanının değeri
                      $total_mn    += $res_arr[$i][3][1]; //Toplam Süre.Diğer aramalar için eklenmeli.
                      $total_price += $res_arr[$i][4][1]; //Toplam Tutar
                      $total_amn   += $res_arr[$i][5][1];  // Toplam Adet
            switch ($res_arr[$i][2][1]){ //İlgili kaydın arama tipi alanının değeri
            case '0':
                $SI[] = $res_arr[$i][3][1]; // Şehir içi Süre
                          $SI[] = $res_arr[$i][4][1]; // Şehir içi Tutar
                          $SI[] = $res_arr[$i][5][1]; // Şehir içi Adet
              break;
              case 1 :
                          $SA[] = $res_arr[$i][3][1]; //Şehirlerarası Süre
                          $SA[] = $res_arr[$i][4][1]; //Şehirlerarası Tutar
                          $SA[] = $res_arr[$i][5][1]; //Şehirlerarası Adet
              break;
              case 2:
                          $GSM[] = $res_arr[$i][3][1]; //GSM Süre
                          $GSM[] = $res_arr[$i][4][1]; //GSM Tutar
                          $GSM[] = $res_arr[$i][5][1]; //GSM Adet
              break;
                        case 3:
                          $UA[] = $res_arr[$i][3][1];  // Uluslararası Süre
                          $UA[] = $res_arr[$i][4][1];  // Uluslararası Tutar
                          $UA[] = $res_arr[$i][5][1];  // Uluslararası Adet
              break;
                        default :
            }
            }
          }
                }
          $M_LEN = date("t");//Ayda bulunan gün adedi
        $M_DAY = date("j");//Ayın bulunulan günü
              ?>
<script>
  function chg_chart(val){
    document.all('topten').src =  val;
  }
</script>
<?//Kişisel forecast için dahili kriteri oluşturuluyor.
  if(!$DAHILI1) $DAHILI1 = "99999";
  if($DAHILI2) $DAHILI1 .= ",". $DAHILI2;
  if($DAHILI3) $DAHILI1 .= ",". $DAHILI3;
?>

             <table width="100%" border="0" cellspacing="2" cellpadding="0" height="100%">
               <tr> 
                 <td valign="top" class="text"> 
                 <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="F0F8FF">
                   <tr> 
                    <td height="30" colspan="5" bgcolor="#91B5D9" align="center" class="header">Şirket Genel Durumu ve Ay Sonu Tahmini</td>
                   </tr>
                   <tr> 
                     <td rowspan="2" height="41" bgcolor="#508AC5" align="center">
                       <img src="<?=IMAGE_ROOT?>genel.gif" title="Şirket Genel"></td>
                     <td colspan="2" align="center" valign="bottom" height="25" bgcolor="#FFCC00" class="header_sm">BU AY</td>
                     <td colspan="2" align="center" valign="bottom" height="25" bgcolor="#FFCC00" class="header_sm">AY SONU TAHMİNİ</td>
                   </tr>
                   <tr> 
                     <td height="25" bgcolor="#508AC5" width="21%" align="center" class="header_beyaz2">Süre</td>
                     <td bgcolor="#508AC5" width="19%" align="center" class="header_beyaz2">Tutar(TL)</td>
                     <td bgcolor="#508AC5" width="21%" align="center" class="header_beyaz2">Süre</td>
                     <td bgcolor="#508AC5" width="22%" align="center" class="header_beyaz2">Tutar(TL)</td>
                   </tr>
                   <tr> 
                     <td height="22" bgcolor="#FFCC00" width="17%" class="header">Ş.İçi</td>
                     <td bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time($SI[0]) ?></td>
                     <td bgcolor="#91B5D9" width="19%" align="right" class=text"><?=write_price($SI[1]) ?></font></td>
                     <td bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time(($SI[0] * $M_LEN) / $M_DAY)?></td>
                     <td bgcolor="#91B5D9" width="22%" align="right" class=text"><?=write_price((($SI[1] * $M_LEN) / $M_DAY))?></td>
                   </tr>
                   <tr> 
                     <td height="22" bgcolor="ECBD00" width="17%" class="header">Ş.Arası</td>
                     <td bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time($SA[0]) ?></td>
                     <td bgcolor="#BED3E9" width="19%" align="right" class=text"><?=write_price($SA[1]) ?></td>
                     <td bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time(($SA[0] * $M_LEN) / $M_DAY) ?></td>
                     <td bgcolor="#BED3E9" width="22%" align="right" class=text"><?=write_price((($SA[1] * $M_LEN) / $M_DAY))?></td>
                   </tr>
                   <tr> 
                     <td height="22" bgcolor="FFCC00" width="17%" class="header">GSM</td>
                     <td bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time($GSM[0]) ?></td>
                     <td bgcolor="#91B5D9" width="19%" align="right" class=text"><?=write_price($GSM[1]) ?></td>
                     <td bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time(($GSM[0] * $M_LEN) / $M_DAY)?></td>
                     <td bgcolor="#91B5D9" width="22%" align="right" class=text"><?=write_price((($GSM[1] * $M_LEN) / $M_DAY))?></td>
                   </tr>
                   <tr> 
                     <td height="22" bgcolor="#ECBD00" width="17%" class="header">U.Arası</td>
                     <td bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time($UA[0]) ?></td>
                     <td bgcolor="#BED3E9" width="19%" align="right" class=text"><?=write_price($UA[1]) ?></td>
                     <td bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time(($UA[0] * $M_LEN) / $M_DAY)?></td>
                     <td bgcolor="#BED3E9" width="22%" align="right" class=text"><?=write_price((($UA[1] * $M_LEN) / $M_DAY))?></td>
                   </tr>
                   <tr> 
                     <td height="25" width="17%" bgcolor="#508AC5" class="header_beyaz2">Toplam</td>
                     <td width="21%" align="right" bgcolor="#508AC5" class="header_beyaz2"><?=calculate_all_time($total_mn) ?></td>
                     <td width="19%" align="right" bgcolor="#508AC5" class="header_beyaz2"><?=write_price($total_price) ?></td>
                     <td width="21%" align="right" bgcolor="#508AC5" class="header_beyaz2"><?=calculate_all_time(($total_mn * $M_LEN) / $M_DAY) ?></td>
                     <td width="22%" align="right" bgcolor="#508AC5" class="header_beyaz2"><?=write_price((($total_price * $M_LEN) / $M_DAY))?></td>
                   </tr>
                 </table>
                 </td>
               </tr>
             </table>
           </div>                              
           </td>
         </tr>
         <tr> 
           <td height="6"></td>
         </tr>
       </table>
       </td>
       <td height="56" valign="top" width="175"> 
       <table width="100%" border="0" cellspacing="0" cellpadding="0">
         <tr> 
           <td valign="top" height="7"> 
           <table width="175"  border="0" cellspacing="0" cellpadding="0" bgcolor="358BCF">
           <form name="main_search" action="main_src.php?act=src" method=post>
             <tr> 
               <td width="20%"><img src="<?=IMAGE_ROOT?>ara1.gif" width="29" height="28"></td>
               <td width="60%"><input type="text" name="src_str" size="12" CLASS="input1"></td>
         <td width="20%"><a href="javascript:src_form_submit();"><img src="<?=IMAGE_ROOT?>ara2.gif" border="0"></a></td>
             </tr>
           </form>
           </table>
           </td>
         </tr>
         <tr> 
           <td valign="top" height="1"></td>
         </tr>
         <tr> 
           <td valign="top"> 
           <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#FFFFFF">
             <tr valign="bottom"> 
               <td height="22" colspan="2" class="header" bgcolor="#88ACD5"><img src="<?=IMAGE_ROOT?>ok_header.gif" width="9" height="8">Bu Ay Giden Çağrılar</td>
             </tr>
             <tr> 
               <td height="15" width="20%" class="text_beyaz" align="center" bgcolor="#B3CAE3"><img src="<?=IMAGE_ROOT?>ok2.gif" border="0"></td>
               <td height="15" class="text_beyaz" width="80%" bgcolor="#D8E4F1">Şehir İçi : <?=$SI[2];?></td>
             </tr>
             <tr> 
               <td height="15" width="20%" class="text_beyaz" align="center" bgcolor="#B3CAE3"><img src="<?=IMAGE_ROOT?>ok2.gif" border="0"></td>
               <td height="15" class="text_beyaz" width="80%" bgcolor="#E6EEF7">Şehirlerarası : <?=$SA[2];?></td>
             </tr>
             <tr> 
               <td height="15" width="20%" class="text_beyaz" align="center" bgcolor="#B3CAE3"><img src="<?=IMAGE_ROOT?>ok2.gif" border="0"></td>
               <td height="15" class="text_beyaz" width="80%" bgcolor="#D8E4F1">GSM : <?=$GSM[2];?></td>
             </tr>
             <tr> 
               <td height="15" width="20%" class="text_beyaz" align="center" bgcolor="#B3CAE3"><img src="<?=IMAGE_ROOT?>ok2.gif" border="0"></td>
               <td height="15" class="text_beyaz" width="80%" bgcolor="#E6EEF7">Uluslararası : <?=$UA[2];?></td>
             </tr>
             <tr valign="bottom"> 
               <td height="22" colspan="2" class="header" bgcolor="#88ACD5"><img src="<?=IMAGE_ROOT?>ok_header.gif" width="9" height="8">Bu Ay Giden Çağrılar</td>
             </tr>
             <tr> 
               <td height="18" colspan="2" class="text_beyaz" align="CENTER" bgcolor="#BED1E7"><br>
                 <img id="chart_outbound" src="/special/chart_outbound.php"><!-- OutBound End -->                      
                 <img src="<?=IMAGE_ROOT?>sure.gif"  style="cursor:hand" Value="Süre" CLASS="buton_ch" onclick="document.all('chart_outbound').src='/special/chart_outbound.php?p=sure'">
                 <img src="<?=IMAGE_ROOT?>tutar.gif" style="cursor:hand" Value="price" CLASS="buton_ch" onclick="document.all('chart_outbound').src='/special/chart_outbound.php?p=price'">
                 <img src="<?=IMAGE_ROOT?>adet.gif"  style="cursor:hand" Value="Adet" CLASS="buton_ch" onclick="document.all('chart_outbound').src='/special/chart_outbound.php?p=adet'">
                 <br>
               </td>
             </tr>
       <tr bgcolor="#9BBADB"> 
               <td height="22" colspan="2" class="header" bgcolor="#88ACD5"><img src="<?=IMAGE_ROOT?>ok_header.gif" width="9" height="8">Çağrı Başına Ortalama</td>
             </tr>
             <tr> 
               <td height="18" width="20%" class="text_beyaz" align="center" bgcolor="#B3CAE3"><img src="<?=IMAGE_ROOT?>ok2.gif" border="0"></td>
               <td height="18" class="text_beyaz" width="80%" bgcolor="#D8E4F1">Süre: <?if($total_amn > 0){echo calculate_all_time(round($total_mn/$total_amn));}else{echo '0';}?></td>
             </tr>
             <tr> 
               <td height="18" width="20%" class="text_beyaz" align="center" bgcolor="#B3CAE3"><img src="<?=IMAGE_ROOT?>ok2.gif" border="0"></td>
               <td height="18" class="text_beyaz" width="80%" bgcolor="#E6EEF7">Tutar: <?if($total_amn > 0){echo write_price(round($total_price/$total_amn));}else{echo '0';}?> TL</td>
             </tr>
             <tr> 
               <td height="22" colspan="2" bgcolor="#91B5D9" class="header"><img src="<?=IMAGE_ROOT?>ok_header.gif" width="9" height="8">CRYSTALINFO ' dan</td>
             </tr>
             <tr> 
               <td valign="top" height="18" colspan="2" bgcolor="#BED3E9" class="text"> 
               <?     ////////////////COMPANY AVERAGE
               $res_arr = get_heavy_sql_result1(8);
               if (is_array($res_arr)){
                 for($i=0;$i<=sizeof($res_arr);$i++){//Gelen dizi içinde benim sitem bulunmalı
                   if ($res_arr[$i][0][1]==$SITE_ID){ //İlgili kaydın ilk alanının değeri
                     $COMP_AVG = $res_arr[$i][1][1];
                   }
                 }
               }
               $res_arr = get_heavy_sql_result1(9);
               if (is_array($res_arr)){
                 for($i=0;$i<=sizeof($res_arr);$i++){//Gelen dizi içinde benim sitem bulunmalı
                   if ($res_arr[$i][0][1]==$SITE_ID){ //İlgili kaydın ilk alanının değeri
                     $EXT_COUNT = $res_arr[$i][1][1];
                   }
                 }
               }
               if($EXT_COUNT > 0) {
                 $PRICE = ($COMP_AVG / $EXT_COUNT);
                 $PRICE = write_price($PRICE);
               echo " <b> Dünkü kullanımınız :</b><br>Dahili başına ortalama <br>".$PRICE." TL'dir.";
               }
               ?>
               </td>
             </tr>
           </table>
         </td>
       </tr>
       <tr> 
         <td valign="top" height="5">
         <TABLE>
   </TABLE>         
         </td>
       </tr>
    </table>
    </td>
 </tr>
</table>
<?page_footer("");?>