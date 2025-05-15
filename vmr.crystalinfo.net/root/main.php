<?
   //require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   require_once $_SERVER['DOCUMENT_ROOT'].'/cgi-bin/functions.php';
   date_default_timezone_set('Europe/Istanbul');
   
   // ini_set('display_errors', 'On');
	//error_reporting(E_ALL ^ E_NOTICE);

     session_cache_limiter('nocache');
   
   if (!defined("IMAGE_ROOT")){ // Note that it should be quoted
      define("IMAGE_ROOT", "/images/");
   }  

   require_valid_login();  
   
   
   $SITE_ID = $_SESSION['site_id'];

  //Hak Kontrolü

   $usr_crt = "";
    if (right_get("SITE_ADMIN")){
      //Site admin hakkı varsa herşeyi görebilir.  
      //Site id gelmemişse kişinin bulunduğu site raporu alınır.
      if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
      // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
      $SITE_ID = $_SESSION['site_id'];
    }elseif(got_dept_right($_SESSION["user_id"])==1){
      //Bir departmanın raporunu görebiliyorsa kendi sitesindekileri girebilir.
      $SITE_ID = $_SESSION['site_id'];
      //echo $dept_crt = get_depts_crt($_SESSION["user_id"]);
      $usr_crt = get_users_crt($_SESSION["user_id"], 3, $SITE_ID);
      $alert = "Bu rapor sadece sizin yetkinizde olan departmanlara ait dahililerin bilgilerini içerir.";
    }else{
      print_error("Bu sayfayı Görme Hakkınız Yok!!!");
      exit;
    }

  //  echo  $usr_crt;
  //Hak kontrolü sonu  
  
   
   $cUtility = new Utility();
   $cdb = new db_layer();
   $conn = $cdb->getConnection();
   page_track();
   cc_page_meta(0);
   page_header("");
   $SITE_ID = $_SESSION['site_id'];
   
?>

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
              <td valign="center" height="90 class="text" colspan="2"> 
            

<?

$haberler="SELECT SIRA_NO, BASLIK FROM NEWS ORDER BY INSERT_DATE DESC LIMIT 10 ";

    if (!($cdb->execute_sql($haberler, $rs_haberler, $error_msg))){
        print_error($error_msg);
                exit;
}
   echo "<marquee align='middle' scrollamount='1' height='88' width='80%' direction='up' scrolldelay='0'><font color='navy'>";      
   while($row = mysqli_fetch_array($rs_haberler))
   echo "{$row['BASLIK']}<br>-----------------------------<br>";
   echo "</font></marquee>";

?>

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
              <td height="20" colspan="2" bgcolor="#91B5D9" class="header"><img src="<?=IMAGE_ROOT?>ok_header.gif" width="9" height="8">Kullanıcı ve Erişim Bilgileri</td>
            </tr>
            <tr>
              <td colspan="2" bgcolor="#BED3E9" class="header"><? echo $_SESSION["adi"]."   ".$_SESSION["soyadi"]."<br>";?></td>
            </tr>
            <tr>
              <td class="Header" bgcolor="#BED3E9" height="18" width="25">IP:</td>
              <td class="text" bgcolor="#D8E4F1" ><?echo $_SERVER['REMOTE_ADDR'];?></td>
	
	            </tr>
				
            <tr>
			  <td class="Header" bgcolor="#BED3E9" height="18" width="25">Login:</td>
              <td class="text" bgcolor="#D8E4F1"><?
						if (!isset($_SESSION['start_time'])){
							$str_time = time();
							$_SESSION['start_time'] = $str_time;
						}
							$date1 = $_SESSION['start_time'];
						echo date("d.m.y H:i:s", $date1);
					?>
				</td>
	        </tr>
			
			<tr>
			  <td class="Header" bgcolor="#BED3E9" height="18" width="25">Sure:</td>
              <td class="text" bgcolor="#D8E4F1"><?

							$date1 = new DateTime(date("Y-m-d H:i:s", $date1));
							
							$date2 = new DateTime(date("Y-m-d H:i:s"));
				
							$diff=date_diff($date1,$date2);
							echo $diff->format('%d %H:%i:%s');
							
					?>
				</td>
	        </tr>
			
			<!--
		   <tr>
              <td align="left" colspan="2"><?//echo "<a href=\"./users/user.php?act=upd&id=".$_SESSION['user_id']."\"><img border=\"0\" src=\"/images/guncelle.gif\"></a><BR></TD>";?>
            </tr> -->
            
			
			<tr> 
              <td height="20" colspan="2" bgcolor="#91B5D9" class="header"><img src="<?=IMAGE_ROOT?>ok_header.gif" width="9" height="8">Sistem Durumu</td>
            </tr>
       
         
 			<tr>
            <td height="20" colspan="2" bgcolor="#CEDBE3">
        <a href="/admin/sanity.php" onmouseover="javascript:show_hide(20)" onmouseout="javascript:show_hide(20)">
        <img border="0" align="center" src="<?=IMAGE_ROOT?>info.gif" alt="">  Sistem Detayları </a> 
		</td>
				</tr>
				
            
  <tr>
    <td width="100%" colspan="2" align="center" height="195">
      <?//////////////////////DISK STATUS//////////////////////////////
            unset($aa);
			exec((" df /| awk '{ print $5 }'"), $aa) ;
            $dolu = str_replace("%", "", $aa[1]);
            $bos = 100 -$dolu;
            ?>
        <iframe src="../chart/disk_stat1.php?dolu=<?=$dolu?>&bos=<?=$bos?>" width="180" height="188"></iframe>
      <?//////////////////////END OF DISK STATUS//////////////////////////////?>      
      <hr>
      </td>
  </tr>

  
  <tr>  
    <td class="Header" bgcolor="#BED3E9" height="18" width="25">DB:</td>
	
	 <td class="text" bgcolor="#D8E4F1">    
      <? 
		  unset($aa);
          exec(("du -h /var/lib/mysql/MCRYSTALINFONE/ | awk '{ print $1 }'"), $aa) ;
          $k =0;
          echo $aa[0];
      ?>

     </td>  
	
		
  </tr> 
    
                    
          </table>
        </td>
        <td height="100" valign="top" width="400"> 
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td height="6"> 
                <table width="100%" border="0" cellspacing="0" cellpadding="0">

                  <tr valign="middle" align="center"> 
				  			<tr valign="bottom" align="center"> 
               <td height="20" colspan="2" class="header" bgcolor="#88ACD5">Son 10 Günün Çağrı Grafiği</td>
             </tr>
                    <td  bgcolor="#BED3E9" height="255"><iframe src="chart/last_week.php" width="99%" height="100%"></iframe></td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr> 
              <td valign="top"> 
              <?
 
	$month = date("m");
    $year = date("Y");
    $cdr_table="CDR_MAIN_".$month."_".$year."";
	$SITE_ID = $_SESSION['site_id'];	
	
	$MY_YEAR_MONTH ="LIKE '".$year."-".$month."%'";
	//echo $MY_YAR_MONTH;
		    					
$val = mysqli_query($conn,"select 1 from ".$cdr_table."  LIMIT 1");

if($val == FALSE)
{
   $cdr_table="CDR_MAIN_DATA";
}
else
{
    $cdr_table="CDR_MAIN_".$month."_".$year."";
}					
								// SEHIR ICI HESAPLAMA
                           $sql_si = "SELECT SUM(PRICE) AS PRICE, 
						               SUM(DURATION) AS SURE,
									   COUNT(".$cdr_table.".CDR_ID) AS ADET
                                       FROM ".$cdr_table."
                                       WHERE SITE_ID = ".$SITE_ID." AND ERR_CODE = 0 AND CALL_TYPE = 1 AND LocationTypeid = 0 AND MY_DATE ".$MY_YEAR_MONTH." ".$usr_crt." " ; 
									   
                           $cdb->execute_sql($sql_si, $result, $error);
						   $row = mysqli_fetch_object($result);
							
					$SI_SURE = $row->SURE;
					$SI_PRICE = $row->PRICE;
					$SI_ADET = $row->ADET;
					
								// SEHIRLER ARASI HESAPLAMA
                           $sql_sa = "SELECT SUM(PRICE) AS PRICE, 
						               SUM(DURATION) AS SURE,
									   COUNT(".$cdr_table.".CDR_ID) AS ADET
                                       FROM ".$cdr_table."
                                       WHERE SITE_ID = ".$SITE_ID." AND ERR_CODE = 0 AND CALL_TYPE = 1 AND LocationTypeid = 1 AND MY_DATE ".$MY_YEAR_MONTH." ".$usr_crt." " ; 
									   
                           $cdb->execute_sql($sql_sa, $result, $error);
						   $row = mysqli_fetch_object($result);
							
					$SA_SURE = $row->SURE;
					$SA_PRICE = $row->PRICE;
					$SA_ADET = $row->ADET;
						
								// GSM HESAPLAMA
                           $sql_gsm = "SELECT SUM(PRICE) AS PRICE, 
						               SUM(DURATION) AS SURE,
									   COUNT(".$cdr_table.".CDR_ID) AS ADET
                                       FROM ".$cdr_table."
                                       WHERE SITE_ID = ".$SITE_ID." AND ERR_CODE = 0 AND CALL_TYPE = 1 AND LocationTypeid = 2 AND MY_DATE ".$MY_YEAR_MONTH." ".$usr_crt." " ; 
									   
                           $cdb->execute_sql($sql_gsm, $result, $error);
						   $row = mysqli_fetch_object($result);
							
					$GSM_SURE = $row->SURE;
					$GSM_PRICE = $row->PRICE;
					$GSM_ADET = $row->ADET;
					
							// UA HESAPLAMA
                           $sql_ua = "SELECT SUM(PRICE) AS PRICE, 
						               SUM(DURATION) AS SURE,
									   COUNT(".$cdr_table.".CDR_ID) AS ADET
                                       FROM ".$cdr_table."
                                       WHERE SITE_ID = ".$SITE_ID." AND ERR_CODE = 0 AND CALL_TYPE = 1 AND LocationTypeid = 3 AND MY_DATE ".$MY_YEAR_MONTH." ".$usr_crt." " ; 
									   
                           $cdb->execute_sql($sql_ua, $result, $error);
						   $row = mysqli_fetch_object($result);
							
					$UA_SURE = $row->SURE;
					$UA_PRICE = $row->PRICE;
					$UA_ADET = $row->ADET;
					
					
							// TOTAL HESAPLAMA
					  $total_mn = 0;$total_price=0;$total_amn=0;
									
					  $total_mn    = $SI_SURE + $SA_SURE + $GSM_SURE + $UA_SURE; //Toplam Süre.Diğer aramalar için eklenmeli.
                      $total_price = $SI_PRICE + $SA_PRICE + $GSM_PRICE + $UA_PRICE; //Toplam Tutar
                      $total_amn   = $SI_ADET + $SA_ADET + $GSM_ADET + $UA_ADET; //Toplam Adet
			
				
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
                    <td height="30" colspan="5" bgcolor="#91B5D9" align="center" class="header">Şirket Genel Durumu ve Ay Sonu Tahmini (Yetki Bazında)</td>
                   </tr>
                   <tr> 
                     <td rowspan="2" height="41" bgcolor="#508AC5" align="center" onclick="javascript:popup('/reports/forecast_pers.php?DAHILI1=<?=$DAHILI1?>','forecast',450,200)" style="cursor:hand">
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
                     <td bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time($SI_SURE) ?></td>
                     <td bgcolor="#91B5D9" width="19%" align="right" class=text"><?=write_price($SI_PRICE) ?></font></td>
                     <td bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time(($SI_SURE * $M_LEN) / $M_DAY)?></td>
                     <td bgcolor="#91B5D9" width="22%" align="right" class=text"><?=write_price((($SI_PRICE * $M_LEN) / $M_DAY))?></td>
                   </tr>
                   <tr> 
                     <td height="22" bgcolor="ECBD00" width="17%" class="header">Ş.Arası</td>
                     <td bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time($SA_SURE) ?></td>
                     <td bgcolor="#BED3E9" width="19%" align="right" class=text"><?=write_price($SA_PRICE) ?></td>
                     <td bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time(($SA_SURE * $M_LEN) / $M_DAY) ?></td>
                     <td bgcolor="#BED3E9" width="22%" align="right" class=text"><?=write_price((($SA_PRICE * $M_LEN) / $M_DAY))?></td>
                   </tr>
                   <tr> 
                     <td height="22" bgcolor="FFCC00" width="17%" class="header">GSM</td>
                     <td bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time($GSM_SURE) ?></td>
                     <td bgcolor="#91B5D9" width="19%" align="right" class=text"><?=write_price($GSM_PRICE) ?></td>
                     <td bgcolor="#91B5D9" width="21%" align="right" class=text"><?=calculate_all_time(($GSM_SURE * $M_LEN) / $M_DAY)?></td>
                     <td bgcolor="#91B5D9" width="22%" align="right" class=text"><?=write_price((($GSM_PRICE * $M_LEN) / $M_DAY))?></td>
                   </tr>
                   <tr> 
                     <td height="22" bgcolor="#ECBD00" width="17%" class="header">U.Arası</td>
                     <td bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time($UA_SURE) ?></td>
                     <td bgcolor="#BED3E9" width="19%" align="right" class=text"><?=write_price($UA_PRICE) ?></td>
                     <td bgcolor="#BED3E9" width="21%" align="right" class=text"><?=calculate_all_time(($UA_SURE * $M_LEN) / $M_DAY)?></td>
                     <td bgcolor="#BED3E9" width="22%" align="right" class=text"><?=write_price((($UA_PRICE * $M_LEN) / $M_DAY))?></td>
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

         </tr>
         <tr> 
           <td valign="top" height="1"></td>
         </tr>
         <tr> 
           <td valign="top"> 
           <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#FFFFFF">

             <tr valign="bottom"> 
               <td height="20" colspan="2" class="header" bgcolor="#88ACD5">Bu Ay Giden Arama(adet)</td>
             </tr>
             <tr> 
               <td height="130" colspan="2" class="text_beyaz" align="CENTER" bgcolor="#BED1E7"><br>
			<iframe src="chart/chart_outb_adet.php" width="97%" height="100%"></iframe>
                 <br>
               </td>
			   
             </tr>
			 <tr>
			 <td valign="top" height="18" colspan="2" bgcolor="#BED3E9" class="text"> 
			</tr>			 
			  <tr valign="bottom"> 
               <td height="20" colspan="2" class="header" bgcolor="#88ACD5">Bu Ay Giden Arama (sure)</td>
             </tr>
             <tr> 
               <td height="130" colspan="2" class="text_beyaz" align="CENTER" bgcolor="#BED1E7"><br>
			<iframe src="chart/chart_outb_sure.php" width="97%" height="100%"></iframe>
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
               <td valign="top" height="18" colspan="2" bgcolor="#BED3E9" class="text"> 
               <?     ////////////////COMPANY AVERAGE


				$day = date("d");
				$year = date("Y");
				$month = date("m");
				if($day == 1){
				$day1 = date("d");
				}else {$day1 = date("d")-1;}
			    
				
				$MY_DATE = $year.'-'.$month.'-'.$day1;
				
			   $DAHILI = str_replace(",", "','", $DAHILI1);
			   
			   $sql_my = "SELECT SUM(PRICE) AS PRICE, 
						               SUM(DURATION) AS SURE,
									   COUNT(".$cdr_table.".CDR_ID) AS ADET
                                       FROM ".$cdr_table."
                                       WHERE SITE_ID = ".$SITE_ID." AND ERR_CODE = 0 AND CALL_TYPE = 1 AND ".$cdr_table.".ORIG_DN IN('".$DAHILI."') AND MY_DATE='".$MY_DATE."'" ; 
									   
                           $cdb->execute_sql($sql_my, $result, $error);
						   $row = mysqli_fetch_object($result);
							
					$MY_PRICE = $row->PRICE;

               echo " <b> Dünkü kullanımınız:</b><br> <br>".$MY_PRICE." TL'dir.";
			   
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
<script>
  setTimeout(function (){
    document.location.href = "/logout.php";
}, 7200000);
</script>
<?page_footer("");?>